<?php

namespace App\Services;

use Spatie\Permission\Models\Role;

class RoleService
{
    public function roles()
    {
        // Retrieve all roles
        $roles = Role::all();

        // Do something with the roles

        return $roles;
    }
}