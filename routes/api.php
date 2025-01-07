<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


use App\Http\Controllers\TokenController;

Route::post('/generate-token', [TokenController::class, 'generateToken']);

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\OrderProductController;

// Protect these routes using Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('transactions', TransactionController::class);

    Route::get('order-products', [OrderProductController::class, 'index']);
    Route::post('order-products', [OrderProductController::class, 'store']);
    Route::get('order-products/{orderID}/{productID}', [OrderProductController::class, 'show']);
    Route::put('order-products/{orderID}/{productID}', [OrderProductController::class, 'update']);
    Route::delete('order-products/{orderID}/{productID}', [OrderProductController::class, 'destroy']);

});
