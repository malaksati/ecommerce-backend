<?php

use App\Http\Controllers\Api\Admin\AddressController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\CartItemController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);

    Route::get('/cart', [CartItemController::class, 'index']);
    Route::post('/cart', [CartItemController::class, 'store']);
    Route::put('/cart/{id}', [CartItemController::class, 'update']);
    Route::delete('/cart/{id}', [CartItemController::class, 'destroy']);
    Route::delete('/cart', [CartItemController::class, 'clear']);

    Route::post('/checkout', [CheckoutController::class, 'checkout']);
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('addresses', AddressController::class);

    // Orders
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::put('orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::put('orders/{id}/payment', [OrderController::class, 'updatePayment']);
});