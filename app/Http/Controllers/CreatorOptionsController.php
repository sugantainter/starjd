<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class CreatorOptionsController extends Controller
{
    public function filters(): JsonResponse
    {
        return response()->json([
            'categories' => config('creator.categories', []),
            'genders' => config('creator.genders', []),
            'languages' => config('creator.languages', []),
            'platforms' => config('creator.platforms', []),
        ]);
    }
}
