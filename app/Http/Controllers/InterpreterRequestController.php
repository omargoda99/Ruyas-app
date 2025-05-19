<?php

namespace App\Http\Controllers;

use App\Models\Interpreter;
use App\Models\InterpreterRequest;
use Illuminate\Http\Request;

class InterpreterRequestController extends Controller
{
     // Admin: List all pending requests
    public function indexPending()
    {
        $requests = InterpreterRequest::where('status', 'pending')->with('user')->get();
        return response()->json($requests);
    }
    // Admin: Approve a request
    public function approve($id)
    {
        $request = InterpreterRequest::findOrFail($id);

        if ($request->status !== 'pending') {
            return response()->json(['message' => 'Request already processed'], 400);
        }

        // Create interpreter profile
        Interpreter::create([
            'user_id' => $request->user_id,
            'age' => $request->age,
            'gender' => $request->gender,
            'years_of_experience' => $request->years_of_experience,
            'memorized_quran_parts' => $request->memorized_quran_parts,
            'languages' => $request->languages,
            'nationality' => $request->nationality,
            'country' => $request->country,
            'city' => $request->city,
            'pervious_work' => $request->pervious_work,
            'status' => 'active',
        ]);

        $request->status = 'approved';
        $request->save();

        return response()->json(['message' => 'Interpreter request approved']);
    }

    // Admin: Reject a request
    public function reject($id)
    {
        $request = InterpreterRequest::findOrFail($id);

        if ($request->status !== 'pending') {
            return response()->json(['message' => 'Request already processed'], 400);
        }

        $request->status = 'rejected';
        $request->save();

        return response()->json(['message' => 'Interpreter request rejected']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name'=>'required|string',
            'age' => 'required|integer',
            'email'=> 'email|string|max:100|unique:interpreter_requests,email',
            'phone'=> 'string|max:15|unique:interpreter_requests,phone',
            'gender' => 'required|in:male,female',
            'years_of_experience' => 'required|integer|min:0',
            'memorized_quran_parts' => 'required|integer|min:0|max:31',
            'languages' => 'nullable|string',
            'nationality' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string',
            'pervious_work' => 'required|string',
        ]);

        if (InterpreterRequest::where('user_id', auth()->id())->exists()) {
            return response()->json(['message' => 'Request already submitted.'], 409);
        }

        $requestData = $request->all();
        $requestData['user_id'] = auth()->id();
        $requestData['languages'] = json_encode($request->languages);

        $interpreterRequest = InterpreterRequest::create($requestData);

        return response()->json(['message' => 'Request submitted successfully', 'data' => $interpreterRequest]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
