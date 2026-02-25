<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AmenityController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Amenity::orderBy('sort_order')->orderBy('name')->get();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:amenities,slug',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $amenity = Amenity::create($data);
        return response()->json(['message' => 'Created', 'amenity' => $amenity], 201);
    }

    public function update(Request $request, Amenity $amenity): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:amenities,slug,' . $amenity->id,
            'icon' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $amenity->update($data);
        return response()->json(['message' => 'Updated', 'amenity' => $amenity->fresh()]);
    }

    public function destroy(Amenity $amenity): JsonResponse
    {
        $amenity->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
