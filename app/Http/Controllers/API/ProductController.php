<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     required={"name_product", "price", "description_product", "id_company", "id_category"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name_product", type="string", example="Produit A"),
 *     @OA\Property(property="picture_product", type="string", example="image.jpg"),
 *     @OA\Property(property="price", type="number", format="float", example=19.99),
 *     @OA\Property(property="description_product", type="string", example="Description du produit A"),
 *     @OA\Property(property="id_company", type="integer", example=1),
 *     @OA\Property(property="id_category", type="integer", example=2)
 * )
 */



class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/product",
     *     summary="Obtenir la liste des produits",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des produits",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     )
     * )
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
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
     * @OA\Post(
     *     path="/product",
     *     summary="Créer un produit",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name_product", "picture_product", "price", "description_product", "id_company", "id_category"},
     *                 @OA\Property(property="name_product", type="string", example="Produit A"),
     *                 @OA\Property(property="picture_product", type="string", format="binary"),
     *                 @OA\Property(property="price", type="number", format="float", example=19.99),
     *                 @OA\Property(property="description_product", type="string", example="Description du produit A"),
     *                 @OA\Property(property="id_company", type="integer", example=1),
     *                 @OA\Property(property="id_category", type="integer", example=2),
     *                 @OA\Property(property="tags", type="string", example="1,2,3")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produit créé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     )
     * )
     */
    public function store(Request $request)
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

        if ($request->has('tags') && !empty($request->tags)) {
            $tags = explode(',', $request->tags);
            $product->tags()->sync($tags);
        } else {
            $product->tags()->sync([]);
        }

        return response()->json([
            'status' => 'Success',
            'data' => $product,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/product/{id}",
     *     summary="Afficher un produit spécifique",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du produit",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du produit",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produit non trouvé"
     *     )
     * )
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
     * @OA\Put(
     *     path="/product/{id}",
     *     summary="Mettre à jour un produit",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du produit",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produit mis à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produit non trouvé"
     *     )
     * )
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

        $filename = $product->picture_product;
        if ($request->hasFile('picture_product')) {
            if ($product->picture_product) {
                Storage::delete('public/uploads/products/' . $product->picture_product);
            }

            $filenameWithExt = $request->file('picture_product')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture_product')->getClientOriginalExtension();
            $filename = $filename . '_' . time() . '.' . $extension;
            $request->file('picture_product')->storeAs('public/uploads/products', $filename);
        }

        $product->update([
            'name_product' => $request->name_product,
            'picture_product' => $filename,
            'price' => $request->price,
            'description_product' => $request->description_product,
            'id_company' => $request->id_company,
            'id_category' => $request->id_category,
        ]);

        if ($request->has('tags') && !empty($request->tags)) {
            $tags = explode(',', $request->tags);
            $product->tags()->sync($tags);
        } else {
            $product->tags()->sync([]);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Product updated successfully',
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/product/{id}",
     *     summary="Supprimer un produit",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du produit",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produit supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produit non trouvé"
     *     )
     * )
     */
    public function destroy(Product $product)
    {
        if ($product->picture_product) {
            Storage::delete('public/uploads/products/' . $product->picture_product);
        }

        $product->delete();

        return response()->json(['status' => 'Success',
            'message' => 'Product deleted successfully',
        ]);
    }
}