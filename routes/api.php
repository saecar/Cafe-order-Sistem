<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'loginadmin']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/contact', [ContactController::class, 'store']);

Route::get('/categories', [CategoriesController::class, 'index']);
Route::get('/categories/{category}', [CategoriesController::class, 'show']);

Route::get('/products', [ProductsController::class, 'index']);
Route::get('/products/{product}', [ProductsController::class, 'show']);

Route::post('/orders', [OrderController::class, 'store']);
Route::post('/orders/{order}/pay', [PaymentController::class, 'transaction']);

Route::post('/payments/notification', [PaymentController::class, 'notification']);

Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('/contacts', [ContactController::class, 'index']);
    Route::get('/contacts/{contact}', [ContactController::class, 'show']);
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);

    Route::post('/categories', [CategoriesController::class, 'store']);
    Route::put('/categories/{category}', [CategoriesController::class, 'update']);
    Route::delete('/categories/{category}', [CategoriesController::class, 'destroy']);

    Route::post('/products', [ProductsController::class, 'store']);
    Route::put('/products/{product}', [ProductsController::class, 'update']);
    Route::delete('/products/{product}', [ProductsController::class, 'destroy']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
});