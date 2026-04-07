<?php

namespace App\Http\Controllers;

use App\Models\Collaboration;
use App\Support\StoragePublicUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileAccessController extends Controller
{
    public function __invoke(Request $request, string $path)
    {
        $user = auth()->user();
        $path = str_replace(['..', '\\'], '', $path);
        $basename = basename($path);

        // Logic check: only proceed if it is a collaboration deliverable 
        // OR a public file (to replace the shadowed public storage route)

        $collaboration = Collaboration::query()
            ->where('deliverable_content', 'like', '%' . $path)
            ->orWhere('deliverable_content', 'like', '%' . $basename)
            ->first();

        if ($collaboration) {
            // Smarter GCS check: try common path variants to avoid prefix/storage format mismatches
            $targetPath = $collaboration->deliverable_content;
            $candidates = array_values(array_unique(array_filter([
                $targetPath,
                $path,
                $basename,
                str_starts_with($targetPath, 'project_deliverables/') ? Str::after($targetPath, 'project_deliverables/') : null,
                str_starts_with($path, 'project_deliverables/') ? Str::after($path, 'project_deliverables/') : null,
                'project_deliverables/' . $basename,
            ])));

            $finalPath = null;
            foreach ($candidates as $candidate) {
                if (Storage::disk('gcs')->exists($candidate)) {
                    $finalPath = $candidate;
                    break;
                }
            }

            if ($finalPath) {
                $url = Storage::disk('gcs')->temporaryUrl($finalPath, now()->addMinutes(10));

                return redirect($url);
            }

            // Fallback to local (private) or public disk for older files
            if (Storage::disk('local')->exists($path)) {
                $path_full = Storage::disk('local')->path($path);
            } elseif (Storage::disk('public')->exists($path)) {
                $path_full = Storage::disk('public')->path($path);
            } else {
                abort(404);
            }

            $isAdmin = $user && $user->role && $user->role->slug === 'admin';
            $isBrand = $user && $user->id === $collaboration->brand_id;
            $isCompleted = $collaboration->status === 'completed';

            // Brands can only preview (inline) if NOT completed
            if ($isBrand && !$isCompleted && !$isAdmin) {
                return response()->file($path_full, [
                    'Content-Disposition' => 'inline',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            }

            // Creator (always) or Brand (after completion) or Admin can download
            return response()->download($path_full);
        }

        // If not a collaboration file, fall back to PUBLIC storage behavior
        $default = config('filesystems.default');
        if ($default === 'gsc') {
            $default = 'gcs';
        }

        // GCS + signed reads: object()->exists() can fail under some IAM setups while signing still works.
        // Only attempt this for known web-public upload prefixes (never receipts, deliverables, etc.).
        if ($default === 'gcs'
            && config('filesystems.disks.gcs.signed_read_urls')
            && self::isPublicWebStoragePath($path)) {
            try {
                $ttlHours = max(1, (int) config('filesystems.disks.gcs.signed_read_ttl_hours', 168));
                $signed = Storage::disk('gcs')->temporaryUrl($path, now()->addHours($ttlHours));

                return redirect($signed);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::debug('FileAccessController: GCS temporaryUrl failed for public path', [
                    'path' => $path,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        if (Storage::disk($default)->exists($path)) {
            $public = StoragePublicUrl::resolve($path);
            if ($public) {
                return redirect($public);
            }
        }

        if (Storage::disk('public')->exists($path)) {
            $path_full = Storage::disk('public')->path($path);
            $mime = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';
            return response()->file($path_full, ['Content-Type' => $mime]);
        }

        abort(404);
    }

    /**
     * Paths we are willing to serve via /storage/{path} without a reliable exists() check (GCS only).
     *
     * @see self::__invoke() public fallback branch
     */
    private static function isPublicWebStoragePath(string $path): bool
    {
        if ($path === '' || str_contains($path, '..')) {
            return false;
        }

        $prefixes = [
            'studios/',
            'banners/',
            'categories/',
            'hero/',
            'partners/',
            'posts/',
            'success_stories/',
            'testimonials/',
            'services/gallery/',
            'avatars/',
            'profiles/',
        ];

        foreach ($prefixes as $p) {
            if (str_starts_with($path, $p)) {
                return true;
            }
        }

        return false;
    }
}
