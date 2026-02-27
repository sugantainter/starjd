<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Coupon::orderBy('created_at', 'desc')->get();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:64', 'unique:coupons,code'],
            'description' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['required', 'string', 'in:percent,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active' => ['boolean'],
            'applicable_to' => ['nullable', 'string', 'in:access,collaboration,booking'],
        ]);
        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active', true);
        $coupon = Coupon::create($data);
        return response()->json($coupon, 201);
    }

    public function update(Request $request, Coupon $coupon): JsonResponse
    {
        $data = $request->validate([
            'code' => ['sometimes', 'string', 'max:64', 'unique:coupons,code,' . $coupon->id],
            'description' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['sometimes', 'string', 'in:percent,fixed'],
            'discount_value' => ['sometimes', 'numeric', 'min:0'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date'],
            'is_active' => ['boolean'],
            'applicable_to' => ['nullable', 'string', 'in:access,collaboration,booking'],
        ]);
        if (isset($data['code'])) {
            $data['code'] = strtoupper(trim($data['code']));
        }
        $coupon->update($data);
        return response()->json($coupon);
    }

    public function destroy(Coupon $coupon): JsonResponse
    {
        $coupon->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
