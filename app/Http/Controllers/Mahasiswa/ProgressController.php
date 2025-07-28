<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Materi;
use App\Models\Progress;

class ProgressController extends Controller
{
    public function getProgress()
    {
        // Get all materi with progress for the current student
        $materis = Materi::with(['progress' => function($query) {
            $query->where('mahasiswa_id', auth()->id());
        }])->get();

        return response()->json([
            'success' => true,
            'data' => $materis
        ]);
    }
} 