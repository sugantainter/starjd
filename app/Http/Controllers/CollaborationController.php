<?php

namespace App\Http\Controllers;

use App\Models\Collaboration;
use App\Models\CreatorProfile;
use App\Models\PlatformSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollaborationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->role === 'brand') {
            $items = $user->collaborationsAsBrand()->with(['creator', 'creator.creatorProfile', 'package'])->latest()->get();
        } elseif ($user->role === 'creator') {
            $items = $user->collaborationsAsCreator()->with(['brand', 'brand.brandProfile', 'package'])->latest()->get();
        } else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'creator_id' => ['required', 'exists:users,id'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'brand_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->user()->role !== 'brand') {
            return response()->json(['message' => 'Only brands can create collaborations'], 403);
        }

        $creator = \App\Models\User::findOrFail($request->creator_id);
        if ($creator->role !== 'creator') {
            return response()->json(['message' => 'Invalid creator'], 422);
        }

        $feePercent = (float) (PlatformSetting::get('platform_fee_percent', 10));
        $amount = (float) $request->amount;
        $platformFee = round($amount * $feePercent / 100, 2);
        $creatorAmount = $amount - $platformFee;

        $collab = Collaboration::create([
            'brand_id' => $request->user()->id,
            'creator_id' => $request->creator_id,
            'package_id' => $request->package_id,
            'amount' => $amount,
            'platform_fee' => $platformFee,
            'creator_amount' => $creatorAmount,
            'status' => 'pending',
            'brand_notes' => $request->brand_notes,
        ]);

        $collab->load(['creator', 'creator.creatorProfile', 'package']);
        return response()->json($collab, 201);
    }

    public function accept(Request $request, Collaboration $collaboration): JsonResponse
    {
        if ($collaboration->creator_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($collaboration->status !== 'pending') {
            return response()->json(['message' => 'Collaboration cannot be updated'], 422);
        }
        $collaboration->update(['status' => 'accepted']);
        $collaboration->load(['brand', 'package']);
        return response()->json($collaboration);
    }

    public function pay(Request $request, Collaboration $collaboration): JsonResponse
    {
        if ($collaboration->brand_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if (! in_array($collaboration->status, ['pending', 'accepted'], true)) {
            return response()->json(['message' => 'Collaboration cannot be paid'], 422);
        }
        // Stub: mark as paid (replace with Stripe/Connect later)
        $collaboration->update(['status' => 'paid', 'paid_at' => now()]);
        $collaboration->load(['creator', 'package']);
        return response()->json($collaboration);
    }
}
