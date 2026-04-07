<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Browser-usable URL for a stored path on the default disk (GCS in production).
 *
 * For GCS with uniform bucket-level access, objects are often private: use signed URLs
 * (GCS_SIGNED_READ_URLS=true) so <img src> works. Public buckets can set GCS_SIGNED_READ_URLS=false.
 *
 * Also accepts full https URLs pointing at *this* app's GCS bucket (e.g. expired signed URLs
 * saved in hero/blog fields) and re-issues a fresh URL from the object key.
 */
final class StoragePublicUrl
{
    /**
     * If $value is a URL for our configured GCS bucket, return the object key; otherwise null.
     */
    public static function gcsObjectKeyFromPublicUrl(string $url): ?string
    {
        $bucket = (string) config('filesystems.disks.gcs.bucket');
        if ($bucket === '') {
            return null;
        }

        $parsed = parse_url($url);
        if ($parsed === false || empty($parsed['host'])) {
            return null;
        }

        $host = strtolower((string) $parsed['host']);
        $path = (string) ($parsed['path'] ?? '');

        if ($host === 'storage.googleapis.com') {
            $prefix = '/'.$bucket.'/';
            if (str_starts_with($path, $prefix)) {
                return rawurldecode(ltrim(substr($path, strlen($prefix)), '/'));
            }

            return null;
        }

        $suffix = '.storage.googleapis.com';
        if (str_ends_with($host, $suffix)) {
            $hostBucket = substr($host, 0, -strlen($suffix));
            if ($hostBucket === strtolower($bucket)) {
                return rawurldecode(ltrim($path, '/'));
            }
        }

        return null;
    }

    /**
     * Object key from a URL whose path is /storage/... (saved after uploads or rich text).
     * We do not require host === APP_URL so www/apex/staging mismatches still resolve.
     */
    public static function objectKeyFromAppStorageUrl(string $url): ?string
    {
        $parsed = parse_url($url);
        if ($parsed === false || empty($parsed['path']) || ! str_starts_with($parsed['path'], '/storage/')) {
            return null;
        }

        $rel = substr($parsed['path'], strlen('/storage/'));

        return rawurldecode(ltrim($rel, '/')) ?: null;
    }

    /**
     * Extract GCS object key from any known URL shape we store in the DB.
     */
    public static function objectKeyFromStoredUrl(string $url): ?string
    {
        $key = self::gcsObjectKeyFromPublicUrl($url);
        if ($key !== null) {
            return $key;
        }

        return self::objectKeyFromAppStorageUrl($url);
    }

    /**
     * Store relative object keys in the DB, not signed URLs. Converts our GCS URLs back to keys.
     */
    public static function normalizeToStoragePath(string $src): string
    {
        $src = trim($src);
        if ($src === '') {
            return '';
        }

        if (str_starts_with($src, '//')) {
            $src = 'https:'.$src;
        }

        if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
            $key = self::objectKeyFromStoredUrl($src);

            return $key ?? $src;
        }

        $p = ltrim($src, '/');
        while (str_starts_with($p, 'storage/')) {
            $p = ltrim(substr($p, strlen('storage/')), '/');
        }
        if (str_starts_with($p, 'public/')) {
            $p = ltrim(substr($p, strlen('public/')), '/');
        }

        return $p;
    }

    /**
     * Replace <img src="/storage/..."> (and site-relative paths) with resolved GCS/public URLs.
     */
    /**
     * Convert img src with our GCS / app URLs back to /storage/{key} for persistence.
     * (Admin editor receives signed URLs; saves must store stable paths.)
     */
    public static function normalizeStorageUrlsInHtml(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        return (string) preg_replace_callback(
            '/\ssrc\s*=\s*(["\'])((?:https?:)?\/\/[^"\']+)\1/i',
            static function (array $m): string {
                $q = $m[1];
                $raw = $m[2];
                $u = $raw;
                if (str_starts_with($u, '//')) {
                    $u = 'https:'.$u;
                }
                $key = self::objectKeyFromStoredUrl($u);
                if ($key === null || $key === '') {
                    return $m[0];
                }

                return ' src='.$q.'/storage/'.$key.$q;
            },
            $html
        );
    }

    public static function rewriteStorageUrlsInHtml(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $rewrite = static function (string $h): string {
            return (string) preg_replace_callback(
                '/\ssrc\s*=\s*(["\'])(\/?storage\/[^"\']+)\1/i',
                static function (array $m): string {
                    $q = $m[1];
                    $raw = $m[2];
                    $key = ltrim(preg_replace('#^/?storage/#', '', $raw) ?? '', '/');
                    $url = self::resolve($key);
                    if ($url === null || $url === '') {
                        return $m[0];
                    }

                    return ' src='.$q.$url.$q;
                },
                $h
            );
        };

        $html = $rewrite($html);

        return (string) preg_replace_callback(
            '/\ssrc\s*=\s*(["\'])((?:https?:)?\/\/[^"\']+\/storage\/[^"\']+)\1/i',
            static function (array $m): string {
                $q = $m[1];
                $raw = $m[2];
                $url = self::resolve($raw);
                if ($url === null || $url === '') {
                    return $m[0];
                }

                return ' src='.$q.$url.$q;
            },
            $html
        );
    }

    public static function resolve(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $path = trim($path);

        if (str_starts_with($path, '//')) {
            $path = 'https:'.$path;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $key = self::objectKeyFromStoredUrl($path);
            if ($key === null) {
                return $path;
            }
            $path = $key;
        } else {
            $path = ltrim($path, '/');
            while (str_starts_with($path, 'storage/')) {
                $path = ltrim(substr($path, strlen('storage/')), '/');
            }
            if (str_starts_with($path, 'public/')) {
                $path = ltrim(substr($path, strlen('public/')), '/');
            }
        }

        $default = config('filesystems.default');
        if ($default === 'gsc') {
            $default = 'gcs';
        }

        if ($default === 'gcs' && config('filesystems.disks.gcs.signed_read_urls')) {
            try {
                $hours = (int) config('filesystems.disks.gcs.signed_read_ttl_hours', 168);

                return Storage::disk('gcs')->temporaryUrl($path, now()->addHours(max(1, $hours)));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('StoragePublicUrl: GCS temporaryUrl failed', [
                    'path' => $path,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return Storage::disk($default)->url($path);
    }
}
