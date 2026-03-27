<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderingController;

Route::get('/', function () {
    return view('welcome');
});

// Ordering System Routes
Route::controller(OrderingController::class)->group(function () {
    Route::get('/ordering', 'selection')->name('ordering.selection');
    Route::get('/menu', 'menu')->name('ordering.menu');
    Route::get('/cart', 'cart')->name('ordering.cart');
    Route::post('/cart/update-quantity', 'updateCartQuantity')->name('ordering.cart.updateQuantity');
    Route::get('/checkout', 'checkout')->name('ordering.checkout');
    Route::post('/add-to-cart', 'addToCart')->name('add-to-cart');
    Route::post('/place-order', 'placeOrder')->name('place-order');
    Route::get('/success', 'success')->name('order.success');
    Route::get('/failure', 'failure')->name('order.failure');
});
