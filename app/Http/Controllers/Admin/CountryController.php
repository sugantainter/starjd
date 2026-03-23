<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Country::orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug', 'code']);

        return response()->json($items);
    }
}
