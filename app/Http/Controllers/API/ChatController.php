<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chat = Chat::all();
        return response()->json($chat);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_chat' => 'required|max:50',
            'created_date' => 'required',
            'id_user' =>  'required'
        ]);

        $chat = Chat::create(['name_chat' => $request->name_chat,
            'created_date' => $request->created_date,
            'id_user' => $request->id_user

        ]);

        // JSON response
        return response()->json([
            'status' => 'Success',
            'data' => $chat,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chat $chat)
    {
        return response()->json($chat);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chat $chat)
    {
        $request->validate([
            'name_chat' => 'required|max:50',
            'created_date' => 'required',
            'id_user' =>  'required'
        ]);

        $chat->update($request->all());

        return response()->json([
            "status" => "Mise à jour avec succèss",
            "data" => $chat
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chat $chat)
    {
        $chat->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}