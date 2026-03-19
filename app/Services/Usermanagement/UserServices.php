<?php

namespace App\Services\Usermanagement;

use App\Models\User;

interface UserServices
{
    /**
     * Persist a new user using the provided attributes.
     */
    public function createNewUser(array $attributes): User;

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(User $user, string $newPassword): bool;

    /**
     * Delete the given user.
     */
    public function deleteUser(User $user): bool;

    /**
     * Update the user with new attributes.
     */
    public function updateUsers(User $user, array $attributes): User;
}
