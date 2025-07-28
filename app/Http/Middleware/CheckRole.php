<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Update role checking logic
        if (!auth()->check()) {
            return redirect('login');
        }

        // Convert role parameter to array for multiple role support
        $roles = explode('|', $role);
        
        if (!in_array(auth()->user()->role_id, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}