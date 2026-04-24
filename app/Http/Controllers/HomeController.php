<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\CreatorProfile;
use App\Models\Studio;
use App\Models\Campaign;
use App\Models\SuccessStory;
use App\Models\ServiceListing;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Display the main SPA (Vue) landing page with injected SEO metadata.
     */
    public function index(Request $request, $any = null)
    {
        $seo = $this->resolveSeoData($request, $any);

        // Optional: Fetch random pages for footer/discovery as before
        $pages = Page::published()
            ->with(['state:id,slug', 'city:id,slug'])
            ->inRandomOrder()
            ->limit(18)
            ->get(['id', 'title', 'slug', 'state_id', 'city_id']);

        return view('welcome', compact('pages', 'seo'));
    }

    /**
     * Resolve SEO metadata based on the current URL path.
     */
    private function resolveSeoData(Request $request, ?string $path): array
    {
        if (!$path) {
            return [];
        }

        // 1. Blog Posts
        if (Str::startsWith($path, 'blog/')) {
            $slug = Str::after($path, 'blog/');
            if ($slug && !Str::contains($slug, '/')) {
                $post = Post::where('slug', $slug)->first();
                if ($post) return $this->mapSeo($post);
            }
        }

        // 2. Creator Profiles
        if (Str::startsWith($path, 'creator-profile/')) {
            $slug = Str::after($path, 'creator-profile/');
            $profile = CreatorProfile::where('slug', $slug)->first();
            if ($profile) {
                return [
                    'title' => ($profile->user?->name ?? 'Creator') . ' | StarJD Profile',
                    'description' => $profile->tagline ?? 'Check out ' . ($profile->user?->name ?? 'this creator') . ' on StarJD.',
                    'keywords' => $profile->category ?? '',
                ];
            }
        }

        // 3. Studios
        if (Str::startsWith($path, 'studios/')) {
            $slug = Str::after($path, 'studios/');
            if ($slug && !Str::contains($slug, ['category/', 'location/'])) {
                $studio = Studio::where('slug', $slug)->first();
                if ($studio) return $this->mapSeo($studio);
            }
        }

        // 4. Campaigns
        if (Str::startsWith($path, 'campaigns/')) {
            $slug = Str::after($path, 'campaigns/');
            $campaign = Campaign::where('slug', $slug)->first();
            if ($campaign) return $this->mapSeo($campaign);
        }

        // 5. Success Stories
        if (Str::startsWith($path, 'success-stories/')) {
            $slug = Str::after($path, 'success-stories/');
            $story = SuccessStory::where('slug', $slug)->first();
            if ($story) return $this->mapSeo($story);
        }

        // 6. Services
        if (Str::startsWith($path, 'services/')) {
            $slug = Str::after($path, 'services/');
            $service = ServiceListing::where('slug', $slug)->first();
            if ($service) return $this->mapSeo($service);
        }

        // 7. Studios Location Search
        if (Str::startsWith($path, 'studios/location/')) {
            $parts = explode('/', trim(Str::after($path, 'studios/location/'), '/'));
            $stateSlug = $parts[0] ?? null;
            $citySlug = $parts[1] ?? null;
            return $this->resolveLocationSearchSeo('Studios', $stateSlug, $citySlug);
        }

        // 8. Creators Search (Flexible Hierarchy)
        if (Str::startsWith($path, 'creators/')) {
            $segments = explode('/', trim(Str::after($path, 'creators/'), '/'));
            return $this->resolveHierarchySearchSeo('Creators', $segments);
        }

        // 9. Marketplace Search (Flexible Hierarchy)
        if (Str::startsWith($path, 'marketplace/')) {
            $segments = explode('/', trim(Str::after($path, 'marketplace/'), '/'));
            return $this->resolveHierarchySearchSeo('Services', $segments);
        }

        // 10. CMS Pages / Location Hubs (The Catch-all)
        return $this->resolveCmsPageSeo($path);
    }

    private function resolveLocationSearchSeo(string $type, ?string $stateSlug, ?string $citySlug): array
    {
        $city = $citySlug ? City::findByUrlSlug($citySlug) : null;
        $state = $stateSlug ? State::findByUrlSlug($stateSlug) : null;
        $locName = ($city ?? $state)?->name ?: Str::headline($citySlug ?: $stateSlug ?: 'India');

        return [
            'title' => "Top {$type} in {$locName} | StarJD",
            'description' => "Find and book the best {$type} in {$locName}. Vetted professionals, transparent pricing, and high-quality results on India's premium creative marketplace.",
            'found' => true,
        ];
    }

    private function resolveHierarchySearchSeo(string $type, array $segments): array
    {
        $state = null;
        $city = null;
        $category = null;

        foreach ($segments as $s) {
            if (!$state) {
                $state = State::findByUrlSlug($s);
                if ($state) continue;
            }
            if ($state && !$city) {
                $city = City::findByUrlSlug($s);
                if ($city) continue;
            }
            // Fallback for search or category keywords in URL
            if (!$category && !in_array($s, ['search'])) {
                $category = Str::headline($s);
            }
        }

        $locName = ($city ?? $state)?->name ?: 'India';
        $catName = $category ? "{$category} " : "";

        return [
            'title' => "Best {$catName}{$type} in {$locName} | StarJD",
            'description' => "Browse vetted {$catName}{$type} in {$locName}. Connect with top-rated talent and get professional content for your brand campaigns.",
            'found' => true,
        ];
    }

    private function resolveCmsPageSeo(string $path): array
    {
        $segments = explode('/', trim($path, '/'));
        $count = count($segments);

        $slug = $segments[$count - 1];
        $stateSlug = $count > 1 ? $segments[0] : null;
        $citySlug = null;

        // Handle slug-in-location format (e.g. influencers-in-bhopal)
        if (Str::contains($slug, '-in-')) {
            $parts = explode('-in-', $slug);
            $citySlug = array_pop($parts);
            $slug = implode('-in-', $parts);
        }

        // Resolve location models
        $stateId = null;
        $cityId = null;

        if ($citySlug) {
            $city = City::findByUrlSlug($citySlug);
            if ($city) {
                $cityId = $city->id;
                $stateId = $city->state_id;
            }
        }

        if (!$cityId && ($stateSlug || $slug)) {
            $locSource = $stateSlug ?: $slug;
            $city = City::findByUrlSlug($locSource);
            if ($city) {
                $cityId = $city->id;
                $stateId = $city->state_id;
                if (!$stateSlug) $slug = 'influencers';
            } else {
                $state = State::findByUrlSlug($locSource);
                if ($state) {
                    $stateId = $state->id;
                    if (!$stateSlug) $slug = 'influencers';
                }
            }
        }

        // Final resolution logic matching Api\PageController
        $baseUrlQuery = Page::published()->where(function($q) use ($slug) {
            $q->where('slug', $slug)->orWhereRaw('LOWER(slug) = ?', [Str::lower($slug)]);
        });

        $page = null;
        if ($cityId) {
            $page = (clone $baseUrlQuery)->where('city_id', $cityId)->first();
        }
        if (!$page && $stateId) {
            $page = (clone $baseUrlQuery)->where('state_id', $stateId)->whereNull('city_id')->first();
        }
        if (!$page) {
            $page = (clone $baseUrlQuery)->global()->first();
        }

        if ($page) {
            $locName = ($city ?? $state)?->name;
            $seo = $this->mapSeo($page, $locName);
            return $this->enrichSeoWithLocationData($seo, $state, $city);
        }

        return [];
    }

    private function mapSeo($model, ?string $locationName = null): array
    {
        $title = $model->meta_title ?: $model->title ?: $model->name ?: '';
        $description = $model->meta_description ?: '';
        $keywords = $model->meta_keywords ?: '';

        if ($locationName) {
            $placeholders = ['{location}', '[location]', '{city}', '[city]', '{state}', '[state]'];
            
            if (Str::contains($title, $placeholders)) {
                $title = str_replace($placeholders, $locationName, $title);
            } elseif (!Str::contains($title, $locationName)) {
                $title .= ' in ' . $locationName;
            }

            if (Str::contains($description, $placeholders)) {
                $description = str_replace($placeholders, $locationName, $description);
            } elseif (!Str::contains($description, $locationName)) {
                $description .= " Discover and connect with the best creators and influencers in {$locationName} on StarJD.";
            }

            if (Str::contains($keywords, $placeholders)) {
                $keywords = str_replace($placeholders, $locationName, $keywords);
            }
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'content' => isset($model->content) ? \App\Support\StoragePublicUrl::rewriteStorageUrlsInHtml(html_entity_decode($model->content)) : null,
            'found' => true,
        ];
    }

    /**
     * Add server-rendered location proof points for local SEO pages.
     */
    private function enrichSeoWithLocationData(array $seo, ?State $state, ?City $city): array
    {
        if (!$state && !$city) {
            return $seo;
        }

        $creatorQuery = CreatorProfile::query()
            ->with(['user:id,name,city_id,state_id'])
            ->where('is_public', true)
            ->whereHas('user', function ($q) use ($state, $city) {
                if ($city) {
                    $q->where('city_id', $city->id);
                } elseif ($state) {
                    $q->where('state_id', $state->id);
                }
            });

        $creators = $creatorQuery
            ->orderByDesc('featured_until')
            ->latest('updated_at')
            ->limit(12)
            ->get(['id', 'user_id', 'slug', 'tagline', 'category', 'min_rate', 'featured_until']);

        $locationPath = $city
            ? '/'.$state->slug.'/'.$city->slug
            : '/'.$state->slug;

        $locationName = $city?->name ?? $state?->name ?? 'India';
        $baseUrl = rtrim(config('app.url', url('/')), '/');
        $canonical = $seo['canonical'] ?? url()->current();

        $seo['canonical'] = $canonical;
        $seo['location_name'] = $locationName;
        $seo['creator_count'] = $creatorQuery->count();
        $seo['creator_cards'] = $creators->map(function (CreatorProfile $profile) use ($locationName) {
            return [
                'name' => $profile->user?->name ?? 'Creator',
                'slug' => $profile->slug,
                'tagline' => $profile->tagline ?: "Available for brand collaborations in {$locationName}.",
                'category' => $profile->category ?: 'Content Creator',
                'min_rate' => $profile->min_rate,
            ];
        })->values()->all();

        $itemList = [];
        foreach ($seo['creator_cards'] as $index => $creator) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'url' => $baseUrl.'/creator-profile/'.$creator['slug'],
                'name' => $creator['name'],
            ];
        }

        $seo['schema'] = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => array_values(array_filter([
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Home',
                        'item' => $baseUrl,
                    ],
                    $state ? [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $state->name,
                        'item' => $baseUrl.'/'.$state->slug,
                    ] : null,
                    $city ? [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $city->name,
                        'item' => $baseUrl.$locationPath,
                    ] : null,
                    [
                        '@type' => 'ListItem',
                        'position' => $city ? 4 : ($state ? 3 : 2),
                        'name' => $seo['title'] ?? "Influencers in {$locationName}",
                        'item' => $canonical,
                    ],
                ])),
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'name' => "Featured creators in {$locationName}",
                'numberOfItems' => count($itemList),
                'itemListElement' => $itemList,
            ],
        ];

        return $seo;
    }
}
