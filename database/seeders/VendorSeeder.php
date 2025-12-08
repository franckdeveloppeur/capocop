<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        // Get vendor user
        $vendorUser = User::where('email', 'vendor@capocop.com')->first();

        if ($vendorUser) {
            Vendor::create([
                'user_id' => $vendorUser->id,
                'name' => 'Capocop',
                'slug' => 'capocop',
                'description' => 'Boutique officielle Capocop - Vente en ligne de qualité',
                'is_verified' => true,
            ]);
        }
    }
}
