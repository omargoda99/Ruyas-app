<?php

namespace App\Http\Controllers;

use App\Models\Dream;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class DreamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all dreams where 'is_chosen' is true, and include the related interpretation data
        $chosenDreams = Dream::where('is_shared', true)
                             ->orderBy('created_at', 'desc')  // Optional: Adjust sorting if needed
                             ->with('interpretation')  // Load interpretation data
                             ->get(['id', 'title', 'description']);  // Only select 'id', 'name', 'description'

        // Return the chosen dreams with interpretations as a JSON response
        return response()->json($chosenDreams);
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
    public function show(Request $request)
    {
        // Get the dream ID from the request
        $id = $request->input('id');

        // Find the dream by ID and eager load the interpretation
        $dream = Dream::with('interpretation')  // Load related interpretation
                      ->where('id', $id)       // Ensure the dream ID matches
                      ->first();               // Get the first result (single dream)

        // Check if the dream exists
        if (!$dream) {
            return response()->json(['message' => 'Dream not found'], 404);
        }

        // Return the dream and its interpretation as a JSON response
        return response()->json($dream);
    }

    // Update a dream
    public function update(Request $request)
    {
        $id = $request->input('id');
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
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $dream = Dream::find($id);

         // Check if the dream exists
         if (!$dream) {
            return response()->json(['message' => 'Dream not found'], 404);
        }
        $dream->delete();

        return response()->json(['message' => 'Dream deleted successfully.']);
    }



     // Add a dream to user's favorites
     public function addFavorite(Request $request)
     {
         $user = auth()->user(); // Get the currently authenticated user
         $dream = Dream::findOrFail($request->dream_id); // Find the dream by ID

         // Add the dream to the user's favorites
         $user->favoriteDreams()->attach($dream->id);

         return response()->json(['message' => 'Dream added to favorites']);
     }

     // Remove a dream from user's favorites
     public function removeFavorite(Request $request)
     {
         $user = auth()->user(); // Get the currently authenticated user
         $dream = Dream::findOrFail($request->dream_id); // Find the dream by ID

         // Remove the dream from the user's favorites
         $user->favoriteDreams()->detach($dream->id);

         return response()->json(['message' => 'Dream removed from favorites']);
     }

     // Get a user's favorite dreams
     public function getFavoriteDreams()
     {
         $user = auth()->user();
         $favoriteDreams = $user->favoriteDreams; // Get all the dreams favorited by the user

         return response()->json($favoriteDreams);
     }
     public function getMyDreams(Request $request)
     {
         // Get the authenticated user
         $user = auth()->user();

         // Get all dreams created by the authenticated user
         // Assuming there is a 'user_id' column on the 'dreams' table
         $myDreams = Dream::where('user_id', $user->id)
                          ->orderBy('created_at', 'desc') // Optional: you can sort by creation date
                          ->get(['id', 'name', 'description','is_explained']); // Only select 'id', 'name', 'description'

         // Return the dreams as a JSON response
         return response()->json($myDreams);
     }
}
