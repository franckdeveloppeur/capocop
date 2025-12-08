<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        // Get capocop vendor
        $vendor = Vendor::where('slug', 'capocop')->first();

        if ($vendor) {
            Shop::create([
                'vendor_id' => $vendor->id,
                'name' => 'Capocop',
                'address_id' => null,
                'currency' => 'XOF',
                'settings' => [
                    'description' => 'Boutique Capocop - Votre partenaire de confiance',
                    'logo' => null,
                    'banner' => null,
                ],
            ]);
        }
    }
}
