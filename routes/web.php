<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PoolZoneController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function () {

    Route::middleware(['role:Admin,Employer,System'])->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::post('/products/add-to-cart', [ProductController::class, 'addToCart'])->name('products.addToCart');
        Route::post('/products/clear-cart', [ProductController::class, 'clearCart'])->name('products.clearCart');
        Route::post('/products/create-order', [ProductController::class, 'createOrder'])->name('products.createOrder');
        Route::delete('/products/cart/{index}', [ProductController::class, 'removeFromCart'])->name('products.removeFromCart');

        Route::get('/booking/map', [BookingController::class, 'index'])->name('booking.map');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::post('/bookings/{booking}/pay-rest', [BookingController::class, 'payRest'])->name('bookings.payRest');
        Route::post('/bookings/store',[BookingController::class,'store'])->name('bookings.store');
        Route::get('/bookings/{id}', [BookingController::class, 'show']);
        Route::post('/bookings/{id}/arrived',[BookingController::class,'markArrived']);
        Route::post('/bookings/{id}/move',[BookingController::class,'move']);
        Route::post('/bookings/{id}/cancel',[BookingController::class,'cancel']);

        Route::get('/orders/debtors', [OrderController::class, 'debtorsToday'])->name('orders.debtors');
        Route::get('/orders/{order}/pay-debt', [OrderController::class, 'payDebtForm'])->name('orders.payDebtForm');
        Route::post('/orders/{order}/pay-debt', [OrderController::class, 'payDebt'])->name('orders.payDebt');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

        Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
        Route::post('/kitchen/update-status/{item}', [KitchenController::class, 'updateStatus'])->name('kitchen.updateStatus');
    });


    Route::middleware(['role:Admin,System'])->group(function () {
        /*Route::get('/poolzones', function () {
            return  redirect()->route('poolzones.index');
        });*/
        Route::get('/zones/map', [\App\Http\Controllers\ZoneController::class, 'map'])->name('zones.map');
        Route::post('/zones/update-position', [\App\Http\Controllers\ZoneController::class, 'updatePosition'])->name('zones.updatePosition');
        Route::get('/poolzones', [PoolZoneController::class, 'index'])->name('poolzones.index');
        Route::post('/poolzones', [PoolZoneController::class, 'store'])->name('poolzones.store');
        Route::post('/zones', [ZoneController::class, 'store'])->name('zones.store');
        Route::post('/zones/{zone}/position', [ZoneController::class, 'updatePosition']);
        Route::delete('/zones/{zone}', [ZoneController::class, 'destroy'])->name('zones.destroy');

        Route::post('/zones/{zone}/price', [ZoneController::class, 'updatePrice'])->name('zones.updatePrice');

        Route::post('/client/check', [ClientController::class, 'findByMobilePhone'])->name('client.check');
        Route::post('/client', [ClientController::class, 'store'])->name('client.store');

        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');

        Route::get('/orders/report', [OrderController::class, 'report'])->name('orders.report');
        Route::get('/orders/details/{order}', [OrderController::class, 'details'])->name('orders.details');


    });

});

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
