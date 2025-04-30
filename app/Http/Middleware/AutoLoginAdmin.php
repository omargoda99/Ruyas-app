<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AutoLoginAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            // Automatically log in the first admin user (adjust as needed)
            $admin = User::where('is_admin', true)->first();
            if ($admin) {
                Auth::login($admin);
            }
        }

        return $next($request);
    }
}