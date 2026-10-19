<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'description' => 'Full system access',
            ],
            [
                'name' => 'Sales User',
                'description' => 'Access to sales and payment modules',
            ],
            [
                'name' => 'Inventory User',
                'description' => 'Access to inventory and purchase modules',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
