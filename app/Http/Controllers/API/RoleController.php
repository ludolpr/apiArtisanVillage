<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Role $roles)
    {
        $request->validate([
            'name_role' => 'required|max:50',
        ]);

        $roles = Role::create([
            'name_role' => $request->name_role
        ]);

        // JSON response
        return response()->json([
            'status' => 'Success',
            'data' => $roles,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name_role' => 'required|max:50',
        ]);

        $role->update($request->all());

        return response()->json([
            "status" => "Mise à jour avec succèss",
            "data" => $role
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}