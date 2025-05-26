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
     * Display a single complain using UUID.
     */
    public function show($uuid)
    {
        $complain = Complain::with(['user', 'interpreter'])->where('uuid', $uuid)->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $complain,
        ]);
    }

    /**
     * Update a complain using UUID.
     */
    public function update(Request $request, $uuid)
    {
        $complain = Complain::where('uuid', $uuid)->firstOrFail();

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
     * Delete a complain using UUID.
     */
    public function destroy($uuid)
    {
        $complain = Complain::where('uuid', $uuid)->firstOrFail();
        $complain->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Complain deleted successfully.',
        ]);
    }
}
