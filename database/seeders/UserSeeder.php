<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@capocop.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create vendor user
        $vendorUser = User::create([
            'name' => 'Vendor',
            'email' => 'vendor@capocop.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create customer users
        User::factory(10)->create([
            'role' => 'customer',
        ]);
    }
}


















