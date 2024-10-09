<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Schema(
 *     schema="Register",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00Z")
 * )
 */

class AuthController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @OA\Post(
     *     path="/register",
     *     operationId="registerUser",
     *     tags={"Register"},
     *     summary="Enregistrer un nouvel utilisateur",
     *     description="Enregistre un nouvel utilisateur et renvoie un jeton JWT.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name_user", "email", "password"},
     *             @OA\Property(property="name_user", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="picture_user", type="string", format="binary", description="Profile picture"),
     *             @OA\Property(property="id_role", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="code", type="integer", example=200),
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="message", type="string", example="User created successfully!")
     *             ),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="name_user", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                     @OA\Property(property="picture_user", type="string", example="profile_picture.jpg"),
     *                     @OA\Property(property="id_role", type="integer", example=1)
     *                 ),
     *                 @OA\Property(property="access_token", type="object",
     *                     @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
     *                     @OA\Property(property="type", type="string", example="Bearer"),
     *                     @OA\Property(property="expires_in", type="integer", example=3600)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Mauvaise demande",
     *         @OA\JsonContent(
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="code", type="integer", example=400),
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Validation error")
     *             )
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name_user' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'picture_user' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:5000',
            'id_role' => 'sometimes|integer|exists:roles,id',
        ]);

        // Handle the profile picture upload
        $filename = null;
        if ($request->hasFile('picture_user')) {
            $filenameWithExt = $request->file('picture_user')->getClientOriginalName();
            $filenameWithoutExt = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture_user')->getClientOriginalExtension();
            $filename = $filenameWithoutExt . '_' . time() . '.' . $extension;
            $path = $request->file('picture_user')->storeAs('public/uploads/users', $filename);
        }

        // Default role ID if not provided
        $roleId = $request->id_role ?? 1;

        // Create the user
        $user = $this->user::create([
            'name_user' => $request->name_user,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'picture_user' => $filename,
            'id_role' => $roleId,
        ]);

        // Generate a JWT token for the user
        $token = auth()->login($user);

        // Generate a signed URL for email verification
        $ficheUrl = URL::temporarySignedRoute(
            'verify',
            Carbon::now()->addMinutes(180),
            ['id' => $user->id]
        );

        // Send the verification email
        Mail::to($user->email)->send(new VerifyEmail($ficheUrl));

        // Return a JSON response with the token
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

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Connexion utilisateur",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="your_password_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie.",
     *         @OA\JsonContent(
     *             @OA\Property(property="meta", type="object", 
     *                 @OA\Property(property="code", type="integer", example=200),
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="message", type="string", example="Login successful.")
     *             ),
     *             @OA\Property(property="data", type="object", 
     *                 @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *                 @OA\Property(property="access_token", type="object", 
     *                     @OA\Property(property="token", type="string", example="your_access_token_here"),
     *                     @OA\Property(property="type", type="string", example="Bearer"),
     *                     @OA\Property(property="expires_in", type="integer", example=3600)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Email ou mot de passe invalide.",
     *         @OA\JsonContent(
     *             @OA\Property(property="meta", type="object", 
     *                 @OA\Property(property="code", type="integer", example=401),
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Invalid email or password")
     *             ),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Attempt to login and get a token
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

        // Return a JSON response with the token
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

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Déconnexion de l'utilisateur",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie.",
     *         @OA\JsonContent(
     *             @OA\Property(property="meta", type="object", 
     *                 @OA\Property(property="code", type="integer", example=200),
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="message", type="string", example="Logout successful.")
     *             ),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié.",
     *         @OA\JsonContent(
     *             @OA\Property(property="meta", type="object", 
     *                 @OA\Property(property="code", type="integer", example=401),
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="Unauthenticated.")
     *             ),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     )
     * )
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'Logout successful.',
            ],
            'data' => [],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/verify/email/{id}",
     *     summary="Vérifier l'e-mail de l'utilisateur",
     *     tags={"Register"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="E-mail vérifié avec succès.",
     *         @OA\JsonContent(
     *             @OA\Property(property="meta", type="object", 
     *                 @OA\Property(property="code", type="integer", example=200),
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="message", type="string", example="Email verified successfully.")
     *             ),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé.",
     *         @OA\JsonContent(
     *             @OA\Property(property="meta", type="object", 
     *                 @OA\Property(property="code", type="integer", example=404),
     *                 @OA\Property(property="status", type="string", example="error"),
     *                 @OA\Property(property="message", type="string", example="User not found.")
     *             ),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     )
     * )
     */
    public function verify($id)
    {
        $user = $this->user::find($id);

        if (!$user) {
            return response()->json([
                'meta' => [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'User not found.',
                ],
                'data' => [],
            ], 404);
        }

        // Assuming the user is verified if they exist
        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'Email verified successfully.',
            ],
            'data' => [],
        ]);
    }
}