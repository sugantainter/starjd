<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use App\Support\StoragePublicUrl;
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

        $paginator = $query->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate($request->integer('limit', 12));

        $paginator->setCollection(
            $paginator->getCollection()->map(fn (SuccessStory $s) => $this->serializeForPublic($s))
        );

        return response()->json($paginator);
    }

    public function show(string $slug): JsonResponse
    {
        $story = SuccessStory::with('role:id,name,slug')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($this->serializeForPublic($story));
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeForPublic(SuccessStory $story): array
    {
        $data = $story->toArray();

        if (! empty($data['image'])) {
            $data['image'] = StoragePublicUrl::resolve($data['image']) ?? $data['image'];
        }

        if (! empty($data['content'])) {
            $data['content'] = StoragePublicUrl::rewriteStorageUrlsInHtml($data['content']);
        }

        return $data;
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
