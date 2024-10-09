<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="Tag",
 *     type="object",
 *     required={"name_tag"},
 *     @OA\Property(property="id", type="integer", example=1, description="ID du tag"),
 *     @OA\Property(property="name_tag", type="string", example="Tag Exemple", description="Nom du tag"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Date de création"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Date de mise à jour"),
 * )
 */
class TagController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tag",
     *     summary="Obtenir la liste des tags",
     *     tags={"Tags"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des tags",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Tag"))
     *     )
     * )
     */
    public function index()
    {
        $tags = Tag::orderBy('name_tag', 'asc')->get();
        return response()->json($tags);
    }

    /**
     * @OA\Post(
     *     path="/tag",
     *     summary="Créer un nouveau tag",
     *     tags={"Tags"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name_tag"},
     *             @OA\Property(property="name_tag", type="string", example="Nouveau Tag")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tag créé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_tag' => 'required|max:50',
        ]);

        $tag = Tag::create([
            'name_tag' => $request->name_tag
        ]);

        return response()->json([
            'status' => 'Success',
            'data' => $tag,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/tag/{id}",
     *     summary="Obtenir un tag par ID",
     *     tags={"Tags"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag trouvé",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
     *     )
     * )
     */
    public function show(Tag $tag)
    {
        return response()->json($tag);
    }

    /**
     * @OA\Put(
     *     path="/tag/{id}",
     *     summary="Mettre à jour un tag",
     *     tags={"Tags"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name_tag"},
     *             @OA\Property(property="name_tag", type="string", example="Tag mis à jour")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag mis à jour avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
     *     )
     * )
     */
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name_tag' => 'required|max:50',
        ]);

        $tag->update($request->all());

        return response()->json([
            "status" => "Mise à jour avec succès",
            "data" => $tag
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/tag/{id}",
     *     summary="Supprimer un tag",
     *     tags={"Tags"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Delete OK")
     *         )
     *     )
     * )
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }

    
}