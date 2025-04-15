<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     * (We can modify this later if you need to handle it with JSON as well)
     */
    public function show(): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Please confirm your password.',
        ]);
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the password
        if (! Auth::guard('web')->validate([
            'email'    => $request->user()->email,
            'password' => $request->password,
        ])) {
            return response()->json([
                'status'  => 'error',
                'message' => __('auth.password'),
            ], 422);
        }

        // Store the password confirmation time
        $request->session()->put('auth.password_confirmed_at', time());

        return response()->json([
            'status'  => 'success',
            'message' => 'Password confirmed successfully.',
        ]);
    }
}
