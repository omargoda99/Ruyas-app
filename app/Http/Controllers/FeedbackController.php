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
        $feedbacks = Feedback::with(['user', 'interpreter', 'interpretation', 'dream'])->latest()->get();
        return response()->json($feedbacks);
    }

    /**
     * Store a newly created feedback in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'interpreter_id' => 'required|exists:interpreters,id',
            'interpretation_id' => 'required|exists:interpretations,id',
            'dream_id' => 'required|exists:dreams,id',
            'feedback_text' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $feedback = Feedback::create([
            'user_id' => Auth::id(),
            'interpreter_id' => $validated['interpreter_id'],
            'interpretation_id' => $validated['interpretation_id'],
            'dream_id' => $validated['dream_id'],
            'feedback_text' => $validated['feedback_text'],
            'rating' => $validated['rating'],
        ]);

        // Recalculate interpreter rating
        $feedback->interpreter->updateRating();

        return response()->json([
            'message' => 'Feedback submitted successfully.',
            'feedback' => $feedback,
        ], 201);
    }

    /**
     * Display the specified feedback.
     */
    public function show(Feedback $feedback)
    {
        return response()->json($feedback->load(['user', 'interpreter', 'interpretation', 'dream']));
    }

    /**
     * Update the specified feedback in storage.
     */
    public function update(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'feedback_text' => 'sometimes|string|min:10',
            'rating' => 'sometimes|integer|min:1|max:5',
        ]);

        $feedback->update($validated);

        // Recalculate interpreter rating
        $feedback->interpreter->updateRating();

        return response()->json([
            'message' => 'Feedback updated.',
            'feedback' => $feedback,
        ]);
    }

    /**
     * Remove the specified feedback from storage.
     */
    public function destroy(Feedback $feedback)
    {
        $interpreter = $feedback->interpreter; // save interpreter reference
        $feedback->delete();

        // Recalculate interpreter rating after deletion
        $interpreter->updateRating();

        return response()->json(['message' => 'Feedback deleted.']);
    }
}
