<?php

namespace App\Http\Controllers;

use App\Models\ServiceListing;
use App\Models\ProfessionalProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionalPublicController extends Controller
{
    /**
     * List all active professional service gig listings.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ServiceListing::query()
            ->with(['user', 'serviceCategory'])
            ->where('is_active', true);

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('category')) {
            $query->whereHas('serviceCategory', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"));
            });
        }

        $perPage = (int) $request->input('per_page', 12);
        $listings = $query->latest()->paginate($perPage);

        return response()->json($listings);
    }

    /**
     * Show a single professional service gig listing.
     */
    public function show(string $slug): JsonResponse
    {
        $listing = ServiceListing::with([
            'user.professionalProfile',
            'serviceCategory',
        ])
        ->where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

        return response()->json($listing);
    }

    /**
     * Show a professional's public profile and their listings.
     */
    public function professionalProfile(string $userId): JsonResponse
    {
        $user = User::with(['professionalProfile', 'serviceListings' => fn($q) => $q->where('is_active', true)])
            ->findOrFail($userId);

        return response()->json([
            'user' => $user->only('id', 'name', 'avatar_url'),
            'profile' => $user->professionalProfile,
            'listings' => $user->serviceListings,
        ]);
    }
}
