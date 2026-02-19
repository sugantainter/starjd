<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $items = DB::table('categories')->orderBy('sort_order')->get();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'image' => 'nullable|string|max:500',
            'count_display' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['created_at'] = $data['updated_at'] = now();
        DB::table('categories')->insert($data);
        return response()->json(['message' => 'Created', 'id' => DB::getPdo()->lastInsertId()]);
    }

    public function update(Request $request, int $category): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'image' => 'nullable|string|max:500',
            'count_display' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
        ]);
        $data['updated_at'] = now();
        DB::table('categories')->where('id', $category)->update($data);
        return response()->json(['message' => 'Updated']);
    }

    public function destroy(int $category): JsonResponse
    {
        DB::table('categories')->where('id', $category)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
