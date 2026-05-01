<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CreatorProfile;
use App\Models\Page;
use App\Models\Post;
use App\Models\ServiceListing;
use App\Models\Studio;
use App\Models\SeoPage;
use App\Models\SuccessStory;
use Carbon\Carbon;
use DOMDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SitemapController extends Controller
{
    private int $limit = 30000;

    /**
     * Generated sitemaps live here (writable by www-data) and are served via /sitemap.xml routes.
     */
    private function sitemapDirectory(): string
    {
        return storage_path('app/sitemaps');
    }

    private function ensureSitemapDirectory(): void
    {
        $dir = $this->sitemapDirectory();
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
    }

    /**
     * Public URLs for crawlers (served from storage via web routes).
     */
    public function servePublic(string $sitemapFile): BinaryFileResponse
    {
        if (!preg_match('/^(sitemap\.xml|sitemap_[0-9]+\.xml)$/', $sitemapFile)) {
            abort(404);
        }
        $path = $this->sitemapDirectory() . DIRECTORY_SEPARATOR . $sitemapFile;
        if (!File::isFile($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function status(): JsonResponse
    {
        $this->ensureSitemapDirectory();
        $files = File::glob($this->sitemapDirectory() . '/sitemap*.xml');
        usort($files, function (string $a, string $b): int {
            $ba = basename($a);
            $bb = basename($b);
            if ($ba === 'sitemap.xml') {
                return -1;
            }
            if ($bb === 'sitemap.xml') {
                return 1;
            }

            return strnatcasecmp($ba, $bb);
        });

        $sitemaps = [];
        $totalUrlCount = 0;

        foreach ($files as $file) {
            $filename = basename($file);
            $urlCount = $this->countUrlEntriesInSitemapFile($file);
            $totalUrlCount += $urlCount;
            $sitemaps[] = [
                'name' => $filename,
                'url' => url($filename),
                'last_modified' => Carbon::createFromTimestamp(File::lastModified($file))->toDateTimeString(),
                'size' => round(File::size($file) / 1024, 2) . ' KB',
                'url_count' => $urlCount,
            ];
        }

        return response()->json([
            'sitemaps' => $sitemaps,
            'total_url_count' => $totalUrlCount,
            'last_generated' => count($sitemaps) > 0 ? Carbon::createFromTimestamp(File::lastModified($files[0]))->diffForHumans() : 'Never',
        ]);
    }

    private function countUrlEntriesInSitemapFile(string $path): int
    {
        if (!File::isFile($path)) {
            return 0;
        }

        $dom = new DOMDocument;
        if (!@$dom->load($path)) {
            return 0;
        }

        return $dom->getElementsByTagName('url')->length;
    }

    public function generate(): JsonResponse
    {
        try {
            $this->ensureSitemapDirectory();
            
            // Clean up existing sitemaps
            $existingFiles = array_merge(
                File::glob($this->sitemapDirectory() . '/sitemap*.xml'),
                File::glob(public_path('sitemap*.xml'))
            );
            foreach ($existingFiles as $file) {
                File::delete($file);
            }

            $urls = [];
            $now = Carbon::now()->toAtomString();
            $sitemapFiles = [];
            $totalUrlsCount = 0;

            // 1. Static routes
            $statics = [
                ['paths' => ['/'], 'priority' => '1.0', 'changefreq' => 'daily'],
                ['paths' => ['/about-us', '/how-it-works', '/contact-us', '/brand', '/campaign', '/campaigns', '/creator', '/blog', '/success-stories', '/videos', '/services', '/marketplace', '/creators', '/studios'], 'priority' => '0.9', 'changefreq' => 'weekly'],
                ['paths' => ['/privacy-policy', '/terms-and-conditions', '/cookie-policy', '/child-safety'], 'priority' => '0.4', 'changefreq' => 'monthly'],
            ];

            foreach ($statics as $group) {
                foreach ($group['paths'] as $path) {
                    $urls[] = ['loc' => url($path), 'lastmod' => $now, 'priority' => $group['priority'], 'changefreq' => $group['changefreq']];
                }
            }

            // Function to handle writing chunks to files
            $writeSitemap = function(&$urls, $force = false) use (&$sitemapFiles, &$totalUrlsCount) {
                while (count($urls) >= $this->limit || ($force && count($urls) > 0)) {
                    $chunk = array_splice($urls, 0, $this->limit);
                    $filename = 'sitemap_' . (count($sitemapFiles) + 1) . '.xml';
                    $this->createSitemapFile($filename, $chunk);
                    $sitemapFiles[] = $filename;
                    $totalUrlsCount += count($chunk);
                }
            };

            // 2. CMS pages
            Page::published()->select('id', 'slug', 'updated_at', 'city_id', 'state_id')->with(['state:id,slug', 'city:id,slug'])->chunk(500, function ($pages) use (&$urls, $now, &$writeSitemap) {
                foreach ($pages as $page) {
                    $path = $page->publicPath();
                    if ($path) {
                        $urls[] = ['loc' => url($path), 'lastmod' => $page->updated_at?->toAtomString() ?? $now, 'priority' => '0.7', 'changefreq' => 'weekly'];
                    }
                }
                $writeSitemap($urls);
            });

            // 3. Blog Posts
            Post::select('id', 'slug', 'updated_at')->chunk(500, function ($posts) use (&$urls, $now, &$writeSitemap) {
                foreach ($posts as $post) {
                    $urls[] = ['loc' => url('/blog/' . $post->slug), 'lastmod' => $post->updated_at?->toAtomString() ?? $now, 'priority' => '0.8', 'changefreq' => 'weekly'];
                }
                $writeSitemap($urls);
            });

            // 4. Creators
            CreatorProfile::where('is_public', true)->select('id', 'slug', 'updated_at')->chunk(500, function ($creators) use (&$urls, $now, &$writeSitemap) {
                foreach ($creators as $creator) {
                    $urls[] = ['loc' => url('/creator-profile/' . $creator->slug), 'lastmod' => $creator->updated_at?->toAtomString() ?? $now, 'priority' => '0.9', 'changefreq' => 'weekly'];
                }
                $writeSitemap($urls);
            });

            // 5. Studios
            Studio::select('id', 'slug', 'updated_at')->chunk(500, function ($studios) use (&$urls, $now, &$writeSitemap) {
                foreach ($studios as $studio) {
                    $urls[] = ['loc' => url('/studios/' . $studio->slug), 'lastmod' => $studio->updated_at?->toAtomString() ?? $now, 'priority' => '0.8', 'changefreq' => 'weekly'];
                }
                $writeSitemap($urls);
            });

            // 6. Campaigns
            Campaign::open()->select('id', 'slug', 'updated_at')->chunk(500, function ($campaigns) use (&$urls, $now, &$writeSitemap) {
                foreach ($campaigns as $campaign) {
                    $urls[] = ['loc' => url('/campaigns/' . $campaign->slug), 'lastmod' => $campaign->updated_at?->toAtomString() ?? $now, 'priority' => '0.85', 'changefreq' => 'daily'];
                }
                $writeSitemap($urls);
            });

            // 7. Services
            ServiceListing::where('is_active', true)->select('id', 'slug', 'updated_at')->chunk(500, function ($services) use (&$urls, $now, &$writeSitemap) {
                foreach ($services as $service) {
                    $urls[] = ['loc' => url('/services/' . $service->slug), 'lastmod' => $service->updated_at?->toAtomString() ?? $now, 'priority' => '0.7', 'changefreq' => 'weekly'];
                }
                $writeSitemap($urls);
            });

            // 8. Success stories
            SuccessStory::select('id', 'slug', 'updated_at')->chunk(500, function ($stories) use (&$urls, $now, &$writeSitemap) {
                foreach ($stories as $story) {
                    $urls[] = ['loc' => url('/success-stories/' . $story->slug), 'lastmod' => $story->updated_at?->toAtomString() ?? $now, 'priority' => '0.6', 'changefreq' => 'monthly'];
                }
                $writeSitemap($urls);
            });

            // 9. Location CMS SEO Pages (The likely culprit)
            SeoPage::where('status', 'published')->select('id', 'slug', 'updated_at')->chunk(1000, function ($pages) use (&$urls, $now, &$writeSitemap) {
                foreach ($pages as $page) {
                    $urls[] = ['loc' => url('/' . $page->slug), 'lastmod' => $page->updated_at?->toAtomString() ?? $now, 'priority' => '0.8', 'changefreq' => 'daily'];
                }
                $writeSitemap($urls);
            });

            // Final force write
            $writeSitemap($urls, true);

            // Handle sitemap index or single file
            if (count($sitemapFiles) > 1) {
                $this->createSitemapIndex('sitemap.xml', $sitemapFiles);
            } elseif (count($sitemapFiles) === 1) {
                // If only one sitemap_1.xml was created, rename it to sitemap.xml
                $path1 = $this->sitemapDirectory() . DIRECTORY_SEPARATOR . 'sitemap_1.xml';
                $pathMain = $this->sitemapDirectory() . DIRECTORY_SEPARATOR . 'sitemap.xml';
                if (File::exists($path1)) {
                    File::move($path1, $pathMain);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Sitemap generated successfully!',
                'total_urls' => $totalUrlsCount,
                'files' => count($sitemapFiles) > 1 ? count($sitemapFiles) + 1 : 1,
            ]);
        } catch (\Exception $e) {
            Log::error("Sitemap Generation Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate sitemap: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getAllUrls(): array
    {
        return []; // No longer used but kept for backward compatibility if needed
    }

    private function formatLastmodForXml(string $atomOrDate): string
    {
        try {
            return Carbon::parse($atomOrDate)->utc()->format('Y-m-d');
        } catch (\Throwable) {
            return Carbon::now()->utc()->format('Y-m-d');
        }
    }

    /**
     * @param  list<array{loc: string, lastmod: string, priority: string, changefreq: string}>  $urls
     */
    private function createSitemapFile(string $filename, array $urls): void
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;

        $urlset = $dom->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $dom->appendChild($urlset);

        foreach ($urls as $row) {
            $urlEl = $dom->createElement('url');
            $urlset->appendChild($urlEl);

            $loc = $dom->createElement('loc');
            $loc->textContent = $row['loc'];
            $urlEl->appendChild($loc);

            $lastmod = $dom->createElement('lastmod');
            $lastmod->textContent = $this->formatLastmodForXml($row['lastmod']);
            $urlEl->appendChild($lastmod);

            $changefreq = $dom->createElement('changefreq');
            $changefreq->textContent = $row['changefreq'];
            $urlEl->appendChild($changefreq);

            $priority = $dom->createElement('priority');
            $priority->textContent = $row['priority'];
            $urlEl->appendChild($priority);
        }

        $xml = $dom->saveXML();
        if ($xml === false) {
            throw new \RuntimeException('Could not serialize sitemap XML');
        }

        File::put($this->sitemapDirectory() . DIRECTORY_SEPARATOR . $filename, $xml);
    }

    /**
     * @param  list<string>  $files
     */
    private function createSitemapIndex(string $filename, array $files): void
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;

        $index = $dom->createElement('sitemapindex');
        $index->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $dom->appendChild($index);

        $now = Carbon::now()->utc()->format('Y-m-d');

        foreach ($files as $file) {
            $sm = $dom->createElement('sitemap');
            $index->appendChild($sm);

            $loc = $dom->createElement('loc');
            $loc->textContent = url(basename($file));
            $sm->appendChild($loc);

            $lastmod = $dom->createElement('lastmod');
            $lastmod->textContent = $now;
            $sm->appendChild($lastmod);
        }

        $xml = $dom->saveXML();
        if ($xml === false) {
            throw new \RuntimeException('Could not serialize sitemap index XML');
        }

        File::put($this->sitemapDirectory() . DIRECTORY_SEPARATOR . $filename, $xml);
    }
}
