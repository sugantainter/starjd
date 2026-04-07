<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\StoragePublicUrl;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        $services = Service::orderBy('sort_order')->orderBy('name')->get();

        return response()->json($services->map(function (Service $s) {
            $a = $s->toArray();
            $a['body'] = StoragePublicUrl::rewriteStorageUrlsInHtml($s->body ?? '');

            return $a;
        }));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'image' => 'nullable|string|max:2000',
            'banner_image' => 'nullable|string|max:2000',
            'image_fit' => 'nullable|string|in:cover,contain',
            'banner_position' => 'nullable|string|in:center,top,bottom',
            'body' => 'nullable|string',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? Service::max('sort_order') + 1;
        foreach (['image', 'banner_image'] as $k) {
            if (! empty($data[$k])) {
                $data[$k] = StoragePublicUrl::normalizeToStoragePath($data[$k]);
            }
        }
        if (array_key_exists('body', $data) && $data['body'] !== null) {
            $data['body'] = StoragePublicUrl::normalizeStorageUrlsInHtml($data['body']);
        }
        $service = Service::create($data);
        $arr = $service->toArray();
        $arr['body'] = StoragePublicUrl::rewriteStorageUrlsInHtml($service->body ?? '');

        return response()->json(['message' => 'Created', 'service' => $arr]);
    }

    public function update(Request $request, int $service): JsonResponse
    {
        $service = Service::findOrFail($service);
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'image' => 'nullable|string|max:2000',
            'banner_image' => 'nullable|string|max:2000',
            'image_fit' => 'nullable|string|in:cover,contain',
            'banner_position' => 'nullable|string|in:center,top,bottom',
            'body' => 'nullable|string',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        foreach (['image', 'banner_image'] as $k) {
            if (array_key_exists($k, $data) && $data[$k] !== null && $data[$k] !== '') {
                $data[$k] = StoragePublicUrl::normalizeToStoragePath($data[$k]);
            }
        }
        if (array_key_exists('body', $data) && $data['body'] !== null) {
            $data['body'] = StoragePublicUrl::normalizeStorageUrlsInHtml($data['body']);
        }
        $service->update($data);
        $fresh = $service->fresh();
        $arr = $fresh->toArray();
        $arr['body'] = StoragePublicUrl::rewriteStorageUrlsInHtml($fresh->body ?? '');

        return response()->json(['message' => 'Updated', 'service' => $arr]);
    }

    public function destroy(int $service): JsonResponse
    {
        Service::findOrFail($service)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
