<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use Illuminate\Http\Request;
use App\Models\Interpreter;
use Illuminate\Support\Facades\File;

class CertificationController extends Controller
{
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
            'interpreter_id'       => 'required|exists:interpreters,id',
            'name'                 => 'required|string|max:255',
            'issuing_organization' => 'required|string|max:255',
            'issue_date'           => 'required|date',
            'credential_img'       => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'credential_id'        => 'nullable|string|max:255',
            'credential_url'       => 'nullable|url',
        ]);

        $imagePath = $request->file('credential_img')->store('certifications', 'public');

        $certification = Certification::create([
            'interpreter_id'       => $validated['interpreter_id'],
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
     * Show certifications for a given interpreter.
     */
    public function show($interpreterId)
    {
        $interpreter = Interpreter::find($interpreterId);

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
     * Delete a specific certification.
     */
    public function destroy($id)
    {
        $certification = Certification::find($id);

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
