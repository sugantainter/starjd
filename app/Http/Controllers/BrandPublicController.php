<?php

namespace App\Http\Controllers;

use App\Models\BrandProfile;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandPublicController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = BrandProfile::query()
            ->with(['user'])
            ->addSelect([
                'active_campaigns_count' => Campaign::query()
                    ->selectRaw('count(*)')
                    ->whereColumn('brand_id', 'brand_profiles.user_id')
                    ->where('status', 'open')
            ])
            ->where('is_public', true)
            ->whereNotNull('slug');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('company_name', 'like', "%{$term}%")
                    ->orWhere('bio', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%"));
            });
        }

        $perPage = (int) $request->input('per_page', 12);
        $brands = $query->paginate($perPage);

        return response()->json($brands);
    }

    public function show(string $slug): JsonResponse
    {
        $brand = BrandProfile::query()
            ->with(['user'])
            ->where('slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();

        // Get campaigns for this brand
        $campaigns = Campaign::query()
            ->where('brand_id', $brand->user_id)
            ->where('status', 'open')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'brand' => $brand,
            'campaigns' => $campaigns,
        ]);
    }
}
