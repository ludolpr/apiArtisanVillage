<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::all();
        return response()->json($category);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_role' => 'required|max:50',
            'description_role' => 'required|max:400',
        ]);

        $category = Category::create([
            'name_role' => $request->name_role,
            'description_role' => $request->description_role

        ]);

        // JSON response
        return response()->json([
            'status' => 'Success',
            'data' => $category,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name_role' => 'required|max:50',
            'description_role' => 'required|max:400',
        ]);

        $category->update($request->all());

        return response()->json([
            "status" => "Mise à jour avec succèss",
            "data" => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}
