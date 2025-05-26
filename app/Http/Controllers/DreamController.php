<?php

namespace App\Http\Controllers;

use App\Models\Dream;
use App\Models\User;
use Illuminate\Http\Request;

class DreamController extends Controller
{
    /**
     * Display a listing of all shared dreams with their interpretation.
     * Returns dreams with UUID, title, description.
     */
    public function index()
    {
        $chosenDreams = Dream::where('is_shared', true)
                        ->with('interpretation')
                        ->orderBy('created_at', 'desc')
                        ->get(['uuid', 'title', 'description']);

        return response()->json($chosenDreams);
    }

    /**
     * Store a new dream.
     * Accepts 'user_uuid' instead of user_id.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_uuid'     => 'required|exists:users,uuid',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'is_favorite'   => 'boolean',
            'is_shared'     => 'boolean',
            'is_explained'  => 'boolean',
        ]);

        // Find user by UUID to get internal ID for foreign key
        $user = User::where('uuid', $validated['user_uuid'])->firstOrFail();

        $dreamData = $validated;
        unset($dreamData['user_uuid']);
        $dreamData['user_id'] = $user->id;

        $dream = Dream::create($dreamData);

        return response()->json($dream, 201);
    }

    /**
     * Show a specific dream by UUID.
     */
    public function show($uuid)
    {
        $dream = Dream::with('interpretation')->where('uuid', $uuid)->first();

        if (!$dream) {
            return response()->json(['message' => 'Dream not found'], 404);
        }

        return response()->json($dream);
    }

    /**
     * Update a dream by UUID.
     */
    public function update(Request $request, $uuid)
    {
        $dream = Dream::where('uuid', $uuid)->firstOrFail();

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

    /**
     * Delete a dream by UUID.
     */
    public function destroy($uuid)
    {
        $dream = Dream::where('uuid', $uuid)->first();

        if (!$dream) {
            return response()->json(['message' => 'Dream not found'], 404);
        }

        $dream->delete();

        return response()->json(['message' => 'Dream deleted successfully.']);
    }

    /**
     * Add a dream to the authenticated user's favorites.
     * Dream identified by UUID.
     */
    public function addFavorite(Request $request)
    {
        $user = auth()->user();

        $dream = Dream::where('uuid', $request->dream_uuid)->firstOrFail();

        // Attach dream internal id to user's favorites pivot
        $user->favoriteDreams()->syncWithoutDetaching($dream->id);

        return response()->json(['message' => 'Dream added to favorites']);
    }

    /**
     * Remove a dream from the authenticated user's favorites.
     * Dream identified by UUID.
     */
    public function removeFavorite(Request $request)
    {
        $user = auth()->user();

        $dream = Dream::where('uuid', $request->dream_uuid)->firstOrFail();

        $user->favoriteDreams()->detach($dream->id);

        return response()->json(['message' => 'Dream removed from favorites']);
    }

    /**
     * Get all favorite dreams of the authenticated user.
     * Returns dreams with UUID, title, description.
     */
    public function getFavoriteDreams()
    {
        $user = auth()->user();

        $favoriteDreams = $user->favoriteDreams()
                              ->orderBy('created_at', 'desc')
                              ->get(['uuid', 'title', 'description', 'is_explained']);

        return response()->json($favoriteDreams);
    }

    /**
     * Get all dreams created by the authenticated user.
     * Returns dreams with UUID, title, description.
     */
    public function getMyDreams()
    {
        $user = auth()->user();

        $myDreams = Dream::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get(['uuid', 'title', 'description', 'is_explained']);

        return response()->json($myDreams);
    }
}
