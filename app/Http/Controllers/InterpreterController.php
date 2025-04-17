<?php

namespace App\Http\Controllers;

use App\Models\Interpreter;
use Illuminate\Http\Request;

class InterpreterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Interpreter::all());
    }

    // Store a new interpreter
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:interpreters,email',
            'password'    => 'required|string|min:6',
            'age'         => 'nullable|integer|min:0',
            'gender'      => 'required|in:male,female',
            'ip_address'  => 'nullable|ip',
            'country'     => 'nullable|string',
            'region'      => 'nullable|string',
            'city'        => 'nullable|string',
            'postal_code' => 'nullable|string',
            'status'      => 'in:active,inactive,banned',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $interpreter = Interpreter::create($validated);

        return response()->json($interpreter, 201);
    }

    // Show a specific interpreter
    public function show($id)
    {
        $interpreter = Interpreter::findOrFail($id);
        return response()->json($interpreter);
    }

    // Update an interpreter
    public function update(Request $request, $id)
    {
        $interpreter = Interpreter::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'email'       => "sometimes|email|unique:interpreters,email,{$id}",
            'password'    => 'nullable|string|min:6',
            'age'         => 'nullable|integer|min:0',
            'gender'      => 'sometimes|in:male,female',
            'ip_address'  => 'nullable|ip',
            'country'     => 'nullable|string',
            'region'      => 'nullable|string',
            'city'        => 'nullable|string',
            'postal_code' => 'nullable|string',
            'status'      => 'in:active,inactive,banned',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $interpreter->update($validated);

        return response()->json($interpreter);
    }

    // Delete an interpreter
    public function destroy($id)
    {
        $interpreter = Interpreter::findOrFail($id);
        $interpreter->delete();

        return response()->json(['message' => 'Interpreter deleted successfully.']);
    }
}
