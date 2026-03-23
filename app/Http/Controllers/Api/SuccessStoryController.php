<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuccessStoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SuccessStory::with('role:id,name,slug')->published();

        if ($request->filled('role_slug')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('slug', $request->role_slug);
            });
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        $stories = $query->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate($request->integer('limit', 12));

        return response()->json($stories);
    }

    public function show(string $slug): JsonResponse
    {
        $story = SuccessStory::with('role:id,name,slug')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($story);
    }

    public function roles(): JsonResponse
    {
        $roles = \App\Models\Role::whereHas('successStories', function ($q) {
            $q->published();
        })
            ->whereNotIn('slug', ['admin', 'customer'])
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return response()->json($roles);
    }
}
