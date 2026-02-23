<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /** Distinct blog categories (label + slug) for footer/links. */
    public function categories(): JsonResponse
    {
        $categories = Post::published()
            ->whereNotNull('category_label')
            ->where('category_label', '!=', '')
            ->select('category_label')
            ->distinct()
            ->orderBy('category_label')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->category_label,
                'slug' => Str::slug($row->category_label),
            ])
            ->values();

        return response()->json(['categories' => $categories]);
    }

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

        $sessionKey = 'post_viewed_' . $post->id;
        if (! session()->has($sessionKey)) {
            $post->increment('view_count');
            session()->put($sessionKey, true);
        }

        $post->refresh();

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
            'updated_at' => $post->updated_at?->format('F j, Y'),
            'author' => $post->author_name ?? $post->author?->name,
            'view_count' => (int) $post->view_count,
        ]);
    }
}
