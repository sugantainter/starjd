<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\AccessPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->brandProfile;
        $collaborations = $user->collaborationsAsBrand()->with(['creator', 'package'])->latest()->limit(10)->get();
        $hasPaid = AccessPayment::hasPaidAccess($user);

        $campaigns = $user->campaignsAsBrand()
            ->withCount(['applications as applications_count'])
            ->withCount(['applications as applications_pending_count' => function ($q) {
                $q->where('status', 'pending');
            }])
            ->latest()
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'campaign_type' => $c->campaign_type,
                    'title' => $c->title,
                    'slug' => $c->slug,
                    'status' => $c->status,
                    'budget' => $c->budget,
                    'max_applications' => $c->max_applications,
                    'starts_at' => $c->starts_at?->toIso8601String(),
                    'ends_at' => $c->ends_at?->toIso8601String(),
                    'created_at' => $c->created_at->toIso8601String(),
                    'applications_count' => (int) ($c->applications_count ?? 0),
                    'applications_pending_count' => (int) ($c->applications_pending_count ?? 0),
                ];
            });

        $campaignStats = [
            'total' => $campaigns->count(),
            'open' => $campaigns->where('status', 'open')->count(),
            'draft' => $campaigns->where('status', 'draft')->count(),
            'completed' => $campaigns->where('status', 'completed')->count(),
            'total_applications' => $campaigns->sum('applications_count'),
            'pending_applications' => $campaigns->sum('applications_pending_count'),
        ];

        return response()->json([
            'user' => $user->only('id', 'name', 'email', 'role'),
            'profile' => $profile,
            'collaborations' => $collaborations,
            'has_paid' => $hasPaid,
            'campaigns' => $campaigns,
            'campaign_stats' => $campaignStats,
        ]);
    }
}
