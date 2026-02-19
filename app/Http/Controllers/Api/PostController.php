<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Post::published()->orderByDesc('published_at')->get()->map(fn (Post $p) => [
            'id' => $p->id,
            'title' => $p->title,
            'slug' => $p->slug,
            'excerpt' => $p->excerpt,
            'category' => $p->category_label,
            'date' => $p->published_at?->format('M j, Y'),
            'image' => $p->image,
            'url' => '/blog/' . $p->slug,
        ]);

        return response()->json(['posts' => $posts]);
    }

    public function show(string $slug): JsonResponse
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        return response()->json([
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'meta_title' => $post->meta_title,
            'meta_description' => $post->meta_description,
            'body' => $post->body,
            'image' => $post->image,
            'category' => $post->category_label,
            'category_tags' => $post->category_tags ?? [],
            'date' => $post->published_at?->format('M j, Y'),
            'author' => $post->author?->name,
        ]);
    }
}
