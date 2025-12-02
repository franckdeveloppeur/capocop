<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function creating(User $user): void
    {
        // User creation logic
    }

    public function created(User $user): void
    {
        // Generate referral code when user is created
        $user->referralCodes()->create([
            'code' => strtoupper(substr(md5($user->email . time()), 0, 8)),
        ]);
    }

    public function updated(User $user): void
    {
        // Log user updates
    }
}

