<?php

namespace App\Http\Controllers;

use App\Models\Interpretation;
use Illuminate\Http\Request;

class InterpretationController extends Controller
{
    public function index()
    {
        // Just list all interpretations
        return response()->json(Interpretation::all());
    }

    public function store(Request $request)
    {
        // Validate by UUID columns, not integer ids
        $request->validate([
            'dream_id'      => 'required|exists:dreams,uuid',
            'interpreter_id'=> 'required|exists:interpreters,uuid',
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

    public function show($uuid)
    {
        $interpretation = Interpretation::where('uuid', $uuid)->firstOrFail();
        return response()->json($interpretation);
    }

    public function showApproved($uuid)
    {
        $interpretation = Interpretation::where('uuid', $uuid)
            ->where('is_approved', true)
            ->firstOrFail();

        return response()->json($interpretation);
    }

    public function indexByDream($dreamUuid)
    {
        // Filter by dream UUID (foreign key)
        $interpretations = Interpretation::where('dream_id', $dreamUuid)->get();
        return response()->json($interpretations);
    }

    public function indexByInterpreter($interpreterUuid)
    {
        // Filter by interpreter UUID (foreign key)
        $interpretations = Interpretation::where('interpreter_id', $interpreterUuid)->get();
        return response()->json($interpretations);
    }

    public function indexByUser($userUuid)
    {
        // Assuming 'dreams' table has a user_id column that now stores UUIDs,
        // filter interpretations where related dream's user_id matches
        $interpretations = Interpretation::whereHas('dream', function ($query) use ($userUuid) {
            $query->where('user_id', $userUuid);
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

    public function approve($uuid)
    {
        $interpretation = Interpretation::where('uuid', $uuid)->firstOrFail();
        $interpretation->is_approved = true;
        $interpretation->save();

        return response()->json($interpretation);
    }

    public function destroy($uuid)
    {
        $interpretation = Interpretation::where('uuid', $uuid)->firstOrFail();
        $interpretation->delete();

        return response()->json(['message' => 'Interpretation deleted successfully.']);
    }
}
