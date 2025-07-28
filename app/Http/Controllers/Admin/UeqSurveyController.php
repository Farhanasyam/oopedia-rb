<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UeqSurvey;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UeqSurveyExport;

class UeqSurveyController extends Controller
{
    public function __construct()
    {
        // Tambahkan middleware untuk memastikan hanya admin dan superadmin yang bisa mengakses
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role_id > 2) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Anda tidak memiliki akses untuk melihat hasil UEQ Survey');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // Ambil semua data survey dengan relasi user
        $query = UeqSurvey::with('user');
        
        // Filter berdasarkan kelas jika ada
        if ($request->has('class') && !empty($request->class)) {
            $query->where('class', $request->class);
        }
        
        $surveys = $query->get();
        
        // Daftar kelas unik untuk filter dropdown
        $classes = UeqSurvey::distinct()->pluck('class')->filter()->values();
        
        // Hitung rata-rata untuk setiap dimensi UEQ
        $averages = $this->calculateAverages($surveys);
        
        // Untuk sidebar materials dropdown
        $materials = Material::all();
        
        return view('admin.ueq.index', [
            'surveys' => $surveys,
            'averages' => $averages,
            'materials' => $materials,
            'classes' => $classes,
            'activePage' => 'ueq',
            'userName' => auth()->user()->name,
            'userRole' => auth()->user()->role->role_name
        ]);
    }
    
    private function calculateAverages($surveys)
    {
        if ($surveys->isEmpty()) {
            return [];
        }
        
        // Inisialisasi array untuk menyimpan total nilai
        $totals = [
            'attractiveness' => 0,
            'perspicuity' => 0,
            'efficiency' => 0,
            'dependability' => 0,
            'stimulation' => 0,
            'novelty' => 0
        ];
        
        foreach ($surveys as $survey) {
            // Attractiveness
            $totals['attractiveness'] += (
                $survey->annoying_enjoyable + 
                $survey->good_bad + 
                $survey->unlikable_pleasing + 
                $survey->unpleasant_pleasant + 
                $survey->attractive_unattractive + 
                $survey->friendly_unfriendly
            ) / 6;
            
            // Perspicuity
            $totals['perspicuity'] += (
                $survey->not_understandable_understandable + 
                $survey->easy_difficult + 
                $survey->complicated_easy + 
                $survey->clear_confusing
            ) / 4;
            
            // Efficiency
            $totals['efficiency'] += (
                $survey->fast_slow + 
                $survey->inefficient_efficient + 
                $survey->impractical_practical + 
                $survey->organized_cluttered
            ) / 4;
            
            // Dependability
            $totals['dependability'] += (
                $survey->unpredictable_predictable + 
                $survey->obstructive_supportive + 
                $survey->secure_not_secure + 
                $survey->meets_expectations_does_not_meet
            ) / 4;
            
            // Stimulation
            $totals['stimulation'] += (
                $survey->valuable_inferior + 
                $survey->boring_exciting + 
                $survey->not_interesting_interesting + 
                $survey->motivating_demotivating
            ) / 4;
            
            // Novelty
            $totals['novelty'] += (
                $survey->creative_dull + 
                $survey->inventive_conventional + 
                $survey->usual_leading_edge + 
                $survey->conservative_innovative
            ) / 4;
        }
        
        // Hitung rata-rata
        $count = $surveys->count();
        $averages = [];
        
        foreach ($totals as $key => $total) {
            $averages[$key] = $total / $count;
        }
        
        return $averages;
    }

    /**
     * Export UEQ Survey results filtered by class
     */
    public function export(Request $request)
    {
        $class = $request->input('class');
        
        // Query data
        $query = UeqSurvey::with('user');
        if ($class) {
            $query->where('class', $class);
        }
        $surveys = $query->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ueq-survey-results.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($surveys, $headers) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'NIM', 'Nama Pengguna', 'Email', 'Kelas', 'Tanggal Pengisian',
                // 26 aspek UEQ
                'Annoying - Enjoyable',
                'Not Understandable - Understandable',
                'Creative - Dull',
                'Easy - Difficult',
                'Valuable - Inferior',
                'Boring - Exciting',
                'Not Interesting - Interesting',
                'Unpredictable - Predictable',
                'Fast - Slow',
                'Inventive - Conventional',
                'Obstructive - Supportive',
                'Good - Bad',
                'Complicated - Easy',
                'Unlikable - Pleasing',
                'Usual - Leading Edge',
                'Unpleasant - Pleasant',
                'Secure - Not Secure',
                'Motivating - Demotivating',
                'Meets Expectations - Does Not Meet',
                'Inefficient - Efficient',
                'Clear - Confusing',
                'Impractical - Practical',
                'Organized - Cluttered',
                'Attractive - Unattractive',
                'Friendly - Unfriendly',
                'Conservative - Innovative',
                'Komentar', 'Saran'
            ]);
            
            // Add data rows
            foreach ($surveys as $survey) {
                fputcsv($file, [
                    $survey->id,
                    $survey->nim ?? '',
                    optional($survey->user)->name ?? 'Tidak ada',
                    optional($survey->user)->email ?? 'Tidak ada',
                    $survey->class ?? '',
                    $survey->created_at->format('d/m/Y H:i'),
                    // 26 aspek UEQ
                    $survey->annoying_enjoyable,
                    $survey->not_understandable_understandable,
                    $survey->creative_dull,
                    $survey->easy_difficult,
                    $survey->valuable_inferior,
                    $survey->boring_exciting,
                    $survey->not_interesting_interesting,
                    $survey->unpredictable_predictable,
                    $survey->fast_slow,
                    $survey->inventive_conventional,
                    $survey->obstructive_supportive,
                    $survey->good_bad,
                    $survey->complicated_easy,
                    $survey->unlikable_pleasing,
                    $survey->usual_leading_edge,
                    $survey->unpleasant_pleasant,
                    $survey->secure_not_secure,
                    $survey->motivating_demotivating,
                    $survey->meets_expectations_does_not_meet,
                    $survey->inefficient_efficient,
                    $survey->clear_confusing,
                    $survey->impractical_practical,
                    $survey->organized_cluttered,
                    $survey->attractive_unattractive,
                    $survey->friendly_unfriendly,
                    $survey->conservative_innovative,
                    $survey->comments ?? '',
                    $survey->suggestions ?? ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function detail($userId)
    {
        $survey = UeqSurvey::where('user_id', $userId)->firstOrFail();
        $user = $survey->user;

        return view('admin.ueq.detail', compact('survey', 'user'));
    }
} 