<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Studio;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BookingService
{
    /**
     * Validate that the slot is available (no overlapping confirmed/paid booking).
     */
    public function validateAvailability(Studio $studio, string $date, string $startTime, string $endTime, ?int $excludeBookingId = null): bool
    {
        $overlap = Booking::where('studio_id', $studio->id)
            ->where('date', $date)
            ->whereNotIn('status', [Booking::STATUS_CANCELLED, Booking::STATUS_REFUNDED])
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '<', $endTime)->where('end_time', '>', $startTime);
                });
            })
            ->exists();

        return ! $overlap;
    }

    /**
     * Calculate total amount and platform commission for a booking.
     */
    public function calculateTotal(Studio $studio, string $date, string $startTime, string $endTime): array
    {
        $start = \Carbon\Carbon::parse($date . ' ' . $startTime);
        $end = \Carbon\Carbon::parse($date . ' ' . $endTime);
        $hours = max(0.5, round($end->diffInMinutes($start) / 60, 2));

        $pricePerHour = $studio->price_per_hour ?? $studio->price_per_day / 24;
        $pricePerDay = $studio->price_per_day ?? $studio->price_per_hour * 24;

        $amount = 0;
        if ($studio->price_per_hour) {
            $amount = round($pricePerHour * $hours, 2);
        } elseif ($studio->price_per_day) {
            $days = ceil($hours / 24);
            $amount = round($pricePerDay * $days, 2);
        }

        $commissionPercent = config('studio.booking_commission_percent', 10);
        $platformCommission = round($amount * ($commissionPercent / 100), 2);
        $studioAmount = round($amount - $platformCommission, 2);

        return [
            'amount' => $amount,
            'platform_commission' => $platformCommission,
            'studio_amount' => $studioAmount,
            'hours' => $hours,
        ];
    }

    /**
     * Create booking (payment_pending), optionally create transaction (hold in escrow).
     * Call confirmBooking() after payment success.
     */
    /**
     * @param  array{amount: float, platform_commission: float, studio_amount: float, hours: float}|null  $totalsOverride
     */
    public function createBooking(
        User $customer,
        Studio $studio,
        string $date,
        string $startTime,
        string $endTime,
        string $cancellationPolicy = Booking::CANCELLATION_MODERATE,
        ?string $customerNotes = null,
        ?array $totalsOverride = null,
        ?int $couponId = null
    ): Booking {
        if (! $this->validateAvailability($studio, $date, $startTime, $endTime)) {
            throw new InvalidArgumentException('The selected slot is no longer available.');
        }

        $totals = $totalsOverride ?? $this->calculateTotal($studio, $date, $startTime, $endTime);

        return DB::transaction(function () use ($customer, $studio, $date, $startTime, $endTime, $cancellationPolicy, $customerNotes, $totals, $couponId) {
            $booking = Booking::create([
                'user_id' => $customer->id,
                'studio_id' => $studio->id,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'amount' => $totals['amount'],
                'platform_commission' => $totals['platform_commission'],
                'studio_amount' => $totals['studio_amount'],
                'payment_status' => 'pending',
                'status' => Booking::STATUS_PAYMENT_PENDING,
                'cancellation_policy' => $cancellationPolicy,
                'customer_notes' => $customerNotes,
                'coupon_id' => $couponId,
            ]);

            return $booking;
        });
    }

    /**
     * Confirm booking after payment success: update status, create transaction (escrow), update studio wallet/owner.
     */
    public function confirmBooking(Booking $booking, string $gateway = 'razorpay', ?string $gatewayRef = null): void
    {
        if ($booking->status !== Booking::STATUS_PAYMENT_PENDING && $booking->payment_status !== 'pending') {
            throw new InvalidArgumentException('Booking is not in payment_pending state.');
        }

        DB::transaction(function () use ($booking, $gateway, $gatewayRef) {
            $booking->update([
                'status' => Booking::STATUS_CONFIRMED,
                'payment_status' => 'paid',
                'gateway_ref' => $gatewayRef,
                'confirmed_at' => now(),
            ]);

            $studioOwner = $booking->studio->user;
            $wallet = Wallet::forUser($studioOwner);

            Transaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'booking',
                'amount' => $booking->amount,
                'platform_amount' => $booking->platform_commission,
                'studio_amount' => $booking->studio_amount,
                'creator_amount' => 0,
                'agency_amount' => 0,
                'tax' => 0,
                'payment_status' => 'paid',
                'gateway' => $gateway,
                'gateway_ref' => $gatewayRef,
                'payable_type' => Booking::class,
                'payable_id' => $booking->id,
                'user_id' => $booking->user_id,
            ]);

            $wallet->increment('balance', (float) $booking->studio_amount);
        });
    }

    /**
     * Cancel booking. Refund logic can be added based on cancellation_policy.
     */
    public function cancelBooking(Booking $booking, string $reason = 'cancelled'): void
    {
        if (in_array($booking->status, [Booking::STATUS_CANCELLED, Booking::STATUS_REFUNDED], true)) {
            throw new InvalidArgumentException('Booking is already cancelled or refunded.');
        }

        DB::transaction(function () use ($booking, $reason) {
            $booking->update([
                'status' => Booking::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);

            if ($booking->payment_status === 'paid') {
                $studioOwner = $booking->studio->user;
                $wallet = Wallet::forUser($studioOwner);
                $wallet->decrement('balance', (float) $booking->studio_amount);
                Transaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'refund',
                    'amount' => -$booking->studio_amount,
                    'platform_amount' => 0,
                    'studio_amount' => -$booking->studio_amount,
                    'payment_status' => 'paid',
                    'payable_type' => Booking::class,
                    'payable_id' => $booking->id,
                    'user_id' => $booking->user_id,
                ]);
            }
        });
    }
}
