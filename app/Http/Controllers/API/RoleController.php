<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="Role",
 *     type="object",
 *     required={"name_role"},
 *     @OA\Property(property="id", type="integer", example=1, description="ID du rôle"),
 *     @OA\Property(property="name_role", type="string", example="Admin", description="Nom du rôle"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Date de création"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Date de mise à jour"),
 * )
 */

class RoleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/role",
     *     summary="Obtenir la liste des rôles",
     *     tags={"Roles"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des rôles",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Role"))
     *     )
     * )
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    /**
     * @OA\Post(
     *     path="/role",
     *     summary="Créer un nouveau rôle",
     *     tags={"Roles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name_role"},
     *             @OA\Property(property="name_role", type="string", example="Admin", description="Nom du rôle")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rôle créé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Role")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_role' => 'required|max:50',
        ]);

        $role = Role::create([
            'name_role' => $request->name_role,
        ]);

        return response()->json([
            'status' => 'Success',
            'data' => $role,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/role/{id}",
     *     summary="Afficher un rôle spécifique",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du rôle",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du rôle",
     *         @OA\JsonContent(ref="#/components/schemas/Role")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rôle non trouvé"
     *     )
     * )
     */
    public function show(Role $role)
    {
        return response()->json($role);
    }

    /**
     * @OA\Put(
     *     path="/role/{id}",
     *     summary="Mettre à jour un rôle",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du rôle",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name_role"},
     *             @OA\Property(property="name_role", type="string", example="Admin", description="Nom du rôle")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rôle mis à jour avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Role")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rôle non trouvé"
     *     )
     * )
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name_role' => 'required|max:50',
        ]);

        $role->update($request->all());

        return response()->json(["status" => "Mise à jour avec succès",
            "data" => $role
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/role/{id}",
     *     summary="Supprimer un rôle",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du rôle",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rôle supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rôle non trouvé"
     *     )
     * )
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}