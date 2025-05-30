<?php

namespace App\Http\Controllers;

use App\Models\Complain;
use App\Models\User;
use App\Models\Interpreter;
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
     * Store a new complain using UUIDs.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id'        => 'required|exists:users,uuid',
                'interpreter_id' => 'required|exists:interpreters,uuid',
                'complain_text'  => 'required|string|min:10',
            ]);

            // Convert UUIDs to internal numeric IDs
            $user = User::where('uuid', $validated['user_id'])->firstOrFail();
            $interpreter = Interpreter::where('uuid', $validated['interpreter_id'])->firstOrFail();

            $complain = Complain::create([
                'user_id'        => $user->id,
                'interpreter_id' => $interpreter->id,
                'complain_text'  => $validated['complain_text'],
                'status'         => Complain::STATUS_PENDING,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Complain submitted successfully.',
                'data' => $complain,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a single complain using UUID.
     */
    public function show(Request $request)
    {
        $uuid = $request->input('uuid');
        $complain = Complain::with(['user', 'interpreter'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $complain,
        ]);
    }

    /**
     * Update a complain using UUID.
     */
    public function update(Request $request)
    {
        $uuid = $request->input('uuid');
        $complain = Complain::where('uuid', $uuid)->firstOrFail();

        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,resolved,closed',
            ]);

            $complain->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Complain updated successfully.',
                'data' => $complain,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Delete a complain using UUID.
     */
    public function destroy(Request $request)
    {
        $uuid = $request->input('uuid');
        $complain = Complain::where('uuid', $uuid)->firstOrFail();
        $complain->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Complain deleted successfully.',
        ]);
    }
}
