<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        $user = Auth::user();
        $isGuest = $user && $user->role_id === 3;
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        if ($isGuest) {
            $user->delete();
        }
        
        return redirect('/');
    }
}