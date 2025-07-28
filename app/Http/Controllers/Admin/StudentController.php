<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
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
        
        // Get all users with role_id 3 (students) with pagination
        $students = User::where('role_id', 3)
            ->paginate(10) // Add pagination with 10 items per page
            ->through(function($student) use ($totalConfiguredQuestions) {
                // Get correct answers count
                $correctAnswers = DB::table('progress')
                    ->where('user_id', $student->id)
                    ->where('is_correct', true)
                    ->count();
                
                // Calculate overall percentage based on configured questions
                $student->overall_progress = $totalConfiguredQuestions > 0 
                    ? min(100, round(($correctAnswers / $totalConfiguredQuestions) * 100))
                    : 0;
                
                // Set total answered questions
                $student->total_answered_questions = DB::table('progress')
                    ->where('user_id', $student->id)
                    ->count();
                
                return $student;
            });

        return view('admin.students.index', [
            'students' => $students,
            'userName' => auth()->user()->name,
            'userRole' => auth()->user()->role->role_name
        ]);
    }

    public function progress(User $student)
    {
        // Ensure we're looking at a student
        abort_if($student->role_id != 3, 404);

        // Get materials with progress
        $materials = DB::table('materials')
            ->leftJoin('questions', 'materials.id', '=', 'questions.material_id')
            ->leftJoin('progress', function($join) use ($student) {
                $join->on('questions.id', '=', 'progress.question_id')
                    ->where('progress.user_id', '=', $student->id)
                    ->where('progress.is_correct', '=', true);
            })
            ->select(
                'materials.id',
                'materials.title',
                DB::raw('COUNT(DISTINCT questions.id) as total_questions'),
                DB::raw('COUNT(DISTINCT progress.question_id) as answered_questions'),
                DB::raw('MAX(progress.updated_at) as last_accessed')
            )
            ->groupBy('materials.id', 'materials.title')
            ->get()
            ->map(function($material) {
                $material->progress = $material->total_questions > 0 
                    ? round(($material->answered_questions / $material->total_questions) * 100)
                    : 0;
                
                // Convert last_accessed to Carbon instance if it exists
                $material->last_accessed = $material->last_accessed 
                    ? \Carbon\Carbon::parse($material->last_accessed)
                    : null;
                
                return $material;
            });

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
            'materials' => $materials,
            'recent_activities' => $recent_activities,
            'userName' => auth()->user()->name,
            'userRole' => auth()->user()->role->role_name
        ]);
    }

    public function showImportForm()
    {
        // Hanya admin dan superadmin yang bisa mengakses fitur ini
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk fitur ini');
        }
        
        return view('admin.students.import');
    }

    public function processImport(Request $request)
    {
        // Hanya admin dan superadmin yang bisa mengakses fitur ini
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
        // Hanya admin dan superadmin yang bisa mengakses fitur ini
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
}