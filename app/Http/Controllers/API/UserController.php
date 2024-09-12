<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the currently authenticated user.
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
     * Display a listing of the users.
     */
    public function index()
    {
        // Retrieve all users from the database
        $users = User::all();
        // Return the users as a JSON response
        return response()->json($users, 200);
    }

    // /**
    //  * Store a newly created user in the database.
    //  */
    // public function store(Request $request)
    // {
    //     // Validate the request inputs
    //     $request->validate([
    //         'name_user' => 'required|max:100',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|min:8',
    //         'picture_user' => 'required|image|max:5000',
    //         'id_role' => 'sometimes|integer|exists:roles,id',
    //     ]);

    //     // Handle file upload
    //     $filename = "";
    //     if ($request->hasFile('picture_user')) {
    //         $filenameWithExt = $request->file('picture_user')->getClientOriginalName();
    //         $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
    //         $extension = $request->file('picture_user')->getClientOriginalExtension();
    //         $filename = $filename . '_' . time() . '.' . $extension;
    //         $request->file('picture_user')->storeAs('public/uploads/users', $filename);
    //     }


    //     // Default role ID if not provided
    //     $roleId = $request->id_role ?? 1;

    //     // Create the user in the database
    //     $user = User::create([
    //         'name_user' => $request->name_user,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'picture_user' => $filename,
    //         'id_role' => $roleId,
    //     ]);

    //     // Return the created user in JSON
    //     return response()->json([
    //         'status' => 'Success',
    //         'data' => $user,
    //     ], 201);
    // }

    /**
     * Display a specific user.
     */
    public function show(User $user)
    {
        return response()->json($user, 200);
    }

    /**
     * Update a user in the database.
     */
    public function update(Request $request, User $user)
    {
        // dd($request);
        // Validate the request inputs
        $request->validate([
            'name_user' => 'required',
            'email' => 'required',
            'id_role' => 'required',
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
        // Update user  data
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
     * Remove a user from the database.
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