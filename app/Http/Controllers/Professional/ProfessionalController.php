<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\ServiceListing;
use App\Models\ServiceBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfessionalController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->professionalProfile;
        
        $listingsCount = $user->serviceListings()->count();
        $activeOrdersCount = $user->serviceBookingsAsSeller()
            ->whereIn('status', [ServiceBooking::STATUS_PENDING, ServiceBooking::STATUS_IN_PROGRESS, ServiceBooking::STATUS_REVISION_REQUESTED])
            ->count();
        
        $totalEarnings = $user->serviceBookingsAsSeller()
            ->where('status', ServiceBooking::STATUS_COMPLETED)
            ->sum('amount');

        $recentOrders = $user->serviceBookingsAsSeller()
            ->with(['buyer:id,name', 'listing:id,title'])
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'user' => $user->only('id', 'name', 'email', 'role', 'state_id', 'city_id'),
            'profile' => $profile->load(['user' => function ($q) {
                $q->select('id', 'name', 'state_id', 'city_id')->with(['state:id,name', 'city:id,name']);
            }]),
            'stats' => [
                'listings_count' => $listingsCount,
                'active_orders_count' => $activeOrdersCount,
                'total_earnings' => $totalEarnings,
            ],
            'recent_orders' => $recentOrders,
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'tagline' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'languages' => 'nullable|array',
            'skills' => 'nullable|array',
            'education' => 'nullable|array',
            'certifications' => 'nullable|array',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        $profile = $user->professionalProfile ?: new \App\Models\ProfessionalProfile(['user_id' => $user->id]);
        $profile->fill($request->only(['tagline', 'bio', 'languages', 'skills', 'education', 'certifications']));
        $profile->save();

        $user->update($request->only(['state_id', 'city_id']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $profile
        ]);
    }

    public function listings(Request $request): JsonResponse
    {
        $listings = $request->user()->serviceListings()->with('serviceCategory')->latest()->get();
        return response()->json($listings);
    }

    public function categories(): JsonResponse
    {
        $categories = \App\Models\Service::active()->get(['id', 'name', 'slug']);
        return response()->json($categories);
    }

    public function orders(Request $request): JsonResponse
    {
        $orders = $request->user()->serviceBookingsAsSeller()->with(['buyer', 'listing'])->latest()->get();
        return response()->json($orders);
    }

    public function storeListing(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'id' => 'nullable|exists:service_listings,id',
            'service_id' => 'required|exists:services,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string',
            'pricing_tiers' => 'required|array',
            'gallery' => 'nullable|array',
            'faqs' => 'nullable|array',
            'metadata' => 'nullable|array',
            'tags' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if (isset($data['id'])) {
            $listing = $user->serviceListings()->findOrFail($data['id']);
            $listing->update($data);
        } else {
            $listing = $user->serviceListings()->create($data);
        }

        return response()->json([
            'message' => 'Service listing saved successfully',
            'listing' => $listing
        ]);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = $request->user();
        $path = $request->file('image')->store(
            'services/gallery/' . $user->id,
            'public'
        );

        return response()->json([
            'url' => Storage::url($path),
            'path' => $path
        ]);
    }
}
