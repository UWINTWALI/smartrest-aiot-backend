<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // First seed the permissions
        $this->call([
            PermissionSeeder::class,
        ]);

        // Then create the admin user
        $this->call([
            AdminUserSeeder::class,
        ]);

        // Create sample users for each role
        $roles = array_keys(Config::get('permissions.roles'));

        foreach ($roles as $role) {
            if ($role === 'admin')
                continue; // Skip admin as it's already created

            User::create([
                'user_id' => User::max('user_id') + 1,
                'first_name' => ucfirst($role),
                'last_name' => 'User',
                'email' => $role . '@example.com',
                'password_hash' => Hash::make('password'),
                'role' => $role,
                'phone' => '123456789' . array_search($role, $roles),
                'is_email_verified' => true,
                'permissions' => [] // Don't store role-based permissions in database
            ]);
        }

        $this->command->info('Sample users created successfully!');
        foreach ($roles as $role) {
            if ($role === 'admin')
                continue;
            $this->command->info(ucfirst($role) . ': ' . $role . '@example.com / password');
        }
    }
}
