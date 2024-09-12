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
            'picture_product' => 'required|image',
            'price' => 'required',
            'description_product' => 'required',
            'id_company' => 'required',
            'id_category' => 'required'
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
            'name_product' => $request->name_product,
            'picture_product' => $filename,
            'price' => $request->price,
            'description_product' => $request->description_product,
            'id_company' => $request->id_company,
            'id_category' => $request->id_category,
        ]);

        // Check if $tags exists and is an array before looping through
        if ($request->has('tags') && is_array($request->tags)) {
            $tags = $request->tags;
            for ($i = 0; $i < count($tags); $i++) {
                $product->tags()->attach($tags[$i]);
            }
        }
    
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
            'picture_product' => 'required|image',
            'price' => 'required',
            'description_product' => 'required',
            'id_company' => 'required',
            'id_category' => 'required'
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
            'description_product' => $request->description_product,
            'id_category' => $request->id_category,
            'id_company' => $request->id_company
        ]);

        if ($request->has('tags')) {
            // Détach old tags
            $product->tags()->detach();

            // Attach news tags
            foreach ($request->tags as $tag) {
                $product->tags()->attach($tag);
            }
        }
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