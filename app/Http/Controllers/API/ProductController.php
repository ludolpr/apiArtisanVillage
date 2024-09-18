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

    public function indexWithTags()
    {
        $productstags = Product::with('tags')->get();

        if ($productstags->isEmpty()) {
            return response()->json([
                'status' => 'Success',
                'message' => 'No products found',
                'data' => [],
            ]);
        }

        return response()->json([
            'status' => 'Success',
            'data' => $productstags,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'name_product' => 'required|max:50',
            'picture_product' => 'required|image|max:10000',
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

        if ($request->has('tags')) {
            $tags = explode(',', $request->tags);
            $product->tags()->attach($tags);
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

    public function showWithTags($id)
    {
        $product = Product::with('tags')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'status' => 'Success',
            'data' => $product,
        ]);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name_product' => 'required|max:50',
            'picture_product' => 'nullable',
            'price' => 'required',
            'description_product' => 'required',
            'id_company' => 'required',
            'id_category' => 'required',
        ]);

        $filename =  $product->picture_product;
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
            // Je récupère mes tags dans le formulaire
            $tags = $request->tags;

            // Je les mets dans un tableau
            $tags_id = explode(",", $tags);
            // detach old tags and attch new tags
            $product->tags()->sync($tags_id);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product)
    {
        if (($request->has('tags'))) {
            $product->tags()->detach();
        }
        $product->save();

        $product->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}