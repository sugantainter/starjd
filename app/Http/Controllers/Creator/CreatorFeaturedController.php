<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\CreatorProfile;
use App\Models\FeaturedPayment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreatorFeaturedController extends Controller
{
    public function plans(): JsonResponse
    {
        $plans = config('creator.featured_plans', []);
        return response()->json($plans);
    }

    public function status(Request $request): JsonResponse
    {
        $profile = $request->user()->creatorProfile;
        $featuredUntil = $profile?->featured_until;
        $isFeatured = $featuredUntil && Carbon::parse($featuredUntil)->isFuture();

        return response()->json([
            'is_featured' => $isFeatured,
            'featured_until' => $featuredUntil?->toIso8601String(),
        ]);
    }

    public function purchase(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $plans = config('creator.featured_plans', []);
        $plan = collect($plans)->firstWhere('id', $request->plan_id);

        if (! $plan || (float) $plan['price'] !== (float) $request->amount) {
            return response()->json(['message' => 'Invalid plan.'], 422);
        }

        $user = $request->user();
        $profile = $user->creatorProfile;
        if (! $profile) {
            $profile = $user->creatorProfile()->create([
                'slug' => \Illuminate\Support\Str::slug($user->name) . '-' . $user->id,
            ]);
        }

        $durationDays = (int) $plan['duration_days'];
        $now = Carbon::now();
        $currentEnd = $profile->featured_until && Carbon::parse($profile->featured_until)->isFuture()
            ? Carbon::parse($profile->featured_until)
            : $now;
        $newFeaturedUntil = $currentEnd->copy()->addDays($durationDays);

        FeaturedPayment::create([
            'creator_id' => $user->id,
            'plan_id' => $plan['id'],
            'amount' => $plan['price'],
            'duration_days' => $durationDays,
            'featured_until' => $newFeaturedUntil,
            'status' => 'paid',
            'paid_at' => $now,
            'gateway_ref' => 'stub_' . uniqid(),
        ]);

        $profile->update(['featured_until' => $newFeaturedUntil]);

        return response()->json([
            'message' => 'You are now featured!',
            'featured_until' => $newFeaturedUntil->toIso8601String(),
            'profile' => $profile->fresh(),
        ]);
    }
}
