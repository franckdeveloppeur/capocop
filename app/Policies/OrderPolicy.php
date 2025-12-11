<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Order $order): bool
    {
        return $user->role === 'admin' || 
               ($user->role === 'vendor' && $order->shop && $order->shop->vendor->user_id === $user->id) ||
               $order->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'customer' || $user->role === 'admin';
    }

    public function update(User $user, Order $order): bool
    {
        return $user->role === 'admin' || 
               ($user->role === 'vendor' && $order->shop && $order->shop->vendor->user_id === $user->id);
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->role === 'admin';
    }
}













