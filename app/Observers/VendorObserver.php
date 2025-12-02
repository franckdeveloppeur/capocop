<?php

namespace App\Observers;

use App\Models\Vendor;

class VendorObserver
{
    public function creating(Vendor $vendor): void
    {
        // Ensure slug is unique
        if (Vendor::where('slug', $vendor->slug)->exists()) {
            $vendor->slug = $vendor->slug . '-' . time();
        }
    }

    public function created(Vendor $vendor): void
    {
        // Create default shop for vendor
        $vendor->shops()->create([
            'name' => $vendor->name . ' Shop',
            'currency' => 'XOF',
        ]);
    }
}

