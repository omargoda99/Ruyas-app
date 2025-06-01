<?php

namespace App\Http\Controllers;

use App\Models\Complain;
use App\Models\Interpreter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ComplainController extends Controller
{
    /**
     * List all complains.
     */
    public function index()
    {
        $complains = Complain::orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $complains,
        ]);
    }
    // return back user's complains
    public function indexUser()
    {
        $userUuid = auth()->user()->uuid;

        $complains = Complain::where('user_uuid', $userUuid)
                            ->orderBy('created_at', 'desc')
                            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $complains,
        ]);
    }


    /**
     * Store a new complain using UUIDs.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'complain_title' => 'required|string|min:5',
                'complain_text'  => 'required|string|min:10',
            ]);

            $user = auth()->user(); // Get authenticated user

            $complain = Complain::create([
                'uuid'           => (string) Str::uuid(),
                'user_uuid'      => $user->uuid,
                'complain_title' => $validated['complain_title'],
                'complain_text'  => $validated['complain_text'],
                'status'         => 'pending',
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Complain submitted successfully.',
                'data'    => $complain,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display a single complain using UUID.
     */
    public function show(Request $request)
    {
        $uuid = $request->input('uuid');
        $complain = Complain::with(['user'])
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
                'status'         => 'nullable|in:pending,resolved,closed',
                'complain_title' => 'nullable|string|min:5',
                'complain_text'  => 'nullable|string|min:10',
            ]);

            // Optional: authorize the owner
            if ($complain->user_uuid !== auth()->user()->uuid) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Unauthorized to update this complaint.',
                ], 403);
            }

            $complain->update(array_filter($validated)); // Ignore null fields

            return response()->json([
                'status'  => 'success',
                'message' => 'Complain updated successfully.',
                'data'    => $complain,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
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
