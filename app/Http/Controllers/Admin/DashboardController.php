<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Material;
use App\Models\Question;
use App\Models\Progress;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get authenticated user data
        $user = auth()->user();
        $userName = $user->name;
        $userRole = $user->role->role_name;

        // Statistics Cards
        $totalStudents = User::where('role_id', 3)->count();
        $totalMaterials = Material::count();
        $totalQuestions = Question::count();
        
        $activeStudents = $this->getActiveStudentsCount();

        // Recent Student Progress
        $recentProgress = Progress::with(['user', 'material', 'question'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get materials with question counts for later use
        $materialsWithQuestionCount = Material::withCount('questions')->get();

        // Student Progress Overview with completion percentage
        $studentProgress = User::where('role_id', 3)
            ->withCount(['progress as completed_questions' => function($query) {
                $query->where('is_correct', true);
            }])
            ->with(['progress' => function($query) {
                $query->where('is_correct', true)
                      ->with('material:id,title');
            }])
            ->having('completed_questions', '>', 0)
            ->orderByDesc('completed_questions')
            ->limit(5)
            ->get()
            ->map(function($student) use ($materialsWithQuestionCount) {
                // Count unique completed materials
                $completedMaterialsCount = $student->progress
                    ->pluck('material')
                    ->unique('id')
                    ->count();
                
                // Get all materials with their question bank configurations
                $materials = Material::with(['questionBankConfigs' => function($query) {
                    $query->where('is_active', true);
                }])->get();
                
                // Calculate total configured questions
                $totalConfiguredQuestions = 0;
                foreach ($materials as $material) {
                    $config = $material->questionBankConfigs->first();
                    if ($config) {
                        $totalConfiguredQuestions += $config->beginner_count + $config->medium_count + $config->hard_count;
                    } else {
                        // If no config, use all questions
                        $totalConfiguredQuestions += $material->questions()->count();
                    }
                }
                
                // Calculate progress percentage based on configured questions
                $correctAnswers = $student->progress->where('is_correct', true)->count();
                
                $student->materials_progress = $totalConfiguredQuestions > 0 
                    ? round(($correctAnswers / $totalConfiguredQuestions) * 100) 
                    : 0;
                
                // Add last active timestamp
                $lastActivity = $student->progress->max('created_at');
                $student->last_active = $lastActivity ? Carbon::parse($lastActivity) : null;
                
                return $student;
            });

        // Material Statistics for Chart
        $materialStats = $this->getMaterialStatistics();

        // Popular Materials
        $popularMaterials = DB::table('materials')
            ->leftJoin('progress', function($join) {
                $join->on('materials.id', '=', 'progress.material_id')
                    ->where('progress.is_correct', '=', true);
            })
            ->leftJoin('users', 'progress.user_id', '=', 'users.id')
            ->select(
                'materials.id',
                'materials.title',
                DB::raw('COUNT(DISTINCT progress.user_id) as students_count'),
                DB::raw('ROUND(
                    (COUNT(DISTINCT CASE WHEN progress.is_correct = 1 THEN progress.id ELSE NULL END) * 100.0) / 
                    NULLIF(COUNT(DISTINCT progress.id), 0), 
                    1
                ) as completion_rate')
            )
            ->groupBy('materials.id', 'materials.title')
            ->orderByDesc('students_count')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'userName',
            'userRole',
            'totalStudents',
            'totalMaterials',
            'totalQuestions',
            'activeStudents',
            'recentProgress',
            'studentProgress',
            'materialStats',
            'popularMaterials'
        ));
    }

    public function dashboard()
    {
        // Get all students (assuming role_id 3 is for students)
        $students = User::where('role_id', 3)
            ->with(['answers']) // Assuming you have an answers relationship
            ->get()
            ->map(function ($student) {
                // Calculate progress percentage
                $totalQuestions = Question::count();
                $answeredQuestions = $student->answers->unique('question_id')->count();
                
                $progress = $totalQuestions > 0 
                    ? min(100, round(($answeredQuestions / $totalQuestions) * 100)) 
                    : 0;

                $student->progress = $progress;
                return $student;
            });

        return view('admin.dashboard', [
            'students' => $students,
            'activePage' => 'dashboard',
            'userName' => auth()->user()->name,
            'userRole' => 'Admin'
        ]);
    }

    private function getActiveStudentsCount()
    {
        // Ambil mahasiswa yang memiliki aktivitas dalam 7 hari terakhir
        return DB::table('users')
            ->join('progress', 'users.id', '=', 'progress.user_id')
            ->where('users.role_id', 3) // role mahasiswa adalah 3
            ->where('progress.created_at', '>=', now()->subDays(7))
            ->distinct('users.id')
            ->count('users.id');
    }

    private function getMaterialStatistics()
    {
        $materials = Material::with(['questions', 'questionBankConfigs' => function($query) {
            $query->where('is_active', true);
        }])->get();
        
        return $materials->map(function($material) {
            // Get active configuration for this material
            $config = $material->questionBankConfigs->first();
            
            // Calculate total configured questions
            $totalConfiguredQuestions = 0;
            if ($config) {
                $totalConfiguredQuestions = $config->beginner_count + $config->medium_count + $config->hard_count;
            } else {
                $totalConfiguredQuestions = $material->questions->count();
            }
            
            // Get progress data for this material
            $progressData = DB::table('progress')
                ->where('material_id', $material->id)
                ->where('is_correct', true)
                ->select('user_id', 'question_id')
                ->get();
            
            // Count unique users who have answered questions in this material
            $activeStudents = $progressData->pluck('user_id')->unique()->count();
            
            // Count unique correctly answered questions
            $correctlyAnsweredQuestions = $progressData->pluck('question_id')->unique()->count();
            
            // Calculate completion rate
            $completionRate = $totalConfiguredQuestions > 0 
                ? round(($correctlyAnsweredQuestions / $totalConfiguredQuestions) * 100, 1)
                : 0;
            
            return (object)[
                'id' => $material->id,
                'title' => $material->title,
                'questions_count' => $totalConfiguredQuestions,
                'active_students' => $activeStudents,
                'completion_rate' => $completionRate
            ];
        });
    }
}