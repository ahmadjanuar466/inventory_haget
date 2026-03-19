<?php

namespace App\Services\Usermanagement;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserImpl implements UserServices
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createNewUser(array $attributes): User
    {
        [$roles, $permissions, $payload] = $this->extractAssignableAttributes($attributes);

        if (isset($payload['password'])) {
            $payload['password'] = Hash::make($payload['password']);
        }

        $user = User::create($payload);

        $this->syncRolesAndPermissions($user, $roles, $permissions);

        return $user;
    }

    public function resetPassword(User $user, string $newPassword): bool
    {
        $user->password = Hash::make($newPassword);

        return $user->save();
    }

    public function deleteUser(User $user): bool
    {
        return (bool) $user->delete();
    }

    public function updateUsers(User $user, array $attributes): User
    {
        [$roles, $permissions, $payload] = $this->extractAssignableAttributes($attributes);

        if (isset($payload['password'])) {
            $payload['password'] = Hash::make($payload['password']);
        }

        $user->fill($payload);
        $user->save();

        $this->syncRolesAndPermissions($user, $roles, $permissions);

        return $user->refresh();
    }

    /**
     * Extract non-persisted attributes (roles & permissions) from the payload.
     *
     * @return array{0:?array,1:?array,2:array}
     */
    protected function extractAssignableAttributes(array $attributes): array
    {
        $roles = $attributes['roles'] ?? null;
        $permissions = $attributes['permissions'] ?? null;

        unset($attributes['roles'], $attributes['permissions']);

        return [$roles, $permissions, $attributes];
    }

    /**
     * Synchronize roles and permissions when explicitly provided.
     */
    protected function syncRolesAndPermissions(User $user, ?array $roles, ?array $permissions): void
    {
        if (! is_null($roles)) {
            $user->syncRoles($roles);
        }

        if (! is_null($permissions)) {
            $user->syncPermissions($permissions);
        }
    }
}
