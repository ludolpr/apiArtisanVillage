<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="Category",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name_category", type="string", example="Nom de la catégorie"),
 *     @OA\Property(property="description_category", type="string", example="Description de la catégorie"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00Z")
 * )
 */

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/categories",
     *     summary="Afficher la liste des catégories",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des catégories affichée avec succès.",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $category = Category::orderBy('name_category', 'asc')->get();
        return response()->json($category);
    }

    /**
     * @OA\Post(
     *     path="/category",
     *     summary="Créer une nouvelle catégorie",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name_category", "description_category"},
     *             @OA\Property(property="name_category", type="string", example="Nom de la catégorie"),
     *             @OA\Property(property="description_category", type="string", example="Description de la catégorie")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Catégorie créée avec succès.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Category")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_category' => 'required|max:50',
            'description_category' => 'required|max:400',
        ]);

        $category = Category::create([
            'name_category' => $request->name_category,
            'description_category' => $request->description_category
        ]);

        // JSON response
        return response()->json([
            'status' => 'Success',
            'data' => $category,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/category/{id}",
     *     summary="Afficher une catégorie spécifique",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Catégorie affichée avec succès.",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Catégorie non trouvée."
     *     )
     * )
     */
    public function show(Category $category)
    {
        return response()->json($category);
    }

    /**
     * @OA\Put(
     *     path="/category/{id}",
     *     summary="Mettre à jour une catégorie spécifique",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name_category", "description_category"},
     *             @OA\Property(property="name_category", type="string", example="Nom de la catégorie"),
     *             @OA\Property(property="description_category", type="string", example="Description de la catégorie")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Catégorie mise à jour avec succès.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Mise à jour avec succès"),
     *             @OA\Property(property="data", ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Catégorie non trouvée."
     *     )
     * )
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name_category' => 'required|max:50',
            'description_category' => 'required|max:400',
        ]);

        $category->update($request->all());

        return response()->json([
            "status" => "Mise à jour avec succès",
            "data" => $category
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/category/{id}",
     *     summary="Supprimer une catégorie spécifique",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Catégorie supprimée avec succès.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Delete OK")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Catégorie non trouvée."
     *     )
     * )
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}