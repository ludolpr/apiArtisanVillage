<?php

namespace App\Http\Controllers\API;

use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $messages = Message::all();
        return response()->json($messages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Message $message)
    {

        $request->validate([
            'message_content' => 'required',
            'id_chat' => 'required',
        ]);

        $messages = Message::create([
            'message_content' => $request->message_content,
            'id_chat' => $request->id_chat
        ]);

        // JSON response
        return response()->json([
            'status' => 'Success',
            'data' => $messages,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        return response()->json($message);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        $request->validate([
            'message_content' => 'required',
            'id_chat' => 'required',
        ]);

        $message->update($request->all());

        return response()->json([
            "status" => "Mise à jour avec succèss",
            "data" => $message
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        $message->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}