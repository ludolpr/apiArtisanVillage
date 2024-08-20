<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tag = Tag::all();
        return response()->json($tag);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_tag' => 'required|max:50',
        ]);

        $tag = Tag::create([
            'name_tag' => $request->name_tag
        ]);

        // JSON response
        return response()->json([
            'status' => 'Success',
            'data' => $tag,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        return response()->json($tag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name_tag' => 'required|max:50',
        ]);

        $tag->update($request->all());

        return response()->json([
            "status" => "Mise à jour avec succèss",
            "data" => $tag
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}
