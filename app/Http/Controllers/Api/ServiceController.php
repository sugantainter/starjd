<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
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

        return response()->json($services);
    }

    /** Single service page by slug (active only). */
    public function show(string $slug): JsonResponse
    {
        $service = Service::active()->where('slug', $slug)->firstOrFail();

        return response()->json([
            'id' => $service->id,
            'name' => $service->name,
            'slug' => $service->slug,
            'short_description' => $service->short_description,
            'image' => $service->image,
            'banner_image' => $service->banner_image,
            'image_fit' => $service->image_fit,
            'banner_position' => $service->banner_position,
            'body' => $service->body,
            'meta_title' => $service->meta_title,
            'meta_description' => $service->meta_description,
        ]);
    }
}
