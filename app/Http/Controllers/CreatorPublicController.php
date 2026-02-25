<?php

namespace App\Http\Controllers;

use App\Http\Resources\CreatorProfileDetailResource;
use App\Http\Resources\CreatorProfileResource;
use App\Models\CreatorProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreatorPublicController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = CreatorProfile::query()
            ->with('user.socialAccounts')
            ->withAvg(['reviews as average_rating' => fn ($q) => $q->where('status', 'approved')], 'rating')
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
        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'followers':
                $query->addSelect([
                    'total_followers_sort' => DB::table('social_accounts')
                        ->selectRaw('COALESCE(SUM(followers_count), 0)')
                        ->whereColumn('social_accounts.user_id', 'creator_profiles.user_id')
                        ->where('is_connected', true),
                ])->orderByDesc('total_followers_sort');
                break;
            case 'rating':
                $query->addSelect([
                    'avg_rating_sort' => DB::table('reviews')
                        ->selectRaw('COALESCE(AVG(rating), 0)')
                        ->where('reviewable_type', CreatorProfile::class)
                        ->whereColumn('reviewable_id', 'creator_profiles.id')
                        ->where('status', 'approved'),
                ])->orderByDesc('avg_rating_sort');
                break;
            case 'price':
                $query->orderByRaw('CASE WHEN min_rate IS NOT NULL THEN min_rate ELSE 999999999 END ASC');
                break;
            case 'newest':
            default:
                $query->orderByRaw('(featured_until IS NOT NULL AND featured_until > NOW()) DESC')
                    ->orderByDesc('featured_until')
                    ->orderByDesc('creator_profiles.created_at');
                break;
        }

        $perPage = (int) $request->input('per_page', 12);
        $perPage = min(max($perPage, 1), 24);
        $profiles = $query->paginate($perPage);

        $profiles->getCollection()->transform(function (CreatorProfile $p) {
            $p->user?->makeHidden(['email']);
            $p->load(['user.packages' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->with(['packageCategory', 'items'])]);
            return (new CreatorProfileResource($p))->resolve();
        });

        return response()->json($profiles);
    }

    public function show(string $slug): JsonResponse
    {
        $profile = CreatorProfile::with([
            'user',
            'user.packages' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->with(['packageCategory', 'items']),
            'user.creatorImagePosts' => fn ($q) => $q->orderBy('sort_order')->orderBy('created_at', 'desc'),
            'user.socialAccounts',
        ])
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->where('status', 'approved')])
            ->withAvg(['reviews as average_rating' => fn ($q) => $q->where('status', 'approved')], 'rating')
            ->where('slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();

        $profile->user?->makeHidden(['email']);
        return response()->json(new CreatorProfileDetailResource($profile));
    }
}
