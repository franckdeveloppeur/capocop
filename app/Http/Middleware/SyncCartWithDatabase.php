<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncCartWithDatabase extends Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user just logged in, migrate session cart to user's cart in database
        if (auth()->check()) {
            $userId = auth()->id();
            $sessionId = session()->getId();

            // Get cart items from session
            $cartItems = \Cart::getContent();

            if ($cartItems->count() > 0) {
                // Get or create user's cart in database
                $userCart = \App\Models\Cart::firstOrCreate(
                    ['user_id' => $userId],
                    ['session_id' => $sessionId, 'status' => 'active']
                );

                // Sync items to database
                foreach ($cartItems as $item) {
                    $userCart->items()->updateOrCreate(
                        ['product_id' => $item->id],
                        ['quantity' => $item->quantity]
                    );
                }
            }
        }

        return $next($request);
    }
}
