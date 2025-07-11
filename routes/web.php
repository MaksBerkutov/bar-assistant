<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CashWithdrawalController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PoolZoneController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\ZonePricingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function () {

    Route::middleware(['role:Admin,Employer,System'])->group(function () {
        Route::prefix('products')->group(function () {
            Route::put('/cart/update/{index}', [ProductController::class, 'updateQuantity'])->name('products.updateQuantity');
            Route::get('/operator', [ProductController::class, 'operator'])->name('products.operator');
            Route::post('/add-to-cart', [ProductController::class, 'addToCart'])->name('products.addToCart');
            Route::post('/clear-cart', [ProductController::class, 'clearCart'])->name('products.clearCart');
            Route::post('/create-order', [ProductController::class, 'createOrder'])->name('products.createOrder');
            Route::delete('/cart/{index}', [ProductController::class, 'removeFromCart'])->name('products.removeFromCart');

        });
        Route::prefix('bookings')->group(function () {
            Route::post('/', [BookingController::class, 'store'])->name('bookings.store');
            Route::get('/map', [BookingController::class, 'index'])->name('booking.map');
            Route::post('/{booking}/pay-rest', [BookingController::class, 'payRest'])->name('bookings.payRest');
            Route::post('/store',[BookingController::class,'store'])->name('bookings.store');
            Route::get('/{id}', [BookingController::class, 'show']);
            Route::post('/{id}/arrived',[BookingController::class,'markArrived']);
            Route::post('/{id}/move',[BookingController::class,'move']);
            Route::post('/{id}/cancel',[BookingController::class,'cancel']);
        });
        Route::prefix('withdrawals')->group(function () {

            Route::get('/create', [CashWithdrawalController::class, 'create'])->name('withdrawals.create');
            Route::post('/', [CashWithdrawalController::class, 'store'])->name('withdrawals.store');

        });

        Route::prefix('orders')->group(function () {
            Route::post('/', [OrderController::class, 'store'])->name('orders.store');
            Route::get('/debtors', [OrderController::class, 'debtorsToday'])->name('orders.debtors');
            Route::get('/{order}/pay-debt', [OrderController::class, 'payDebtForm'])->name('orders.payDebtForm');
            Route::post('/{order}/pay-debt', [OrderController::class, 'payDebt'])->name('orders.payDebt');
        });

        Route::prefix('kitchen')->group(function () {
            Route::get('/', [KitchenController::class, 'index'])->name('kitchen.index');
            Route::post('/update-status/{item}', [KitchenController::class, 'updateStatus'])->name('kitchen.updateStatus');
        });
        Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');

    });


    Route::middleware(['role:Admin,System'])->group(function () {

        Route::prefix('zones')->group(function () {
            Route::post('/', [ZoneController::class, 'store'])->name('zones.store');
            Route::post('/{zone}/position', [ZoneController::class, 'updatePosition']);
            Route::delete('/{zone}', [ZoneController::class, 'destroy'])->name('zones.destroy');
            Route::get('/map', [ZoneController::class, 'map'])->name('zones.map');
            Route::post('/update-position', [ZoneController::class, 'updatePosition'])->name('zones.updatePosition');
            Route::post('/{zone}/price', [ZoneController::class, 'updatePrice'])->name('zones.updatePrice');
        });

        Route::prefix('poolzones')->group(function () {
            Route::get('/', [PoolZoneController::class, 'index'])->name('poolzones.index');
            Route::post('/', [PoolZoneController::class, 'store'])->name('poolzones.store');

        });
        Route::prefix('withdrawals')->group(function () {
            Route::get('/', [CashWithdrawalController::class, 'index'])->name('withdrawals.index');
        });

        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('products.index');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
            Route::get('/create', [ProductController::class, 'create'])->name('products.create');
            Route::post('/create', [ProductController::class, 'store'])->name('products.store');

        });

        Route::prefix('orders')->group(function () {
            Route::get('/report', [OrderController::class, 'report'])->name('orders.report');
            Route::get('/details/{order}', [OrderController::class, 'details'])->name('orders.details');
            Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
            Route::put('/{order}', [OrderController::class, 'update'])->name('orders.update');
            Route::get('/', [OrderController::class, 'index'])->name('orders.index');
            Route::delete('/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        });

        Route::prefix('clients')->group(function () {
            Route::get('/', [ClientController::class, 'index'])->name('clients.index');
            Route::post('/', [ClientController::class, 'store'])->name('client.store');
            Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
            Route::put('/{client}', [ClientController::class, 'update'])->name('clients.update');
            Route::post('/check', [ClientController::class, 'findByMobilePhone'])->name('client.check');

        });
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    });

    Route::middleware(['role:System'])->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('users.index');
            Route::put('/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');
        });
        Route::prefix('command')->group(function () {
            Route::get('/zone-pricing', [ZonePricingController::class, 'form'])->name('zone.pricing.form');
            Route::post('/zone-pricing', [ZonePricingController::class, 'update'])->name('zone.pricing.update');

        });


    });

});

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
