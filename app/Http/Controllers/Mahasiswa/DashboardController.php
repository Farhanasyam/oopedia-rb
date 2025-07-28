<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\Question;
use App\Models\QuestionBankConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Progress;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);

        // Get all materials
        $allMaterials = Material::with(['questions'])->get();
        $totalMaterials = $allMaterials->count();
        
        // Variables to store configured question counts
        $configuredTotalQuestions = 0;
        $configuredEasyQuestions = 0;
        $configuredMediumQuestions = 0;
        $configuredHardQuestions = 0;
        
        // Calculate configured question counts
        foreach ($allMaterials as $material) {
            if ($isGuest) {
                // For guests, use fixed values (3 per difficulty)
                $configuredEasyQuestions += min(3, $material->questions->where('difficulty', 'beginner')->count());
                $configuredMediumQuestions += min(3, $material->questions->where('difficulty', 'medium')->count());
                $configuredHardQuestions += min(3, $material->questions->where('difficulty', 'hard')->count());
            } else {
                // For registered users, use admin configuration
                $config = QuestionBankConfig::where('material_id', $material->id)
                    ->where('is_active', true)
                    ->first();
                    
                if ($config) {
                    $configuredEasyQuestions += $config->beginner_count;
                    $configuredMediumQuestions += $config->medium_count;
                    $configuredHardQuestions += $config->hard_count;
                } else {
                    // Default if no configuration exists
                    $configuredEasyQuestions += $material->questions->where('difficulty', 'beginner')->count();
                    $configuredMediumQuestions += $material->questions->where('difficulty', 'medium')->count();
                    $configuredHardQuestions += $material->questions->where('difficulty', 'hard')->count();
                }
            }
        }
        
        $configuredTotalQuestions = $configuredEasyQuestions + $configuredMediumQuestions + $configuredHardQuestions;
        
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

        // Calculate material statistics
        $completedMaterials = 0;
        $inProgressMaterials = 0;
        $totalMaterialProgress = 0;

        // Calculate question statistics
        $totalAnsweredQuestions = 0;
        $totalCorrectQuestions = 0;

        $materials = Material::with(['questions', 'questionBankConfigs'])->get()
            ->map(function($material) use ($progressStats) {
                // Get active configuration
                $config = $material->questionBankConfigs()->where('is_active', true)->first();
                
                // Calculate total configured questions
                if ($config) {
                    $totalQuestions = $config->beginner_count + $config->medium_count + $config->hard_count;
                } else {
                    $totalQuestions = $material->questions->count();
                }
                
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

        // Calculate overall progress percentages
        $materialProgressPercentage = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100) : 0;
        $questionProgressPercentage = $configuredTotalQuestions > 0 ? round(($totalCorrectQuestions / $configuredTotalQuestions) * 100) : 0;

        // Get recent activities
        $recentActivities = $this->getRecentActivities($userId);

        // Metode 1: Menggunakan array untuk mengirim data ke view
        return view('mahasiswa.dashboard.index', [
            'totalMaterials' => $totalMaterials,
            'totalQuestions' => $configuredTotalQuestions,
            'easyQuestions' => $configuredEasyQuestions,
            'mediumQuestions' => $configuredMediumQuestions,
            'hardQuestions' => $configuredHardQuestions,
            'materialProgressPercentage' => $materialProgressPercentage,
            'questionProgressPercentage' => $questionProgressPercentage,
            'completedMaterials' => $completedMaterials,
            'inProgressMaterials' => $inProgressMaterials,
            'totalMaterialProgress' => $totalMaterialProgress,
            'totalAnsweredQuestions' => $totalAnsweredQuestions,
            'totalCorrectQuestions' => $totalCorrectQuestions,
            'recentActivities' => $recentActivities,
            'allMaterials' => $materials
        ]);

        /* 
        // Metode 2: Menggunakan compact (harus menggunakan nama variabel yang sama)
        $totalQuestions = $configuredTotalQuestions;
        $easyQuestions = $configuredEasyQuestions;
        $mediumQuestions = $configuredMediumQuestions;
        $hardQuestions = $configuredHardQuestions;

        return view('mahasiswa.dashboard.index', compact(
            'totalMaterials',
            'totalQuestions',
            'easyQuestions',
            'mediumQuestions',
            'hardQuestions',
            'materialProgressPercentage',
            'questionProgressPercentage',
            'completedMaterials',
            'inProgressMaterials',
            'totalMaterialProgress',
            'totalAnsweredQuestions',
            'totalCorrectQuestions',
            'recentActivities',
            'allMaterials'
        ));
        */
    }

    public function inProgress()
    {
        $userId = auth()->id();
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        // Get progress statistics grouped by material and difficulty
        $progressStats = DB::table('progress')
            ->select(
                'progress.material_id',
                'questions.difficulty',
                DB::raw('COUNT(DISTINCT progress.question_id) as total_answered'),
                DB::raw('SUM(CASE WHEN progress.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->join('questions', 'progress.question_id', '=', 'questions.id')
            ->where('progress.user_id', $userId)
            ->groupBy('progress.material_id', 'questions.difficulty')
            ->get();

        // Get stats for all progress (used for filtering materials)
        $materialProgress = DB::table('progress')
            ->select(
                'material_id',
                DB::raw('COUNT(DISTINCT question_id) as total_answered'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->where('user_id', $userId)
            ->groupBy('material_id')
            ->get();

        $materials = Material::with(['questions'])
            ->get()
            ->filter(function($material) use ($materialProgress) {
                $progress = $materialProgress->firstWhere('material_id', $material->id);
                $totalQuestions = $material->questions->count();
                
                if ($progress && $totalQuestions > 0) {
                    $correctAnswers = $progress->correct_answers;
                    return $correctAnswers > 0 && $correctAnswers < $totalQuestions;
                }
                
                return false;
            });

        // Calculate stats for each difficulty level
        $materialsWithStats = $materials->map(function($material) use ($progressStats, $isGuest) {
            // Get questions by difficulty
            $beginnerQuestions = $material->questions->where('difficulty', 'beginner');
            $mediumQuestions = $material->questions->where('difficulty', 'medium');
            $hardQuestions = $material->questions->where('difficulty', 'hard');
            
            // Get configured question counts based on user type
            if ($isGuest) {
                // For guests, use fixed values (3 per difficulty)
                $configuredBeginnerTotal = min(3, $beginnerQuestions->count());
                $configuredMediumTotal = min(3, $mediumQuestions->count());
                $configuredHardTotal = min(3, $hardQuestions->count());
            } else {
                // For registered users, use admin configuration
                $config = QuestionBankConfig::where('material_id', $material->id)
                    ->where('is_active', true)
                    ->first();
                    
                if ($config) {
                    $configuredBeginnerTotal = $config->beginner_count;
                    $configuredMediumTotal = $config->medium_count;
                    $configuredHardTotal = $config->hard_count;
                } else {
                    // Default if no configuration exists
                    $configuredBeginnerTotal = $beginnerQuestions->count();
                    $configuredMediumTotal = $mediumQuestions->count();
                    $configuredHardTotal = $hardQuestions->count();
                }
            }
            
            // Calculate stats for beginner questions
            $beginnerStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'beginner')
                ->first();
            $beginnerCorrect = $beginnerStats ? $beginnerStats->correct_answers : 0;
            $beginnerTotal = $beginnerQuestions->count();
            $beginnerPercentage = $configuredBeginnerTotal > 0 ? round(($beginnerCorrect / $configuredBeginnerTotal) * 100) : 0;
            
            // Calculate stats for medium questions
            $mediumStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'medium')
                ->first();
            $mediumCorrect = $mediumStats ? $mediumStats->correct_answers : 0;
            $mediumTotal = $mediumQuestions->count();
            $mediumPercentage = $configuredMediumTotal > 0 ? round(($mediumCorrect / $configuredMediumTotal) * 100) : 0;
            
            // Calculate stats for hard questions
            $hardStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'hard')
                ->first();
            $hardCorrect = $hardStats ? $hardStats->correct_answers : 0;
            $hardTotal = $hardQuestions->count();
            $hardPercentage = $configuredHardTotal > 0 ? round(($hardCorrect / $configuredHardTotal) * 100) : 0;
            
            // Overall stats
            $totalCorrect = $beginnerCorrect + $mediumCorrect + $hardCorrect;
            $configuredTotalQuestions = $configuredBeginnerTotal + $configuredMediumTotal + $configuredHardTotal;
            $overallPercentage = $configuredTotalQuestions > 0 ? round(($totalCorrect / $configuredTotalQuestions) * 100) : 0;
            
            return [
                'material' => $material,
                'stats' => [
                    'overall' => [
                        'correct' => $totalCorrect,
                        'total' => $configuredTotalQuestions,
                        'percentage' => $overallPercentage
                    ],
                    'beginner' => [
                        'correct' => $beginnerCorrect,
                        'total' => $beginnerTotal,
                        'configured_total' => $configuredBeginnerTotal,
                        'percentage' => $beginnerPercentage
                    ],
                    'medium' => [
                        'correct' => $mediumCorrect,
                        'total' => $mediumTotal,
                        'configured_total' => $configuredMediumTotal,
                        'percentage' => $mediumPercentage
                    ],
                    'hard' => [
                        'correct' => $hardCorrect,
                        'total' => $hardTotal,
                        'configured_total' => $configuredHardTotal,
                        'percentage' => $hardPercentage
                    ]
                ]
            ];
        });

        return view('mahasiswa.dashboard.in-progress', [
            'materialsWithStats' => $materialsWithStats
        ]);
    }

    public function complete()
    {
        $userId = auth()->id();
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        // Get progress statistics grouped by material and difficulty
        $progressStats = DB::table('progress')
            ->select(
                'progress.material_id',
                'questions.difficulty',
                DB::raw('COUNT(DISTINCT progress.question_id) as total_answered'),
                DB::raw('SUM(CASE WHEN progress.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->join('questions', 'progress.question_id', '=', 'questions.id')
            ->where('progress.user_id', $userId)
            ->groupBy('progress.material_id', 'questions.difficulty')
            ->get();

        // Get stats for all progress (used for filtering materials)
        $materialProgress = DB::table('progress')
            ->select(
                'material_id',
                DB::raw('COUNT(DISTINCT question_id) as total_answered'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->where('user_id', $userId)
            ->groupBy('material_id')
            ->get();

        $materials = Material::with(['questions'])
            ->get()
            ->filter(function($material) use ($materialProgress, $isGuest) {
                $progress = $materialProgress->firstWhere('material_id', $material->id);
                
                if ($progress) {
                    $correctAnswers = $progress->correct_answers;
                    
                    // Get configured question count
                    if ($isGuest) {
                        // For guests, calculate max 9 questions (3 per difficulty)
                        $beginnerCount = min(3, $material->questions->where('difficulty', 'beginner')->count());
                        $mediumCount = min(3, $material->questions->where('difficulty', 'medium')->count());
                        $hardCount = min(3, $material->questions->where('difficulty', 'hard')->count());
                        $configuredTotalQuestions = $beginnerCount + $mediumCount + $hardCount;
                    } else {
                        // For registered users, use admin configuration
                        $config = QuestionBankConfig::where('material_id', $material->id)
                            ->where('is_active', true)
                            ->first();
                            
                        if ($config) {
                            $configuredTotalQuestions = $config->beginner_count + $config->medium_count + $config->hard_count;
                        } else {
                            // Default if no configuration exists
                            $configuredTotalQuestions = $material->questions->count();
                        }
                    }
                    
                    return $correctAnswers >= $configuredTotalQuestions; // Only completed materials
                }
                
                return false;
            });

        // Calculate stats for each difficulty level
        $materialsWithStats = $materials->map(function($material) use ($progressStats, $isGuest) {
            // Get questions by difficulty
            $beginnerQuestions = $material->questions->where('difficulty', 'beginner');
            $mediumQuestions = $material->questions->where('difficulty', 'medium');
            $hardQuestions = $material->questions->where('difficulty', 'hard');
            
            // Get configured question counts based on user type
            if ($isGuest) {
                // For guests, use fixed values (3 per difficulty)
                $configuredBeginnerTotal = min(3, $beginnerQuestions->count());
                $configuredMediumTotal = min(3, $mediumQuestions->count());
                $configuredHardTotal = min(3, $hardQuestions->count());
            } else {
                // For registered users, use admin configuration
                $config = QuestionBankConfig::where('material_id', $material->id)
                    ->where('is_active', true)
                    ->first();
                    
                if ($config) {
                    $configuredBeginnerTotal = $config->beginner_count;
                    $configuredMediumTotal = $config->medium_count;
                    $configuredHardTotal = $config->hard_count;
                } else {
                    // Default if no configuration exists
                    $configuredBeginnerTotal = $beginnerQuestions->count();
                    $configuredMediumTotal = $mediumQuestions->count();
                    $configuredHardTotal = $hardQuestions->count();
                }
            }
            
            // Calculate stats for beginner questions
            $beginnerStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'beginner')
                ->first();
            $beginnerCorrect = $beginnerStats ? $beginnerStats->correct_answers : 0;
            
            // Calculate stats for medium questions
            $mediumStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'medium')
                ->first();
            $mediumCorrect = $mediumStats ? $mediumStats->correct_answers : 0;
            
            // Calculate stats for hard questions
            $hardStats = $progressStats->where('material_id', $material->id)
                ->where('difficulty', 'hard')
                ->first();
            $hardCorrect = $hardStats ? $hardStats->correct_answers : 0;
            
            // For completed materials we show 100% for all stats
            return [
                'material' => $material,
                'stats' => [
                    'overall' => [
                        'correct' => $configuredBeginnerTotal + $configuredMediumTotal + $configuredHardTotal,
                        'total' => $configuredBeginnerTotal + $configuredMediumTotal + $configuredHardTotal,
                        'percentage' => 100
                    ],
                    'beginner' => [
                        'correct' => $configuredBeginnerTotal,
                        'total' => $beginnerQuestions->count(),
                        'configured_total' => $configuredBeginnerTotal,
                        'percentage' => 100
                    ],
                    'medium' => [
                        'correct' => $configuredMediumTotal,
                        'total' => $mediumQuestions->count(),
                        'configured_total' => $configuredMediumTotal,
                        'percentage' => 100
                    ],
                    'hard' => [
                        'correct' => $configuredHardTotal,
                        'total' => $hardQuestions->count(),
                        'configured_total' => $configuredHardTotal,
                        'percentage' => 100
                    ]
                ]
            ];
        });

        return view('mahasiswa.dashboard.complete', [
            'materialsWithStats' => $materialsWithStats
        ]);
    }

    public function completed()
    {
        $materials = Material::all();
        return view('mahasiswa.dashboard.completed', compact('materials'));
    }

    private function getActiveStudentsCount()
    {
        // Ambil mahasiswa yang memiliki aktivitas dalam 7 hari terakhir
        return DB::table('users')
            ->join('progress', 'users.id', '=', 'progress.user_id')
            ->where('users.role_id', 3) // Role mahasiswa sekarang adalah 3
            ->where('progress.created_at', '>=', now()->subDays(7))
            ->distinct('users.id')
            ->count('users.id');
    }

    private function determineActivityType($activity)
    {
        if ($activity->total_correct >= 5) {
            return 'achievement';
        } elseif ($activity->difficulty === 'hard' && $activity->is_correct) {
            return 'milestone';
        } else {
            return 'progress';
        }
    }

    private function getRecentActivities($userId)
    {
        // Implementasi untuk mendapatkan recent activities
        // Contoh: Menggunakan query yang sama dengan metode index()
        return DB::table('progress as p1')
            ->join('materials', 'p1.material_id', '=', 'materials.id')
            ->join('questions', 'p1.question_id', '=', 'questions.id')
            ->where('p1.user_id', $userId)
            ->where('p1.is_correct', true)
            ->whereRaw('p1.created_at = (
                SELECT MAX(p2.created_at)
                FROM progress p2
                WHERE p2.material_id = p1.material_id
                AND p2.user_id = p1.user_id
                AND p2.is_correct = true
            )')
            ->select(
                'materials.title as material_title',
                'materials.id as material_id',
                'questions.difficulty',
                'p1.created_at',
                'p1.is_correct',
                DB::raw('(
                    SELECT COUNT(DISTINCT p3.question_id) 
                    FROM progress p3 
                    WHERE p3.material_id = materials.id 
                    AND p3.user_id = ' . $userId . '
                    AND p3.is_correct = 1
                ) as total_correct')
            )
            ->orderBy('p1.created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($activity) {
                $activity->type = $this->determineActivityType($activity);
                return $activity;
            });
    }
}
