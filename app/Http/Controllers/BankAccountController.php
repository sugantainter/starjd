<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $accounts = BankAccount::where('user_id', $request->user()->id)->get();
        return response()->json($accounts);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'account_holder_name' => 'required|string',
            'account_number' => 'required|string',
            'bank_name' => 'required|string',
            'ifsc_code' => 'required|string',
        ]);

        $account = BankAccount::create([
            'user_id' => $request->user()->id,
            'account_holder_name' => $request->account_holder_name,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
            'is_verified' => true, // Auto-verify for simplicity now, or add admin verification later
        ]);

        return response()->json($account);
    }

    public function destroy(Request $request, BankAccount $bankAccount): JsonResponse
    {
        if ($bankAccount->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $bankAccount->delete();
        return response()->json(['message' => 'Bank account removed']);
    }
}
