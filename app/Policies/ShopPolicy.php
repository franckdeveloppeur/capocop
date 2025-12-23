<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;

class ShopPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Shop $shop): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'vendor' || $user->role === 'admin';
    }

    public function update(User $user, Shop $shop): bool
    {
        return $user->role === 'admin' || 
               ($user->role === 'vendor' && $shop->vendor->user_id === $user->id);
    }

    public function delete(User $user, Shop $shop): bool
    {
        return $user->role === 'admin' || 
               ($user->role === 'vendor' && $shop->vendor->user_id === $user->id);
    }
}





















