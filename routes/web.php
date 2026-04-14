<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderingController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AdminController;

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

    // Admin Routes
    Route::middleware('admin')->group(function () {
        // Admin Dashboard
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Menu Management
        Route::get('/admin/menu', [MenuController::class, 'index'])->name('admin.menu.index');
        Route::get('/admin/menu/create', [MenuController::class, 'create'])->name('admin.menu.create');
        Route::post('/admin/menu', [MenuController::class, 'store'])->name('admin.menu.store');
        Route::get('/admin/menu/{item}/edit', [MenuController::class, 'edit'])->name('admin.menu.edit');
        Route::put('/admin/menu/{item}', [MenuController::class, 'update'])->name('admin.menu.update');
        Route::delete('/admin/menu/{item}', [MenuController::class, 'destroy'])->name('admin.menu.destroy');

        // Order Management
        Route::get('/admin/orders', [UserController::class, 'ordersKanban'])->name('admin.orders');
        Route::post('/admin/orders/status', [UserController::class, 'updateOrderStatus'])->name('admin.orders.status');
    });

    // Customer Routes
    Route::get('/customer/orders', [OrderingController::class, 'orderHistory'])->name('customer.orders');
});

// Ordering System Routes (Auth Required)
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
