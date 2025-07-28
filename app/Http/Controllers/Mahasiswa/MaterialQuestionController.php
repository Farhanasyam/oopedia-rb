<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Answer;
use App\Models\QuestionBankConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MaterialQuestionController extends Controller
{
    public function index()
    {
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        $userId = $isGuest ? session()->getId() : auth()->id();
        
        $allMaterials = Material::with(['questions', 'media'])->get();
        
        // Untuk guest, hanya tampilkan setengah dari total materi
        if ($isGuest) {
            $totalMaterials = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $allMaterials = $allMaterials->take($materialsToShow);
        }
        
        // Mendapatkan statistik progress
        $progressStats = DB::table('progress')
            ->select(
                'material_id',
                DB::raw('COUNT(DISTINCT question_id) as answered_questions'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers')
            )
            ->where('user_id', $userId)
            ->groupBy('material_id')
            ->get();
        
        // Mendapatkan jumlah mahasiswa unik yang mencoba soal untuk setiap materi
        $studentCounts = DB::table('progress')
            ->select('material_id', DB::raw('COUNT(DISTINCT user_id) as student_count'))
            ->groupBy('material_id')
            ->get()
            ->keyBy('material_id');

        // Proses setiap materi
        $materials = $allMaterials->map(function($material) use ($progressStats, $isGuest, $studentCounts) {
            // Hitung soal berdasarkan konfigurasi
            if ($isGuest) {
                // Untuk guest, batasi 3 soal per tingkat kesulitan
                $beginnerCount = min(3, $material->questions->where('difficulty', 'beginner')->count());
                $mediumCount = min(3, $material->questions->where('difficulty', 'medium')->count());
                $hardCount = min(3, $material->questions->where('difficulty', 'hard')->count());
                $configuredTotalQuestions = $beginnerCount + $mediumCount + $hardCount;
            } else {
                // Untuk pengguna terdaftar, gunakan konfigurasi admin
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
            
            $progressPercentage = $configuredTotalQuestions > 0 
                ? min(100, round(($correctAnswers / $configuredTotalQuestions) * 100))
                : 0;
            
            // Ambil jumlah mahasiswa yang sudah mencoba soal ini
            $studentCount = isset($studentCounts[$material->id]) ? $studentCounts[$material->id]->student_count : 0;
            
            $material->progress_percentage = $progressPercentage;
            $material->total_questions = $configuredTotalQuestions;
            $material->completed_questions = $correctAnswers;
            $material->student_count = $studentCount;
            
            return $material;
        });
        
        return view('mahasiswa.materials.questions.index', compact('materials', 'isGuest'));
    }

    public function show(Material $material, Request $request)
    {
        $materials = Material::orderBy('created_at', 'asc')->get();
        $difficulty = $request->query('difficulty', 'beginner');
        $questionId = $request->query('question');
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        // Filter soal berdasarkan difficulty
        $questionsQuery = $material->questions();
        if ($difficulty !== 'all') {
            $questionsQuery->where('difficulty', $difficulty);
        }
        
        // Ambil semua soal yang tersedia
        $availableQuestions = $questionsQuery->get();
        
        // Batasi jumlah soal berdasarkan konfigurasi
        if ($isGuest) {
            // Untuk guest, batasi maksimal 3 soal per tingkat kesulitan
            if ($difficulty === 'all') {
                $beginnerQuestions = $availableQuestions->where('difficulty', 'beginner')->take(3);
                $mediumQuestions = $availableQuestions->where('difficulty', 'medium')->take(3);
                $hardQuestions = $availableQuestions->where('difficulty', 'hard')->take(3);
                
                $questions = $beginnerQuestions->concat($mediumQuestions)->concat($hardQuestions);
            } else {
                $questions = $availableQuestions->take(3);
            }
            
            // Total untuk guest selalu 3 soal per tingkat kesulitan yang dipilih
            $totalFilteredQuestions = $difficulty === 'all' ? 9 : 3;
        } else {
            // Untuk user terdaftar, gunakan konfigurasi dari admin
            $config = QuestionBankConfig::where('material_id', $material->id)
                ->where('is_active', true)
                ->first();
                
            if ($config) {
                if ($difficulty === 'all') {
                    $beginnerQuestions = $availableQuestions->where('difficulty', 'beginner')->take($config->beginner_count);
                    $mediumQuestions = $availableQuestions->where('difficulty', 'medium')->take($config->medium_count);
                    $hardQuestions = $availableQuestions->where('difficulty', 'hard')->take($config->hard_count);
                    
                    $questions = $beginnerQuestions->concat($mediumQuestions)->concat($hardQuestions);
                    $totalFilteredQuestions = $config->beginner_count + $config->medium_count + $config->hard_count;
                } else {
                    // Tentukan jumlah soal berdasarkan tingkat kesulitan
                    $countField = $difficulty . '_count';
                    $limit = $config->$countField;
                    $questions = $availableQuestions->take($limit);
                    $totalFilteredQuestions = $limit;
                }
            } else {
                $questions = $availableQuestions;
                $totalFilteredQuestions = $questions->count();
            }
        }
        
        // Acak urutan soal sambil mempertahankan tingkat kesulitan
        if ($difficulty === 'all') {
            // Jika semua tingkat kesulitan, acak dalam masing-masing tingkat
            $beginnerQuestionsShuffled = $questions->where('difficulty', 'beginner')->shuffle();
            $mediumQuestionsShuffled = $questions->where('difficulty', 'medium')->shuffle();
            $hardQuestionsShuffled = $questions->where('difficulty', 'hard')->shuffle();
            
            // Gabungkan kembali dengan urutan beginner, medium, hard
            $questions = $beginnerQuestionsShuffled->concat($mediumQuestionsShuffled)->concat($hardQuestionsShuffled);
        } else {
            // Jika hanya satu tingkat kesulitan, cukup acak semuanya
            $questions = $questions->shuffle();
        }
        
        // Get answered questions for progress tracking
        $answeredQuestionIds = collect([]);
        
        // Check if user is a guest
        if ($isGuest) {
            // For guest users, check session data
            $materialProgress = session('guest_progress.' . $material->id, []);
            
            // Convert session data to a collection of question IDs
            if (!empty($materialProgress)) {
                $answeredQuestionIds = collect(array_keys($materialProgress));
            }
        } else {
            // For logged-in users, get from database
            $answeredQuestionIds = Progress::where('user_id', auth()->id())
                ->where('material_id', $material->id)
                ->where('is_correct', true)
                ->pluck('question_id');
        }
        
        // Inisialisasi currentQuestion
        $currentQuestion = null;
        
        // Jika ada parameter question di URL, cari soal tersebut di koleksi yang sudah difilter
        if ($questionId) {
            $currentQuestion = $questions->firstWhere('id', $questionId);
        }
        
        // Jika tidak ada parameter question atau soal tidak ditemukan, cari soal pertama yang belum dijawab
        if (!$currentQuestion) {
            $currentQuestion = $questions->reject(function($question) use ($answeredQuestionIds) {
                return $answeredQuestionIds->contains($question->id);
            })->first();
        }
        
        // Acak urutan jawaban jika saat ini ada soal dan bukan tipe isian
        if ($currentQuestion && $currentQuestion->question_type !== 'fill_in_the_blank') {
            // Load relasi answers jika belum dimuat
            if (!$currentQuestion->relationLoaded('answers')) {
                $currentQuestion->load('answers');
            }
            
            // Acak urutan jawaban
            $currentQuestion->setRelation('answers', $currentQuestion->answers->shuffle());
        }
        
        // Hitung nomor soal saat ini berdasarkan jumlah soal yang sudah dijawab
        $answeredCount = 0;
        if ($difficulty === 'all') {
            $answeredCount = $answeredQuestionIds->count();
        } else {
            // Hitung hanya soal yang dijawab dengan difficulty yang sama
            $questionIdsInDifficulty = $availableQuestions->pluck('id');
            $answeredCount = Progress::where('user_id', auth()->id() ?? session()->getId())
                ->where('material_id', $material->id)
                ->where('is_correct', true)
                ->whereIn('question_id', $questionIdsInDifficulty)
                ->count();
        }
        
        // Nomor soal saat ini adalah jumlah soal yang sudah dijawab + 1
        $currentQuestionNumber = $answeredCount + 1;
        
        // Jika sudah menjawab semua soal, tampilkan "Review"
        if ($answeredCount >= $totalFilteredQuestions) {
            $currentQuestionNumber = $totalFilteredQuestions;
        }
        
        return view('mahasiswa.materials.questions.show', [
            'material' => $material,
            'materials' => $materials,
            'questions' => $questions,
            'difficulty' => $difficulty,
            'isGuest' => $isGuest,
            'currentQuestion' => $currentQuestion,
            'currentQuestionNumber' => $currentQuestionNumber,
            'totalFilteredQuestions' => $totalFilteredQuestions
        ]);
    }

    public function levels(Material $material, Request $request)
    {
        $materials = Material::orderBy('created_at', 'asc')->get();
        $difficulty = $request->query('difficulty', 'beginner');
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        // Filter questions based on difficulty
        $questions = $material->questions()
            ->when($difficulty !== 'all', function($query) use ($difficulty) {
                return $query->where('difficulty', $difficulty);
            })
            ->get();

        // Determine which questions have been answered correctly
        $userId = auth()->id() ?? session()->getId();
        
        // For guests, special handling needed (both session formats)
        if ($isGuest) {
            $answeredQuestionIds = collect([]);
            
            // Check both formats of guest progress storage
            $guestProgress = session('guest_progress', []);
            $materialProgress = session('guest_progress.' . $material->id, []);
            
            // FORCE ADD QUESTION IDs FROM MATERIAL PROGRESS
            // This ensures that if a question is marked as correct in the material progress
            // it gets added to the answered questions list
            if (is_array($materialProgress)) {
                foreach (array_keys($materialProgress) as $questionId) {
                    $answeredQuestionIds->push((int)$questionId);
                }
            }
            
            // Also check format 1 (additional check, can be removed if not needed)
            foreach ($guestProgress as $key => $progress) {
                // Format 1: "material_id_question_id" => ["is_correct" => true]
                if (is_array($progress) && isset($progress['is_correct']) && $progress['is_correct']) {
                    $parts = explode('_', $key);
                    if (count($parts) >= 2 && $parts[0] == $material->id) {
                        $questionId = (int)$parts[1];
                        if (!$answeredQuestionIds->contains($questionId)) {
                            $answeredQuestionIds->push($questionId);
                        }
                    }
                }
            }
        } else {
            // Regular user progress from database
            $answeredQuestionIds = Progress::where('user_id', $userId)
                ->where('material_id', $material->id)
                ->where('is_correct', true)
                ->pluck('question_id');
        }
        
        $levels = [];
        $questionsArray = $questions->toArray();
        
        foreach ($questions as $index => $question) {
            $questionIndex = $index + 1;
            $isAnswered = $answeredQuestionIds->contains($question->id);
            
            if ($isAnswered) {
                // Question already answered correctly, mark as completed
                $status = 'completed';
            } elseif ($questionIndex === 1) {
                // First question is always unlocked
                $status = 'unlocked';
            } elseif ($index > 0 && $answeredQuestionIds->contains($questions[$index-1]->id)) {
                // Previous question was answered correctly, unlock this one
                $status = 'unlocked';
            } else {
                // Previous question not answered correctly, keep this locked
                $status = 'locked';
            }
            
            $levels[] = [
                'level' => $questionIndex,
                'question_id' => $question->id,
                'status' => $status,
                'difficulty' => $question->difficulty
            ];
        }
        
        return view('mahasiswa.materials.questions.levels', compact(
            'material', 
            'materials', 
            'levels', 
            'difficulty',
            'isGuest'
        ));
    }

    public function review($id, Request $request)
    {
        $material = Material::with(['questions.answers'])->findOrFail($id);
        
        // Get all materials for sidebar
        $materials = Material::orderBy('created_at', 'asc')->get();
        
        // Filter questions by difficulty if specified
        $difficulty = $request->query('difficulty', 'all');
        $questions = $material->questions;
        
        if ($difficulty && $difficulty !== 'all') {
            $questions = $questions->where('difficulty', $difficulty);
        }
        
        // Determine if user is guest
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        // Get only questions that the user has answered
        if ($isGuest) {
            // For guests, filter questions based on session data
            $answeredQuestionIds = collect(session('guest_progress.' . $material->id, []));
            $questions = $questions->whereIn('id', $answeredQuestionIds);
        } else {
            // For logged-in users, get from database
            $answeredQuestionIds = Progress::where('user_id', auth()->id())
                ->where('material_id', $material->id)
                ->where('is_answered', true)
                ->pluck('question_id');
            $questions = $questions->whereIn('id', $answeredQuestionIds);
        }
        
        if ($request->ajax()) {
            return view('mahasiswa.partials.question-review-filtered', [
                'material' => $material,
                'questions' => $questions,
                'difficulty' => $difficulty
            ])->render();
        }
        
        // For direct access, return the full review page
        return view('mahasiswa.materials.questions.review', [
            'material' => $material,
            'materials' => $materials,
            'questions' => $questions,
            'difficulty' => $difficulty,
            'isGuest' => $isGuest
        ]);
    }

    public function getAttempts(Material $material, Question $question)
    {
        // Determine if user is guest
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        if ($isGuest) {
            // For guest users, get attempts from session
            $progressKey = $material->id . '_' . $question->id;
            $guestProgress = session('guest_progress', []);
            $attempts = isset($guestProgress[$progressKey]) ? $guestProgress[$progressKey]['attempt_number'] : 0;
        } else {
            // For logged-in users, get from database
            $attempts = Progress::where('user_id', auth()->id())
                ->where('material_id', $material->id)
                ->where('question_id', $question->id)
                ->count();
        }
        
        return response()->json(['attempts' => $attempts]);
    }

    public function checkAnswer(Material $material, Question $question, Request $request)
    {
        try {
            $difficulty = $request->input('difficulty', 'beginner');
            $userId = auth()->id() ?? session()->getId();
            $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
            
            // Default messages
            $successMessage = 'Jawaban benar!';
            
            // Logic untuk mengecek jawaban
            $isCorrect = false;
            $questionType = $question->question_type;
            
            // Check jawaban berdasarkan tipe soal
            if ($questionType === 'multiple_choice') {
                $selectedAnswer = Answer::findOrFail($request->answer);
                $isCorrect = $selectedAnswer->is_correct;
            } elseif ($questionType === 'fill_in_the_blank') {
                $answer = trim(strtolower($request->fill_in_the_blank_answer));
                $correctAnswer = trim(strtolower($question->correct_answer));
                $isCorrect = $answer === $correctAnswer;
            } elseif ($questionType === 'true_false') {
                $selectedAnswer = ($request->answer === 'true');
                $isCorrect = $selectedAnswer === $question->is_true;
            }
            
            // Jika user auth, simpan progress ke database
            if (auth()->check() && auth()->user()->role_id !== 4) {
                // Cek jika soal ini sudah pernah dijawab benar
                $existingCorrectProgress = Progress::where([
                    'user_id' => $userId,
                    'material_id' => $material->id,
                    'question_id' => $question->id,
                    'is_correct' => true
                ])->exists();
                
                // Jika belum pernah dijawab benar atau jawaban saat ini salah, catat percobaan baru
                if (!$existingCorrectProgress || !$isCorrect) {
                    // Hitung jumlah percobaan sebelumnya
                    $attemptsCount = Progress::where([
                        'user_id' => $userId,
                        'material_id' => $material->id,
                        'question_id' => $question->id
                    ])->count();
                    
                    // Fix attempt number logic
                    $attemptNumber = $attemptsCount > 0 ? $attemptsCount + 1 : 1;
                    
                    // Buat record baru
                    $newAttempt = Progress::create([
                        'user_id' => $userId,
                        'material_id' => $material->id,
                        'question_id' => $question->id,
                        'is_correct' => $isCorrect,
                        'is_answered' => true,
                        'attempt_number' => $attemptNumber
                    ]);
                    
                    // Cache buster untuk memaksa refresh leaderboard
                    Cache::forget('leaderboard_data');
                }
            } else {
                // Untuk guest, simpan progress di session
                $sessionKey = 'guest_progress';
                $guestProgress = session($sessionKey, []);
                
                $progressKey = $material->id . '_' . $question->id;
                $guestProgress[$progressKey] = [
                    'is_correct' => $isCorrect,
                    'attempt_number' => isset($guestProgress[$progressKey]) ? 
                        $guestProgress[$progressKey]['attempt_number'] + 1 : 1
                ];
                
                session([$sessionKey => $guestProgress]);
            }
            
            // Response sesuai hasil jawaban
            if ($isCorrect) {
                return response()->json([
                    'status' => 'success',
                    'message' => $successMessage,
                    'selectedAnswerText' => $selectedAnswerText,
                    'correctAnswerText' => $correctAnswerText,
                    'explanation' => $explanation,
                    'hasNextQuestion' => false,
                    'levelUrl' => route('mahasiswa.materials.questions.levels', [
                        'material' => $material->id,
                        'difficulty' => $request->input('difficulty')
                    ])
                ]);
            } else {
                // Jika jawaban salah, tetap di halaman soal yang sama
                $nextUrl = route('mahasiswa.materials.questions.show', [
                    'material' => $material->id,
                    'difficulty' => $difficulty,
                    'question' => $question->id
                ]);
                
                $hasNextQuestion = true; // Tetap true untuk memastikan tombol "Coba Lagi" muncul
            }
            
            return response()->json([
                'status' => $isCorrect ? 'success' : 'error',
                'message' => $isCorrect ? $successMessage : 'Jawaban salah, coba lagi.',
                'hasNextQuestion' => $hasNextQuestion,
                'nextUrl' => $nextUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display question levels for a material
     */
    public function showLevels(Material $material, Request $request)
    {
        $materials = Material::orderBy('created_at', 'asc')->get();
        $difficulty = $request->query('difficulty', 'beginner');
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        // Filter soal berdasarkan difficulty
        $questions = $material->questions()->where('difficulty', $difficulty)->get();
        
        // Jika user adalah guest, batasi hanya 3 soal
        if ($isGuest) {
            $questions = $questions->take(3);
        } else {
            // Untuk user terdaftar, gunakan konfigurasi dari admin
            $config = QuestionBankConfig::where('material_id', $material->id)
                ->where('is_active', true)
                ->first();
                
            if ($config) {
                $countField = $difficulty . '_count';
                $limit = $config->$countField;
                $questions = $questions->take($limit);
            }
        }
        
        // Get answered questions for progress tracking
        $answeredQuestionIds = collect([]);

        // Check if user is a guest
        if ($isGuest) {
            // For guest users, check session data
            $materialProgress = session('guest_progress.' . $material->id, []);
            
            // Convert session data to a collection of question IDs
            if (!empty($materialProgress)) {
                $answeredQuestionIds = collect(array_keys($materialProgress));
            }
        } else {
            // For logged-in users, get from database
            $answeredQuestionIds = Progress::where('user_id', auth()->id())
                ->where('material_id', $material->id)
                ->where('is_correct', true)
                ->pluck('question_id');
        }
        
        $levels = [];
        $questionsArray = $questions->toArray();
        
        foreach ($questions as $index => $question) {
            $questionIndex = $index + 1;
            $isAnswered = $answeredQuestionIds->contains($question->id);
            
            if ($isAnswered) {
                // Question already answered correctly, mark as completed
                $status = 'completed';
            } elseif ($questionIndex === 1) {
                // First question is always unlocked
                $status = 'unlocked';
            } elseif ($index > 0 && $answeredQuestionIds->contains($questions[$index-1]->id)) {
                // Previous question was answered correctly, unlock this one
                $status = 'unlocked';
            } else {
                // Previous question not answered correctly, keep this locked
                $status = 'locked';
            }
            
            $levels[] = [
                'level' => $questionIndex,
                'question_id' => $question->id,
                'status' => $status,
                'difficulty' => $question->difficulty
            ];
        }
        
        return view('mahasiswa.materials.questions.levels', compact(
            'material', 
            'materials', 
            'levels', 
            'difficulty',
            'isGuest'
        ));
    }

    public function dashboard()
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
        
        // Get other data you need for the dashboard...
        
        return view('mahasiswa.dashboard.index', [
            'totalMaterials' => $totalMaterials,
            'configuredTotalQuestions' => $configuredTotalQuestions,
            'configuredEasyQuestions' => $configuredEasyQuestions,
            'configuredMediumQuestions' => $configuredMediumQuestions,
            'configuredHardQuestions' => $configuredHardQuestions,
            // other variables...
        ]);
    }

    /**
     * Debug endpoint for guest progress issues
     */
    public function debugGuestProgressIssue(Request $request, $materialId)
    {
        // Check current session structure
        $sessionId = session()->getId();
        $guestProgress = session('guest_progress', []);
        $materialProgress = session('guest_progress.' . $materialId, []);
        
        // Check for specific question_id progress
        $specificProgress = [];
        foreach ($guestProgress as $key => $progress) {
            if (strpos($key, $materialId . '_') === 0) {
                $specificProgress[$key] = $progress;
            }
        }
        
        // Check current question IDs for this material and difficulty
        $difficulty = $request->query('difficulty', 'beginner');
        $questions = Question::where('material_id', $materialId)
                      ->where('difficulty', $difficulty)
                      ->get(['id', 'difficulty']);
        
        // Return all the debug info
        return response()->json([
            'session_id' => $sessionId,
            'guest_progress' => $guestProgress,
            'material_progress' => $materialProgress,
            'specific_progress' => $specificProgress,
            'material_id' => $materialId,
            'difficulty' => $difficulty,
            'available_questions' => $questions->pluck('id')->toArray()
        ]);
    }

    public function submitAnswer(Request $request, Material $material, Question $question)
    {
        try {
            $difficulty = $request->input('difficulty', 'beginner');
            $userId = auth()->id() ?? session()->getId();
            $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
            
            // Default messages
            $successMessage = 'Jawaban benar!';
            
            // Logic untuk mengecek jawaban
            $isCorrect = false;
            $questionType = $question->question_type;
            
            // Check jawaban berdasarkan tipe soal
            if ($questionType === 'multiple_choice') {
                $selectedAnswer = Answer::findOrFail($request->answer);
                $isCorrect = $selectedAnswer->is_correct;
            } elseif ($questionType === 'fill_in_the_blank') {
                $answer = trim(strtolower($request->fill_in_the_blank_answer));
                $correctAnswer = trim(strtolower($question->correct_answer));
                $isCorrect = $answer === $correctAnswer;
            } elseif ($questionType === 'true_false') {
                $selectedAnswer = ($request->answer === 'true');
                $isCorrect = $selectedAnswer === $question->is_true;
            }
            
            // Gunakan HANYA kode ini untuk menyimpan progress
            if (auth()->check() && auth()->user()->role_id !== 4) {
                // Cek jika soal ini sudah pernah dijawab benar
                $existingCorrectProgress = Progress::where([
                    'user_id' => $userId,
                    'material_id' => $material->id,
                    'question_id' => $question->id,
                    'is_correct' => true
                ])->exists();
                
                // Jika belum pernah dijawab benar atau jawaban saat ini salah, catat percobaan baru
                if (!$existingCorrectProgress || !$isCorrect) {
                    // Hitung jumlah percobaan sebelumnya
                    $attemptsCount = Progress::where([
                        'user_id' => $userId,
                        'material_id' => $material->id,
                        'question_id' => $question->id
                    ])->count();
                    
                    // Fix attempt number logic
                    $attemptNumber = $attemptsCount > 0 ? $attemptsCount + 1 : 1;
                    
                    // Buat record baru
                    $newAttempt = Progress::create([
                        'user_id' => $userId,
                        'material_id' => $material->id,
                        'question_id' => $question->id,
                        'is_correct' => $isCorrect,
                        'is_answered' => true,
                        'attempt_number' => $attemptNumber
                    ]);
                    
                    // Cache buster untuk memaksa refresh leaderboard
                    Cache::forget('leaderboard_data');
                }
            }
            
            // Kode selanjutnya tetap sama
        } catch (\Exception $e) {
            \Log::error("Error submit answer: " . $e->getMessage());
            // Error handling
        }
    }
} 