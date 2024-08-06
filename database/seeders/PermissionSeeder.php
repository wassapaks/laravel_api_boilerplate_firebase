<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'create-users']);
        Permission::create(['name' => 'delete-users']);
        Permission::create(['name' => 'edit-users']);
        Permission::create(['name' => 'view-users']);

        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'accounts-manager']);
        $role->givePermissionTo([
            'create-users', 
            'edit-users', 
            'view-users'
        ]);

        // or may be done by chaining
        $role = Role::create(['name' => 'operations-manager'])
            ->givePermissionTo(['view-users']);

        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());
    }
}