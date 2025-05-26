<?php

namespace App\Http\Controllers;

use App\Models\Interpreter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class InterpreterController extends Controller
{
    /**
     * Display a listing of the interpreters.
     */
    public function index()
    {
        // Return interpreters with uuid only, no sensitive data like password
        $interpreters = Interpreter::select('uuid', 'name', 'email', 'age', 'gender', 'country', 'city', 'status', 'years_of_experience', 'memorized_quran_parts', 'nationality')
            ->get();

        return response()->json($interpreters);
    }

    /**
     * Store a newly created interpreter.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|unique:interpreters,email',
            'password'             => 'required|string|min:6',
            'age'                  => 'nullable|integer|min:0',
            'gender'               => 'required|in:male,female',
            'country'              => 'required|string',
            'city'                 => 'required|string',
            'status'               => 'in:active,inactive,banned',
            'languages'            => 'required|array',
            'languages.*'          => 'required|string',
            'years_of_experience'  => 'required|integer|min:0',
            'memorized_quran_parts'=> 'required|integer|min:0|max:31',
            'nationality'          => 'required|string',
        ]);

        // Hash the password before saving
        $validated['password'] = Hash::make($validated['password']);

        $interpreter = Interpreter::create($validated);

        return response()->json($interpreter, 201);
    }

    /**
     * Display the specified interpreter by UUID.
     */
    public function show($uuid)
    {
        $interpreter = Interpreter::where('uuid', $uuid)->first();

        if (!$interpreter) {
            return response()->json(['message' => 'Interpreter not found'], 404);
        }

        // Hide password from output
        $interpreter->makeHidden(['password']);

        return response()->json($interpreter);
    }

    /**
     * Update the specified interpreter by UUID.
     */
    public function update(Request $request, $uuid)
    {
        $interpreter = Interpreter::where('uuid', $uuid)->first();

        if (!$interpreter) {
            return response()->json(['message' => 'Interpreter not found'], 404);
        }

        try {
            $validated = $request->validate([
                'name'                 => 'required|string|max:255',
                'email'                => 'required|email|unique:interpreters,email,' . $interpreter->id,
                'password'             => 'nullable|string|min:6',
                'age'                  => 'nullable|integer|min:0',
                'gender'               => 'required|in:male,female',
                'country'              => 'required|string',
                'city'                 => 'required|string',
                'status'               => 'in:active,inactive,banned',
                'languages'            => 'required|array',
                'languages.*'          => 'required|string',
                'years_of_experience'  => 'required|integer|min:0',
                'memorized_quran_parts'=> 'required|integer|min:0|max:31',
                'nationality'          => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $interpreter->update($validated);

        // Hide password before sending response
        $interpreter->makeHidden(['password']);

        return response()->json([
            'status' => 'success',
            'data' => $interpreter
        ]);
    }

    /**
     * Delete the specified interpreter by UUID.
     */
    public function destroy($uuid)
    {
        $interpreter = Interpreter::where('uuid', $uuid)->first();

        if (!$interpreter) {
            return response()->json(['message' => 'Interpreter not found'], 404);
        }

        $interpreter->delete();

        return response()->json(['message' => 'Interpreter deleted successfully.']);
    }
}
