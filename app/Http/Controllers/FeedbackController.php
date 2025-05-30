<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use App\Models\Interpreter;
use App\Models\Interpretation;
use App\Models\Dream;
use Illuminate\Http\Request;

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
     * Store a newly created feedback using UUIDs.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id'           => 'required|exists:users,uuid',
                'interpreter_id'    => 'required|exists:interpreters,uuid',
                'interpretation_id' => 'required|exists:interpretations,uuid',
                'dream_id'          => 'required|exists:dreams,uuid',
                'feedback_text'     => 'required|string|min:10',
                'rating'            => 'required|integer|min:1|max:5',
            ]);

            // Convert UUIDs to internal IDs
            $userId = User::where('uuid', $validated['user_id'])->firstOrFail()->id;
            $interpreterId = Interpreter::where('uuid', $validated['interpreter_id'])->firstOrFail()->id;
            $interpretationId = Interpretation::where('uuid', $validated['interpretation_id'])->firstOrFail()->id;
            $dreamId = Dream::where('uuid', $validated['dream_id'])->firstOrFail()->id;

            $feedback = Feedback::create([
                'user_id'          => $userId,
                'interpreter_id'   => $interpreterId,
                'interpretation_id'=> $interpretationId,
                'dream_id'         => $dreamId,
                'feedback_text'    => $validated['feedback_text'],
                'rating'           => $validated['rating'],
            ]);

            // Update interpreter rating after new feedback
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
     * Display a specific feedback using UUID.
     */
    public function show(Request $request)
    {
        $uuid = $request->input('uuid');
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
     * Update feedback using UUID.
     */
    public function update(Request $request)
    {
        $uuid = $request->input('uuid');
        $feedback = Feedback::where('uuid', $uuid)->first();

        if (!$feedback) {
            return response()->json(['success' => false, 'message' => 'Feedback not found'], 404);
        }

        $validated = $request->validate([
            'feedback_text' => 'sometimes|string|min:10',
            'rating'        => 'sometimes|integer|min:1|max:5',
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
     * Delete feedback using UUID.
     */
    public function destroy(Request $request)
    {
        $uuid = $request->input('uuid');
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
