<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderingController;

Route::get('/', function () {
    return redirect()->route('ordering.selection');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login.submit');
    Route::get('/register', [UserController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [UserController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/admin/orders', [UserController::class, 'ordersKanban'])->name('admin.orders');
        Route::post('/admin/orders/status', [UserController::class, 'updateOrderStatus'])->name('admin.orders.status');
    });
});

// Ordering System Routes
Route::middleware('auth')->controller(OrderingController::class)->group(function () {
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
