<?php

namespace App\Policies;

use App\Models\Studio;
use App\Models\User;

class StudioPolicy
{
    /**
     * Determine whether the user can view the studio (owner or public active).
     */
    public function view(?User $user, Studio $studio): bool
    {
        if ($studio->status === 'active') {
            return true;
        }
        return $user && (int) $user->id === (int) $studio->user_id;
    }

    /**
     * Determine whether the user can update the studio.
     */
    public function update(User $user, Studio $studio): bool
    {
        return (int) $user->id === (int) $studio->user_id;
    }

    /**
     * Determine whether the user can delete the studio.
     */
    public function delete(User $user, Studio $studio): bool
    {
        return (int) $user->id === (int) $studio->user_id;
    }

    /**
     * Determine whether the user can create studios (must be studio_owner role).
     */
    public function create(User $user): bool
    {
        return $user->hasRole('studio_owner');
    }
}
