<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\JsonResponse;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create()
    {
        // This is typically used for rendering a view in a web application.
        // For JSON, you might not need this, unless you want to provide a view response.
        return response()->json([
            'message' => 'Password reset link request page',
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the email input
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Attempt to send the password reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Check the status of the password reset link and return a JSON response
        if ($status == Password::RESET_LINK_SENT) {
            return response()->json([
                'status'  => 'success',
                'message' => __('We have e-mailed your password reset link!'),
            ]);
        }

        // If an error occurs (e.g., email not found), return an error response
        return response()->json([
            'status'  => 'error',
            'message' => __('We couldn’t find a user with that e-mail address.'),
        ], 400);
    }
}
