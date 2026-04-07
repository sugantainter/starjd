<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('sub_categories')
            ->join('categories', 'sub_categories.category_id', '=', 'categories.id')
            ->select('sub_categories.*', 'categories.name as category_name');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $items = $query->orderBy('sort_order')->get();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['created_at'] = $data['updated_at'] = now();

        DB::table('sub_categories')->insert($data);
        return response()->json(['message' => 'Sub category created']);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $data = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['updated_at'] = now();

        DB::table('sub_categories')->where('id', $id)->update($data);
        return response()->json(['message' => 'Sub category updated']);
    }

    public function destroy($id): JsonResponse
    {
        DB::table('sub_categories')->where('id', $id)->delete();
        return response()->json(['message' => 'Sub category deleted']);
    }
}
