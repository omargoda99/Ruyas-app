<?php

namespace App\Http\Controllers;

use App\Models\Complain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplainController extends Controller
{
    /**
     * Display a listing of the complains.
     */
    public function index()
    {
        $complains = Complain::with(['user', 'interpreter'])->latest()->get();
        return response()->json($complains);
    }

    /**
     * Store a newly created complain in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'interpreter_id' => 'required|exists:interpreters,id',
            'complain_text' => 'required|string|min:10',
        ]);

        $complain = Complain::create([
            'user_id' => Auth::id(), // or $request->user()->id
            'interpreter_id' => $validated['interpreter_id'],
            'complain_text' => $validated['complain_text'],
            'status' => Complain::STATUS_PENDING,
        ]);

        return response()->json([
            'message' => 'Complain submitted successfully.',
            'complain' => $complain,
        ], 201);
    }

    /**
     * Display the specified complain.
     */
    public function show(Complain $complain)
    {
        return response()->json($complain->load(['user', 'interpreter']));
    }

    /**
     * Update the specified complain in storage.
     */
    public function update(Request $request, Complain $complain)
    {
        $validated = $request->validate([
            'status' => 'in:pending,resolved,closed',
        ]);

        $complain->update($validated);

        return response()->json([
            'message' => 'Complain updated.',
            'complain' => $complain,
        ]);
    }

    /**
     * Remove the specified complain from storage.
     */
    public function destroy(Complain $complain)
    {
        $complain->delete();

        return response()->json(['message' => 'Complain deleted.']);
    }
}
