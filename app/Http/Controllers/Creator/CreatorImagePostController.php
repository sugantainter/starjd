<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\CreatorImagePost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CreatorImagePostController extends Controller
{
    public const IMAGE_MAX_SIZE_KB = 2048;

    public function index(Request $request): JsonResponse
    {
        $posts = $request->user()->creatorImagePosts()->orderBy('sort_order')->orderBy('created_at', 'desc')->get();
        return response()->json($posts);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:' . self::IMAGE_MAX_SIZE_KB],
            'caption' => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();
        $path = $request->file('image')->store(
            'profiles/creator-posts/' . $user->id,
            'public'
        );

        $maxOrder = $user->creatorImagePosts()->max('sort_order') ?? 0;
        $post = $user->creatorImagePosts()->create([
            'image' => $path,
            'caption' => $request->input('caption'),
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json($post, 201);
    }

    public function destroy(Request $request, CreatorImagePost $creatorImagePost): JsonResponse
    {
        if ($creatorImagePost->creator_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        Storage::disk('public')->delete($creatorImagePost->image);
        $creatorImagePost->delete();
        return response()->json(null, 204);
    }
}
