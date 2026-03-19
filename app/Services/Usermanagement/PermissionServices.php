<?php

namespace App\Services\Usermanagement;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

interface PermissionServices
{
    /**
     * Create a new permission with the provided attributes.
     */
    public function createPermission(array $attributes): Permission;

    /**
     * Update an existing permission with the provided attributes.
     */
    public function updatePermission(Permission $permission, array $attributes): Permission;

    /**
     * Delete the given permission.
     */
    public function deletePermission(Permission $permission): bool;

    /**
     * Retrieve all permissions ordered by name.
     */
    public function allPermissions(): Collection;
}
