<?php

namespace App\Http\Controllers;

use App\Models\SeoPage;
use App\Models\Page;
use App\Models\CreatorProfile;
use App\Models\Studio;
use App\Models\BrandProfile;
use App\Support\StoragePublicUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationPublicController extends Controller
{
    /**
     * Resolve a root-level slug. 
     */
    public function show($slug)
    {
        $seoPage = SeoPage::with('entity')->where('slug', $slug)->first();

        if ($seoPage) {
            if ($seoPage->status !== 'published' && !auth()->check()) {
                abort(404);
            }

            $city = $seoPage->entity->city ?? ($seoPage->entity->location ?? null);
            $type = $seoPage->type;
            
            // Grouped Interlinking (Tabbed Data)
            $tabbedLinks = [
                'Popular Areas' => SeoPage::where('status', 'published')
                    ->where(function($q) use ($city) {
                        if ($city) $q->where('slug', 'like', "%" . Str::slug($city) . "%");
                    })
                    ->where('type', 'area')
                    ->limit(16)->get(['title', 'slug']),
                
                'Top Services' => SeoPage::where('status', 'published')
                    ->where(function($q) use ($city) {
                        if ($city) $q->where('slug', 'like', "%" . Str::slug($city) . "%");
                    })
                    ->whereIn('type', ['service', 'influencer', 'hospital', 'school', 'market', 'metro'])
                    ->limit(16)->get(['title', 'slug']),

                'Popular Cities' => SeoPage::where('status', 'published')
                    ->where('type', $type)
                    ->where(function($q) use ($city) {
                        if ($city) $q->where('slug', 'not like', "%" . Str::slug($city) . "%");
                    })
                    ->limit(16)->get(['title', 'slug']),
                
                'Platform Hub' => [
                    ['title' => 'Vetted Influencers', 'slug' => 'creators'],
                    ['title' => 'Professional Studios', 'slug' => 'studios'],
                    ['title' => 'Brand Campaigns', 'slug' => 'campaign'],
                    ['title' => 'Marketing Marketplace', 'slug' => 'marketplace'],
                    ['title' => 'Success Stories', 'slug' => 'success-stories'],
                    ['title' => 'Professional Profiles', 'slug' => 'professionals'],
                    ['title' => 'Creative Gigs', 'slug' => 'gigs'],
                    ['title' => 'Star Blog', 'slug' => 'blog'],
                ]
            ];

            // Filter out empty groups (except Platform Hub which is static)
            $tabbedLinks = array_filter($tabbedLinks, function($links, $key) {
                if ($key === 'Platform Hub') return true;
                return !empty($links) && (is_array($links) ? count($links) > 0 : $links->isNotEmpty());
            }, ARRAY_FILTER_USE_BOTH);

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
                'tabbed_links' => $tabbedLinks,
                'relevant_influencers' => $relevantInfluencers,
                'schema' => $this->generateSchema($seoPage, $city),
            ]);
        }

        // Fallback to static pages or state-based pages
        $page = Page::with(['state', 'city'])->where('slug', $slug)->published()->first();

        // If not found, check if it's a state-based page (e.g. influencers-in-delhi)
        if (!$page && str_contains($slug, '-in-')) {
            $parts = explode('-in-', $slug);
            $locationSlug = array_pop($parts);
            $pageSlug = implode('-in-', $parts);

            $state = \App\Models\State::where('slug', $locationSlug)->first();
            if ($state) {
                $page = Page::with(['state', 'city'])
                    ->where('slug', $pageSlug)
                    ->where('state_id', $state->id)
                    ->whereNull('city_id')
                    ->published()
                    ->first();
            }
        }

        // Final fallback: Check if the slug itself is a state name (e.g. /delhi -> influencers in delhi)
        if (!$page) {
            $state = \App\Models\State::where('slug', $slug)->first();
            if ($state) {
                $page = Page::with(['state', 'city'])
                    ->where('slug', 'influencers')
                    ->where('state_id', $state->id)
                    ->whereNull('city_id')
                    ->published()
                    ->first();
            }
        }

        if ($page) {
            $contentRaw = $page->content ? html_entity_decode($page->content) : '';
            $locationName = $page->state?->name ?? $page->city?->name;
            $title = $page->title;

            if ($locationName) {
                $placeholders = ['{location}', '[location]', '{city}', '[city]', '{state}', '[state]'];
                if (Str::contains($title, $placeholders)) {
                    $title = str_replace($placeholders, $locationName, $title);
                } elseif ($title && !Str::contains($title, $locationName)) {
                    $title .= ' in ' . $locationName;
                }
            }

            return response()->json([
                'type' => 'static_page',
                'page' => [
                    'id' => $page->id,
                    'title' => $title,
                    'slug' => $page->slug,
                    'content' => StoragePublicUrl::rewriteStorageUrlsInHtml($contentRaw),
                    'meta_title' => ($page->meta_title ?: $title) . ($locationName && !Str::contains($page->meta_title, $locationName) ? ' in ' . $locationName : ''),
                    'meta_description' => $page->meta_description,
                    'meta_keywords' => $page->meta_keywords,
                ]
            ]);
        }

        abort(404);
    }

    private function generateSchema($page, $city)
    {
        $schemas = [];
        $schemas[] = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                ["@type" => "ListItem", "position" => 1, "name" => "Home", "item" => url('/')],
                ["@type" => "ListItem", "position" => 2, "name" => $page->title, "item" => url($page->slug)]
            ]
        ];
        if (!empty($page->faqs)) {
            $faqItems = [];
            foreach ($page->faqs as $faq) {
                if (!empty($faq['q']) && !empty($faq['a'])) {
                    $faqItems[] = ["@type" => "Question", "name" => $faq['q'], "acceptedAnswer" => ["@type" => "Answer", "text" => strip_tags($faq['a'])]];
                }
            }
            if ($faqItems) $schemas[] = ["@context" => "https://schema.org", "@type" => "FAQPage", "mainEntity" => $faqItems];
        }
        $schemas[] = ["@context" => "https://schema.org", "@type" => "Service", "name" => $page->title, "provider" => ["@type" => "Organization", "name" => "StarJD", "url" => url('/')], "areaServed" => ["@type" => "City", "name" => $city ?: "India"]];
        return $schemas;
    }
}
