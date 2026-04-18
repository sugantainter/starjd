<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Support\StoragePublicUrl;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    /** For navbar dropdown and services listing: active services only, ordered. */
    public function index(): JsonResponse
    {
        $services = Service::active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'short_description', 'image', 'image_fit']);

        $data = $services->map(fn (Service $s) => [
            'id' => $s->id,
            'name' => $s->name,
            'slug' => $s->slug,
            'short_description' => $s->short_description,
            'image' => $s->image ? StoragePublicUrl::resolve($s->image) : null,
            'image_fit' => $s->image_fit,
        ]);

        return response()->json($data);
    }

    /** Single service page by slug (active only). */
    public function show(string $slug): JsonResponse
    {
        $service = Service::active()->where('slug', $slug)->firstOrFail();

        $body = $service->body ? html_entity_decode($service->body) : '';
        if ($body !== '') {
            $body = StoragePublicUrl::rewriteStorageUrlsInHtml($body);
        }

        return response()->json([
            'id' => $service->id,
            'name' => $service->name,
            'slug' => $service->slug,
            'short_description' => $service->short_description,
            'image' => $service->image ? StoragePublicUrl::resolve($service->image) : null,
            'banner_image' => $service->banner_image ? StoragePublicUrl::resolve($service->banner_image) : null,
            'image_fit' => $service->image_fit,
            'banner_position' => $service->banner_position,
            'body' => $body,
            'meta_title' => $service->meta_title,
            'meta_description' => $service->meta_description,
            'meta_keywords' => $service->meta_keywords,
        ]);
    }
}
