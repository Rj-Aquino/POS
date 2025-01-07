<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    // Get all products
    public function index()
    {
        $products = Product::all();  // Get all products
        return response()->json($products);
    }

    // Get a single product by ID
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    // Create a new product
    public function store(Request $request)
    {
        $request->validate([
            'CategoryID' => 'required|exists:category,id', // Ensure the CategoryID exists in the category table
            'Name' => 'required|string|max:255',
            'Price' => 'required|numeric|min:0',
        ]);

        // Create a new product
        $product = Product::create([
            'CategoryID' => $request->CategoryID,
            'Name' => $request->Name,
            'Price' => $request->Price,
        ]);

        return response()->json($product, 201);
    }

    // Update an existing product
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Validate incoming request
        $request->validate([
            'CategoryID' => 'nullable|exists:category,id', // Ensure the CategoryID exists in the category table
            'Name' => 'nullable|string|max:255',
            'Price' => 'nullable|numeric|min:0',
        ]);

        // Update the product
        $product->update([
            'CategoryID' => $request->CategoryID ?? $product->CategoryID,
            'Name' => $request->Name ?? $product->Name,
            'Price' => $request->Price ?? $product->Price,
        ]);

        return response()->json($product);
    }

    // Delete a product
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Delete the product
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    // Taga Validate kung meron ba talagang ganung product sa database
    public function validateProductCode(Request $request)
    {
        try {
            $productCode = $request->input('product_code');

            if (!$productCode) {
                return response()->json(['exists' => false, 'error' => 'Product code is missing.'], 400);
            }

            // Use the correct primary key column: ProductID
            $product = Product::where('ProductID', $productCode)->first();

            if ($product) {
                return response()->json([
                    'exists' => true,
                    'productName' => $product->Name,  // Use correct column name
                    'productPrice' => $product->Price // Use correct column name
                ]);
            } else {
                return response()->json(['exists' => false]);
            }
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Validation Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
