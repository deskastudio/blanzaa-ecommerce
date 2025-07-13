<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@electroshop.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@electroshop.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Test Customer
        User::create([
            'name' => 'John Customer',
            'email' => 'customer@electroshop.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'email_verified_at' => now(),
            'is_active' => true,
            'phone' => '081234567890',
            'address' => 'Jl. Test No. 123',
            'city' => 'Jakarta',
            'postal_code' => '12345',
            'province' => 'DKI Jakarta',
        ]);

        // Additional test customers
        User::factory(10)->create([
            'role' => 'customer',
        ]);
    }
}