<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function register(Request $request)
    {
        // Validation des champs
        $request->validate([
            'name_user' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'picture_user' => 'nullable|max:5000',
            'id_role' => 'sometimes|integer|exists:roles,id',
        ]);

        // Valeur par défaut pour le rôle
        $roleId = $request->id_role ?? 1;

        // Gestion de l'image de profil
        $filename = "";
        if ($request->hasFile('picture_user')) {
            $filenameWithExt = $request->file('picture_user')->getClientOriginalName();
            $filenameWithoutExt = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture_user')->getClientOriginalExtension();
            $filename = $filenameWithoutExt . '_' . time() . '.' . $extension;
            $path = $request->file('picture_user')->storeAs('public/uploads/users', $filename);
        } else {
            $filename = null;
        }
        //valeur de l'user
        $roleId = $request->id_role ?? 1;

        // Création de l'utilisateur
        $user = $this->user::create([
            'name_user' => $request->name_user,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'picture_user' => $filename,
            'id_role' => $roleId,
        ]);

        // Génération du token JWT
        $token = auth()->login($user);

        // Réponse JSON avec le token
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'User created successfully!',
            ],
            'data' => [
                'user' => $user,
                'access_token' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expires_in' => auth()->factory()->getTTL() * 3600,
                ],
            ],
        ]);
    }

    public function login(Request $request)
    {
        // Validation des champs
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Tentative de connexion
        if (!$token = auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'meta' => [
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Invalid email or password',
                ],
                'data' => [],
            ], 401);
        }

        // Réponse JSON avec le token
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'Login successful.',
            ],
            'data' => [
                'user' => auth()->user(),
                'access_token' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expires_in' => auth()->factory()->getTTL() * 3600,
                ],
            ],
        ]);
    }

    public function logout()
    {
        // Récupération du token JWT
        $token = JWTAuth::getToken();
        if (!$token) {
            return response()->json([
                'meta' => [
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Token not provided',
                ],
                'data' => [],
            ], 401);
        }

        // Invalidation du token
        JWTAuth::invalidate($token);

        // Réponse JSON après déconnexion
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'Successfully logged out',
            ],
            'data' => [],
        ]);
    }
}
