<?php

namespace App\Http\Controllers;

use App\Models\SeoPage;
use App\Models\Page;
use App\Models\CreatorProfile;
use App\Models\Studio;
use App\Models\BrandProfile;
use Illuminate\Http\Request;

class LocationPublicController extends Controller
{
    /**
     * Resolve a root-level slug. 
     * Priority: 1. New SEO Pages (Location-based) 2. Legacy Static Pages
     */
    public function show($slug)
    {
        // 1. Try New SEO Pages
        $seoPage = SeoPage::with('entity')->where('slug', $slug)->first();

        if ($seoPage) {
            if ($seoPage->status !== 'published' && !auth()->check()) {
                abort(404);
            }

            // Extract city from entity to find relevant services
            $city = $seoPage->entity->city ?? null;
            
            $relevantInfluencers = [];
            if ($city) {
                $relevantInfluencers = CreatorProfile::where('location', 'like', "%$city%")
                    ->where('is_public', true)
                    ->limit(6)
                    ->get();
            }

            return response()->json([
                'type' => 'seo_page',
                'page' => $seoPage,
                'relevant_influencers' => $relevantInfluencers,
            ]);
        }

        // 2. Try Legacy Static Pages (Fallback)
        // Re-using PageController logic but in a simplified way for the unified endpoint
        $page = Page::where('slug', $slug)->published()->first();
        if ($page) {
            return response()->json([
                'type' => 'static_page',
                'page' => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'content' => $page->content,
                    'meta_title' => $page->meta_title,
                    'meta_description' => $page->meta_description,
                ]
            ]);
        }

        abort(404);
    }
}
