<?php

namespace App\Services\Usermanagement;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

interface RoleServices
{
    /**
     * Create a new role with the given attributes.
     */
    public function createRole(array $attributes): Role;

    /**
     * Update the provided role with new attributes.
     */
    public function updateRole(Role $role, array $attributes): Role;

    /**
     * Delete the given role.
     */
    public function deleteRole(Role $role): bool;

    /**
     * Retrieve all roles ordered by name.
     */
    public function allRoles(): Collection;
}
