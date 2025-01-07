<?php

namespace App\Http\Controllers;

use App\Models\OrderProduct;
use Illuminate\Http\Request;

class OrderProductController extends Controller
{
    public function index()
    {
        return OrderProduct::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'OrderID' => 'required|exists:orders,OrderID',
            'ProductID' => 'required|exists:products,id',
            'Quantity' => 'required|integer|min:1',
            'TotalPrice' => 'required|numeric|min:0',
        ]);

        $orderProduct = OrderProduct::create($validated);

        return response()->json($orderProduct, 201);
    }

    public function show($orderID, $productID)
    {
        $orderProduct = OrderProduct::where('OrderID', $orderID)
            ->where('ProductID', $productID)
            ->firstOrFail();

        return $orderProduct;
    }

    public function update(Request $request, $orderID, $productID)
    {
        $orderProduct = OrderProduct::where('OrderID', $orderID)
            ->where('ProductID', $productID)
            ->firstOrFail();

        $validated = $request->validate([
            'Quantity' => 'sometimes|integer|min:1',
            'TotalPrice' => 'sometimes|numeric|min:0',
        ]);

        $orderProduct->update($validated);

        return response()->json($orderProduct);
    }

    public function destroy($orderID, $productID)
    {
        $orderProduct = OrderProduct::where('OrderID', $orderID)
            ->where('ProductID', $productID)
            ->firstOrFail();

        $orderProduct->delete();

        return response()->json(null, 204);
    }
}
