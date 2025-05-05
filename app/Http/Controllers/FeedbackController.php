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
                // In production, you would use Auth::id() instead
                'user_id' => 'required|exists:users,id',
                'interpreter_id' => 'required|exists:interpreters,id',
                'interpretation_id' => 'required|exists:interpretations,id',
                'dream_id' => 'required|exists:dreams,id',
                'feedback_text' => 'required|string|min:10',
                'rating' => 'required|integer|min:1|max:5',
            ]);

            $feedback = Feedback::create([
                'user_id' => $validated['user_id'], // Replace with Auth::id() in production
                'interpreter_id' => $validated['interpreter_id'],
                'interpretation_id' => $validated['interpretation_id'],
                'dream_id' => $validated['dream_id'],
                'feedback_text' => $validated['feedback_text'],
                'rating' => $validated['rating'],
            ]);

            // Recalculate interpreter rating
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
     * Display the specified feedback.
     */
    public function show(Feedback $feedback)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $feedback->load(['user', 'interpreter', 'interpretation', 'dream']),
                'message' => 'Feedback retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve feedback.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified feedback in storage.
     */
    public function update(Request $request, Feedback $feedback)
    {
        try {
            $validated = $request->validate([
                'feedback_text' => 'sometimes|string|min:10',
                'rating' => 'sometimes|integer|min:1|max:5',
            ]);

            $feedback->update($validated);

            // Recalculate interpreter rating if rating was updated
            if (isset($validated['rating'])) {
                $feedback->interpreter->updateRating();
            }

            return response()->json([
                'success' => true,
                'data' => $feedback,
                'message' => 'Feedback updated successfully.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update feedback.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified feedback from storage.
     */
    public function destroy(Feedback $feedback)
    {
        try {
            $interpreter = $feedback->interpreter;
            $feedback->delete();

            // Recalculate interpreter rating after deletion
            $interpreter->updateRating();

            return response()->json([
                'success' => true,
                'message' => 'Feedback deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete feedback.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
