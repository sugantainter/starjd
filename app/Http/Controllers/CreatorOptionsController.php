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
                ->pluck('name')
                ->toArray();
        }
        
        // If no categories in database, use config
        if (empty($categories)) {
            $categories = config('creator.categories', []);
        }
        
        return response()->json([
            'categories' => $categories,
            'genders' => config('creator.genders', []),
            'languages' => config('creator.languages', []),
            'platforms' => config('creator.platforms', []),
        ]);
    }
}

