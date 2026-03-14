<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LegalPageController extends Controller
{
    private const PAGE_DEFINITIONS = [
        'terms' => 'Terms and Conditions',
        'privacy' => 'Privacy Policy',
        'cookie-policy' => 'Cookie Policy',
    ];

    public function index(): JsonResponse
    {
        $pages = Page::query()
            ->whereIn('slug', array_keys(self::PAGE_DEFINITIONS))
            ->whereNull('state_id')
            ->whereNull('city_id')
            ->get()
            ->keyBy('slug');

        $items = collect(self::PAGE_DEFINITIONS)->map(function (string $title, string $slug) use ($pages) {
            $page = $pages->get($slug);

            return [
                'id' => $page?->id,
                'slug' => $slug,
                'title' => $page?->title ?? $title,
                'content' => $page?->content ?? '',
                'meta_title' => $page?->meta_title,
                'meta_description' => $page?->meta_description,
                'status' => $page?->status ?? 'draft',
                'template' => $page?->template ?? 'default',
                'sort_order' => $page?->sort_order ?? 0,
                'exists' => (bool) $page,
            ];
        })->values();

        return response()->json($items);
    }

    public function update(Request $request, string $slug): JsonResponse
    {
        abort_unless(array_key_exists($slug, self::PAGE_DEFINITIONS), 404);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'status' => ['required', Rule::in(['draft', 'published'])],
        ]);

        $page = Page::firstOrNew([
            'slug' => $slug,
            'state_id' => null,
            'city_id' => null,
        ]);

        $page->fill([
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'] ?? null,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'status' => $data['status'],
            'template' => 'default',
            'sort_order' => $page->exists ? $page->sort_order : 0,
            'state_id' => null,
            'city_id' => null,
        ]);
        $page->save();

        return response()->json([
            'message' => 'Legal page updated successfully.',
            'page' => $page->fresh(),
        ]);
    }
}
