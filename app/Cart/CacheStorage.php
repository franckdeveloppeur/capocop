<?php

namespace App\Cart;

use Carbon\Carbon;
use Darryldecode\Cart\CartCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class CacheStorage
{
    private $data = [];
    private $cart_id;

    public function __construct()
    {
        // Récupérer l'ID du panier depuis le cookie ou en créer un nouveau
        $this->cart_id = Cookie::get('capocop_cart_id');
        
        if ($this->cart_id) {
            // Charger les données du panier depuis le cache
            $this->data = Cache::get('capocop_cart_' . $this->cart_id, []);
        } else {
            // Créer un nouvel ID de panier unique
            $this->cart_id = 'cart_' . uniqid() . '_' . time();
            
            // Définir le cookie pour 30 jours
            Cookie::queue(
                Cookie::make('capocop_cart_id', $this->cart_id, 60 * 24 * 30, '/', null, false, false)
            );
        }
    }

    /**
     * Vérifier si une clé existe dans le stockage
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Récupérer les données d'une clé
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return new CartCollection($this->data[$key] ?? []);
        }
        
        return new CartCollection([]);
    }

    /**
     * Stocker les données dans le cache et mettre à jour le cookie
     */
    public function put($key, $value)
    {
        $this->data[$key] = $value;
        
        // Stocker dans le cache pour 30 jours
        Cache::put('capocop_cart_' . $this->cart_id, $this->data, Carbon::now()->addDays(30));

        // S'assurer que le cookie est défini pour 30 jours
        if (!Cookie::hasQueued('capocop_cart_id')) {
            Cookie::queue(
                Cookie::make('capocop_cart_id', $this->cart_id, 60 * 24 * 30, '/', null, false, false)
            );
        }
    }

    /**
     * Supprimer une clé du stockage
     */
    public function forget($key)
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
            Cache::put('capocop_cart_' . $this->cart_id, $this->data, Carbon::now()->addDays(30));
        }
    }

    /**
     * Vider tout le stockage
     */
    public function flush()
    {
        $this->data = [];
        Cache::forget('capocop_cart_' . $this->cart_id);
    }

    /**
     * Obtenir l'ID du panier
     */
    public function getCartId()
    {
        return $this->cart_id;
    }
}

