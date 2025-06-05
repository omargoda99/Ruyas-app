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
use Illuminate\Support\Str;

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
            'email'    => 'nullable|email|string|max:100|unique:users',
            'phone'    => 'nullable|string|max:15|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 422);
        }

        // Generate a name based on whether it's email or phone registration
        if ($request->filled('email')) {
            $email = $request->email;
            $name = strstr($email, '@', true); // part before @
        } elseif ($request->filled('phone')) {
            $name = 'user_' . Str::random(6); // random string like user_xz39lk
        } else {
            return response()->json([
                'message' => 'Please provide either an email or a phone number.'
            ], 400);
        }

        // Create the user
        $user = User::create([
            'name'     => $name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => bcrypt($request->password),
        ]);

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

        // Get authenticated user
        $user = auth()->user();

        // Return only selected fields
        return response()->json([
            'name'            => $user->name,
            'email'           => $user->email,
            'phone'           => $user->phone,
            'age'             => $user->age,
            'marital_status'  => $user->marital_status,
        ]);
    }

   public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name'           => 'nullable|string|max:100',
            'email'          => 'nullable|email|unique:users,email,' . $user->uuid . ',uuid',
            'phone'          => 'nullable|string|max:15|unique:users,phone,' . $user->uuid . ',uuid',
            'age'            => 'nullable|integer|min:0',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = [];

        // name, age, marital_status can always be updated
        if ($request->filled('name')) {
            $data['name'] = $request->name;
        }

        if ($request->filled('age')) {
            $data['age'] = $request->age;
        }

        if ($request->filled('marital_status')) {
            $data['marital_status'] = $request->marital_status;
        }

        // email and phone can only be updated if currently null
        if ($request->filled('email') && $user->email === null) {
            $data['email'] = $request->email;
        }

        if ($request->filled('phone') && $user->phone === null) {
            $data['phone'] = $request->phone;
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => $user->fresh()
        ]);
    }




    public function createNewToken($token){
    return response()->json([
        'access_token' => $token,
        'token_type'   => 'bearer',
        'expires_in'   => auth()->factory()->getTTL() * 60, // now reflects 365 days
        'user'         => auth()->user()
    ]);
    }

}
