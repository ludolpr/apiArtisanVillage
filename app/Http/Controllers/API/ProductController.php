<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::all();
        return response()->json($product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'name_product' => 'required|max:50',
            'picture_product' => 'required|max:400',
            'price' => 'required|image|max:5000',
            'product_description' => 'required|max:155',
            'id_category' => 'required',
            'id_company' => 'required'
        ]);

        $filename = "";
        if ($request->hasFile('picture_product')) {
            $filenameWithExt = $request->file('picture_product')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture_product')->getClientOriginalExtension();
            $filename = $filename . '_' . time() . '.' . $extension;
            $request->file('picture_product')->storeAs('public/uploads/products', $filename);
        }

        $product = Product::create([
            'name_product' => $request->name_escape,
            'picture_product' => $filename,
            'price' => $request->price,
            'product_description' => $request->product_description,
        ]);


        return response()->json([
            'status' => 'Success',
            'data' => $product,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name_product' => 'required|max:50',
            'picture_product' => 'sometimes|image|max:5000',
            'price' => 'required|image|max:5000',
            'product_description' => 'required|max:155',
            'id_category' => 'required',
            'id_company' => 'required'
        ]);

        if ($request->hasFile('picture_product')) {
            // Delete old file
            if ($product->picture_product) {
                Storage::delete('public/uploads/products/' . $product->picture_product);
            }

            $filenameWithExt = $request->file('picture_product')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture_product')->getClientOriginalExtension();
            $filename = $filename . '_' . time() . '.' . $extension;
            $request->file('picture_product')->storeAs('public/uploads/products', $filename);

            $product->picture_product = $filename;
        }

        $product->update([
            'name_product' => $request->name_product,
            'picture_product' =>  $filename,
            'price' => $request->price,
            'product_description' => $request->product_description,
            'id_category' => $request->id_category,
            'id_company' => $request->id_company
        ]);

        // Return the updated information in JSON
        return response()->json([
            'status' => 'Update OK',
            'data' => $product,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}
