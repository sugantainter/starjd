<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public const POST_IMAGE_MAX_KB = 2048; // 2MB

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
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'body' => 'required|string',
            'image' => 'nullable|string|max:2000',
            'category_label' => 'nullable|string|max:100',
            'category_tags' => 'nullable|array',
            'category_tags.*' => 'string|max:50',
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
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'body' => 'sometimes|string',
            'image' => 'nullable|string|max:2000',
            'category_label' => 'nullable|string|max:100',
            'category_tags' => 'nullable|array',
            'category_tags.*' => 'string|max:50',
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

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:' . self::POST_IMAGE_MAX_KB],
        ], [
            'image.required' => 'Please select an image file.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'Allowed formats: JPEG, PNG, JPG, WebP, GIF.',
            'image.max' => 'The image must not be larger than 2 MB.',
        ]);

        $file = $request->file('image');
        try {
            $path = $file->store('posts/' . date('Y-m-d'), 'public');
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Storage error. Ensure storage is linked (php artisan storage:link).'], 500);
        }
        $url = asset('storage/' . $path);

        return response()->json(['url' => $url, 'path' => $path]);
    }
}
