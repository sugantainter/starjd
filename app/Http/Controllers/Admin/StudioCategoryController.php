<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudioCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudioCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $items = StudioCategory::orderBy('sort_order')->orderBy('name')->get();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:studio_categories,slug',
            'parent_id' => 'nullable|integer|exists:studio_categories,id',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $category = StudioCategory::create($data);
        return response()->json(['message' => 'Created', 'category' => $category], 201);
    }

    public function update(Request $request, StudioCategory $studio_category): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:studio_categories,slug,' . $studio_category->id,
            'parent_id' => 'nullable|integer|exists:studio_categories,id',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $studio_category->update($data);
        return response()->json(['message' => 'Updated', 'category' => $studio_category->fresh()]);
    }

    public function destroy(StudioCategory $studio_category): JsonResponse
    {
        if ($studio_category->studios()->exists()) {
            return response()->json(['message' => 'Cannot delete: category has studios.'], 422);
        }
        $studio_category->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
