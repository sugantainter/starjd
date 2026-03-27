<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Database\QueryException;
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

        // Prevent SQL "Duplicate entry" errors for (state_id, slug) unique constraint.
        $existing = City::where('state_id', $data['state_id'])
            ->where('slug', $data['slug'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'City already exists for this state.',
                'city' => $existing,
            ], 409);
        }

        try {
            $city = City::create($data);
        } catch (QueryException $e) {
            // Fallback for race conditions on the (state_id, slug) unique key.
            if ((string) $e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                $existing = City::where('state_id', $data['state_id'])
                    ->where('slug', $data['slug'])
                    ->first();

                return response()->json([
                    'message' => 'City already exists for this state.',
                    'city' => $existing,
                ], 409);
            }

            throw $e;
        }
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

        $newStateId = $data['state_id'] ?? $city->state_id;
        $newSlug = $data['slug'] ?? $city->slug;

        $existing = City::where('state_id', $newStateId)
            ->where('slug', $newSlug)
            ->where('id', '!=', $city->id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'City already exists for this state.',
                'city' => $existing,
            ], 409);
        }

        try {
            $city->update($data);
        } catch (QueryException $e) {
            if ((string) $e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                $existing = City::where('state_id', $newStateId)
                    ->where('slug', $newSlug)
                    ->first();

                return response()->json([
                    'message' => 'City already exists for this state.',
                    'city' => $existing,
                ], 409);
            }

            throw $e;
        }
        $city->load('state:id,name,slug');
        return response()->json(['message' => 'Updated', 'city' => $city->fresh()]);
    }

    public function destroy(City $city): JsonResponse
    {
        $city->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
