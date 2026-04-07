<?php

namespace App\Services;

use App\Models\User;
use App\Models\AccessPayment;
use App\Models\CreatorProfile;
use App\Models\BrandProfile;
use App\Models\Studio;
use App\Models\SocialAccount;
use App\Models\Coupon;

class OnboardingService
{
    /**
     * Determine if the user has completed payment.
     */
    public function hasPaidAccess(User $user): bool
    {
        return AccessPayment::hasPaidAccess($user);
    }

    /**
     * Determine if the user's role-specific profile is complete.
     */
    public function isProfileComplete(User $user): bool
    {
        $role = $user->primaryRole()?->slug ?? $user->role;
        switch ($role) {
            case 'creator':
                $profile = $user->creatorProfile;
                return $profile && $profile->category && $profile->bio && $profile->gender;
            case 'brand':
                $profile = $user->brandProfile;
                return $profile && $profile->company_name && $profile->website && $profile->bio;
            case 'studio_owner':
                $studio = $user->studios()->first();
                return $studio && $studio->name && $studio->description;
            default:
                return true; // other roles may not require profile
        }
    }

    /**
     * Determine if the creator has any connected social accounts.
     */
    public function hasSocialConnected(User $user): bool
    {
        return $user->socialAccounts()->where('is_connected', true)->exists();
    }

    /**
     * Get an applicable coupon for the user (first active public coupon).
     */
    public function getApplicableCoupon(User $user): ?string
    {
        $coupon = Coupon::where('is_active', true)
            ->where('is_public', true)
            ->orderByDesc('created_at')
            ->first();
        return $coupon?->code;
    }
}
?>
