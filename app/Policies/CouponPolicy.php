<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;

class CouponPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'vendor';
    }

    public function view(User $user, Coupon $coupon): bool
    {
        return $user->role === 'admin' || $user->role === 'vendor';
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'vendor';
    }

    public function update(User $user, Coupon $coupon): bool
    {
        return $user->role === 'admin' || $user->role === 'vendor';
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->role === 'admin';
    }
}







