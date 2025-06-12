<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PoolZoneController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return  redirect()->route('poolzones.index');
    return view('welcome');
});

Route::get('/zones/map', [\App\Http\Controllers\ZoneController::class, 'map'])->name('zones.map');
Route::post('/zones/update-position', [\App\Http\Controllers\ZoneController::class, 'updatePosition'])->name('zones.updatePosition');
Route::get('/poolzones', [PoolZoneController::class, 'index'])->name('poolzones.index');
Route::post('/poolzones', [PoolZoneController::class, 'store'])->name('poolzones.store');
Route::post('/zones', [ZoneController::class, 'store'])->name('zones.store');
Route::post('/zones/{zone}/position', [ZoneController::class, 'updatePosition']);
Route::delete('/zones/{zone}', [ZoneController::class, 'destroy'])->name('zones.destroy');

Route::get('/booking/map', [BookingController::class, 'index'])->name('booking.map');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::post('/zones/{zone}/price', [ZoneController::class, 'updatePrice'])->name('zones.updatePrice');

Route::post('/client/check', [ClientController::class, 'findByMobilePhone'])->name('client.check');
Route::post('/client', [ClientController::class, 'store'])->name('client.store');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::post('/products/add-to-cart', [ProductController::class, 'addToCart'])->name('products.addToCart');
Route::post('/products/clear-cart', [ProductController::class, 'clearCart'])->name('products.clearCart');
Route::post('/products/create-order', [ProductController::class, 'createOrder'])->name('products.createOrder');
Route::delete('/products/cart/{index}', [ProductController::class, 'removeFromCart'])->name('products.removeFromCart');

Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/report', [OrderController::class, 'report'])->name('orders.report');
Route::get('/orders/details/{order}', [OrderController::class, 'details'])->name('orders.details');


