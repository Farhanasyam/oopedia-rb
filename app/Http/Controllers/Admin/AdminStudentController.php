<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Material;
use App\Models\Progress;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminStudentController extends Controller
{
    public function index(Request $request)
    {
        // Admin dan superadmin dapat mengakses daftar mahasiswa
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk melihat daftar mahasiswa');
        }
        
        $query = User::where('role_id', 3); // Hanya tampilkan mahasiswa
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Get students with progress data
        $students = $query->paginate(10);
        
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
        
        // Calculate progress for each student
        foreach ($students as $student) {
            // Get correct answers count
            $correctAnswers = DB::table('progress')
                ->where('user_id', $student->id)
                ->where('is_correct', true)
                ->count();
            
            // Calculate progress percentage based on configured questions
            $student->overall_progress = $totalConfiguredQuestions > 0 
                ? min(100, round(($correctAnswers / $totalConfiguredQuestions) * 100))
                : 0;
            
            // Set total answered questions
            $student->total_answered_questions = DB::table('progress')
                ->where('user_id', $student->id)
                ->count();
        }
        
        return view('admin.students.index', compact('students'));
    }

    public function progress(User $student)
    {
        // Ensure we're looking at a student
        abort_if($student->role_id != 3, 404);
    
        // Get all materials with their question bank configurations
        $materials = Material::with(['questions', 'questionBankConfigs' => function($query) {
            $query->where('is_active', true);
        }])->get();
        
        // Get progress data for this student
        $progressData = DB::table('progress')
            ->where('user_id', $student->id)
            ->where('is_correct', true)
            ->select('material_id', 'question_id')
            ->get();
        
        // Group progress by material
        $progressByMaterial = $progressData->groupBy('material_id');
        
        // Identifikasi jumlah soal yang belum dijawab dengan benar per materi
        $missingQuestionsByMaterial = [];
        $materialsWithProgress = collect();
        
        foreach ($materials as $material) {
            // Get active configuration for this material
            $config = $material->questionBankConfigs->first();
            
            // Calculate total configured questions
            $totalConfiguredQuestions = 0;
            if ($config) {
                $totalConfiguredQuestions = $config->beginner_count + $config->medium_count + $config->hard_count;
            } else {
                // If no config, use all questions
                $totalConfiguredQuestions = $material->questions->count();
            }
            
            // Get correct answers for this material
            $correctAnswers = isset($progressByMaterial[$material->id]) 
                ? $progressByMaterial[$material->id]->count() 
                : 0;
            
            // Calculate progress percentage based ONLY on configured questions
            $progressPercentage = $totalConfiguredQuestions > 0 
                ? min(100, round(($correctAnswers / $totalConfiguredQuestions) * 100))
                : 0;
            
            // Get last access time
            $lastAccessed = DB::table('progress')
                ->where('user_id', $student->id)
                ->where('material_id', $material->id)
                ->max('updated_at');
            
            // Hitung soal yang belum dijawab dengan benar
            $missingCount = max(0, $totalConfiguredQuestions - $correctAnswers);
            
            $materialsWithProgress->push((object)[
                'id' => $material->id,
                'title' => $material->title,
                'total_questions' => $totalConfiguredQuestions,
                'answered_questions' => $correctAnswers,
                'progress' => $progressPercentage,
                'last_accessed' => $lastAccessed ? \Carbon\Carbon::parse($lastAccessed) : null
            ]);
            
            // Jika ada soal yang belum dijawab dengan benar
            if ($missingCount > 0) {
                $missingQuestionsByMaterial[] = [
                    'material_title' => $material->title,
                    'missing_count' => $missingCount
                ];
            }
        }
        
        // Get recent activities
        $recent_activities = DB::table('progress')
            ->join('questions', 'progress.question_id', '=', 'questions.id')
            ->join('materials', 'questions.material_id', '=', 'materials.id')
            ->where('progress.user_id', $student->id)
            ->select(
                'materials.title as material_title',
                'questions.question_text as question_title',
                'progress.is_correct',
                'progress.created_at'
            )
            ->orderBy('progress.created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.students.progress', [
            'student' => $student,
            'materials' => $materialsWithProgress,
            'recent_activities' => $recent_activities,
            'missingQuestionsByMaterial' => $missingQuestionsByMaterial
        ]);
    }

    private function calculateOverallProgress($student)
    {
        $totalQuestions = Question::count();
        if ($totalQuestions === 0) return 0;

        $correctAnswers = Progress::where('user_id', $student->id)
            ->where('is_correct', true)
            ->count();

        return round(($correctAnswers / $totalQuestions) * 100);
    }

    public function destroy(User $student)
    {
        // Admin dan superadmin dapat menghapus mahasiswa
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus mahasiswa');
        }
        
        // Pastikan yang dihapus adalah mahasiswa
        if ($student->role_id != 3) {
            return redirect()->route('admin.students.index')
                ->with('error', 'User ini bukan mahasiswa');
        }
        
        try {
            DB::beginTransaction();
            
            // Check if user_ranks table exists before attempting to delete
            if (Schema::hasTable('user_ranks')) {
                DB::table('user_ranks')->where('user_id', $student->id)->delete();
            }
            
            // Check if progress table exists before attempting to delete
            if (Schema::hasTable('progress')) {
                DB::table('progress')->where('user_id', $student->id)->delete();
            }
            
            // Check if student_answers table exists before attempting to delete
            if (Schema::hasTable('student_answers')) {
                DB::table('student_answers')->where('student_id', $student->id)->delete();
            }
            
            // Check if quiz_attempts table exists before attempting to delete
            if (Schema::hasTable('quiz_attempts')) {
                DB::table('quiz_attempts')->where('user_id', $student->id)->delete();
            }
            
            // Finally delete the student
            $student->delete();
            
            DB::commit();
            
            return redirect()->route('admin.students.index')
                ->with('success', 'Data mahasiswa telah berhasil dihapus dari sistem');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.students.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function showImportForm()
    {
        // Admin dan superadmin dapat mengakses fitur ini
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk fitur ini');
        }
        
        return view('admin.students.import');
    }
    
    public function processImport(Request $request)
    {
        // Admin dan superadmin dapat mengakses fitur ini
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk fitur ini');
        }
        
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:2048', // 2MB max size
        ]);
        
        $file = $request->file('excel_file');
        $path = $file->getRealPath();
        
        // Read the Excel/CSV file
        $data = [];
        if (($handle = fopen($path, 'r')) !== false) {
            // Read the header row
            $header = fgetcsv($handle, 1000, ',');
            
            // Check if the file has the required columns
            $requiredColumns = ['name', 'email', 'password'];
            $missingColumns = array_diff($requiredColumns, $header);
            
            if (!empty($missingColumns)) {
                return redirect()->back()->with('error', 'File tidak memiliki kolom yang diperlukan: ' . implode(', ', $missingColumns));
            }
            
            // Map column indexes
            $nameIndex = array_search('name', $header);
            $emailIndex = array_search('email', $header);
            $passwordIndex = array_search('password', $header);
            
            // Process each row
            $rowNumber = 1; // Start from 1 to account for header row
            $successCount = 0;
            $errorRows = [];
            
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty($row[$nameIndex]) && empty($row[$emailIndex])) {
                    continue;
                }
                
                // Validate the row data
                $rowData = [
                    'name' => $row[$nameIndex] ?? '',
                    'email' => $row[$emailIndex] ?? '',
                    'password' => $row[$passwordIndex] ?? '',
                ];
                
                $validator = Validator::make($rowData, [
                    'name' => 'required|string|max:255',
                    'email' => [
                        'required',
                        'string',
                        'email',
                        'max:255',
                        Rule::unique('users'),
                    ],
                    'password' => 'required|string|min:8',
                ]);
                
                if ($validator->fails()) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'errors' => $validator->errors()->all(),
                    ];
                    continue;
                }
                
                // Create the user with is_approved set to true
                try {
                    $user = new User();
                    $user->name = $row[$nameIndex];
                    $user->email = $row[$emailIndex];
                    $user->password = Hash::make($row[$passwordIndex]);
                    $user->role_id = 3; // Mahasiswa role
                    $user->is_approved = true; // Explicitly set to true
                    $user->save();
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $errorRows[] = [
                        'row' => $rowNumber,
                        'errors' => [$e->getMessage()],
                    ];
                }
            }
            fclose($handle);
            
            // Prepare the result message
            $message = "Berhasil menambahkan {$successCount} mahasiswa.";
            if (!empty($errorRows)) {
                $message .= " Terdapat " . count($errorRows) . " baris dengan error.";
            }
            
            return redirect()->route('admin.students.index')
                ->with('success', $message)
                ->with('importErrors', $errorRows);
        }
        
        return redirect()->back()->with('error', 'Gagal membaca file. Pastikan format file benar.');
    }
    
    public function downloadTemplate()
    {
        // Admin dan superadmin dapat mengakses fitur ini
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk fitur ini');
        }
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mahasiswa_template.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'email', 'password']);
            fputcsv($file, ['Nama Mahasiswa', 'mahasiswa@example.com', 'password123']);
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function show(User $student)
    {
        if ($student->role_id !== 3) {
            abort(404);
        }

        // Get progress statistics
        $progressStats = DB::table('progress')
            ->select(
                'material_id',
                DB::raw('COUNT(DISTINCT question_id) as answered_questions'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->where('user_id', $student->id)
            ->groupBy('material_id')
            ->get();

        // Get all materials with their questions
        $materials = Material::withCount('questions')
            ->get()
            ->map(function($material) use ($progressStats) {
                $progress = $progressStats->firstWhere('material_id', $material->id);
                
                $correctAnswers = $progress ? $progress->correct_answers : 0;
                $progressPercentage = $material->questions_count > 0 
                    ? min(100, round(($correctAnswers / $material->questions_count) * 100))
                    : 0;

                $material->progress_percentage = $progressPercentage;
                $material->correct_answers = $correctAnswers;
                
                return $material;
            });

        return view('admin.students.show', compact('student', 'materials'));
    }
}