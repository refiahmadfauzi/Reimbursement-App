<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'limit_per_month'    => 'required',

        ]);

        $user = Category::create([
            'name'     => $validated['name'],
            'limit_per_month'    => $validated['limit_per_month'],
        ]);

        return response()->json(['message' => 'User registered', 'user' => $user], 201);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id'       => 'required',
            'name'     => 'required|string|max:255',
            'limit_per_month'    => 'required',

        ]);

        $updated = Category::where('id', $validated['id'])->update([
            'name' => $validated['name'],
            'limit_per_month' => $validated['limit_per_month'],
        ]);

        if ($updated === 0) {
            return response()->json(['message' => 'Category not found or no changes made'], 404);
        }

        $category = Category::find($validated['id']);

        return response()->json(['message' => 'Category updated', 'data' => $category], 200);
    }
}
