<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\CampaignApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandCampaignApplicationController extends Controller
{
    /**
     * Approve or reject a campaign application (brand owner only).
     */
    public function update(Request $request, CampaignApplication $campaign_application): JsonResponse
    {
        $campaign = $campaign_application->campaign;
        if (! $campaign || $campaign->brand_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'status' => ['required', 'string', 'in:approved,rejected'],
        ]);

        $campaign_application->update([
            'status' => $request->status,
            'responded_at' => now(),
        ]);

        return response()->json([
            'message' => $request->status === 'approved' ? 'Application approved.' : 'Application rejected.',
            'application' => $campaign_application->fresh(),
        ]);
    }
}
