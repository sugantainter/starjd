<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandCampaignController extends Controller
{
    /**
     * List campaigns for the authenticated brand.
     */
    public function index(Request $request): JsonResponse
    {
        $campaigns = $request->user()
            ->campaignsAsBrand()
            ->latest()
            ->get(['id', 'campaign_type', 'title', 'slug', 'status', 'targeting', 'created_at']);

        return response()->json(['campaigns' => $campaigns]);
    }

    /**
     * Store a new campaign (draft with type and targeting).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campaign_type' => 'required|string|in:instagram,tiktok,ugc,youtube',
            'influencer_count' => 'required|integer|min:1|max:500',
            'niches' => 'nullable|array',
            'niches.*' => 'string|max:100',
            'follower_ranges' => 'nullable|array',
            'follower_ranges.*' => 'string|max:50',
            'countries' => 'nullable|array',
            'countries.*' => 'string|max:100',
            'cities' => 'nullable|array',
            'cities.*' => 'string|max:100',
            'genders' => 'nullable|array',
            'genders.*' => 'string|max:50',
            'ages' => 'nullable|array',
            'ages.*' => 'string|max:50',
            'ethnicities' => 'nullable|array',
            'ethnicities.*' => 'string|max:100',
            'languages' => 'nullable|array',
            'languages.*' => 'string|max:100',
        ]);

        $typeLabel = match ($validated['campaign_type']) {
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'ugc' => 'User Generated Content',
            'youtube' => 'YouTube',
            default => 'Campaign',
        };

        $campaign = $request->user()->campaignsAsBrand()->create([
            'campaign_type' => $validated['campaign_type'],
            'title' => $typeLabel . ' Campaign',
            'slug' => null,
            'status' => 'draft',
            'max_applications' => $validated['influencer_count'],
            'budget' => 0,
            'targeting' => [
                'niches' => $validated['niches'] ?? [],
                'follower_ranges' => $validated['follower_ranges'] ?? [],
                'countries' => $validated['countries'] ?? [],
                'cities' => $validated['cities'] ?? [],
                'genders' => $validated['genders'] ?? [],
                'ages' => $validated['ages'] ?? [],
                'ethnicities' => $validated['ethnicities'] ?? [],
                'languages' => $validated['languages'] ?? [],
            ],
        ]);

        $campaign->update([
            'slug' => Str::slug($typeLabel . '-campaign-' . $campaign->id),
        ]);

        return response()->json([
            'message' => 'Campaign created successfully.',
            'campaign' => $campaign->only('id', 'campaign_type', 'title', 'slug', 'status', 'targeting', 'max_applications'),
        ], 201);
    }
}
