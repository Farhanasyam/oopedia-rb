<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SessionsController extends Controller
{
    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Refresh user data dari database
            $user = User::find($user->id);
            
            // Cek role dan status approval
            if ($user->role_id == 1) {
                // Superadmin
                return redirect()->intended('admin/dashboard');
            } else if ($user->role_id == 2) {
                // Admin
                if ($user->is_approved) {
                    return redirect()->intended('admin/dashboard');
                } else {
                    return redirect()->route('admin.pending-approval');
                }
            } else if ($user->role_id == 3) {
                // Mahasiswa
                return redirect()->intended('mahasiswa/dashboard');
            } else {
                // Tamu (role_id = 4)
                return redirect()->intended('mahasiswa/materials');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->onlyInput('email');
    }

    public function show()
    {
        request()->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            request()->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    public function update()
    {
        request()->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]); 
        
        $status = Password::reset(
            request()->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => ($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        $isGuest = $user && $user->role_id === 4;
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        if ($isGuest) {
            // Hapus user tamu dari database
            $user->delete();
        }
        
        return redirect('/');
    }

    public function guestLogin()
    {
        // Create a temporary guest user
        $guestUser = User::create([
            'name' => 'Tamu_' . Str::random(8),
            'email' => 'guest_' . Str::random(8) . '@temporary.com',
            'password' => Hash::make(Str::random(16)),
            'role_id' => 4 // Ubah dari 3 menjadi 4 jika ingin membedakan tamu, atau tetap 3 jika tamu dianggap mahasiswa
        ]);

        Auth::login($guestUser);
        
        return redirect()->route('mahasiswa.materials.index');
    }
} 