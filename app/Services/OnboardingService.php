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
     * Roles that should receive onboarding reminders.
     */
    private const ONBOARDING_ROLES = ['creator', 'brand', 'studio_owner', 'professional', 'agency'];

    public function roleSlug(User $user): ?string
    {
        return $user->primaryRole()?->slug ?? $user->role;
    }

    public function shouldSendOnboardingReminder(User $user): bool
    {
        $role = $this->roleSlug($user);
        return $role !== null && in_array($role, self::ONBOARDING_ROLES, true);
    }

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
        $role = $this->roleSlug($user);
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
            case 'professional':
                $profile = $user->professionalProfile;
                return $profile && $profile->headline && $profile->bio;
            case 'agency':
                return true;
            default:
                return true; // other roles may not require profile
        }
    }

    /**
     * Determine if the creator has any connected social accounts.
     */
    public function hasSocialConnected(User $user): bool
    {
        $role = $this->roleSlug($user);

        if ($role === 'creator') {
            return $user->socialAccounts()->where('is_connected', true)->exists();
        }

        if ($role === 'brand') {
            return $user->campaignsAsBrand()->exists();
        }

        if ($role === 'studio_owner') {
            return $user->studios()->whereHas('availabilitySlots')->exists();
        }

        if ($role === 'professional') {
            return $user->serviceListings()->exists();
        }

        return true;
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
