<?php

namespace App\Http\Controllers;

use App\Models\Complain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ComplainController extends Controller
{
    /**
     * List all complains.
     */
    public function index()
    {
        $complains = Complain::with(['user', 'interpreter'])->latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $complains
        ]);
    }

    /**
     * Store a new complain.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                // temporarily for testing
                'user_id' => 'required|exists:users,id',
                //--------
                'interpreter_id' => 'required|exists:interpreters,id',
                'complain_text' => 'required|string|min:10',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $complain = Complain::create([
            'user_id' => $validated['user_id'],
            // temporarily for testing
            'interpreter_id' => $validated['interpreter_id'],
            'complain_text' => $validated['complain_text'],
            'status' => Complain::STATUS_PENDING,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Complain submitted successfully.',
            'data' => $complain,
        ], 201);
    }

    /**
     * Display a single complain.
     */
    public function show($id)
    {
        $complain = Complain::with(['user', 'interpreter'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $complain,
        ]);
    }

    /**
     * Update a complain.
     */
    public function update(Request $request, $id)
    {
        $complain = Complain::findOrFail($id);

        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,resolved,closed',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $complain->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Complain updated successfully.',
            'data' => $complain,
        ]);
    }

    /**
     * Delete a complain.
     */
    public function destroy($id)
    {
        $complain = Complain::findOrFail($id);
        $complain->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Complain deleted successfully.',
        ]);
    }
}
