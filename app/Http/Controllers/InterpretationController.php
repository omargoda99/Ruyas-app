<?php

namespace App\Http\Controllers;

use App\Models\Interpretation;
use Illuminate\Http\Request;

class InterpretationController extends Controller
{
    public function index()
    {
        return response()->json(Interpretation::all());
    }
    /**
     * Display a listing of the resource.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dream_id'      => 'required|exists:dreams,id',
            'interpreter_id'=> 'required|exists:interpreters,id',
            'content'       => 'required|string',
        ]);

        $interpretation = Interpretation::create([
            'dream_id'      => $request->dream_id,
            'interpreter_id'=> $request->interpreter_id,
            'content'       => $request->content,
            'is_approved'   => $request->is_approved ?? false,
        ]);

        return response()->json($interpretation, 201);
    }

    /**
     * Show the interpretation of a dream.
     */
    public function show($dreamId)
    {
        $interpretation = Interpretation::where('dream_id', $dreamId)->firstOrFail();

        return response()->json($interpretation);
    }

    /**
     * Approve an interpretation.
     */
    public function approve($id)
    {
        $interpretation = Interpretation::findOrFail($id);
        $interpretation->is_approved = true;
        $interpretation->save();

        return response()->json($interpretation);
    }

    /**
     * Delete an interpretation.
     */
    public function destroy($id)
    {
        $interpretation = Interpretation::findOrFail($id);
        $interpretation->delete();

        return response()->json(['message' => 'Interpretation deleted successfully.']);
    }
}
