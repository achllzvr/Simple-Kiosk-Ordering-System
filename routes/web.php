<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderingController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\PayMongoWebhookController;
use App\Http\Controllers\PayMongoReturnController;

Route::get('/', function () {
    return redirect()->route('ordering.selection');
});

Route::post('/webhooks/paymongo', PayMongoWebhookController::class)->name('paymongo.webhook');
Route::get('/paymongo/return', PayMongoReturnController::class)->name('paymongo.return');

Route::middleware('guest')->group(function () {
    Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/admin/menu', [MenuController::class, 'index'])->name('admin.menu.index');
        Route::get('/admin/menu/create', [MenuController::class, 'create'])->name('admin.menu.create');
        Route::post('/admin/menu', [MenuController::class, 'store'])->name('admin.menu.store');
        Route::get('/admin/menu/{item}/edit', [MenuController::class, 'edit'])->name('admin.menu.edit');
        Route::put('/admin/menu/{item}', [MenuController::class, 'update'])->name('admin.menu.update');
        Route::delete('/admin/menu/{item}', [MenuController::class, 'destroy'])->name('admin.menu.destroy');

        Route::get('/admin/restaurants', [RestaurantController::class, 'index'])->name('admin.restaurants.index');
        Route::get('/admin/restaurants/create', [RestaurantController::class, 'create'])->name('admin.restaurants.create');
        Route::post('/admin/restaurants', [RestaurantController::class, 'store'])->name('admin.restaurants.store');
        Route::get('/admin/restaurants/{restaurant}/edit', [RestaurantController::class, 'edit'])->name('admin.restaurants.edit');
        Route::put('/admin/restaurants/{restaurant}', [RestaurantController::class, 'update'])->name('admin.restaurants.update');
        Route::delete('/admin/restaurants/{restaurant}', [RestaurantController::class, 'destroy'])->name('admin.restaurants.destroy');

        Route::get('/admin/orders', [UserController::class, 'ordersKanban'])->name('admin.orders');
        Route::post('/admin/orders/status', [UserController::class, 'updateOrderStatus'])->name('admin.orders.status');
        Route::post('/admin/orders/reconcile-paymongo', [UserController::class, 'reconcilePaymongo'])->name('admin.orders.reconcile');
    });
});

// Guest kiosk ordering (no account required)
Route::controller(OrderingController::class)->group(function () {
    Route::get('/ordering', 'selection')->name('ordering.selection');
    Route::get('/ordering/location', 'location')->name('ordering.location');
    Route::post('/ordering/location', 'saveLocation')->name('ordering.location.save');
    Route::get('/ordering/nearby-stores', 'nearbyStores')->name('ordering.nearby');
    Route::get('/menu', 'menu')->name('ordering.menu');
    Route::get('/cart', 'cart')->name('ordering.cart');
    Route::post('/cart/update-quantity', 'updateCartQuantity')->name('ordering.cart.updateQuantity');
    Route::get('/checkout', 'checkout')->name('ordering.checkout');
    Route::post('/add-to-cart', 'addToCart')->name('add-to-cart');
    Route::post('/place-order', 'placeOrder')->name('place-order');
    Route::get('/success', 'success')->name('order.success');
    Route::get('/failure', 'failure')->name('order.failure');
    Route::get('/order/track', 'track')->name('order.track');
});
