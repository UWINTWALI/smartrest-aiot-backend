<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::create([
            'user_id' => 1,
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '1234567890',
            'is_email_verified' => true,
            'permissions' => [] // Don't store role-based permissions in database
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: admin123');
    }
}