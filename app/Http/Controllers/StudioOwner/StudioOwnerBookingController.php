<?php

namespace App\Http\Controllers\StudioOwner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudioOwnerBookingController extends Controller
{
    /**
     * GET /api/studio-owner/bookings — List bookings for owner's studios.
     */
    public function index(Request $request): JsonResponse
    {
        $studioIds = $request->user()->studios()->pluck('id');

        $bookings = Booking::whereIn('studio_id', $studioIds)
            ->with(['user:id,name', 'studio:id,name,slug,city'])
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->paginate((int) $request->input('per_page', 15));

        $bookings->getCollection()->transform(function (Booking $b) {
            return [
                'id' => $b->id,
                'date' => $b->date->format('Y-m-d'),
                'start_time' => $b->start_time,
                'end_time' => $b->end_time,
                'amount' => (float) $b->amount,
                'studio_amount' => (float) $b->studio_amount,
                'status' => $b->status,
                'payment_status' => $b->payment_status,
                'customer' => $b->user ? ['id' => $b->user->id, 'name' => $b->user->name] : null,
                'studio' => $b->studio ? ['id' => $b->studio->id, 'name' => $b->studio->name, 'slug' => $b->studio->slug, 'city' => $b->studio->city] : null,
                'confirmed_at' => $b->confirmed_at?->toIso8601String(),
                'created_at' => $b->created_at->toIso8601String(),
            ];
        });

        return response()->json($bookings);
    }
}
