<?php

namespace App\Http\Controllers;

use App\Models\Dream;
use Illuminate\Http\Request;

class DreamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Dream::with('user')->get());
    }

    // Store a new dream
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'is_favorite'   => 'boolean',
            'is_shared'     => 'boolean',
            'is_explained'  => 'boolean',
        ]);

        $dream = Dream::create($validated);

        return response()->json($dream, 201);
    }

    // Show a specific dream
    public function show($id)
    {
        $dream = Dream::with('user')->findOrFail($id);
        return response()->json($dream);
    }

    // Update a dream
    public function update(Request $request, $id)
    {
        $dream = Dream::findOrFail($id);

        $validated = $request->validate([
            'title'         => 'sometimes|string|max:255',
            'description'   => 'nullable|string',
            'is_favorite'   => 'boolean',
            'is_shared'     => 'boolean',
            'is_explained'  => 'boolean',
        ]);

        $dream->update($validated);

        return response()->json($dream);
    }

    // Delete a dream
    public function destroy($id)
    {
        $dream = Dream::findOrFail($id);
        $dream->delete();

        return response()->json(['message' => 'Dream deleted successfully.']);
    }
}
