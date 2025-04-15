<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request and return a JSON response.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        // Authenticate the user
        $request->authenticate();

        // Regenerate the session
        $request->session()->regenerate();

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful',
            'redirect' => route('home'),  // Assuming 'home' is your intended redirect route
        ], 200);
    }

    /**
     * Destroy an authenticated session and return a JSON response.
     */
    public function destroy(Request $request): JsonResponse
    {
        // Logout the user
        Auth::guard('web')->logout();

        // Invalidate and regenerate session token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout successful',
            'redirect' => url('/'),  // Redirect to home page
        ], 200);
    }
}
