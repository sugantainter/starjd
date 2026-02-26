<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreatorWalletController extends Controller
{
    /**
     * Wallet balance for current user (creator). Creates wallet if missing.
     */
    public function show(Request $request): JsonResponse
    {
        $wallet = Wallet::forUser($request->user());

        return response()->json([
            'balance' => (float) $wallet->balance,
            'currency' => $wallet->currency,
            'is_active' => $wallet->is_active,
        ]);
    }

    /**
     * List transactions for current user's wallet (creator).
     */
    public function transactions(Request $request): JsonResponse
    {
        $wallet = Wallet::forUser($request->user());

        $transactions = $wallet->transactions()
            ->orderByDesc('created_at')
            ->paginate((int) $request->input('per_page', 15));

        $transactions->getCollection()->transform(function ($t) {
            return [
                'id' => $t->id,
                'type' => $t->type,
                'amount' => (float) $t->amount,
                'creator_amount' => (float) $t->creator_amount,
                'payment_status' => $t->payment_status,
                'payable_type' => $t->payable_type,
                'payable_id' => $t->payable_id,
                'created_at' => $t->created_at->toIso8601String(),
            ];
        });

        return response()->json($transactions);
    }
}
