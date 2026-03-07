<?php

namespace App\Http\Controllers\StudioOwner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Studio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudioOwnerController extends Controller
{
    /**
     * Dashboard overview for studio owner.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $studioIds = $user->studios()->pluck('id');

        $activeStudios = Studio::where('user_id', $user->id)->where('status', 'active')->count();
        $totalStudios = Studio::where('user_id', $user->id)->count();

        $allBookings = Booking::whereIn('studio_id', $studioIds);
        $totalBookings = (clone $allBookings)->count();
        $pendingBookings = (clone $allBookings)->where('status', 'pending')->count();
        
        // Sum of studio_amount for paid/successful bookings
        $totalEarnings = (clone $allBookings)
            ->where('payment_status', 'paid') // Adjust based on your payment_status values
            ->sum('studio_amount');

        $recentBookings = (clone $allBookings)
            ->with(['user:id,name', 'studio:id,name,slug'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'amount' => (float) $b->amount,
                    'status' => $b->status,
                    'customer' => $b->user?->name,
                    'studio' => $b->studio?->name,
                    'date' => $b->date->format('Y-m-d'),
                ];
            });

        return response()->json([
            'user' => $user->only('id', 'name', 'email'),
            'stats' => [
                'total_studios' => $totalStudios,
                'active_studios' => $activeStudios,
                'total_bookings' => $totalBookings,
                'pending_bookings' => $pendingBookings,
                'total_earnings' => (float) $totalEarnings,
            ],
            'recent_bookings' => $recentBookings,
        ]);
    }
}
