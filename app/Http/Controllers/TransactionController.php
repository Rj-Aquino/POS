<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        return Transaction::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'OrderID' => 'required|exists:orders,OrderID',
            'LoyaltyCardID' => 'required|exists:loyalty_cards,id',
            'TotalPointsUsed' => 'required|integer',
            'PointsEarned' => 'required|integer',
            'TransactionDate' => 'required|date',
        ]);

        $transaction = Transaction::create($validated);

        return response()->json($transaction, 201);
    }

    public function show($id)
    {
        return Transaction::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $validated = $request->validate([
            'OrderID' => 'sometimes|exists:orders,OrderID',
            'LoyaltyCardID' => 'sometimes|exists:loyalty_cards,id',
            'TotalPointsUsed' => 'sometimes|integer',
            'PointsEarned' => 'sometimes|integer',
            'TransactionDate' => 'sometimes|date',
        ]);

        $transaction->update($validated);

        return response()->json($transaction);
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return response()->json(null, 204);
    }

    public function storeTransaction(Request $request)
    {
        // Retrieve the products from the request
        $products = $request->input('products');

        // Calculate subtotal and total based on the products
        $subtotal = 0;
        $total = 0;
        // foreach ($products as $product) {
        //     $productData = json_decode($product, true);  // Decode the JSON data
        //     $subtotal += $productData['TotalPrice'];  // Assuming TotalPrice is one of the fields
        // }

        // If you need to apply any discounts, loyalty points, etc., you can modify the total here

        // Now you can handle the logic for creating an order, inserting order products, and the transaction

        // For example, creating the order:
        Order::create([
            'OrderDate' => now()->toDateString(),
            'Subtotal' => $subtotal,
            'Total' => $total,
        ]);

        // Then add order products (as before)
        // foreach ($products as $product) {
        //     $productData = json_decode($product, true);
        //     OrderProduct::create([
        //         'OrderID' => $order->OrderID,
        //         'ProductID' => $productData['ProductID'],
        //         'Quantity' => $productData['Quantity'],
        //         'TotalPrice' => $productData['TotalPrice'],
        //     ]);
        // }

        // Add transaction logic here (e.g., calculating loyalty points, etc.)

        return response()->json([
            'success' => true,
            'message' => 'Transaction completed successfully!',
        ]);
    }


}
