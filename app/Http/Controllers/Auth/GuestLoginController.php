<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class GuestLoginController extends Controller
{
    public function login()
    {
        $guestUser = User::create([
            'name' => 'Tamu_' . Str::random(8),
            'email' => 'guest_' . Str::random(8) . '@temporary.com',
            'password' => Hash::make(Str::random(16)),
            'role_id' => 4 
        ]);

        // Auth::login($guestUser);
        
        // // Set a flash message to inform the user they're in guest mode
        // session()->flash('info', 'Anda masuk sebagai tamu. Beberapa fitur dan konten materi akan terbatas.');
        
        return redirect()->route('mahasiswa.materials.index');
    }

    public function logout(Request $request)
    {
        // Get current user ID before logging out
        $userId = auth()->id();
        Auth::logout();
        // Delete the guest user from database
        if ($userId) {
            User::where('id', $userId)->where('role_id', 4)->delete();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirect to login or register based on request
        return redirect($request->input('redirect', route('login')));
    }
} 