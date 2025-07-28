<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PendingApprovalController extends Controller
{
    public function index()
    {
        // Jika user adalah superadmin, tampilkan daftar admin yang pending
        if (auth()->user()->role_id == 1) {
            // Pastikan query mengambil data terbaru dari database
            DB::statement("SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED");
            $pendingAdmins = User::where('role_id', 2)
                ->where('is_approved', false)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return view('admin.users.pending-approval', compact('pendingAdmins'));
        }
        
        // Jika user adalah admin yang belum diapprove, tampilkan halaman menunggu persetujuan
        if (auth()->user()->role_id == 2 && !auth()->user()->is_approved) {
            // Pastikan data user diambil fresh dari database
            $user = User::find(auth()->id());
            
            // Double check apakah user sudah diapprove
            if ($user->is_approved) {
                return redirect()->route('admin.dashboard');
            }
            
            return view('admin.users.pending-approval');
        }
        
        // Jika bukan superadmin atau admin yang belum diapprove, redirect sesuai role
        if (auth()->user()->role_id == 2 && auth()->user()->is_approved) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('mahasiswa.dashboard');
        }
    }
} 