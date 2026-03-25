<?php

namespace Database\Seeders;

use GuzzleHttp\Promise\Create;
use Illuminate\Container\Attributes\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleImplementationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //\


        $permission = Permission::create(['name' => 'delete']);
        $permission = Permission::create(['name' => 'edit']);
        $permission = Permission::create(['name' => 'insert']);
        $permission = Permission::create(['name' => 'persetujuan']);
        $permission = Permission::create(['name' => 'report']);
        $permission = Permission::create(['name' => 'view']);

        $adminRole = Role::create(['name' => 'administrator']);
        $adminRole->givePermissionTo(Permission::all());

        $oprRole = Role::create(['name' => 'Operator']);

        $oprRole->syncPermissions(['delete', 'edit', 'insert']);
    }
}
