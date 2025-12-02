<?php

namespace App\Observers;

use App\Models\Shop;

class ShopObserver
{
    public function creating(Shop $shop): void
    {
        // Set default currency if not provided
        if (empty($shop->currency)) {
            $shop->currency = 'XOF';
        }
    }
}

