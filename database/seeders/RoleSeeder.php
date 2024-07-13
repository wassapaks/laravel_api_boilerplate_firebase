<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            [
                'name' => 'Super Admin',
                'guard_name' => 'SuperAdmin'
            ],
            [
                'name' => 'Secretary',
                'guard_name' => 'Secretary'
            ],
            [
                'name' => 'Accounts Manager',
                'guard_name' => 'AccountsManager'
            ],
        ]);
    }
}
