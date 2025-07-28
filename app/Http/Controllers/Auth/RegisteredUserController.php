<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;
use Illuminate\Http\RedirectResponse;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\'\s]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Tentukan role berdasarkan checkbox
        $role_id = $request->has('register_as_admin') ? 2 : 3; // 2 untuk admin, 3 untuk mahasiswa
        
        // Admin baru perlu approval, mahasiswa langsung approved
        $is_approved = $role_id == 3;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role_id,
            'is_approved' => $is_approved,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Jika mendaftar sebagai admin dan belum diapprove, arahkan ke halaman menunggu persetujuan
        if ($role_id == 2 && !$is_approved) {
            return redirect()->route('admin.pending-approval');
        }

        // Arahkan berdasarkan role
        if ($role_id == 2) {
            return redirect()->route('admin.dashboard');
        }
        
        // Jika mahasiswa, langsung ke dashboard mahasiswa
        return redirect()->route('mahasiswa.dashboard');
    }
} 