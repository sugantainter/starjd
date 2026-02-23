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
        if ($citySlug && $stateSlug) {
            $state = \App\Models\State::where('slug', $stateSlug)->first();
            if ($state) {
                $city = \App\Models\City::where('state_id', $state->id)->where('slug', $citySlug)->first();
                if ($city) {
                    $stateId = $state->id;
                    $cityId = $city->id;
                }
            }
        } elseif ($stateSlug) {
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
            'title' => $page->title,
            'slug' => $page->slug,
            'content' => $page->content,
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'template' => $page->template,
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
