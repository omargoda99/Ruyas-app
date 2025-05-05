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
    

    public function show($id)
    {
        $interpretation = Interpretation::findOrFail($id);
        return response()->json($interpretation);
    }

    public function showApproved($id)
    {
        $interpretation = Interpretation::where('id', $id)
            ->where('is_approved', true)
            ->firstOrFail();

        return response()->json($interpretation);
    }

    public function indexByDream($dreamId)
    {
        $interpretations = Interpretation::where('dream_id', $dreamId)->get();
        return response()->json($interpretations);
    }

    public function indexByInterpreter($interpreterId)
    {
        $interpretations = Interpretation::where('interpreter_id', $interpreterId)->get();
        return response()->json($interpretations);
    }

    public function indexByUser($userId)
    {
        $interpretations = Interpretation::whereHas('dream', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return response()->json($interpretations);
    }

    public function indexApproved()
    {
        $interpretations = Interpretation::where('is_approved', true)->get();
        return response()->json($interpretations);
    }

    public function indexUnapproved()
    {
        $interpretations = Interpretation::where('is_approved', false)->get();
        return response()->json($interpretations);
    }

    public function approve($id)
    {
        $interpretation = Interpretation::findOrFail($id);
        $interpretation->is_approved = true;
        $interpretation->save();

        return response()->json($interpretation);
    }

    public function destroy($id)
    {
        $interpretation = Interpretation::findOrFail($id);
        $interpretation->delete();

        return response()->json(['message' => 'Interpretation deleted successfully.']);
    }
}
