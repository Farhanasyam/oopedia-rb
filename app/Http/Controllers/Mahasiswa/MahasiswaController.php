<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\User;
use App\Models\Question;
use App\Models\Progress;
use App\Models\QuestionBankConfig;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        $userId = auth()->id();
        
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

        $materials = Material::with(['questions'])->get()
            ->map(function($material) use ($progressStats) {
                $totalQuestions = $material->questions->count();
                $materialProgress = $progressStats->firstWhere('material_id', $material->id);
                
                $correctAnswers = $materialProgress ? $materialProgress->correct_answers : 0;
                $progressPercentage = $totalQuestions > 0 
                    ? min(100, round(($correctAnswers / $totalQuestions) * 100))
                    : 0;

                return (object)[
                    'id' => $material->id,
                    'title' => $material->title,
                    'description' => $material->description,
                    'progress_percentage' => $progressPercentage,
                    'total_questions' => $totalQuestions,
                    'completed_questions' => $correctAnswers
                ];
            });

        return view('mahasiswa.dashboard.dashboard', [
            'materials' => Material::all(), // For navbar
            'dashboardMaterials' => $materials // For dashboard cards
        ]);
    }

    public function materi($slug = null)
    {
        $materials = Material::all();
        if ($slug) {
            $material = Material::where('title', str_replace('-', ' ', $slug))->firstOrFail();
            return view('mahasiswa.materi', compact('materials', 'material'));
        }
        return view('mahasiswa.materi', compact('materials'));
    }

    public function leaderboard()
    {
        // Hitung jumlah soal per kategori untuk badge (dari bank soal yang aktif)
        $totalBeginner = 0;
        $totalMedium = 0;
        $totalHard = 0;

        // Ambil data materials dengan konfigurasi bank soal yang aktif
        $materials = Material::with(['questionBankConfigs' => function($query) {
            $query->where('is_active', true);
        }])->get();

        // Hitung total soal per tingkat kesulitan dari bank soal aktif
        foreach ($materials as $material) {
            $config = $material->questionBankConfigs->first();
            if ($config) {
                $totalBeginner += $config->beginner_count;
                $totalMedium += $config->medium_count;
                $totalHard += $config->hard_count;
            } else {
                // Jika tidak ada konfigurasi, gunakan semua soal berdasarkan kesulitan
                $totalBeginner += $material->questions()->where('difficulty', 'beginner')->count();
                $totalMedium += $material->questions()->where('difficulty', 'medium')->count();
                $totalHard += $material->questions()->where('difficulty', 'hard')->count();
            }
        }
        
        // Ambil data jawaban benar untuk setiap user dengan percobaan minimum
        $correctAnswers = DB::table('progress')
            ->join('questions', 'progress.question_id', '=', 'questions.id')
            ->join('users', 'progress.user_id', '=', 'users.id')
            ->select(
                'progress.user_id',
                'progress.question_id',
                'questions.difficulty',
                DB::raw('MIN(progress.attempt_number) as attempts_needed')
            )
            ->where('progress.is_correct', 1)
            ->where('users.role_id', 3)
            ->groupBy('progress.user_id', 'progress.question_id', 'questions.difficulty')
            ->get();
        
        // SISTEM POIN SEDERHANA YANG DIPERBAIKI
        $userScores = [];
        
        foreach ($correctAnswers as $answer) {
            $userId = $answer->user_id;
            $attempts = (int)$answer->attempts_needed;
            
            if (!isset($userScores[$userId])) {
                $userScores[$userId] = 0;
            }
            
            // SISTEM POIN DASAR: Nilai berdasarkan kesulitan
            $basePoin = 0;
            switch ($answer->difficulty) {
                case 'beginner':
                    $basePoin = 5; // Soal beginner = 5 poin
                    break;
                case 'medium':
                    $basePoin = 10; // Soal medium = 10 poin
                    break;
                case 'hard':
                    $basePoin = 15; // Soal hard = 15 poin
                    break;
            }
            
            // SISTEM BONUS/PENALTI: Berdasarkan jumlah percobaan
            $attemptMultiplier = 1.0; // Default untuk percobaan pertama
            
            if ($attempts == 1) {
                $attemptMultiplier = 1.0; // 100% poin untuk percobaan pertama
            } elseif ($attempts == 2) {
                $attemptMultiplier = 0.8; // 80% poin untuk percobaan kedua
            } elseif ($attempts == 3) {
                $attemptMultiplier = 0.6; // 60% poin untuk percobaan ketiga
            } elseif ($attempts == 4) {
                $attemptMultiplier = 0.4; // 40% poin untuk percobaan keempat
            } else {
                $attemptMultiplier = 0.2; // 20% poin untuk percobaan kelima atau lebih
            }
            
            // Hitung poin akhir (pembulatan ke bawah)
            $finalPoin = floor($basePoin * $attemptMultiplier);
            
            // Tambahkan poin ke total user
            $userScores[$userId] += $finalPoin;
            
            // Log untuk debugging
            \Log::info("POIN: User {$userId}, Soal {$answer->question_id}, Kesulitan: {$answer->difficulty}, 
                       Percobaan: {$attempts}, Poin Dasar: {$basePoin}, Multiplier: {$attemptMultiplier}, Poin Akhir: {$finalPoin}");
        }
        
        // Ambil data untuk leaderboard
        $leaderboardData = DB::table('users')
            ->leftJoin('progress', 'users.id', '=', 'progress.user_id')
            ->leftJoin('questions', 'progress.question_id', '=', 'questions.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(DISTINCT CASE WHEN progress.is_correct = 1 THEN progress.question_id END) as total_correct_questions'),
                DB::raw('COUNT(DISTINCT progress.question_id) as total_attempted'),
                DB::raw('SUM(CASE WHEN progress.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
                DB::raw('MAX(progress.updated_at) as completion_date'),
                DB::raw('COUNT(DISTINCT CASE WHEN progress.is_correct = 1 AND questions.difficulty = "beginner" THEN progress.question_id END) as beginner_completed'),
                DB::raw('COUNT(DISTINCT CASE WHEN progress.is_correct = 1 AND questions.difficulty = "medium" THEN progress.question_id END) as medium_completed'),
                DB::raw('COUNT(DISTINCT CASE WHEN progress.is_correct = 1 AND questions.difficulty = "hard" THEN progress.question_id END) as hard_completed'),
                DB::raw('COUNT(progress.id) as total_attempts')
            )
            ->where('users.role_id', 3)
            ->groupBy('users.id', 'users.name', 'users.email')
            ->get();
        
        // Tambahkan weighted_score ke leaderboard data
        foreach ($leaderboardData as $data) {
            $data->weighted_score = $userScores[$data->id] ?? 0;
            \Log::info("TOTAL SKOR: {$data->name} (ID: {$data->id}): {$data->weighted_score}");
        }
        
        // Urutkan berdasarkan skor
        $leaderboardData = $leaderboardData->sortByDesc('weighted_score')->values();
        
        $rank = 1;
        foreach ($leaderboardData as $index => $data) {
            $data->rank = $rank++;
            
            // Ambil data materials dengan konfigurasi bank soal yang aktif
            $materials = Material::with(['questionBankConfigs' => function($query) {
                $query->where('is_active', true);
            }])->get();

            // Hitung total soal yang dikonfigurasi dari seluruh materi
            $totalConfiguredQuestions = 0;
            foreach ($materials as $material) {
                $config = $material->questionBankConfigs->first();
                if ($config) {
                    $totalConfiguredQuestions += $config->beginner_count + $config->medium_count + $config->hard_count;
                } else {
                    // Jika tidak ada konfigurasi, gunakan semua soal
                    $totalConfiguredQuestions += $material->questions()->count();
                }
            }

            // Hitung persentase berdasarkan total soal yang dikonfigurasi
            $data->percentage = $totalConfiguredQuestions > 0
                ? min(100, round(($data->total_correct_questions / $totalConfiguredQuestions) * 100))
                : 0;
            
            $data->formatted_score = number_format($data->weighted_score, 0, ',', '.');
            
            // Penentuan badge berdasarkan level
            if ($data->hard_completed >= $totalHard && $totalHard > 0) {
                $data->badge = 'Hard';
                $data->badge_color = 'danger';
            } elseif ($data->medium_completed >= $totalMedium && $totalMedium > 0) {
                $data->badge = 'Medium';
                $data->badge_color = 'warning';
            } elseif ($data->beginner_completed >= $totalBeginner && $totalBeginner > 0) {
                $data->badge = 'Beginner';
                $data->badge_color = 'success';
            } else {
                $data->badge = 'Learner';
                $data->badge_color = 'secondary';
            }
        }
        
        // Tentukan peringkat pengguna saat ini
        $currentUserId = auth()->id();
        $currentUserRank = null;
        
        foreach ($leaderboardData as $data) {
            if ($data->id == $currentUserId) {
                $currentUserRank = $data;
                break;
            }
        }
        
        return view('mahasiswa.leaderboard', compact('leaderboardData', 'currentUserRank'));
    }
}