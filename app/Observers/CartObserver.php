<?php

namespace App\Observers;

use App\Models\Cart;

class CartObserver
{
    public function creating(Cart $cart): void
    {
        // Set default currency if not provided
        if (empty($cart->currency)) {
            $cart->currency = 'XOF';
        }
    }
}







