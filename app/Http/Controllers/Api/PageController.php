<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
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
        $stateSlug = $request->query('state_slug');
        $citySlug = $request->query('city_slug');
        $stateId = null;
        $cityId = null;

        if ($citySlug) {
            // Try to find city first (if citySlug is same as stateSlug, it will still try)
            $city = \App\Models\City::where('slug', $citySlug)->first();
            if ($city) {
                $cityId = $city->id;
                $stateId = $city->state_id;
            }
        }

        if (!$cityId && $stateSlug) {
            // If no city found, or only state provided, try to find state
            $state = \App\Models\State::where('slug', $stateSlug)->first();
            if ($state) {
                $stateId = $state->id;
            }
        }

        $page = $this->resolvePage($slug, $stateId, $cityId);
        if (! $page) {
            return response()->json(['message' => 'Page not found'], 404);
        }
        return response()->json([
            'id' => $page->id,
            'title' => $page->title ? html_entity_decode($page->title) : '',
            'slug' => $page->slug,
            'content' => $page->content ? html_entity_decode($page->content) : '',
            'meta_title' => $page->meta_title ? html_entity_decode($page->meta_title) : '',
            'meta_description' => $page->meta_description ? html_entity_decode($page->meta_description) : '',
            'template' => $page->template,
            'state_id' => $page->state_id,
            'city_id' => $page->city_id,
            'state' => $page->state ? $page->state->only(['id', 'name', 'slug']) : null,
            'city' => $page->city ? $page->city->only(['id', 'name', 'slug', 'state_id']) : null,
        ]);
    }

    private function resolvePage(string $slug, ?int $stateId, ?int $cityId): ?Page
    {
        if ($cityId) {
            $page = Page::published()->where('slug', $slug)->where('city_id', $cityId)->first();
            if ($page) {
                return $page;
            }
        }
        if ($stateId) {
            $page = Page::published()->where('slug', $slug)->where('state_id', $stateId)->whereNull('city_id')->first();
            if ($page) {
                return $page;
            }
        }
        return Page::published()->global()->where('slug', $slug)->first();
    }
}
