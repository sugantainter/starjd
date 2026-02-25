<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudioDetailResource;
use App\Http\Resources\StudioResource;
use App\Models\Studio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StudioPublicController extends Controller
{
    private const LIST_CACHE_TTL = 300; // 5 minutes

    private const LIST_CACHE_KEY_PREFIX = 'studio_list:';

    /**
     * GET /api/studios — Public listing with filters, sort, pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = self::LIST_CACHE_KEY_PREFIX.md5(serialize($request->only([
            'category', 'city', 'min_price', 'max_price', 'amenities', 'rating', 'featured',
            'available_date', 'sort', 'per_page', 'page',
        ])));

        $result = Cache::remember($cacheKey, self::LIST_CACHE_TTL, function () use ($request) {
            return $this->buildStudioList($request);
        });

        return response()->json($result);
    }

    private function buildStudioList(Request $request): array
    {
        $query = Studio::query()
            ->with(['category', 'images'])
            ->where('status', 'active');

        if ($request->filled('category')) {
            $cat = $request->category;
            if (is_numeric($cat)) {
                $query->where('category_id', $cat);
            } else {
                $query->whereHas('category', fn ($q) => $q->where('slug', $cat));
            }
        }
        if ($request->filled('city')) {
            $query->where('city', 'like', '%'.$request->city.'%');
        }
        if ($request->filled('min_price')) {
            $query->where(function ($q) use ($request) {
                $min = (float) $request->min_price;
                $q->where('price_per_hour', '>=', $min)
                    ->orWhere('price_per_day', '>=', $min);
            });
        }
        if ($request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $max = (float) $request->max_price;
                $q->where('price_per_hour', '<=', $max)
                    ->orWhere('price_per_day', '<=', $max);
            });
        }
        if ($request->filled('amenities')) {
            $ids = is_array($request->amenities) ? $request->amenities : explode(',', $request->amenities);
            $ids = array_filter(array_map('intval', $ids));
            if (! empty($ids)) {
                $query->whereHas('amenities', fn ($q) => $q->whereIn('amenities.id', $ids));
            }
        }
        if ($request->filled('rating')) {
            $query->where('rating_avg', '>=', (float) $request->rating);
        }
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }
        if ($request->filled('available_date')) {
            $date = $request->available_date;
            $query->whereHas('availabilitySlots', function ($q) use ($date) {
                $q->where('date', $date)->where('is_available', true);
            });
        }

        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderByRaw('COALESCE(price_per_hour, price_per_day, 999999999) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(price_per_hour, price_per_day, 0) DESC');
                break;
            case 'rating':
                $query->orderByDesc('rating_avg')->orderByDesc('reviews_count');
                break;
            case 'newest':
            default:
                $query->orderByDesc('is_featured')->orderByDesc('created_at');
                break;
        }

        $perPage = (int) $request->input('per_page', 12);
        $perPage = min(max($perPage, 1), 24);
        $paginator = $query->paginate($perPage);

        $paginator->getCollection()->transform(fn (Studio $s) => (new StudioResource($s))->resolve());

        return $paginator->toArray();
    }

    /**
     * GET /api/studios/{slugOrId} — Full detail by slug or numeric id (so /studios/5 and /studios/demo-xxx both work).
     */
    public function show(string $slugOrId): JsonResponse
    {
        $query = Studio::with([
            'category',
            'images',
            'amenities',
            'user:id,name',
            'reviews' => fn ($q) => $q->approved()->with('user:id,name')->latest()->limit(20),
            'availabilitySlots' => fn ($q) => $q->where('is_available', true)->where('date', '>=', now()->toDateString())->orderBy('date')->orderBy('start_time')->limit(100),
        ])->where('status', 'active');

        if (ctype_digit($slugOrId)) {
            $studio = $query->where('id', (int) $slugOrId)->firstOrFail();
        } else {
            $studio = $query->where('slug', $slugOrId)->firstOrFail();
        }

        $similar = $this->getSimilarStudios($studio, 4);
        $payload = (new StudioDetailResource($studio))->resolve();
        $payload['similar_studios'] = $similar;

        return response()->json($payload);
    }

    private function getSimilarStudios(Studio $studio, int $limit): array
    {
        $query = Studio::query()
            ->with(['category', 'images'])
            ->where('status', 'active')
            ->where('id', '!=', $studio->id);

        if ($studio->category_id) {
            $query->where('category_id', $studio->category_id);
        }
        if ($studio->city) {
            $query->where('city', $studio->city);
        }
        if (! $studio->category_id && ! $studio->city) {
            $query->orderByDesc('rating_avg')->orderByDesc('reviews_count');
        }

        $studios = $query->limit($limit)->get();

        return $studios->map(fn (Studio $s) => (new StudioResource($s))->resolve())->all();
    }

    /**
     * GET /api/studios/categories — List active categories (for filters).
     */
    public function categories(): JsonResponse
    {
        $categories = Cache::remember('studio_categories_active', 600, function () {
            return \App\Models\StudioCategory::active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'parent_id']);
        });

        return response()->json($categories);
    }
}
