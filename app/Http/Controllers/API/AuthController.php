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

class AuthController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Register a new user and send a verification email.
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
     * Login a user and return a JWT token.
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
     * Logout the user and invalidate the token.
     */
    public function logout()
    {
        // Get the current token
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

        // Invalidate the token
        JWTAuth::invalidate($token);

        // Return a JSON response after logout
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'Successfully logged out',
            ],
            'data' => [],
        ]);
    }

    /**
     * Verify the user's email address using the signed URL.
     */
    public function verifyEmail(Request $request, $id)
    {
        // Check if the URL has a valid signature
        if (!$request->hasValidSignature()) {
            return redirect()->to('http://localhost:3000/email/verify?status=invalid_link');
        }

        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return redirect()->to('http://localhost:3000/email/verify?status=user_not_found');
        }

        // Check if the email is already verified
        if ($user->email_verified_at) {
            return redirect()->to('http://localhost:3000/email/verify?status=already_verified');
        }

        // Mark the email as verified
        try {
            $user->email_verified_at = now();
            $user->save();
        } catch (\Exception $e) {
            return redirect()->to('http://localhost:3000/email/verify?status=error');
        }

        // Redirect to React app with a success message
        return redirect()->to('http://localhost:3000/email/verify?status=success');
    }
}