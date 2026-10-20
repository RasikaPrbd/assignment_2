<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure the required roles exist
        $this->call(RoleSeeder::class);

        $adminRole = Role::where('name', 'Admin')->firstOrFail();

        // Create the initial admin account
        User::firstOrCreate(
            ['email' => 'admin@123.com'],
            [
                'name' => 'System Admin',
                'password' => 'Admin@12345',
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );
    }
}
