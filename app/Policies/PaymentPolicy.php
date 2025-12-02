<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'vendor';
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->role === 'admin' || 
               ($user->role === 'vendor' && $payment->order->shop && $payment->order->shop->vendor->user_id === $user->id) ||
               ($payment->order->user_id === $user->id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->role === 'admin';
    }
}

