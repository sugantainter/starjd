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

        // Logic check: only proceed if it is a collaboration deliverable 
        // OR a public file (to replace the shadowed public storage route)
        
        $collaboration = Collaboration::where('deliverable_content', 'like', '%' . $path)->first();

        if ($collaboration) {
            \Log::info('Collaboration file matched', ['id' => $collaboration->id, 'path' => $path]);
            
            // Smarter GCS check: try both prefixed and raw paths to avoid .env mismatch failures
            $targetPath = $collaboration->deliverable_content;
            if (Storage::disk('gcs')->exists($targetPath)) {
                $finalPath = $targetPath;
            } elseif (str_starts_with($targetPath, 'project_deliverables/') && Storage::disk('gcs')->exists(Str::after($targetPath, 'project_deliverables/'))) {
                $finalPath = Str::after($targetPath, 'project_deliverables/');
            } else {
                $finalPath = null;
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
        if (Storage::disk('public')->exists($path)) {
            $path_full = Storage::disk('public')->path($path);
            $mime = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';
            return response()->file($path_full, ['Content-Type' => $mime]);
        }

        abort(404);
    }
}
