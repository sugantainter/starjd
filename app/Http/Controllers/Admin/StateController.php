<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StateController extends Controller
{
    public function index(): JsonResponse
    {
        $items = State::orderBy('sort_order')->orderBy('name')->get();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:states,slug',
            'code' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $state = State::create($data);
        return response()->json(['message' => 'Created', 'state' => $state]);
    }

    public function update(Request $request, State $state): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:states,slug,' . $state->id,
            'code' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
        ]);
        $state->update($data);
        return response()->json(['message' => 'Updated', 'state' => $state->fresh()]);
    }

    public function destroy(State $state): JsonResponse
    {
        $state->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
