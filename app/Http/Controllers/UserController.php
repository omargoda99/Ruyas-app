<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return view('pages.admin.home'); // if admin, he has dashboard (future feature)
        }
        return response()->json(User::all());
    }

    // Store a new user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|string|min:6',
            'age'               => 'nullable|integer',
            'marital_status'    => ['nullable', Rule::in(['single', 'married', 'divorced', 'widowed'])],
            'gender'            => ['required', Rule::in(['male', 'female'])],
            'employment_status' => ['nullable', Rule::in(['employed', 'unemployed'])],
            'image_url'         => 'nullable|url',
            'ip_address'        => 'nullable|ip',
            'country'           => 'nullable|string|max:100',
            'region'            => 'nullable|string|max:100',
            'city'              => 'nullable|string|max:100',
            'postal_code'       => 'nullable|string|max:20',
            'status'            => ['nullable', Rule::in(['active', 'inactive', 'banned'])],
        ]);

        $validated['password_hash'] = Hash::make($validated['password']);
        unset($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    // Show a single user
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // Update a user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'              => 'sometimes|string|max:255',
            'email'             => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password'          => 'sometimes|string|min:6',
            'age'               => 'nullable|integer',
            'marital_status'    => ['nullable', Rule::in(['single', 'married', 'divorced', 'widowed'])],
            'gender'            => ['sometimes', Rule::in(['male', 'female'])],
            'employment_status' => ['nullable', Rule::in(['employed', 'unemployed'])],
            'image_url'         => 'nullable|url',
            'ip_address'        => 'nullable|ip',
            'country'           => 'nullable|string|max:100',
            'region'            => 'nullable|string|max:100',
            'city'              => 'nullable|string|max:100',
            'postal_code'       => 'nullable|string|max:20',
            'status'            => ['nullable', Rule::in(['active', 'inactive', 'banned'])],
        ]);

        if (isset($validated['password'])) {
            $validated['password_hash'] = Hash::make($validated['password']);
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    // Delete a user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
