<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\AccessPayment;
use App\Models\Collaboration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreatorController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->creatorProfile;
        $packages = $user->packages()->where('is_active', true)->orderBy('sort_order')->get();
        $socialAccounts = $user->socialAccounts;
        $collaborations = $user->collaborationsAsCreator()->with(['brand', 'package'])->latest()->limit(10)->get();
        $campaignApplications = $user->campaignApplications()
            ->with(['campaign:id,title,slug,status,campaign_type'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'status' => $app->status,
                    'cover_message' => $app->cover_message,
                    'quoted_amount' => $app->quoted_amount ? (float) $app->quoted_amount : null,
                    'responded_at' => $app->responded_at?->toIso8601String(),
                    'created_at' => $app->created_at->toIso8601String(),
                    'campaign' => $app->campaign ? [
                        'id' => $app->campaign->id,
                        'title' => $app->campaign->title,
                        'slug' => $app->campaign->slug,
                        'status' => $app->campaign->status,
                        'campaign_type' => $app->campaign->campaign_type,
                    ] : null,
                ];
            });
        $hasPaid = AccessPayment::hasPaidAccess($user);

        return response()->json([
            'user' => $user->only('id', 'name', 'email', 'role'),
            'profile' => $profile,
            'packages' => $packages,
            'social_accounts' => $socialAccounts,
            'collaborations' => $collaborations,
            'campaign_applications' => $campaignApplications,
            'has_paid' => $hasPaid,
        ]);
    }
}
