<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Fortify;

class AuthenticateUser
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function __invoke(Request $request)
    {
        // Forcer le remember me à true
        $request->merge(['remember' => true]);
        
        $user = Auth::guard('web')->getProvider()->retrieveByCredentials([
            Fortify::username() => $request->input(Fortify::username()),
        ]);

        if ($user &&
            Auth::guard('web')->getProvider()->validateCredentials($user, [
                'password' => $request->password,
            ])) {
            // Retourner l'utilisateur - Fortify gérera la connexion avec remember me
            return $user;
        }

        // Retourner null si l'authentification échoue
        return null;
    }
}

