<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

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

// Recherche
Route::get('/recherche', function () {
    return view('recherche');
})->name('search');

// checkout
Route::get('/checkout', function () {
    return view('orders.checkout');
})->name('checkout')->middleware(['auth']);

// Confirmation de commande
Route::get('/commande/{order}/confirmation', function ($order) {
    return view('orders.confirmation', ['orderId' => $order]);
})->name('orders.confirmation');

Route::get('/commandes', [OrderController::class, 'viewCommandes'])->name('commandes')->middleware(['auth']);
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
