<?php

namespace App\Http\Controllers;

use App\Models\Studio;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService
    ) {}

    /**
     * Calculate price for a slot (no auth required for preview).
     */
    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'studio_id' => ['required', 'integer', 'exists:studios,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'string', 'date_format:H:i'],
            'end_time' => ['required', 'string', 'date_format:H:i', 'after:start_time'],
        ]);

        $studio = Studio::where('status', 'active')->findOrFail($request->studio_id);
        $totals = $this->bookingService->calculateTotal(
            $studio,
            $request->date,
            $request->start_time,
            $request->end_time
        );

        $available = $this->bookingService->validateAvailability(
            $studio,
            $request->date,
            $request->start_time,
            $request->end_time
        );

        return response()->json([
            'amount' => $totals['amount'],
            'platform_commission' => $totals['platform_commission'],
            'studio_amount' => $totals['studio_amount'],
            'hours' => $totals['hours'],
            'available' => $available,
        ]);
    }

    /**
     * Create booking (payment_pending). Frontend then calls payment gateway and on success calls confirm.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'studio_id' => ['required', 'integer', 'exists:studios,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'string', 'date_format:H:i'],
            'end_time' => ['required', 'string', 'date_format:H:i', 'after:start_time'],
            'cancellation_policy' => ['nullable', 'string', 'in:flexible,moderate,strict'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $studio = Studio::where('status', 'active')->findOrFail($request->studio_id);
        $policy = $request->input('cancellation_policy', $studio->cancellation_policy ?? 'moderate');

        $booking = $this->bookingService->createBooking(
            $request->user(),
            $studio,
            $request->date,
            $request->start_time,
            $request->end_time,
            $policy,
            $request->customer_notes
        );

        $booking->load(['studio:id,name,slug,city', 'studio.category:id,name,slug']);

        return response()->json([
            'message' => 'Booking created. Complete payment to confirm.',
            'booking' => [
                'id' => $booking->id,
                'studio' => [
                    'id' => $booking->studio->id,
                    'name' => $booking->studio->name,
                    'slug' => $booking->studio->slug,
                    'city' => $booking->studio->city,
                    'category' => $booking->studio->category?->name,
                ],
                'date' => $booking->date->format('Y-m-d'),
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'amount' => (float) $booking->amount,
                'platform_commission' => (float) $booking->platform_commission,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
            ],
        ], 201);
    }

    /**
     * Confirm booking after payment success (e.g. Razorpay/Stripe webhook or frontend callback).
     */
    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'booking_id' => ['required', 'integer', 'exists:bookings,id'],
            'gateway' => ['nullable', 'string', 'in:razorpay,stripe'],
            'gateway_ref' => ['nullable', 'string'],
        ]);

        $booking = \App\Models\Booking::where('user_id', $request->user()->id)->findOrFail($request->booking_id);

        $this->bookingService->confirmBooking(
            $booking,
            $request->input('gateway', 'razorpay'),
            $request->gateway_ref
        );

        $booking->refresh();

        return response()->json([
            'message' => 'Booking confirmed.',
            'booking' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'confirmed_at' => $booking->confirmed_at?->toIso8601String(),
            ],
        ]);
    }
}
