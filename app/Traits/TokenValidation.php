<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

trait TokenValidation
{
    /**
     * Validate JWT token and return authenticated user or error response.
     *
     * @return \App\Models\User|\Illuminate\Http\JsonResponse
     */
   public function ValidateToken()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            Log::info('Token valid for user ID: ' . $user->id); // log debug info
            // return response()->json(['123'],404);
        } catch (TokenExpiredException $e) {
            return response()->json(['message' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token not provided'], 401);
        }
    }
}
