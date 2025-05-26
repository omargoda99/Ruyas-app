<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Admin::all());
    }

    /**
     * Store a new admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $admin = Admin::create($validated);

        return response()->json($admin, 201);
    }

    /**
     * Display the specified admin by UUID.
     */
    public function show($uuid)
    {
        $admin = Admin::where('uuid', $uuid)->firstOrFail();
        return response()->json($admin);
    }

    /**
     * Update the specified admin by UUID.
     */
    public function update(Request $request, $uuid)
    {
        $admin = Admin::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => "sometimes|email|unique:admins,email,{$admin->id}",
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $admin->update($validated);

        return response()->json($admin);
    }

    /**
     * Remove the specified admin by UUID.
     */
    public function destroy($uuid)
    {
        $admin = Admin::where('uuid', $uuid)->firstOrFail();
        $admin->delete();

        return response()->json(['message' => 'Admin deleted successfully.']);
    }
}
