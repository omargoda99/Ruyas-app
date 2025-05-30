<?php

namespace App\Http\Controllers;

use App\Models\Interpreter;
use App\Models\InterpreterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InterpreterRequestController extends Controller
{
    // Admin: List all pending requests (with related user)
    public function indexPending()
    {
        $requests = InterpreterRequest::where('status', 'pending')->with('user')->get();
        return response()->json($requests);
    }

    // Admin: Approve a request by UUID
    public function approve($uuid)
    {
        $request = InterpreterRequest::where('uuid', $uuid)->first();

        if (!$request) {
            return response()->json(['message' => 'Interpreter request not found'], 404);
        }

        if ($request->status !== 'pending') {
            return response()->json(['message' => 'Request already processed'], 400);
        }

        Interpreter::create([
            'user_id'               => $request->user_id,
            'age'                   => $request->age,
            'gender'                => $request->gender,
            'years_of_experience'   => $request->years_of_experience,
            'memorized_quran_parts' => $request->memorized_quran_parts,
            'languages'             => $request->languages,
            'nationality'           => $request->nationality,
            'country'               => $request->country,
            'city'                  => $request->city,
            'pervious_work'         => $request->pervious_work,
            'status'                => 'active',
        ]);

        $request->status = 'approved';
        $request->save();

        return response()->json(['message' => 'Interpreter request approved']);
    }

    // Admin: Reject a request by UUID
    public function reject($uuid)
    {
        $request = InterpreterRequest::where('uuid', $uuid)->first();

        if (!$request) {
            return response()->json(['message' => 'Interpreter request not found'], 404);
        }

        if ($request->status !== 'pending') {
            return response()->json(['message' => 'Request already processed'], 400);
        }

        $request->status = 'rejected';
        $request->save();

        return response()->json(['message' => 'Interpreter request rejected']);
    }

    public function store(Request $request)
    {
        // Get authenticated user's UUID
        $user = auth()->user();
        $userUuid = $user->uuid;

        // Check if the user already submitted a request
        if (InterpreterRequest::where('user_uuid', $userUuid)->exists()) {
            return response()->json(['message' => 'Request already submitted.'], 409);
        }
        $request->validate([
            'name'                  => 'required|string',
            'age'                   => 'required|integer',
            'email'                 => 'required|email|string|max:100|unique:interpreter_requests,email',
            'phone'                 => 'required|string|max:15|unique:interpreter_requests,phone',
            'gender'                => 'required|in:male,female',
            'years_of_experience'   => 'required|integer|min:0',
            'memorized_quran_parts' => 'required|integer|min:0|max:31',
            'languages'             => 'nullable|string',
            'languages.*'           => 'string',
            'nationality'           => 'required|string',
            'country'               => 'required|string',
            'city'                  => 'required|string',
            'pervious_work'         => 'required|string',
        ]);


        $requestData = $request->all();
        $requestData['user_uuid'] = $userUuid;
        $requestData['uuid'] = (string) Str::uuid(); // Add UUID to the request itself

        // Convert languages array to JSON if provided
        if (!empty($requestData['languages'])) {
            $requestData['languages'] = json_encode($requestData['languages']);
        }

        $interpreterRequest = InterpreterRequest::create($requestData);

        return response()->json([
            'message' => 'Request submitted successfully',
            'data' => $interpreterRequest
        ], 201);
    }
}
