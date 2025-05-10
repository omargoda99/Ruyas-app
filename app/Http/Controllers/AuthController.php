<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    //
    /**
     * Create A new AuthController instance
     *
     * @return void
     */

     public function __construct(){
        $this->middleware('auth:api',['except'=>['login','register']]);
     }

     public function login(Request $request)
     {
         // Validate the incoming request
         $validator = Validator::make($request->all(), [
             'email'    => 'nullable|email',
             'phone'    => 'nullable|string|max:15',
             'password' => 'required|string|min:6'
         ]);

         if ($validator->fails()) {
             return response()->json($validator->errors(), 422);
         }

         // Check if user is logging in with email or phone
         $user = null;

         if ($request->has('email')) {
             $user = User::where('email', $request->email)->first();
         } elseif ($request->has('phone')) {
             $user = User::where('phone', $request->phone)->first();
         }

         // If the user is not found or the password does not match, return an error
         if (!$user || !Hash::check($request->password, $user->password)) {
             return response()->json(['error' => 'Invalid credentials'], 422);
         }

         // Attempt to generate a token for the user
         $token = auth()->login($user);

         // Return the new token
         return $this->createNewToken($token);
     }

    public function register(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'email'                 => 'nullable|email|string|max:100|unique:users',
            'phone'                 => 'nullable|string|max:15|unique:users',
            'password'              => 'required|string|min:6|confirmed', // Ensure password confirmation
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 422);
        }

        // Check if the user is registering with email or phone number
        if ($request->has('email')) {
            $user = User::create(array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            ));
        } elseif ($request->has('phone')) {
            // Handle registration with phone number
            $user = User::create(array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            ));
        } else {
            return response()->json([
                'message' => 'Please provide either an email or a phone number.'
            ], 400);
        }

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user
        ], 201);
    }
    public function logout(){
        auth()->logout();
        return response()->json(['message'=>'user signed out successfully']);
    }

    public function refresh(Request $request)
{
    try {
        // Attempt to refresh the token
        $newToken = JWTAuth::refresh(JWTAuth::getToken());

        return response()->json(['token' => $newToken]);

    } catch (JWTException $e) {
        return response()->json(['message' => 'Could not refresh token'], 500);
    }
}

    public function userProfile(Request $request)
    {
        // Check if the user is authenticated
        if (!auth()->check()) {
            return response()->json(['message' => 'Not authorized'], 401);
        }

        // Return user profile if authenticated
        return response()->json(auth()->user());
    }

    public function createNewToken($token){
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            "expires_in"=>auth()->factory()->getTTL()*60,
            "user"=>auth()->user()
        ]);
    }
}
