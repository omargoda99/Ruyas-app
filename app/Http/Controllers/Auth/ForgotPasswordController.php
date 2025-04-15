<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

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
     * Handle the password reset email request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = $this->validateEmail($request->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == \Password::RESET_LINK_SENT) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Password reset link sent successfully.',
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to send password reset link.',
        ], 500);
    }

    /**
     * Validate the email request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateEmail(array $data)
    {
        return Validator::make(
            $data,
            [
                'email' => 'required|email|max:255',
            ],
            [
                'email.required' => 'Email is required.',
                'email.email'    => 'Invalid email format.',
            ]
        );
    }
}
