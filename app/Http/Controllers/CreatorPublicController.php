<?php

namespace App\Http\Controllers;

use App\Models\CreatorProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreatorPublicController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = CreatorProfile::with('user')
            ->where('is_public', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }
        if ($request->filled('min_rate')) {
            $query->where('min_rate', '>=', (float) $request->min_rate);
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('tagline', 'like', "%{$term}%")
                    ->orWhere('bio', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%"));
            });
        }
        if ($request->filled('featured') && $request->boolean('featured')) {
            $query->whereNotNull('featured_until')->where('featured_until', '>', now());
        }
        if ($request->filled('platform')) {
            $platform = strtolower((string) $request->platform);
            $query->whereHas('user.socialAccounts', function ($q) use ($platform) {
                $q->where('platform', $platform)->where('is_connected', true);
            });
        }

        $query->orderByRaw('(featured_until IS NOT NULL AND featured_until > NOW()) DESC');
        $query->orderByDesc('featured_until');
        $query->orderByDesc('created_at');

        $perPage = (int) $request->input('per_page', 12);
        $perPage = min(max($perPage, 1), 24);
        $profiles = $query->paginate($perPage);
        $profiles->getCollection()->transform(function (CreatorProfile $p) {
            $p->user->makeHidden(['email']);
            $p->load(['user.packages' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->with(['packageCategory', 'items'])]);
            return $p;
        });
        return response()->json($profiles);
    }

    public function show(string $slug): JsonResponse
    {
        $profile = CreatorProfile::with([
            'user',
            'user.packages' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->with(['packageCategory', 'items']),
            'user.creatorImagePosts' => fn ($q) => $q->orderBy('sort_order')->orderBy('created_at', 'desc'),
        ])
            ->where('slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();
        $profile->user->makeHidden(['email']);
        $profile->user->load('socialAccounts');
        return response()->json($profile);
    }
}
