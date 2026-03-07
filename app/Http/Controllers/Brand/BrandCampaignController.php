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

    /**
     * Show a single campaign with its applications (for the brand owner).
     */
    public function show(Request $request, Campaign $campaign): JsonResponse
    {
        if ($campaign->brand_id !== $request->user()->id) {
            abort(403);
        }

        $campaign->load([
            'applications' => function ($q) {
                $q->orderByDesc('created_at');
            },
            'applications.creator:id,name',
            'applications.creator.creatorProfile:id,user_id,slug',
        ]);

        $data = $campaign->only([
            'id', 'campaign_type', 'title', 'slug', 'description', 'deliverables',
            'budget', 'status', 'starts_at', 'ends_at', 'max_applications', 'targeting', 'created_at',
        ]);
        $data['applications'] = $campaign->applications->map(function ($app) {
            $creator = $app->creator;
            $profile = $creator?->creatorProfile;
            return [
                'id' => $app->id,
                'cover_message' => $app->cover_message,
                'quoted_amount' => $app->quoted_amount ? (float) $app->quoted_amount : null,
                'status' => $app->status,
                'brand_notes' => $app->brand_notes,
                'responded_at' => $app->responded_at?->toIso8601String(),
                'created_at' => $app->created_at->toIso8601String(),
                'creator' => $creator ? [
                    'id' => $creator->id,
                    'name' => $creator->name,
                    'profile_slug' => $profile?->slug,
                ] : null,
            ];
        });

        return response()->json($data);
    }

    /**
     * Update campaign (e.g. set status to open to accept applications).
     */
    public function update(Request $request, Campaign $campaign): JsonResponse
    {
        if ($campaign->brand_id !== $request->user()->id) {
            abort(403);
        }
        $validated = $request->validate([
            'status' => 'sometimes|string|in:draft,open,in_progress,completed,cancelled',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'sometimes|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);
        $campaign->update($validated);
        return response()->json(['message' => 'Campaign updated.', 'campaign' => $campaign->fresh()]);
    }
}
