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
        return response()->json([
            'user' => $user->only('id', 'name', 'email', 'role'),
            'profile' => $profile,
            'collaborations' => $collaborations,
            'has_paid' => $hasPaid,
        ]);
    }
}
