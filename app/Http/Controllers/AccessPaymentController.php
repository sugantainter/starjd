<?php

namespace App\Http\Controllers;

use App\Models\AccessPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccessPaymentController extends Controller
{
    public function plans(Request $request): JsonResponse
    {
        $role = $request->user()?->role;
        if (! in_array($role, ['creator', 'brand'], true)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $plans = config('plans.' . $role, []);
        return response()->json($plans);
    }

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user || ! in_array($user->role, ['creator', 'brand'], true)) {
            return response()->json(['has_paid' => false], 403);
        }
        return response()->json(['has_paid' => AccessPayment::hasPaidAccess($user)]);
    }

    public function pay(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $user = $request->user();
        if (! $user || ! in_array($user->role, ['creator', 'brand'], true)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $plans = config('plans.' . $user->role, []);
        $plan = collect($plans)->firstWhere('id', $request->plan_id);
        if (! $plan || (float) $plan['price'] !== (float) $request->amount) {
            return response()->json(['message' => 'Invalid plan'], 422);
        }

        $payment = AccessPayment::create([
            'user_id' => $user->id,
            'type' => $user->role,
            'amount' => $plan['price'],
            'status' => 'paid',
            'paid_at' => now(),
            'gateway_ref' => 'stub_' . uniqid(),
        ]);

        return response()->json([
            'message' => 'Payment successful',
            'payment' => $payment,
            'redirect' => $user->role === 'creator' ? url('/creator/dashboard') : url('/brand/dashboard'),
        ]);
    }
}
