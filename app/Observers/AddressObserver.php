<?php

namespace App\Observers;

use App\Models\Address;

class AddressObserver
{
    public function creating(Address $address): void
    {
        // Set default label if not provided
        if (empty($address->label)) {
            $address->label = 'Home';
        }
    }
}







