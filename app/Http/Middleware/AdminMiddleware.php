<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the authenticated user has the 'admin' role
        if (auth()->check() && auth()->user()->role !== 'admin') {
            // If the user is not an admin, return a 403 Unauthorized response
            return response()->json(['message' => 'Unauthorized, Admin access required'], 403);
        }

        // If the user is an admin, continue to the next request
        return $next($request);
    }
}
