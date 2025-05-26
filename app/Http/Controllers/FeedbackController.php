<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the feedbacks.
     */
    public function index()
    {
        try {
            $feedbacks = Feedback::with(['user', 'interpreter', 'interpretation', 'dream'])
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $feedbacks,
                'message' => 'Feedbacks retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve feedbacks.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created feedback in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                // Use uuid columns for existence check
                'user_id'          => 'required|exists:users,uuid',
                'interpreter_id'   => 'required|exists:interpreters,uuid',
                'interpretation_id'=> 'required|exists:interpretations,uuid',
                'dream_id'         => 'required|exists:dreams,uuid',
                'feedback_text'    => 'required|string|min:10',
                'rating'           => 'required|integer|min:1|max:5',
            ]);

            $feedback = Feedback::create([
                'user_id'          => $validated['user_id'], // Use Auth::id() in production if applicable
                'interpreter_id'   => $validated['interpreter_id'],
                'interpretation_id'=> $validated['interpretation_id'],
                'dream_id'         => $validated['dream_id'],
                'feedback_text'    => $validated['feedback_text'],
                'rating'           => $validated['rating'],
            ]);

            // Recalculate interpreter rating after creating feedback
            $feedback->interpreter->updateRating();

            return response()->json([
                'success' => true,
                'data' => $feedback,
                'message' => 'Feedback submitted successfully.'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit feedback.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified feedback by UUID.
     */
    public function show($uuid)
    {
        $feedback = Feedback::where('uuid', $uuid)
            ->with(['user', 'interpreter', 'interpretation', 'dream'])
            ->first();

        if (!$feedback) {
            return response()->json(['success' => false, 'message' => 'Feedback not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $feedback,
            'message' => 'Feedback retrieved successfully.'
        ]);
    }

    /**
     * Update the specified feedback in storage by UUID.
     */
    public function update(Request $request, $uuid)
    {
        $feedback = Feedback::where('uuid', $uuid)->first();

        if (!$feedback) {
            return response()->json(['success' => false, 'message' => 'Feedback not found'], 404);
        }

        $validated = $request->validate([
            'feedback_text' => 'sometimes|string|min:10',
            'rating'       => 'sometimes|integer|min:1|max:5',
        ]);

        $feedback->update($validated);

        if (isset($validated['rating'])) {
            $feedback->interpreter->updateRating();
        }

        return response()->json([
            'success' => true,
            'data' => $feedback,
            'message' => 'Feedback updated successfully.'
        ]);
    }

    /**
     * Remove the specified feedback from storage by UUID.
     */
    public function destroy($uuid)
    {
        $feedback = Feedback::where('uuid', $uuid)->first();

        if (!$feedback) {
            return response()->json(['success' => false, 'message' => 'Feedback not found'], 404);
        }

        $interpreter = $feedback->interpreter;
        $feedback->delete();

        $interpreter->updateRating();

        return response()->json([
            'success' => true,
            'message' => 'Feedback deleted successfully.'
        ]);
    }
}
