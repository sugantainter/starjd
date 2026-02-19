<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreatorPackageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json($request->user()->packages()->orderBy('sort_order')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'deliverables' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $max = $request->user()->packages()->max('sort_order') ?? 0;
        $package = $request->user()->packages()->create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'deliverables' => $request->deliverables,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $max + 1,
        ]);
        return response()->json($package, 201);
    }

    public function update(Request $request, Package $package): JsonResponse
    {
        if ($package->creator_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'deliverables' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $package->update($request->only(['name', 'price', 'description', 'deliverables', 'is_active', 'sort_order']));
        return response()->json($package);
    }

    public function destroy(Request $request, Package $package): JsonResponse
    {
        if ($package->creator_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $package->delete();
        return response()->json(null, 204);
    }
}
