<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public const IMAGE_MAX_KB = 2048; // 2MB

    public function index(): JsonResponse
    {
        $items = DB::table('categories')->orderBy('sort_order')->get();
        $items->transform(function ($row) {
            $row->image_url = $this->imageUrl($row->image);
            return $row;
        });
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'count_display' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
        ]);
        if ($request->hasFile('image')) {
            $request->validate(['image' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:' . self::IMAGE_MAX_KB]]);
            $data['image'] = $request->file('image')->store('categories', 'public');
        } else {
            $data['image'] = null;
        }
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['created_at'] = $data['updated_at'] = now();
        DB::table('categories')->insert($data);
        $id = DB::getPdo()->lastInsertId();
        $row = DB::table('categories')->find($id);
        $row->image_url = $this->imageUrl($row->image);
        return response()->json(['message' => 'Created', 'id' => (int) $id, 'category' => $row]);
    }

    public function update(Request $request, int $category): JsonResponse
    {
        $row = DB::table('categories')->where('id', $category)->first();
        if (! $row) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'count_display' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
        ]);
        if ($request->hasFile('image')) {
            $request->validate(['image' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:' . self::IMAGE_MAX_KB]]);
            if ($row->image && ! str_starts_with($row->image, 'http')) {
                Storage::disk('public')->delete($row->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }
        $data['updated_at'] = now();
        DB::table('categories')->where('id', $category)->update($data);
        $updated = DB::table('categories')->find($category);
        $updated->image_url = $this->imageUrl($updated->image);
        return response()->json(['message' => 'Updated', 'category' => $updated]);
    }

    public function destroy(int $category): JsonResponse
    {
        $row = DB::table('categories')->where('id', $category)->first();
        if ($row && $row->image && ! str_starts_with($row->image, 'http')) {
            Storage::disk('public')->delete($row->image);
        }
        DB::table('categories')->where('id', $category)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    private function imageUrl(?string $image): ?string
    {
        if (! $image) {
            return null;
        }
        if (str_starts_with($image, 'http')) {
            return $image;
        }
        return asset('storage/' . $image);
    }
}
