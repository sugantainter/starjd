<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $type = $request->query('applicable_to'); // access, collaboration, booking, package
        $user = $request->user();
        $role = $user ? $user->role : null;

        $query = Coupon::where('is_active', true)
            ->where('is_public', true)
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', Carbon::now());
            });

        if ($type) {
            $query->where(function ($q) use ($type) {
                $q->whereNull('applicable_to')
                  ->orWhere('applicable_to', $type);
            });
        }

        if ($role) {
            $query->where(function ($q) use ($role) {
                $q->where(function($sq) use ($role) {
                    $sq->whereNull('applicable_roles')
                      ->orWhereRaw("applicable_roles LIKE ?", ["%$role%"]);
                });
            });
        }

        $coupons = $query->orderBy('discount_value', 'desc')->get();

        return response()->json($coupons);
    }
}
