<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SaveSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Pastikan session disimpan sebelum mengirim response
        if ($request->session()->isStarted()) {
            $request->session()->save();
        }
        
        return $response;
    }
} 