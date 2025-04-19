<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use Illuminate\Http\Request;
use App\Models\Interpreter;
use App\Models\AppGuide;

class CertificationController extends Controller
{
    public function index(){
        return response()->json(Certification::all());
    }
    /**
     * Display a listing of the resource.
     */
    public function store(Request $request)
    {
        $request->validate([
            'interpreter_id'       => 'required|exists:interpreters,id',
            'name'                 => 'required|string|max:255',
            'issuing_organization' => 'required|string|max:255',
            'issue_date'           => 'required|date',
            'credential_img'       => 'required|image',
        ]);

        $filePath = $request->file('credential_img')->store('certifications'); // Store image in the "certifications" directory

        $certification = Certification::create([
            'interpreter_id'       => $request->interpreter_id,
            'name'                 => $request->name,
            'issuing_organization' => $request->issuing_organization,
            'issue_date'           => $request->issue_date,
            'credential_id'        => $request->credential_id,
            'credential_url'       => $request->credential_url,
            'credential_img'       => $filePath, // Save the file path
        ]);

        return response()->json($certification, 201);
    }

    /**
     * Show the certifications of an interpreter.
     */
    public function show($interpreterId)
    {
        $interpreter = Interpreter::findOrFail($interpreterId);

        return response()->json($interpreter->certifications);
    }

    /**
     * Remove the specified certification.
     */
    public function destroy($id)
    {
        $certification = Certification::findOrFail($id);
        $certification->delete();

        return response()->json(['message' => 'Certification deleted successfully.']);
    }
}
