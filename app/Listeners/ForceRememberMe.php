<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class ForceRememberMe
{
    /**
     * Handle the event.
     */
    public function handle(Login|Registered $event): void
    {
        // S'assurer que le remember token existe
        if ($event->user && !$event->user->getRememberToken()) {
            $event->user->setRememberToken(\Illuminate\Support\Str::random(60));
            $event->user->save();
        }
        
        // Pour l'inscription, s'assurer que l'utilisateur est connecté avec remember me
        if ($event instanceof Registered && $event->user && Auth::check() && Auth::id() === $event->user->id) {
            // Le remember me est déjà géré par CreateNewUser lors de l'inscription
        }
    }
}

