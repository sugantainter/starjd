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

        // 7. CMS Pages / Location Hubs (The Andaman case)
        return $this->resolveCmsPageSeo($path);
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
            return $this->mapSeo($page, $locName);
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
            'found' => true,
        ];
    }
}
