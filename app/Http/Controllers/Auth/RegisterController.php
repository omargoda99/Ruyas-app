<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\Traits\ActivationTrait;
use App\Traits\CaptureIpTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class RegisterController extends Controller
{
    use ActivationTrait;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        // Remove the captcha check and related validation
        return Validator::make(
            $data,
            [
                'name'                  => 'required|max:255|unique:users|alpha_dash',
                'first_name'            => 'alpha_dash',
                'last_name'             => 'alpha_dash',
                'email'                 => 'required|email|max:255|unique:users',
                'password'              => 'required|min:6|max:30|confirmed',
                'password_confirmation' => 'required|same:password',
            ],
            [
                'name.unique'           => trans('auth.userNameTaken'),
                'name.required'         => trans('auth.userNameRequired'),
                'first_name.required'   => trans('auth.fNameRequired'),
                'last_name.required'    => trans('auth.lNameRequired'),
                'email.required'        => trans('auth.emailRequired'),
                'email.email'           => trans('auth.emailInvalid'),
                'password.required'     => trans('auth.passwordRequired'),
                'password.min'          => trans('auth.PasswordMin'),
                'password.max'          => trans('auth.PasswordMax'),
            ]
        );
    }

    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $this->create($request->all());

        return response()->json([
            'status'  => 'success',
            'message' => 'User registered successfully',
            'user'    => $user,
        ]);
    }

    protected function create(array $data)
    {
        $ip = $this->getPublicIp();

        $role = config('settings.activation')
            ? Role::where('slug', 'unverified')->first()
            : Role::where('slug', 'user')->first();

        $activated = !config('settings.activation');

        $user = User::create([
            'name'              => strip_tags($data['name']),
            'first_name'        => strip_tags($data['first_name']),
            'last_name'         => strip_tags($data['last_name']),
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'token'             => str()->random(64),
            'signup_ip_address' => $ip,
            'activated'         => $activated,
        ]);

        $user->attachRole($role);

        $profile = new Profile();
        $user->profile()->save($profile);

        return $user;
    }



    public function getPublicIp(): string
    {
        try {
            $response = Http::get('https://api.ipify.org?format=text');
            return $response->successful() ? $response->body() : 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }


}
