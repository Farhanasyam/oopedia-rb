<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GuestAccess
{
    public function handle(Request $request, Closure $next)
    {
        // TAMBAHAN: Biarkan semua request lewat untuk route latihan soal
        $allowedPaths = [
            'mahasiswa/materials/questions',
            'mahasiswa/materials/*/questions',
            'mahasiswa/materials/*/questions/levels',
            'mahasiswa/materials/*/questions/review',
            'questions/check-answer'
        ];
        
        $currentPath = $request->path();
        
        foreach ($allowedPaths as $path) {
            if (fnmatch($path, $currentPath)) {
                return $next($request);
            }
        }
        
        // Biarkan semua pengguna yang tidak login untuk mengakses semua route
        if (!auth()->check()) {
            return $next($request);
        }
        
        if (auth()->check()) {
            $user = auth()->user();
            
            // Allow regular students (role 3) and admin/superadmin (roles 1-2) to access all routes
            if ($user->role_id <= 3) {
                return $next($request);
            }
            
            // Restrict guest access (role 4)
            if ($user->role_id === 4) {
                $allowedRoutes = [
                    'mahasiswa.materials.index',
                    'mahasiswa.materials.show',
                    'mahasiswa.materials.questions.index',
                    'mahasiswa.materials.questions.show',
                    'mahasiswa.materials.questions.review',
                    'mahasiswa.materials.questions.levels',
                    'mahasiswa.questions.check-answer',
                    'questions.check-answer',
                    'mahasiswa.questions.show',
                    'mahasiswa.materials.reset',
                    'logout',
                    'login',
                    'register'
                ];
                
                // TAMBAHAN: Cetak route name untuk debugging
                \Log::info('Current route: ' . $request->route()->getName());
                \Log::info('Current path: ' . $request->path());
                
                // TAMBAHKAN KONDISI UNTUK PATH QUESTIONS
                if (strpos($request->path(), 'materials/questions') !== false || 
                    strpos($request->path(), 'questions/check-answer') !== false) {
                    return $next($request);
                }
                
                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('mahasiswa.materials.index')
                        ->with('info', 'Fitur ini hanya tersedia untuk mahasiswa terdaftar.');
                }
            }
        }

        return $next($request);
    }
} 