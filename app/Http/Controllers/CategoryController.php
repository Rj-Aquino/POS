<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Method to get all categories
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    // Method to get a single category by ID
    public function show($id)
    {
        $category = Category::find($id);
        
        // Check if category exists
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category);
    }

    // Method to create a new category
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'Name' => 'required|string|max:100',
            'Description' => 'nullable|string|max:255',
        ]);

        // Create new category
        $category = Category::create([
            'Name' => $request->Name,
            'Description' => $request->Description,
        ]);

        return response()->json($category, 201);
    }

    // Method to update a category by ID
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        // Check if category exists
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Validate request data
        $request->validate([
            'Name' => 'nullable|string|max:100',
            'Description' => 'nullable|string|max:255',
        ]);

        // Update category
        $category->update([
            'Name' => $request->Name ?? $category->Name,
            'Description' => $request->Description ?? $category->Description,
        ]);

        return response()->json($category);
    }

    // Method to delete a category by ID
    public function destroy($id)
    {
        $category = Category::find($id);

        // Check if category exists
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Delete the category
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
