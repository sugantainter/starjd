<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Post::with('author:id,name')->orderByDesc('created_at')->get();
        return response()->json($posts);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'image' => 'nullable|string|max:500',
            'category_label' => 'nullable|string|max:100',
            'status' => 'nullable|in:draft,published',
            'published_at' => 'nullable|date',
            'sort_order' => 'nullable|integer',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['user_id'] = $request->user()->id;
        $data['status'] = $data['status'] ?? 'draft';
        if (($data['status'] ?? '') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        $post = Post::create($data);
        return response()->json(['message' => 'Created', 'post' => $post]);
    }

    public function update(Request $request, int $post): JsonResponse
    {
        $post = Post::findOrFail($post);
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'sometimes|string',
            'image' => 'nullable|string|max:500',
            'category_label' => 'nullable|string|max:100',
            'status' => 'nullable|in:draft,published',
            'published_at' => 'nullable|date',
            'sort_order' => 'nullable|integer',
        ]);
        if (isset($data['status']) && $data['status'] === 'published' && ! $post->published_at) {
            $data['published_at'] = $data['published_at'] ?? now();
        }
        $post->update($data);
        return response()->json(['message' => 'Updated', 'post' => $post->fresh()]);
    }

    public function destroy(int $post): JsonResponse
    {
        Post::findOrFail($post)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
