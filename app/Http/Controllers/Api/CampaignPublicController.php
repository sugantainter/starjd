<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignPublicController extends Controller
{
    /**
     * List open campaigns for creators (public). Supports filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Campaign::query()
            ->open()
            ->with('brand:id,name')
            ->orderByDesc('created_at');

        if ($request->filled('campaign_type')) {
            $query->where('campaign_type', $request->campaign_type);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }
        if ($request->filled('budget_min')) {
            $query->where('budget', '>=', (float) $request->budget_min);
        }
        if ($request->filled('budget_max')) {
            $query->where('budget', '<=', (float) $request->budget_max);
        }
        if ($request->filled('niche')) {
            $niche = $request->niche;
            $query->whereJsonContains('targeting->niches', $niche);
        }
        if ($request->filled('country')) {
            $country = $request->country;
            $query->whereJsonContains('targeting->countries', $country);
        }
        if ($request->filled('language')) {
            $lang = $request->language;
            $query->whereJsonContains('targeting->languages', $lang);
        }

        $perPage = max(1, min(50, (int) $request->input('per_page', 12)));
        $campaigns = $query->paginate($perPage);

        $campaigns->getCollection()->transform(function (Campaign $c) {
            return [
                'id' => $c->id,
                'slug' => $c->slug,
                'title' => $c->title,
                'campaign_type' => $c->campaign_type,
                'description' => $c->description ? \Str::limit($c->description, 160) : null,
                'budget' => $c->budget,
                'max_applications' => $c->max_applications,
                'starts_at' => $c->starts_at?->toIso8601String(),
                'ends_at' => $c->ends_at?->toIso8601String(),
                'targeting' => $c->targeting,
                'brand' => $c->brand ? ['id' => $c->brand->id, 'name' => $c->brand->name] : null,
                'applications_count' => $c->applications()->count(),
                'created_at' => $c->created_at->toIso8601String(),
            ];
        });

        return response()->json($campaigns);
    }

    /**
     * Show a single open campaign by slug (SEO-friendly).
     */
    public function show(string $slug): JsonResponse
    {
        $campaign = Campaign::query()
            ->open()
            ->where('slug', $slug)
            ->with('brand:id,name')
            ->firstOrFail();

        $applicationsCount = $campaign->applications()->count();

        return response()->json([
            'id' => $campaign->id,
            'slug' => $campaign->slug,
            'title' => $campaign->title,
            'campaign_type' => $campaign->campaign_type,
            'description' => $campaign->description,
            'deliverables' => $campaign->deliverables,
            'budget' => $campaign->budget,
            'max_applications' => $campaign->max_applications,
            'starts_at' => $campaign->starts_at?->toIso8601String(),
            'ends_at' => $campaign->ends_at?->toIso8601String(),
            'targeting' => $campaign->targeting,
            'brand' => $campaign->brand ? ['id' => $campaign->brand->id, 'name' => $campaign->brand->name] : null,
            'applications_count' => $applicationsCount,
            'created_at' => $campaign->created_at->toIso8601String(),
        ]);
    }

    /**
     * Filter options for campaigns (campaign types, niches from open campaigns, etc.).
     */
    public function filters(): JsonResponse
    {
        $types = Campaign::open()->distinct()->pluck('campaign_type')->filter()->values();
        $campaigns = Campaign::open()->get(['targeting']);
        $niches = $campaigns->flatMap(fn ($c) => $c->targeting['niches'] ?? [])->unique()->filter()->sort()->values();
        $countries = $campaigns->flatMap(fn ($c) => $c->targeting['countries'] ?? [])->unique()->filter()->sort()->values();

        return response()->json([
            'campaign_types' => $types,
            'niches' => $niches,
            'countries' => $countries,
        ]);
    }
}
