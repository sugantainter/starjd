<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = City::with('state:id,name,slug')->orderBy('sort_order')->orderBy('name');
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }
        $items = $query->get();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'state_id' => 'required|exists:states,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $city = City::create($data);
        $city->load('state:id,name,slug');
        return response()->json(['message' => 'Created', 'city' => $city]);
    }

    public function update(Request $request, City $city): JsonResponse
    {
        $data = $request->validate([
            'state_id' => 'sometimes|exists:states,id',
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);
        $city->update($data);
        $city->load('state:id,name,slug');
        return response()->json(['message' => 'Updated', 'city' => $city->fresh()]);
    }

    public function destroy(City $city): JsonResponse
    {
        $city->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
