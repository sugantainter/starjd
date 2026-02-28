<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public const TYPE_PERCENT = 'percent';
    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_amount',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
        'applicable_to',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_amount' => 'decimal:2',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Apply coupon to amount. Returns ['discount' => float, 'final_amount' => float] or error message.
     */
    public static function apply(string $code, float $amount, ?string $applicableTo = null): array
    {
        $coupon = self::where('code', strtoupper(trim($code)))
            ->where('is_active', true)
            ->first();

        if (! $coupon) {
            return ['error' => 'Invalid or inactive coupon code.'];
        }

        if ($coupon->valid_from && Carbon::now()->lt($coupon->valid_from)) {
            return ['error' => 'This coupon is not yet valid.'];
        }
        if ($coupon->valid_until && Carbon::now()->gt($coupon->valid_until)) {
            return ['error' => 'This coupon has expired.'];
        }
        if ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses) {
            return ['error' => 'This coupon has reached its usage limit.'];
        }
        if ($coupon->min_amount !== null && $amount < (float) $coupon->min_amount) {
            return ['error' => 'Minimum order amount for this coupon is ₹' . number_format((float) $coupon->min_amount, 2) . '.'];
        }
        if ($applicableTo && $coupon->applicable_to !== null && $coupon->applicable_to !== $applicableTo) {
            return ['error' => 'This coupon is not applicable to this type of purchase.'];
        }

        $discount = 0.0;
        if ($coupon->discount_type === self::TYPE_PERCENT) {
            $discount = round($amount * (float) $coupon->discount_value / 100, 2);
        } else {
            $discount = min((float) $coupon->discount_value, $amount);
        }
        $finalAmount = max(0, round($amount - $discount, 2));

        return [
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
            'discount' => $discount,
            'final_amount' => $finalAmount,
            'original_amount' => $amount,
        ];
    }

    public function incrementUsed(): void
    {
        $this->increment('used_count');
    }
}
