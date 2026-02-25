<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreatorCampaignApplicationController extends Controller
{
    /**
     * List current user's campaign applications.
     */
    public function index(Request $request): JsonResponse
    {
        $applications = $request->user()
            ->campaignApplications()
            ->with(['campaign:id,title,budget,status,brand_id'])
            ->orderByDesc('created_at')
            ->paginate((int) $request->input('per_page', 15));

        return response()->json($applications);
    }

    /**
     * Apply to a campaign (creator).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
            'cover_message' => ['nullable', 'string', 'max:2000'],
            'quoted_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $campaign = Campaign::findOrFail($request->campaign_id);

        if ($campaign->status !== 'open') {
            return response()->json(['message' => 'This campaign is not accepting applications.'], 422);
        }

        $user = $request->user();
        $exists = CampaignApplication::where('campaign_id', $campaign->id)
            ->where('creator_id', $user->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'You have already applied to this campaign.'], 422);
        }

        if ($campaign->max_applications !== null) {
            $current = $campaign->applications()->count();
            if ($current >= $campaign->max_applications) {
                return response()->json(['message' => 'This campaign has reached the maximum number of applications.'], 422);
            }
        }

        $application = CampaignApplication::create([
            'campaign_id' => $campaign->id,
            'creator_id' => $user->id,
            'cover_message' => $request->cover_message,
            'quoted_amount' => $request->quoted_amount,
            'status' => 'pending',
        ]);

        $application->load('campaign:id,title,budget,status');
        return response()->json($application, 201);
    }
}
