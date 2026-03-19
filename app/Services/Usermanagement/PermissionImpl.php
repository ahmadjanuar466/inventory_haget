<?php

namespace App\Services\Usermanagement;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

class PermissionImpl implements PermissionServices
{
    public function createPermission(array $attributes): Permission
    {
        return Permission::create($this->preparePayload($attributes));
    }

    public function updatePermission(Permission $permission, array $attributes): Permission
    {
        $permission->fill($this->preparePayload($attributes));
        $permission->save();

        return $permission->refresh();
    }

    public function deletePermission(Permission $permission): bool
    {
        return (bool) $permission->delete();
    }

    public function allPermissions(): Collection
    {
        return Permission::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * Normalize incoming attributes before persisting.
     */
    protected function preparePayload(array $attributes): array
    {
        $payload = Arr::only($attributes, ['name', 'guard_name']);
        $payload['guard_name'] = $payload['guard_name'] ?? 'web';

        return $payload;
    }
}
