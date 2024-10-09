<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"name_user", "email", "id_role"},
 *     @OA\Property(property="id", type="integer", example=1, description="ID de l'utilisateur"),
 *     @OA\Property(property="name_user", type="string", example="Nom d'utilisateur", description="Nom de l'utilisateur"),
 *     @OA\Property(property="email", type="string", example="user@example.com", description="Email de l'utilisateur"),
 *     @OA\Property(property="id_role", type="integer", example=1, description="ID du rôle de l'utilisateur"),
 *     @OA\Property(property="picture_user", type="string", example="profile.jpg", description="Nom du fichier de l'image de l'utilisateur"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Date de création"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Date de mise à jour"),
 * )
 */
class UserController extends Controller
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @OA\Get(
     *     path="/currentuser",
     *     summary="Obtenir l'utilisateur authentifié",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="code", type="integer", example=200),
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="message", type="string", example="User fetched successfully!")
     *             ),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/User")
     *             )
     *         )
     *     )
     * )
     */
    public function currentUser(Request $request)
    {
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'User fetched successfully!',
            ],
            'data' => [
                'user' => $request->user(),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Obtenir la liste des utilisateurs",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des utilisateurs",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */
    public function index()
    {
        // Retrieve all users from the database
        $users = User::all();
        // Return the users as a JSON response
        return response()->json($users, 200);
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Obtenir un utilisateur par ID",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur trouvé",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     )
     * )
     */
    public function show(User $user)
    {
        return response()->json($user, 200);
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Mettre à jour un utilisateur",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name_user", "email", "id_role"},
     *             @OA\Property(property="name_user", type="string", example="Nom d'utilisateur"),
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="id_role", type="integer", example=1),
     *             @OA\Property(property="picture_user", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur mis à jour avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     )
     * )
     */
    public function update(Request $request, User $user)
    {
        // Validate the request inputs
        $request->validate([
            'name_user' => 'required',
            'email' => 'required',
            'id_role' => 'required',
            'picture_user' => 'nullable',
        ]);
        
        $filename =  $user->picture_user;
        // Handle file upload and delete old file
        if ($request->hasFile('picture_user')) {
            if ($user->picture_user) {
                Storage::delete('public/uploads/users/' . $user->picture_user);
            }
            $filenameWithExt = $request->file('picture_user')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture_user')->getClientOriginalExtension();
            $filename = $filename . '_' . time() . '.' . $extension;
            $request->file('picture_user')->storeAs('public/uploads/users', $filename);
            $user->picture_user = $filename;
        }
        // Update user data
        $user->update([
            'name_user' => $request->name_user,
            'email' => $request->email,
            'picture_user' => $filename,
            'id_role' => $user->id_role,
        ]);

        // Return the updated user in JSON
        return response()->json([
            'status' => 'Update OK',
            'data' => $user,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Supprimer un utilisateur",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Delete OK")
     *         )
     *     )
     * )
     */
    public function destroy(User $user)
    {
        // Delete the user picture if exists
        if ($user->picture_user) {
            Storage::delete('public/uploads/users/' . $user->picture_user);
        }

        // Delete the user
        $user->delete();

        // Return the response
        return response()->json([
            'status' => 'Delete OK',
        ], 200);
    }

   
}