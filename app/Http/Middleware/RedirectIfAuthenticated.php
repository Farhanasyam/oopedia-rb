<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                return match($user->role_id) {
                    1 => redirect()->route('admin.dashboard'),
                    2 => redirect()->route('admin.dashboard'),
                    3 => redirect()->route('mahasiswa.dashboard'),
                    4 => redirect()->route('mahasiswa.materials.index'),
                    default => redirect('/')
                };
            }
        }

        return $next($request);
    }
}
