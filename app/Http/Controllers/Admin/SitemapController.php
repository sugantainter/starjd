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
use DOMDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
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
            $urls = $this->getAllUrls();
            $chunks = array_chunk($urls, $this->limit);

            // Clean up existing sitemaps (storage + legacy public copies)
            $existingFiles = array_merge(
                File::glob($this->sitemapDirectory() . '/sitemap*.xml'),
                File::glob(public_path('sitemap*.xml'))
            );
            foreach ($existingFiles as $file) {
                File::delete($file);
            }

            $sitemapFiles = [];

            if (count($chunks) > 1) {
                foreach ($chunks as $index => $chunk) {
                    $filename = 'sitemap_' . ($index + 1) . '.xml';
                    $this->createSitemapFile($filename, $chunk);
                    $sitemapFiles[] = $filename;
                }
                $this->createSitemapIndex('sitemap.xml', $sitemapFiles);
            } else {
                $this->createSitemapFile('sitemap.xml', $urls);
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

    /**
     * @return list<array{loc: string, lastmod: string, priority: string, changefreq: string}>
     */
    private function getAllUrls(): array
    {
        $urls = [];
        $now = Carbon::now()->toAtomString();

        // 1. Static routes (no login/register — not for organic search)
        $statics = [
            ['paths' => ['/'], 'priority' => '1.0', 'changefreq' => 'daily'],
            [
                'paths' => [
                    '/about-us',
                    '/how-it-works',
                    '/contact-us',
                    '/brand',
                    '/campaign',
                    '/campaigns',
                    '/creator',
                    '/blog',
                    '/success-stories',
                    '/videos',
                    '/services',
                    '/marketplace',
                    '/creators',
                    '/studios',
                ],
                'priority' => '0.9',
                'changefreq' => 'weekly'
            ],
            [
                'paths' => [
                    '/privacy-policy',
                    '/terms-and-conditions',
                    '/cookie-policy',
                    '/child-safety',
                ],
                'priority' => '0.4',
                'changefreq' => 'monthly'
            ],
        ];

        foreach ($statics as $group) {
            foreach ($group['paths'] as $path) {
                $urls[] = [
                    'loc' => url($path),
                    'lastmod' => $now,
                    'priority' => $group['priority'],
                    'changefreq' => $group['changefreq'],
                ];
            }
        }

        // 2. CMS pages — single canonical path per page (/{slug} or /{slug}-in-{location}), not /page/{slug}
        $staticPathKeys = collect($statics)->pluck('paths')->flatten()->map(fn($p) => ltrim($p, '/'))->filter()->values()->all();
        Page::published()
            ->with(['state:id,slug', 'city:id,slug'])
            ->orderBy('id')
            ->get()
            ->each(function (Page $page) use (&$urls, $now, $staticPathKeys) {
                $path = $page->publicPath();
                if ($path === null) {
                    return;
                }
                $key = ltrim($path, '/');
                if ($key === '' || in_array($key, $staticPathKeys, true)) {
                    return;
                }
                $urls[] = [
                    'loc' => url($path),
                    'lastmod' => $page->updated_at?->toAtomString() ?? $now,
                    'priority' => '0.7',
                    'changefreq' => 'weekly',
                ];
            });

        // 3. Blog
        Post::all()->each(function ($post) use (&$urls, $now) {
            $urls[] = [
                'loc' => url('/blog/' . $post->slug),
                'lastmod' => $post->updated_at?->toAtomString() ?? $now,
                'priority' => '0.8',
                'changefreq' => 'weekly',
            ];
        });

        // 4. Creators
        CreatorProfile::where('is_public', true)->get()->each(function ($creator) use (&$urls, $now) {
            $urls[] = [
                'loc' => url('/creators/' . $creator->slug),
                'lastmod' => $creator->updated_at?->toAtomString() ?? $now,
                'priority' => '0.9',
                'changefreq' => 'weekly',
            ];
        });

        // 5. Studios
        Studio::all()->each(function ($studio) use (&$urls, $now) {
            $urls[] = [
                'loc' => url('/studios/' . $studio->slug),
                'lastmod' => $studio->updated_at?->toAtomString() ?? $now,
                'priority' => '0.8',
                'changefreq' => 'weekly',
            ];
        });

        // 6. Open campaigns
        Campaign::open()->get()->each(function ($campaign) use (&$urls, $now) {
            $urls[] = [
                'loc' => url('/campaigns/' . $campaign->slug),
                'lastmod' => $campaign->updated_at?->toAtomString() ?? $now,
                'priority' => '0.85',
                'changefreq' => 'daily',
            ];
        });

        // 7. Services (canonical /services/{slug}; /gigs/{slug} is alternate UI only — not duplicated in sitemap)
        ServiceListing::where('is_active', true)->get()->each(function ($service) use (&$urls, $now) {
            $urls[] = [
                'loc' => url('/services/' . $service->slug),
                'lastmod' => $service->updated_at?->toAtomString() ?? $now,
                'priority' => '0.7',
                'changefreq' => 'weekly',
            ];
        });

        // 8. Success stories
        SuccessStory::all()->each(function ($story) use (&$urls, $now) {
            $urls[] = [
                'loc' => url('/success-stories/' . $story->slug),
                'lastmod' => $story->updated_at?->toAtomString() ?? $now,
                'priority' => '0.6',
                'changefreq' => 'monthly',
            ];
        });

        return collect($urls)
            ->unique('loc')
            ->values()
            ->all();
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
