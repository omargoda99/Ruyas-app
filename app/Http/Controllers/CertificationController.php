<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Interpreter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CertificationController extends Controller
{
    /**
     * List all certifications.
     */
    public function index()
    {
        $certifications = Certification::all();

        return response()->json([
            'status' => 'success',
            'data' => $certifications
        ]);
    }

    /**
     * Store a new certification.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'interpreter_uuid'     => 'required|exists:interpreters,uuid',
            'name'                 => 'required|string|max:255',
            'issuing_organization' => 'required|string|max:255',
            'issue_date'           => 'required|date',
            'credential_img'       => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'credential_id'        => 'nullable|string|max:255',
            'credential_url'       => 'nullable|url',
        ]);

        // Retrieve interpreter by UUID
        $interpreter = Interpreter::where('uuid', $validated['interpreter_uuid'])->firstOrFail();

        $imagePath = $request->file('credential_img')->store('certifications', 'public');

        $certification = Certification::create([
            'interpreter_id'       => $interpreter->id,
            'name'                 => $validated['name'],
            'issuing_organization' => $validated['issuing_organization'],
            'issue_date'           => $validated['issue_date'],
            'credential_id'        => $validated['credential_id'] ?? null,
            'credential_url'       => $validated['credential_url'] ?? null,
            'credential_img'       => $imagePath,
        ]);

        return response()->json([
            'status' => 'created',
            'data' => $certification
        ], 201);
    }

    /**
     * Show certifications for a given interpreter UUID.
     */
    public function show($interpreterUuid)
    {
        $interpreter = Interpreter::where('uuid', $interpreterUuid)->first();

        if (!$interpreter) {
            return response()->json([
                'status' => 'error',
                'message' => 'Interpreter not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $interpreter->certifications
        ]);
    }

    /**
     * Delete a specific certification by UUID.
     */
    public function destroy($uuid)
    {
        $certification = Certification::where('uuid', $uuid)->first();

        if (!$certification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certification not found.'
            ], 404);
        }

        if ($certification->credential_img && File::exists(public_path('storage/' . $certification->credential_img))) {
            File::delete(public_path('storage/' . $certification->credential_img));
        }

        $certification->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Certification deleted successfully.'
        ]);
    }
}
