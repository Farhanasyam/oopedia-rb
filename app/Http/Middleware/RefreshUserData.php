<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RefreshUserData
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Refresh user data dari database
            $user = User::find(Auth::id());
            Auth::setUser($user);
        }
        
        return $next($request);
    }
} 