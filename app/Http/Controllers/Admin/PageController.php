<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\StoragePublicUrl;
use App\Models\City;
use App\Models\Page;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Page::with(['state:id,name,slug', 'city:id,name,slug,state_id'])->orderBy('sort_order')->orderBy('title');
        if ($request->filled('scope')) {
            if ($request->scope === 'global') {
                $query->global();
            } elseif ($request->scope === 'state' && $request->filled('state_id')) {
                // State-level pages and all city pages in that state (city rows store state_id too).
                $query->where('state_id', (int) $request->state_id);
            } elseif ($request->scope === 'city' && $request->filled('city_id')) {
                $query->where('city_id', (int) $request->city_id);
            }
        }

        $perPage = min(max((int) $request->input('per_page', 20), 5), 100);

        $paginator = $query->paginate($perPage);
        $paginator->getCollection()->transform(function (Page $page) {
            $page->setAttribute('content', StoragePublicUrl::rewriteStorageUrlsInHtml($page->content ?? ''));

            return $page;
        });

        return response()->json($paginator);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'template' => 'nullable|string|max:50',
            'status' => 'nullable|in:draft,published',
            'sort_order' => 'nullable|integer',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);
        $data['status'] = $data['status'] ?? 'draft';
        $data['sort_order'] = $data['sort_order'] ?? 0;
        if (! empty($data['city_id'])) {
            $data['state_id'] = City::find($data['city_id'])->state_id ?? null;
        } elseif (empty($data['state_id'])) {
            $data['state_id'] = null;
            $data['city_id'] = null;
        } else {
            $data['city_id'] = null;
        }

        $data['slug'] = $this->normalizePageSlug($data['slug'] ?? null, $data['title']);
        $data['slug'] = $this->dedupeLocationSuffixInSlug(
            $data['slug'],
            $data['state_id'] ?? null,
            $data['city_id'] ?? null
        );

        if (array_key_exists('content', $data) && $data['content'] !== null) {
            $data['content'] = StoragePublicUrl::normalizeStorageUrlsInHtml($data['content']);
        }

        $page = Page::create($data);
        $page->load(['state:id,name,slug', 'city:id,name,slug,state_id']);
        $page->setAttribute('content', StoragePublicUrl::rewriteStorageUrlsInHtml($page->content ?? ''));

        return response()->json(['message' => 'Created', 'page' => $page]);
    }

    public function update(Request $request, Page $page): JsonResponse
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'template' => 'nullable|string|max:50',
            'status' => 'nullable|in:draft,published',
            'sort_order' => 'nullable|integer',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);
        if (isset($data['city_id']) && $data['city_id']) {
            $data['state_id'] = City::find($data['city_id'])->state_id ?? $page->state_id;
        } elseif (isset($data['state_id']) && !$data['state_id']) {
            $data['city_id'] = null;
        } elseif (! array_key_exists('city_id', $data) && ! array_key_exists('state_id', $data)) {
            // leave as is
        } elseif (isset($data['state_id']) && $data['state_id']) {
            $data['city_id'] = null;
        }

        $stateId = array_key_exists('state_id', $data) ? $data['state_id'] : $page->state_id;
        $cityId = array_key_exists('city_id', $data) ? $data['city_id'] : $page->city_id;
        $titleForSlug = $data['title'] ?? $page->title;

        if (isset($data['slug'])) {
            $data['slug'] = $this->normalizePageSlug($data['slug'], $titleForSlug);
            $data['slug'] = $this->dedupeLocationSuffixInSlug($data['slug'], $stateId, $cityId);
        } elseif (array_key_exists('city_id', $data) || array_key_exists('state_id', $data)) {
            $data['slug'] = $this->dedupeLocationSuffixInSlug($page->slug, $stateId, $cityId);
        }

        if (array_key_exists('content', $data) && $data['content'] !== null) {
            $data['content'] = StoragePublicUrl::normalizeStorageUrlsInHtml($data['content']);
        }

        $page->update($data);
        $page->load(['state:id,name,slug', 'city:id,name,slug,state_id']);
        $fresh = $page->fresh();
        $fresh->setAttribute('content', StoragePublicUrl::rewriteStorageUrlsInHtml($fresh->content ?? ''));

        return response()->json(['message' => 'Updated', 'page' => $fresh]);
    }

    /**
     * Clean, URL-safe slug (ASCII, single dashes, length cap). Empty input falls back to title.
     */
    private function normalizePageSlug(?string $slug, string $title): string
    {
        $raw = $slug !== null && trim($slug) !== '' ? trim($slug) : Str::slug($title);
        if ($raw === '') {
            $raw = 'page';
        }
        $out = Str::slug($raw);
        if ($out === '') {
            $out = 'page';
        }

        return Str::limit($out, 200, '');
    }

    /**
     * Avoid URLs like /makeup-artist-in-new-delhi-in-new-delhi when the title slug already ends with -in-{city}
     * and a city (or state) scope is set — the public URL adds -in-{location} again.
     */
    private function dedupeLocationSuffixInSlug(string $slug, mixed $stateId, mixed $cityId): string
    {
        $slug = trim($slug);
        if ($slug === '') {
            return '';
        }

        $cityId = $cityId ? (int) $cityId : null;
        $stateId = $stateId ? (int) $stateId : null;

        if ($cityId) {
            $city = City::query()->find($cityId);
            if ($city && filled($city->slug)) {
                $suffix = '-in-'.Str::slug($city->slug);
                if (str_ends_with($slug, $suffix)) {
                    $slug = substr($slug, 0, -strlen($suffix));
                }
            }
        } elseif ($stateId) {
            $state = State::query()->find($stateId);
            if ($state && filled($state->slug)) {
                $suffix = '-in-'.Str::slug($state->slug);
                if (str_ends_with($slug, $suffix)) {
                    $slug = substr($slug, 0, -strlen($suffix));
                }
            }
        }

        return $slug;
    }

    public function destroy(Page $page): JsonResponse
    {
        $page->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
