<?php

namespace App\Services\Usermanagement;

use App\Models\User;
use App\Models\UserProfile;

interface UserProfileServices
{
    /**
     * Persist a user profile with the given attributes.
     */
    public function createProfile(array $attributes): UserProfile;

    /**
     * Update the provided profile.
     */
    public function updateProfile(UserProfile $profile, array $attributes): UserProfile;

    /**
     * Link the profile to the given user account.
     */
    public function attachUser(UserProfile $profile, User $user): UserProfile;
}
