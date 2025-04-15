<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Handle the password reset and return a JSON response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request): JsonResponse
    {
        $this->validateReset($request);

        // Attempt to reset the password
        $status = $this->resetPassword($request);

        // Check if the reset was successful and return the appropriate JSON response
        if ($status == 'password-reset') {
            return response()->json([
                'status'  => 'success',
                'message' => 'Password has been reset successfully.',
            ], 200);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to reset the password.',
        ], 400);
    }

    /**
     * Custom method to validate and reset password.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function validateReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);
    }

    /**
     * Perform the password reset.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function resetPassword(Request $request)
    {
        // Here you can perform the actual password reset logic
        // For now, let's assume it's always successful
        return 'password-reset'; // In a real implementation, you'd call Password::reset here
    }
}
