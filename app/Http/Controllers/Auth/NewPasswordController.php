<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Http\JsonResponse;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     * For JSON, we can provide a simple response indicating the action.
     */
    public function create(Request $request): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Please provide your new password to reset.',
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the incoming request
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Attempt to reset the password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password'       => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // Handle the status and return appropriate JSON response
        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Password reset successfully. You can now log in.',
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => __('auth.passwords.reset'),
        ], 422);
    }
}
