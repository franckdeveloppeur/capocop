<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Le package darryldecode/cart s'enregistre automatiquement
        // Le stockage personnalisé est configuré dans config/shopping_cart.php
        // Le package utilisera automatiquement CacheStorage via la configuration
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Le panier est maintenant persistant via cookies et cache pendant 30 jours
        // Fonctionne pour les visiteurs et les utilisateurs connectés
        // Les visiteurs peuvent ajouter des produits au panier sans être connectés
        // Le stockage personnalisé CacheStorage est utilisé via la configuration
    }
}
