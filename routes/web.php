<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', "pages::home")->name('home');

Route::group(['prefix' => 'explore'], function() {
    Route::livewire('/', "pages::product.index")->name('product.explore');
});

Route::middleware('auth')
->prefix("cart")
->group(function() {
    Route::livewire('/checkout', "pages::cart.checkout")->name('cart.checkout');
});

Route::middleware("auth")
->prefix("transactions")
->group(function() {
    // Route::livewire('/', "pages::transaction.index")->name('transaction.index');
    Route::livewire('/{transaction:trx_id}', "pages::transaction.view")->name('transaction.view');
});

Route::prefix("payments")
->middleware('auth')
->group(function() {
    // Route::livewire('/', "pages::payment.index")->name('payment.index');
    Route::livewire('/{payment:payment_code}', "pages::payment.view")->name('payment.view');
});

Route::prefix('auth')
->middleware('guest')
->group(function() {
    Route::livewire('/login', "pages::auth.login")->name('login');
    Route::livewire('/register', "pages::auth.register")->name('register');
});

Route::prefix('dashboard')
->middleware('auth')
->group(function() {
    Route::livewire('/', "pages::dashboard.index")->name('dashboard');
    Route::livewire("/payment-list", "pages::dashboard.payment-list")->name('dashboard.payment-list');
    Route::livewire("/account-settings", "pages::dashboard.account-settings")->name('dashboard.account-settings');

});



Route::livewire("/{product:slug}", "pages::product.view")->name('product.view');

