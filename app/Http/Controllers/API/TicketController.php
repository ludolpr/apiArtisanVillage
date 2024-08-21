<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ticket = Ticket::all();
        return response()->json($ticket);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'title' => 'required|max:50',
            'ticket_content' => 'required|max:400',
            'id_user' => 'required'
        ]);

        $ticket = Ticket::create([
            'title' => $request->title,
            'ticket_content' => $request->ticket_content,
            'id_user' => $request->id_user
        ]);

        // JSON response
        return response()->json([
            'status' => 'Success',
            'data' => $ticket,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        return response()->json($ticket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $request->validate(['title' => 'required|max:50',
            'ticket_content' => 'required|max:400',
            'id_user' => 'required',

        ]);

        $ticket->update($request->all());

        return response()->json([
            "status" => "Mise à jour avec succèss",
            "data" => $ticket
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}