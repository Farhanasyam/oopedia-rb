<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Material;
use App\Models\StudentAnswer;

class QuestionController extends Controller
{
    public function checkAnswer(Request $request)
    {
        // Tambahkan di awal fungsi checkAnswer atau submitAnswer pada setiap controller
        Log::info("CONTROLLER_TRACE: " . __CLASS__ . " handling answer submission");
        
        // Log semua data request untuk debugging
        Log::info('Request data for checkAnswer:', $request->all());
        
        try {
            // Validasi dasar
            $request->validate([
                'question_id' => 'required|exists:questions,id',
                'material_id' => 'required|exists:materials,id'
            ]);
            
            $question = Question::findOrFail($request->question_id);
            $isCorrect = false;
            $correctAnswerText = null;
            $selectedAnswerText = null;
            $explanation = null;
            
            if ($question->question_type === 'fill_in_the_blank') {
                // Validasi untuk soal fill in the blank
                if (!$request->has('fill_in_the_blank_answer')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'The answer field is required.'
                    ], 422);
                }
                
                $userAnswer = $request->fill_in_the_blank_answer;
                if (empty(trim($userAnswer))) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'The answer field is required.'
                    ], 422);
                }
                
                $correctAnswer = Answer::where('question_id', $question->id)
                                    ->where('is_correct', true)
                                    ->first();
                
                if ($correctAnswer) {
                    // Normalisasi jawaban untuk perbandingan yang lebih fleksibel
                    $normalizedUserAnswer = strtolower(trim($userAnswer));
                    $normalizedCorrectAnswer = strtolower(trim($correctAnswer->answer_text));
                    
                    // Cek apakah ada beberapa jawaban yang benar (dipisahkan dengan pipe |)
                    $acceptableAnswers = explode('|', $normalizedCorrectAnswer);
                    $isCorrect = false;
                    
                    foreach ($acceptableAnswers as $answer) {
                        if (trim($normalizedUserAnswer) === trim($answer)) {
                            $isCorrect = true;
                            break;
                        }
                    }
                    
                    $selectedAnswerText = $userAnswer;
                    
                    if (!$isCorrect) {
                        $correctAnswerText = $correctAnswer->answer_text;
                    }
                    
                    $explanation = $correctAnswer->explanation;
                }
            } elseif ($question->question_type === 'radio_button') {
                // Validasi untuk soal pilihan ganda
                if (!$request->has('answer')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Pilih salah satu jawaban.'
                    ], 422);
                }
                
                $selectedAnswer = Answer::findOrFail($request->answer);
                $isCorrect = $selectedAnswer->is_correct;
                $selectedAnswerText = $selectedAnswer->answer_text;
                
                if (!$isCorrect) {
                    $correctAnswer = Answer::where('question_id', $question->id)
                                        ->where('is_correct', true)
                                        ->first();
                    $correctAnswerText = $correctAnswer->answer_text ?? null;
                }
                
                $explanation = $selectedAnswer->explanation;
            } else {
                // Validasi untuk soal drag and drop
                if (!$request->has('drag_and_drop_answers')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Silakan lengkapi semua jawaban drag and drop.',
                    ], 422);
                }

                $request->validate([
                    'drag_and_drop_answers' => 'required|json',
                ]);

                $userAnswers = json_decode($request->drag_and_drop_answers, true);

                // Ambil jawaban yang benar dari DB dan urutkan berdasarkan zona/id
                $correctAnswers = $question->answers()
                    ->where('is_correct', true)
                    ->orderBy('id') // atau bisa juga orderBy('zone') jika ada kolom zone
                    ->pluck('answer_text')
                    ->toArray();

                // Ambil jawaban user dan urutkan berdasarkan zona (1,2,3,...)
                $userSubmittedAnswers = collect($userAnswers)
                    ->sortBy('zone') // pastikan nama key 'zone' sesuai dengan yang dikirim di front-end
                    ->pluck('answer')
                    ->toArray();

                // Cek apakah semua jawaban benar
                $isCorrect = ($userSubmittedAnswers === $correctAnswers);

                // Jawaban user dan jawaban benar dalam bentuk teks (json)
                $selectedAnswerText = json_encode($userSubmittedAnswers);
                $correctAnswerText = $isCorrect ? null : json_encode($correctAnswers);

                // Penjelasan dari salah satu jawaban yang benar
                $firstCorrect = $question->answers()->where('is_correct', true)->first();
                $explanation = $firstCorrect?->explanation;
            }
            
            // Update progress jika user login
            if (auth()->check()) {
                // Hitung attempt number dengan benar
                $attemptsCount = Progress::where([
                    'user_id' => auth()->id(),
                    'material_id' => $request->material_id,
                    'question_id' => $question->id
                ])->count();
                
                $attemptNumber = $attemptsCount > 0 ? $attemptsCount + 1 : 1;
                
                Progress::updateOrCreate(
                    [
                        'user_id' => auth()->id(),
                        'material_id' => $request->material_id,
                        'question_id' => $question->id
                    ],
                    [
                        'is_correct' => $isCorrect,
                        'is_answered' => true,
                        'attempt_number' => $attemptNumber
                    ]
                );
            } else {
                // Untuk guest, simpan progress di session
                $sessionKey = 'guest_progress';
                $guestProgress = session($sessionKey, []);
                
                // Format 1: "material_id_question_id" => ["is_correct" => true]
                $progressKey = $request->material_id . '_' . $question->id;
                $guestProgress[$progressKey] = [
                    'is_correct' => $isCorrect,
                    'attempt_number' => isset($guestProgress[$progressKey]) ? 
                        $guestProgress[$progressKey]['attempt_number'] + 1 : 1
                ];
                
                session([$sessionKey => $guestProgress]);
                
                // Format 2: guest_progress.material_id => [question_id => [...]]
                if ($isCorrect) {
                    // Pastikan array guest_progress ada
                    if (!session()->has('guest_progress')) {
                        session(['guest_progress' => []]);
                    }
                    
                    // Pastikan array untuk material ini ada
                    if (!session()->has('guest_progress.' . $request->material_id)) {
                        session(['guest_progress.' . $request->material_id => []]);
                    }
                    
                    // Simpan progres dengan format yang dibutuhkan untuk level tracking
                    $currentProgress = session('guest_progress.' . $request->material_id, []);
                    $currentProgress[$question->id] = [
                        'is_correct' => true,
                        'answered_at' => now()->toDateTimeString()
                    ];
                    
                    session(['guest_progress.' . $request->material_id => $currentProgress]);
                    
                    Log::info('Guest progress saved', [
                        'session_after' => session()->all(),
                        'material_progress' => session('guest_progress.' . $request->material_id)
                    ]);
                }
            }
            
            // Cek apakah ada soal berikutnya
            $nextQuestion = Question::where('material_id', $request->material_id)
                                ->where('id', '>', $question->id)
                                ->orderBy('id')
                                ->first();
            
            return response()->json([
                'status' => $isCorrect ? 'success' : 'error',
                'message' => $isCorrect ? 'Jawaban Benar!' : 'Jawaban Salah',
                'selectedAnswerText' => $selectedAnswerText,
                'correctAnswerText' => $correctAnswerText,
                'explanation' => $explanation,
                'hasNextQuestion' => !is_null($nextQuestion),
                'nextUrl' => $nextQuestion ? route('mahasiswa.materials.questions.show', [
                    'material' => $request->material_id,
                    'question' => $nextQuestion->id,
                    'difficulty' => $request->difficulty ?? 'all'
                ]) : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error in checkAnswer: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index($id)
    {
        $material = Material::with(['questions', 'media'])->findOrFail($id);
        
        // Dapatkan total questions dan completed questions
        $totalQuestions = $material->questions->count();
        
        // Jika pengguna sudah login, hitung progress
        if (auth()->check()) {
            $completedQuestions = StudentAnswer::where('student_id', auth()->id())
                ->whereIn('question_id', $material->questions->pluck('id'))
                ->where('is_correct', true)
                ->distinct('question_id')
                ->count();
        } else {
            $completedQuestions = 0;
        }
        
        // Hitung persentase
        $progressPercentage = $totalQuestions > 0 ? round(($completedQuestions / $totalQuestions) * 100) : 0;
        
        return view('mahasiswa.materials.questions.index', compact(
            'material', 
            'totalQuestions', 
            'completedQuestions', 
            'progressPercentage'
        ));
    }
}