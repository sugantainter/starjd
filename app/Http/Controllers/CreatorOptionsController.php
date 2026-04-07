<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatorOptionsController extends Controller
{
    public function filters(): JsonResponse
    {
        // Fetch categories from database, fallback to config
        $categories = [];
        if (Schema::hasTable('categories')) {
            $categories = DB::table('categories')
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug', 'show_on_navbar'])
                ->toArray();
        }
        
        // If no categories in database, fallback to minimal formatting
        if (empty($categories)) {
            $configCategories = config('creator.categories', []);
            $categories = array_map(fn($c, $i) => ['id' => $i+1, 'name' => $c, 'slug' => \Illuminate\Support\Str::slug($c), 'show_on_navbar' => false], $configCategories, array_keys($configCategories));
        }
        
        $subCategories = DB::table('sub_categories')
            ->join('categories', 'sub_categories.category_id', '=', 'categories.id')
            ->select('sub_categories.id', 'sub_categories.category_id', 'sub_categories.name', 'sub_categories.slug', 'categories.name as category_name')
            ->orderBy('sub_categories.sort_order')
            ->get();
        
        return response()->json([
            'categories' => $categories,
            'sub_categories' => $subCategories,
            'genders' => config('creator.genders', []),
            'languages' => config('creator.languages', []),
            'platforms' => config('creator.platforms', []),
        ]);
    }
}

