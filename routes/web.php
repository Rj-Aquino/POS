<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LoyaltyCardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;

// Route for the checkout page (GET)
Route::get('/', function () {
    return view('checkout');
})->name('checkout');

// Route for handling the payment logic (POST)
Route::post('/', function (\Illuminate\Http\Request $request) {
    // Retrieve the products and loyalty points data from the form submission
    $products = $request->input('products', []); // Products array
    $loyaltyPoints = $request->input('loyalty_points', 0); // Loyalty points
    
    // You can add your payment logic here (e.g., processing payment, reducing points)
    
    // For now, we just return a success response
    return response()->json([
        'success' => true,
        'message' => 'Purchase Successful!', // You can customize the message here
    ]);
})->name('payment');

Route::post('/transactions/store', [TransactionController::class, 'storeTransaction'])->name('transactions.store');

// Route ng Validate product, kung meron talagang ganung product sa database
Route::post('/validate-product', [ProductController::class, 'validateProductCode']);

Route::get('/loyalty-cards', function () {
    return view('loyaltycards');
});

Route::get('/update-loyalty-cards', function () {
    return view('update-loyalty-cards');
});