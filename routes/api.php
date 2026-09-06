<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'loginadmin']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Public Routes 
|--------------------------------------------------------------------------
*/
Route::post('/contact', [ContactController::class, 'store']);

Route::get('/categories', [CategoriesController::class, 'index']);
Route::get('/categories/{category}', [CategoriesController::class, 'show']);

Route::get('/products', [ProductsController::class, 'index']);
Route::get('/products/{product}', [ProductsController::class, 'show']);

Route::post('/orders', [OrderController::class, 'store']);
Route::post('/orders/{order}/pay', [PaymentController::class, 'transaction']);
Route::get('/orders/{orderNumber}/check-status', [PaymentController::class, 'checkStatus']);

Route::post('/payments/notification', [PaymentController::class, 'notification']); // webhook Midtrans


/*
|--------------------------------------------------------------------------
| Admin Routes (wajib Bearer Token)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('/products', [ProductsController::class, 'adminIndex']);
    Route::get('/dashboard/best-seller', [DashboardController::class, 'bestSeller']);
    Route::get('/dashboard/profit-recap', [DashboardController::class, 'profitRecap']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

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