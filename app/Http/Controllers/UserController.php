<?php

namespace App\Http\Controllers;
use App\Models\Notification;
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
            return view('pages.admin.home');
        }

        return response()->json(User::all());
    }

    // Store a new user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'nullable|email|string|max:100|unique:users,email',
            'phone'             => 'nullable|string|max:15|unique:users,phone',
            'password'          => 'required|string|min:6|confirmed',
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

        if (!$request->has('email') && !$request->has('phone')) {
            return response()->json(['message' => 'Please provide either an email or a phone number.'], 400);
        }

        $validated['password_hash'] = Hash::make($validated['password']);
        unset($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    // Show a single user by UUID
    public function show($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        return response()->json($user);
    }

    // Update a user by UUID
    public function update(Request $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name'              => 'sometimes|string|max:255',
            'email'             => ['sometimes', 'email', Rule::unique('users')->ignore($user->uuid, 'uuid')],
            'phone'             => ['sometimes', 'string', 'max:15', Rule::unique('users')->ignore($user->uuid, 'uuid')],
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

    // Delete a user by UUID
    public function destroy($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    // Mark a notification as read by UUID
    public function markAsRead($uuid)
    {
        $notification = Notification::where('uuid', $uuid)->firstOrFail();

        $notification->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Notification marked as read'
        ], 200);
    }
}
