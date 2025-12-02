<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/produits', function () {
    return view('produits');
});

Route::get('/details', function () {
    return view('details');
});

Route::get('/panier', function () {
    return view('panier');
});

Route::get('/commandes', function () {
    return view('commandes');
});

Route::get('/signup', function () {
    return view('signup');
});
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
