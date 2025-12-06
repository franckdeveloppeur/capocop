<?php

namespace App\Policies;

use App\Models\Vendor;
use App\Models\User;

class VendorPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Vendor $vendor): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'customer' || $user->role === 'admin';
    }

    public function update(User $user, Vendor $vendor): bool
    {
        return $user->role === 'admin' || $vendor->user_id === $user->id;
    }

    public function delete(User $user, Vendor $vendor): bool
    {
        return $user->role === 'admin';
    }
}





