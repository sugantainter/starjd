<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CreatorProfile;
use App\Models\Page;
use App\Models\Post;
use App\Models\ServiceListing;
use App\Models\Studio;
use App\Models\SuccessStory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    private int $limit = 30000;

    public function status(): JsonResponse
    {
        $publicPath = public_path();
        $files = File::glob($publicPath . '/sitemap*.xml');
        $sitemaps = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $sitemaps[] = [
                'name' => $filename,
                'url' => url($filename),
                'last_modified' => Carbon::createFromTimestamp(File::lastModified($file))->toDateTimeString(),
                'size' => round(File::size($file) / 1024, 2) . ' KB',
            ];
        }

        return response()->json([
            'sitemaps' => $sitemaps,
            'last_generated' => count($sitemaps) > 0 ? Carbon::createFromTimestamp(File::lastModified($files[0]))->diffForHumans() : 'Never',
        ]);
    }

    public function generate(): JsonResponse
    {
        try {
            $urls = $this->getAllUrls();
            $chunks = array_chunk($urls, $this->limit);

            // Clean up existing sitemaps
            $existingFiles = File::glob(public_path('/sitemap*.xml'));
            foreach ($existingFiles as $file) {
                File::delete($file);
            }

            $sitemapFiles = [];

            if (count($chunks) > 1) {
                // Generate chunked sitemaps and an index
                foreach ($chunks as $index => $chunk) {
                    $filename = "sitemap_" . ($index + 1) . ".xml";
                    $this->createSitemapFile($filename, $chunk);
                    $sitemapFiles[] = $filename;
                }
                $this->createSitemapIndex("sitemap.xml", $sitemapFiles);
            } else {
                // Generate a single sitemap.xml
                $this->createSitemapFile("sitemap.xml", $urls);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sitemap generated successfully!',
                'total_urls' => count($urls),
                'files' => count($chunks) > 1 ? count($chunks) + 1 : 1,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate sitemap: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getAllUrls(): array
    {
        $urls = [];
        $now = Carbon::now()->toAtomString();

        // 1. Static Routes (Must match router/index.js)
        $statics = [
            '/', '/about', '/how-it-works', '/contact', '/privacy', '/terms',
            '/cookie-policy', '/child-safety', '/brand-landing', '/campaign',
            '/campaigns', '/creator-landing', '/blog', '/success-stories',
            '/videos', '/services', '/marketplace', '/creators', '/studios',
            '/login', '/register', '/forgot-password'
        ];

        foreach ($statics as $path) {
            $urls[] = ['loc' => url($path), 'lastmod' => $now, 'priority' => $path === '/' ? '1.0' : '0.8'];
        }

        // 2. Dynamic Pages
        Page::published()->get()->each(function ($page) use (&$urls, $now) {
            $urls[] = ['loc' => url('/page/' . $page->slug), 'lastmod' => $page->updated_at?->toAtomString() ?? $now, 'priority' => '0.7'];
            // Dynamic root pages fallback handled by router
            $urls[] = ['loc' => url('/' . $page->slug), 'lastmod' => $page->updated_at?->toAtomString() ?? $now, 'priority' => '0.6'];
        });

        // 3. Blog Posts
        Post::all()->each(function ($post) use (&$urls, $now) {
            $urls[] = ['loc' => url('/blog/' . $post->slug), 'lastmod' => $post->updated_at?->toAtomString() ?? $now, 'priority' => '0.8'];
        });

        // 4. Creators
        CreatorProfile::where('is_public', true)->get()->each(function ($creator) use (&$urls, $now) {
            $urls[] = ['loc' => url('/creators/' . $creator->slug), 'lastmod' => $creator->updated_at?->toAtomString() ?? $now, 'priority' => '0.9'];
        });

        // 5. Studios
        Studio::all()->each(function ($studio) use (&$urls, $now) {
            $urls[] = ['loc' => url('/studios/' . $studio->slug), 'lastmod' => $studio->updated_at?->toAtomString() ?? $now, 'priority' => '0.8'];
        });

        // 6. Campaigns
        Campaign::open()->get()->each(function ($campaign) use (&$urls, $now) {
            $urls[] = ['loc' => url('/campaigns/' . $campaign->slug), 'lastmod' => $campaign->updated_at?->toAtomString() ?? $now, 'priority' => '0.8'];
        });

        // 7. Services / Gigs
        ServiceListing::where('is_active', true)->get()->each(function ($service) use (&$urls, $now) {
            $urls[] = ['loc' => url('/services/' . $service->slug), 'lastmod' => $service->updated_at?->toAtomString() ?? $now, 'priority' => '0.7'];
            $urls[] = ['loc' => url('/gigs/' . $service->slug), 'lastmod' => $service->updated_at?->toAtomString() ?? $now, 'priority' => '0.7'];
        });

        // 8. Success Stories
        SuccessStory::all()->each(function ($story) use (&$urls, $now) {
            $urls[] = ['loc' => url('/success-stories/' . $story->slug), 'lastmod' => $story->updated_at?->toAtomString() ?? $now, 'priority' => '0.6'];
        });

        return $urls;
    }

    private function createSitemapFile(string $filename, array $urls): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        File::put(public_path($filename), $xml);
    }

    private function createSitemapIndex(string $filename, array $files): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($files as $file) {
            $xml .= '  <sitemap>' . PHP_EOL;
            $xml .= '    <loc>' . url(basename($file)) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . Carbon::now()->toAtomString() . '</lastmod>' . PHP_EOL;
            $xml .= '  </sitemap>' . PHP_EOL;
        }

        $xml .= '</sitemapindex>';

        File::put(public_path($filename), $xml);
    }
}
