<?php

namespace App\Http\Controllers;

use App\Models\Interpreter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InterpreterController extends Controller
{
    /**
     * Display a listing of the interpreters.
     */
    public function index()
    {
        return response()->json(Interpreter::all());
    }

    /**
     * Store a newly created interpreter.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:interpreters,email',
            'password'            => 'required|string|min:6',
            'age'                 => 'nullable|integer|min:0',
            'gender'              => 'required|in:male,female',
            'country'             => 'required|string',
            'city'                => 'required|string',
            'status'              => 'in:active,inactive,banned',
            'languages'           => 'required|array',
            'languages.*'         => 'required|string',
            'years_of_experience' => 'required|integer|min:0',
            'memorized_quran_parts'=> 'required|integer|min:0|max:31',
            'nationality'         => 'required|string',
        ]);
    
        // Debugging: check the data before saving
        \Log::info($validated);
    
        // Hash the password before saving
        $validated['password'] = Hash::make($validated['password']);
    
        // Create the interpreter
        $interpreter = Interpreter::create($validated);
    
        return response()->json($interpreter, 201);
    }

    /**
     * Display the specified interpreter.
     */
    public function show($id)
    {
        $interpreter = Interpreter::findOrFail($id);
        return response()->json($interpreter);
    }

    /**
     * Update the specified interpreter.
     */
    public function update(Request $request, $id)
    {
        $interpreter = Interpreter::findOrFail($id);
    
        try {
            $validated = $request->validate([
                'name'                => 'required|string|max:255',
                'email'               => 'required|email|unique:interpreters,email,' . $id,
                'password'            => 'nullable|string|min:6',
                'age'                 => 'nullable|integer|min:0',
                'gender'              => 'required|in:male,female',
                'country'             => 'required|string',
                'city'                => 'required|string',
                'status'              => 'in:active,inactive,banned',
                'languages'           => 'required|array',
                'languages.*'         => 'required|string',
                'years_of_experience' => 'required|integer|min:0',
                'memorized_quran_parts'=> 'required|integer|min:0|max:31',
                'nationality'         => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    
        if (!empty($validated['password'])) {
            $validated['password'] = \Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
    
        $interpreter->update($validated);
    
        return response()->json([
            'status' => 'success',
            'data' => $interpreter
        ]);

        // If password is provided, hash it
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            // Remove password field if it's not provided
            unset($validated['password']);
        }

        // Update the interpreter with the validated data
        $interpreter->update($validated);

        return response()->json($interpreter);
    }

    /**
     * Delete the specified interpreter.
     */
    public function destroy($id)
    {
        $interpreter = Interpreter::findOrFail($id);
        $interpreter->delete();

        return response()->json(['message' => 'Interpreter deleted successfully.']);
    }
}
