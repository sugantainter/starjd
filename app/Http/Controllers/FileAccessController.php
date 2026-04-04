<?php

namespace App\Http\Controllers;

use App\Models\Collaboration;
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
            \Log::info('Collaboration file matched', ['id' => $collaboration->id, 'path' => $path]);
            
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
                // For GCS, we return a signed temporary URL
                $url = Storage::disk('gcs')->temporaryUrl($finalPath, now()->addMinutes(10));
                \Log::info('Generated GCS Signed URL', ['final_path' => $finalPath]);
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
        // Check default cloud storage first (now that it's migrated)
        if (Storage::exists($path)) {
            return redirect(Storage::url($path));
        }

        if (Storage::disk('public')->exists($path)) {
            $path_full = Storage::disk('public')->path($path);
            $mime = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';
            return response()->file($path_full, ['Content-Type' => $mime]);
        }

        abort(404);
    }
}
