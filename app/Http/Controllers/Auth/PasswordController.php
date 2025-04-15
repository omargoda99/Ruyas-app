<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\JsonResponse;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): JsonResponse
    {
        // Validate the incoming request
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Update the password in the database
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Return success response
        return response()->json([
            'status'  => 'success',
            'message' => 'Password updated successfully.',
        ]);
    }
}
