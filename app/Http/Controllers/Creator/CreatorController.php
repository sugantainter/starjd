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
        $hasPaid = AccessPayment::hasPaidAccess($user);

        return response()->json([
            'user' => $user->only('id', 'name', 'email', 'role'),
            'profile' => $profile,
            'packages' => $packages,
            'social_accounts' => $socialAccounts,
            'collaborations' => $collaborations,
            'has_paid' => $hasPaid,
        ]);
    }
}
