<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GuestRole
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role_id === 3) {
            return $next($request);
        }
        
        return redirect('/');
    }
} 