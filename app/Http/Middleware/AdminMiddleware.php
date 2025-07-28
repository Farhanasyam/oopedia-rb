<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Superadmin selalu diizinkan
            if ($user->role_id == 1) {
                return $next($request);
            }
            
            // Admin harus diapprove
            if ($user->role_id == 2 && $user->is_approved) {
                return $next($request);
            }
        }
        
        return redirect()->route('login')
            ->with('error', 'Anda tidak memiliki akses ke halaman admin');
    }
} 