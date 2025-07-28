<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role_id == 1) {
            return $next($request);
        }
        
        return redirect()->route('admin.dashboard')
            ->with('error', 'Anda tidak memiliki akses untuk halaman ini');
    }
} 
