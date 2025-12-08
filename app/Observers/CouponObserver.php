<?php

namespace App\Observers;

use App\Models\Coupon;

class CouponObserver
{
    public function creating(Coupon $coupon): void
    {
        // Ensure code is uppercase
        $coupon->code = strtoupper($coupon->code);
    }

    public function updated(Coupon $coupon): void
    {
        // Update used count when applied to order
        if ($coupon->wasChanged('used_count')) {
            // Additional logic if needed
        }
    }
}






