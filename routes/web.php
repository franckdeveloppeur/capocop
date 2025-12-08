<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/produits', function () {
    return view('produits.produits-all');
})->name('produits');

Route::get('/produit/{slug}', function ($slug) {
    return view('details', ['slug' => $slug]);
})->name('products.show');

Route::get('/details', function () {
    return view('details');
});

Route::get('/panier', function () {
    return view('panier');
});

// checkout
Route::get('/checkout', function () {
    return view('orders.checkout');
})->name('checkout');

Route::get('/commandes', function () {
    return view('commandes');
});
// routes pour les favoris 
Route::get('/favoris', function () {
    return view('produits.favoris');
})->name('favoris')->middleware(['auth']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
