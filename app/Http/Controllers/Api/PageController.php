<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\StoragePublicUrl;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Get a single page by slug. Optional state_slug and city_slug for location-specific pages.
     * Resolves: city page > state page > global page.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $stateSlug = $request->query('state_slug') ?? $request->query('state');
        $citySlug = $request->query('city_slug') ?? $request->query('city');
        $stateId = null;
        $cityId = null;

        if ($citySlug) {
            $city = \App\Models\City::findByUrlSlug($citySlug);
            if ($city) {
                $cityId = $city->id;
                $stateId = $city->state_id;
            }
        }

        if (! $cityId && ($stateSlug || $slug)) {
            // Check state_slug first (from query or URL hierarchy)
            $locSource = $stateSlug ?: $slug;
            $city = \App\Models\City::findByUrlSlug($locSource);
            if ($city) {
                $cityId = $city->id;
                $stateId = $city->state_id;
                if (! $stateSlug) $slug = 'influencers'; // If slug was the location, fallback to default
            } else {
                $state = \App\Models\State::findByUrlSlug($locSource);
                if ($state) {
                    $stateId = $state->id;
                    if (! $stateSlug) $slug = 'influencers'; // If slug was the location, fallback to default
                }
            }
        }

        $page = $this->resolvePage($slug, $stateId, $cityId);
        if (! $page) {
            return response()->json(['message' => 'Page not found'], 404);
        }
        $contentRaw = $page->content ? html_entity_decode($page->content) : '';

        return response()->json([
            'id' => $page->id,
            'title' => $page->title ? html_entity_decode($page->title) : '',
            'slug' => $page->slug,
            'content' => StoragePublicUrl::rewriteStorageUrlsInHtml($contentRaw),
            'meta_title' => $page->meta_title ? html_entity_decode($page->meta_title) : '',
            'meta_description' => $page->meta_description ? html_entity_decode($page->meta_description) : '',
            'meta_keywords' => $page->meta_keywords ? html_entity_decode($page->meta_keywords) : '',
            'template' => $page->template,
            'state_id' => $page->state_id,
            'city_id' => $page->city_id,
            'state' => $page->state ? $page->state->only(['id', 'name', 'slug']) : null,
            'city' => $page->city ? $page->city->only(['id', 'name', 'slug', 'state_id']) : null,
        ]);
    }

    private function resolvePage(string $slug, ?int $stateId, ?int $cityId): ?Page
    {
        $baseUrlQuery = Page::published()->where(function($q) use ($slug) {
            $q->where('slug', $slug)->orWhereRaw('LOWER(slug) = ?', [Str::lower($slug)]);
        });

        // 1. Try Specific City
        if ($cityId) {
            $page = (clone $baseUrlQuery)->where('city_id', $cityId)->first();
            if ($page) return $page;
        }

        // 2. Try State (fallback or specific)
        if ($stateId) {
            $page = (clone $baseUrlQuery)->where('state_id', $stateId)->whereNull('city_id')->first();
            if ($page) return $page;
        }

        // 3. Try Global
        return (clone $baseUrlQuery)->global()->first();
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        $pages = Page::published()->with(['state', 'city'])->inRandomOrder()->limit(18)->get();
        return response()->json($pages->map(function($p) {
            $path = $p->publicPath();
            $fullSlug = $path ? ltrim($path, '/') : $p->slug;

            return [
                'id' => $p->id,
                'title' => $p->title ? html_entity_decode($p->title) : '',
                'slug' => $p->slug,
                'full_slug' => $fullSlug,
            ];
        }));
    }
}
