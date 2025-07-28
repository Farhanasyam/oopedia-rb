<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Progress;
use App\Models\QuestionBankConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    public function show($id)
    {
        $material = Material::findOrFail($id);
        
        // Check if user is guest
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        // Get all materials first
        $allMaterials = Material::orderBy('created_at', 'asc')->get();
        
        // If user is guest, only show half of the materials
        if ($isGuest) {
            $totalMaterials = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $materials = $allMaterials->take($materialsToShow);
        } else {
            $materials = $allMaterials;
        }
        
        // Acak urutan jawaban untuk setiap soal
        foreach ($material->questions as $question) {
            if ($question->question_type !== 'fill_in_the_blank') {
                $question->answers = $question->answers->shuffle();
            }
        }
        
        // Limit questions for guest users (both when logged in as guest or not logged in)
        if ($isGuest) {
            // Get the first 3 questions only
            $limitedQuestions = $material->questions->take(3);
            
            // Replace the original questions collection with the limited one
            $material->setRelation('questions', $limitedQuestions);
        }
        
        $answeredQuestionIds = Progress::where('user_id', auth()->id())
            ->where('material_id', $id)
            ->where('is_correct', true)
            ->pluck('question_id')
            ->toArray();
        
        $currentQuestion = $material->questions
            ->whereNotIn('id', $answeredQuestionIds)
            ->first();
        
        if (!$currentQuestion && $material->questions->count() > 0) {
            $currentQuestion = $material->questions->first();
        }
        
        $answeredCount = count($answeredQuestionIds);
        $currentQuestionNumber = $answeredCount + 1;

        if ($answeredCount >= $material->questions->count()) {
            $currentQuestionNumber = "Review";
        }
        
        return view('mahasiswa.materials.show', compact('material', 'materials', 'currentQuestionNumber'));
    }

    public function index()
    {
        $userId = auth()->id();
        
        // Define isGuest variable here
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        // Get progress statistics
        $progressStats = DB::table('progress')
            ->select(
                'material_id',
                DB::raw('COUNT(DISTINCT question_id) as answered_questions'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->where('user_id', $userId)
            ->groupBy('material_id')
            ->get();

        // Get all materials first
        $allMaterials = Material::with(['questions'])->orderBy('created_at', 'asc')->get();
        
        // If user is guest, only show half of the materials
        if ($isGuest) {
            $totalMaterials = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $allMaterials = $allMaterials->take($materialsToShow);
        }

        $materials = $allMaterials->map(function($material) use ($progressStats, $isGuest) {
            // Hitung jumlah soal berdasarkan konfigurasi
            if ($isGuest) {
                // Untuk guest, maksimal 3 soal per tingkat kesulitan
                $beginnerCount = min(3, $material->questions->where('difficulty', 'beginner')->count());
                $mediumCount = min(3, $material->questions->where('difficulty', 'medium')->count());
                $hardCount = min(3, $material->questions->where('difficulty', 'hard')->count());
                $configuredTotalQuestions = $beginnerCount + $mediumCount + $hardCount;
            } else {
                // Untuk pengguna terdaftar, gunakan konfigurasi dari admin
                $config = QuestionBankConfig::where('material_id', $material->id)
                    ->where('is_active', true)
                    ->first();
                
                if ($config) {
                    $configuredTotalQuestions = $config->beginner_count + $config->medium_count + $config->hard_count;
                } else {
                    $configuredTotalQuestions = $material->questions->count();
                }
            }
            
            $materialProgress = $progressStats->firstWhere('material_id', $material->id);
            $correctAnswers = $materialProgress ? $materialProgress->correct_answers : 0;
            
            // Gunakan total soal terkonfigurasi untuk menghitung persentase
            $progressPercentage = $configuredTotalQuestions > 0 
                ? min(100, round(($correctAnswers / $configuredTotalQuestions) * 100))
                : 0;

            $material->progress_percentage = $progressPercentage;
            $material->total_questions = $configuredTotalQuestions;
            $material->completed_questions = $correctAnswers;
            
            // Pastikan media sudah ter-load 
            if (!$material->relationLoaded('media')) {
                $material->load('media');
            }
            
            return $material;
        });
        
        return view('mahasiswa.materials.index', compact('materials'));
    }

    public function reset($id)
    {
        // Delete all progress for this material
        Progress::where('user_id', auth()->id())
            ->where('material_id', $id)
            ->delete();

        return redirect()->route('mahasiswa.materials.questions.show', ['material' => $id])
            ->with('success', 'Progress direset. Anda dapat mengerjakan soal kembali.');
    }

    public function dashboard()
    {
        $dashboardMaterials = Material::select([
            'id',
            'title',
            DB::raw('SUBSTRING(description, 1, 150) as description'), // Batasi deskripsi ke 150 karakter
            // ... field lainnya
        ])->get()->map(function($material) {
            // Bersihkan HTML tags dan batasi panjang teks
            $material->description = strip_tags($material->description);
            // Tambahkan ellipsis jika teks terpotong
            if(strlen($material->description) >= 150) {
                $material->description .= '...';
            }
            return $material;
        });

        return view('mahasiswa.dashboard.dashboard', compact('dashboardMaterials'));
    }
} 