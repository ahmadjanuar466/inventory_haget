<?php

namespace App\Services\Usermanagement;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleImpl implements RoleServices
{
    public function createRole(array $attributes): Role
    {
        [$payload, $permissions] = $this->separatePermissions($attributes);

        return DB::transaction(function () use ($payload, $permissions) {
            $role = Role::create($payload);
            $this->syncPermissions($role, $permissions);

            return $role;
        });
    }

    public function updateRole(Role $role, array $attributes): Role
    {
        [$payload, $permissions] = $this->separatePermissions($attributes);

        return DB::transaction(function () use ($role, $payload, $permissions) {
            $role->fill($payload);
            $role->save();

            $this->syncPermissions($role, $permissions);

            return $role->refresh();
        });
    }

    public function deleteRole(Role $role): bool
    {
        return (bool) $role->delete();
    }

    public function allRoles(): Collection
    {
        return Role::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * Prepare payload and permissions array from attributes.
     */
    protected function separatePermissions(array $attributes): array
    {
        $payload = Arr::only($attributes, ['name', 'guard_name']);
        $payload['guard_name'] = $payload['guard_name'] ?? 'web';

        $permissions = $attributes['permissions'] ?? null;

        return [$payload, $permissions];
    }

    /**
     * Synchronize permissions when provided.
     */
    protected function syncPermissions(Role $role, ?array $permissions): void
    {
        if (is_null($permissions)) {
            return;
        }

        if ($permissions === []) {
            $role->syncPermissions([]);

            return;
        }

        $permissionNames = Permission::query()
            ->whereIn('name', $permissions)
            ->pluck('name')
            ->all();

        $role->syncPermissions($permissionNames);
    }
}
