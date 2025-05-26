<?php

namespace App\Http\Controllers;

use App\Models\Dream;
use App\Models\User;
use Illuminate\Http\Request;

class DreamController extends Controller
{
    // Display a listing of all shared dreams
    public function index()
    {
        $chosenDreams = Dream::where('is_shared', true)
                        ->with('interpretation')
                        ->orderBy('created_at', 'desc')
                        ->get(['dreams.uuid', 'title', 'description']);

        return response()->json($chosenDreams);
    }

    // Store a new dream using user UUID
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,uuid',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'is_favorite'   => 'boolean',
            'is_shared'     => 'boolean',
            'is_explained'  => 'boolean',
        ]);

        $user = User::where('uuid', $validated['user_id'])->firstOrFail();

        $dreamData = $validated;
        unset($dreamData['user_id']);
        $dreamData['user_id'] = $user->id;

        $dream = Dream::create($dreamData);

        return response()->json($dream, 201);
    }

    // Show a specific dream by UUID
    public function show(Request $request)
    {
        $uuid = $request->input('uuid');
        $dream = Dream::with('interpretation')->where('uuid', $uuid)->first();

        if (!$dream) {
            return response()->json(['message' => 'Dream not found'], 404);
        }

        return response()->json($dream);
    }

    // Update a dream by UUID
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

    // Delete a dream by UUID
    public function destroy(Request $request)
    {
        $uuid = $request->input('uuid');
        $dream = Dream::where('uuid', $uuid)->first();

        if (!$dream) {
            return response()->json(['message' => 'Dream not found'], 404);
        }

        $dream->delete();

        return response()->json(['message' => 'Dream deleted successfully.']);
    }

    // Add a dream to favorites (UUID-based)
    public function addFavorite(Request $request)
    {
        $uuid = $request->input('uuid');
        $user = auth()->user();

        $dream = Dream::where('uuid', $uuid)->firstOrFail();

        $user->favoriteDreams()->syncWithoutDetaching($dream->id);

        return response()->json(['message' => 'Dream added to favorites']);
    }

    // Remove a dream from favorites (UUID-based)
    public function removeFavorite(Request $request)
    {
        $uuid = $request->input('uuid');
        $user = auth()->user();

        $dream = Dream::where('uuid', $uuid)->firstOrFail();

        $user->favoriteDreams()->detach($dream->id);

        return response()->json(['message' => 'Dream removed from favorites']);
    }

    // ✅ Fix ambiguous UUID issue by prefixing table name
    public function getFavoriteDreams()
    {
        $user = auth()->user();

        $favoriteDreams = $user->favoriteDreams()
            ->orderBy('dreams.created_at', 'desc') // fully qualify to avoid ambiguity
            ->get(['dreams.uuid', 'dreams.title', 'dreams.description', 'dreams.is_explained']);

        return response()->json($favoriteDreams);
    }

    // Get dreams created by the authenticated user
    public function getMyDreams()
    {
        $user = auth()->user();

        $myDreams = Dream::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get(['uuid', 'title', 'description', 'is_explained']);

        return response()->json($myDreams);
    }
}
