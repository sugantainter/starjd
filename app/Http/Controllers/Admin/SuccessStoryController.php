<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SuccessStoryController extends Controller
{
    public function index(): JsonResponse
    {
        $stories = SuccessStory::with('role:id,name,slug')->orderByDesc('created_at')->get();
        return response()->json($stories);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:success_stories,slug',
            'content' => 'nullable|string',
            'image' => 'nullable|string',
            'role_id' => 'nullable|exists:roles,id',
            'author_name' => 'nullable|string|max:255',
            'author_designation' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'status' => 'nullable|in:draft,published',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['status'] = $data['status'] ?? 'draft';
        $data['is_featured'] = $data['is_featured'] ?? false;

        $story = SuccessStory::create($data);
        $story->load('role:id,name,slug');

        return response()->json(['message' => 'Created', 'story' => $story]);
    }

    public function show(SuccessStory $successStory): JsonResponse
    {
        $successStory->load('role:id,name,slug');
        return response()->json($successStory);
    }

    public function update(Request $request, SuccessStory $successStory): JsonResponse
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:success_stories,slug,' . $successStory->id,
            'content' => 'nullable|string',
            'image' => 'nullable|string',
            'role_id' => 'nullable|exists:roles,id',
            'author_name' => 'nullable|string|max:255',
            'author_designation' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'status' => 'nullable|in:draft,published',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
        ]);

        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $successStory->update($data);
        $successStory->load('role:id,name,slug');

        return response()->json(['message' => 'Updated', 'story' => $successStory]);
    }

    public function destroy(SuccessStory $successStory): JsonResponse
    {
        $successStory->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('success_stories', 'public');
            return response()->json(['url' => asset('storage/' . $path)]);
        }

        return response()->json(['message' => 'No image uploaded'], 400);
    }
}
