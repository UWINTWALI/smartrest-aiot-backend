<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'email' => 'admin@smartrest.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone' => '+1234567890',
            'is_email_verified' => true,
        ]);

        User::create([
            'email' => 'customer@smartrest.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'first_name' => 'Customer',
            'last_name' => 'User',
            'phone' => '+0987654321',
            'is_email_verified' => true,
        ]);
    }
}
