<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\StoragePublicUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StorageUrlController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $path = (string) $request->query('path', '');
        $path = str_replace(['..', '\\'], '', $path);
        $path = ltrim(trim($path), '/');
        while (str_starts_with($path, 'storage/')) {
            $path = ltrim(substr($path, strlen('storage/')), '/');
        }

        if ($path === '' || strlen($path) > 2000) {
            return response()->json(['message' => 'Invalid path'], 422);
        }

        $default = config('filesystems.default');
        if ($default === 'gsc') {
            $default = 'gcs';
        }

        if ($default !== 'gcs' && ! Storage::exists($path) && ! Storage::disk('public')->exists($path)) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $url = StoragePublicUrl::resolve($path);
        if ($url === null || $url === '') {
            return response()->json(['message' => 'Could not resolve URL'], 404);
        }

        return response()->json(['url' => $url]);
    }
}
