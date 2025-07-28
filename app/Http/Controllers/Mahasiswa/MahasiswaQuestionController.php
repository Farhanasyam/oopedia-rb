<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Progress;
use App\Models\Material;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MahasiswaQuestionController extends Controller
{
    public function checkAnswer(Request $request)
    {
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
            
            // Cek jawaban berdasarkan tipe soal
            if ($question->question_type === 'fill_in_the_blank') {
                $userAnswer = trim(strtolower($request->fill_in_the_blank_answer));
                $correctAnswer = trim(strtolower($question->correct_answer));
                $isCorrect = $userAnswer === $correctAnswer;
                $selectedAnswerText = $request->fill_in_the_blank_answer;
                $correctAnswerText = $question->correct_answer;
            } elseif ($question->question_type === 'true_false') {
                $userAnswer = $request->answer === 'true';
                $isCorrect = $userAnswer === $question->is_true;
                $selectedAnswerText = $userAnswer ? 'Benar' : 'Salah';
                $correctAnswerText = $question->is_true ? 'Benar' : 'Salah';
            } else {
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
            }
            
            // Update progress jika user login, simpan ke session jika guest
            if (auth()->check() && auth()->user()->role_id !== 4) {
                // Hitung attempt number dengan SQL count + 1
                $attemptsCount = Progress::where([
                    'user_id' => auth()->id(),
                    'material_id' => $request->material_id,
                    'question_id' => $question->id
                ])->count();
                
                // Debug logging to identify the issue
                \Log::info("Current attempts count for question {$question->id}: $attemptsCount");
                
                // Buat progress baru - Fix attempt number logic
                $attemptNumber = $attemptsCount > 0 ? $attemptsCount + 1 : 1;
                Progress::create([
                    'user_id' => auth()->id(),
                    'material_id' => $request->material_id,
                    'question_id' => $question->id,
                    'is_correct' => $isCorrect,
                    'is_answered' => true,
                    'attempt_number' => $attemptNumber
                ]);
                
                \Log::info("User " . auth()->id() . " menjawab soal " . $question->id . ", percobaan ke-" . ($attemptNumber) . 
                    ", kesulitan: " . $question->difficulty . ", hasil: " . ($isCorrect ? 'BENAR' : 'SALAH'));
            } else {
                // For guest users (not logged in or role_id = 4), track in session
                // Format untuk session guest_progress
                $sessionKey = 'guest_progress';
                $guestProgress = session($sessionKey, []);
                
                $progressKey = $request->material_id . '_' . $question->id;
                $guestProgress[$progressKey] = [
                    'is_correct' => $isCorrect,
                    'attempt_number' => isset($guestProgress[$progressKey]) ? 
                        $guestProgress[$progressKey]['attempt_number'] + 1 : 1
                ];
                
                session([$sessionKey => $guestProgress]);
                
                // Update format untuk level unlocking jika jawaban benar
                if ($isCorrect) {
                    // Pastikan array guest_progress ada
                    if (!session()->has('guest_progress')) {
                        session(['guest_progress' => []]);
                    }
                    
                    // Pastikan array untuk material ini ada
                    if (!session()->has('guest_progress.' . $request->material_id)) {
                        session(['guest_progress.' . $request->material_id => []]);
                    }
                    
                    // Simpan progres dengan format yang sama seperti di MaterialQuestionController
                    $currentProgress = session('guest_progress.' . $request->material_id, []);
                    $currentProgress[$question->id] = [
                        'is_correct' => true,
                        'answered_at' => now()->toDateTimeString()
                    ];
                    
                    session(['guest_progress.' . $request->material_id => $currentProgress]);
                }
            }
            
            // Tambahkan info untuk navigasi
            $answeredCount = count(session('guest_progress.' . $request->material_id, []));
            
            // PERUBAHAN: Jika jawaban benar, arahkan kembali ke halaman levels
            if ($isCorrect && (!auth()->check() || auth()->user()->role_id === 4)) {
                // Kirim respons sederhana yang mengarahkan langsung ke halaman levels
                return response()->json([
                    'status' => 'success',
                    'message' => 'Jawaban Benar!',
                    'selectedAnswerText' => $selectedAnswerText ?? null,
                    'correctAnswerText' => $correctAnswerText ?? null,
                    'explanation' => $explanation ?? null,
                    // Parameter khusus untuk menangani guest
                    'redirect_url' => route('mahasiswa.materials.questions.levels', [
                        'material' => $request->material_id,
                        'difficulty' => $request->difficulty
                    ])
                ]);
            } else {
                // Jika jawaban salah, tetap di halaman yang sama
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jawaban Salah',
                    'selectedAnswerText' => $selectedAnswerText,
                    'correctAnswerText' => $correctAnswerText,
                    'explanation' => $explanation,
                    'hasNextQuestion' => true,
                    'nextUrl' => null
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in checkAnswer: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check all answers for a material
     */
    public function checkAllAnswers(Request $request)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'answers' => 'required|array'
        ]);

        $materialId = $request->material_id;
        $answers = $request->answers;
        
        $totalQuestions = count($answers);
        $correctAnswers = 0;
        $results = [];
        
        // Check each answer
        foreach ($answers as $questionId => $answerId) {
            $question = Question::with('answers')->findOrFail($questionId);
            $selectedAnswer = $question->answers->find($answerId);
            
            $isCorrect = $selectedAnswer && $selectedAnswer->is_correct;
            $correctAnswer = $question->answers->where('is_correct', true)->first();
            
            // Hitung attempt_number
            $attemptNumber = Progress::where('user_id', auth()->id())
                ->where('question_id', $questionId)
                ->count() + 1;
            
            // Save progress regardless of correctness
            Progress::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'material_id' => $materialId,
                    'question_id' => $questionId
                ],
                [
                    'is_correct' => $isCorrect,
                    'is_answered' => true,
                    'attempt_number' => $attemptNumber
                ]
            );
            
            if ($isCorrect) {
                $correctAnswers++;
            }
            
            // Store result for feedback
            $results[$questionId] = [
                'is_correct' => $isCorrect,
                'question_text' => $question->question_text,
                'selected_answer' => $selectedAnswer ? $selectedAnswer->answer_text : null,
                'correct_answer' => $isCorrect ? null : ($correctAnswer->answer_text ?? null),
                'explanation' => $correctAnswer->explanation ?? null,
                'selected_explanation' => $selectedAnswer ? $selectedAnswer->explanation : null
            ];
        }
        
        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
        
        // Return JSON response for AJAX request
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => "Anda menjawab benar $correctAnswers dari $totalQuestions soal (Skor: $score%)",
                'score' => $score,
                'results' => $results,
                'nextUrl' => route('mahasiswa.dashboard')
            ]);
        }
        
        return redirect()->route('mahasiswa.dashboard')
            ->with('success', "Anda menjawab benar $correctAnswers dari $totalQuestions soal (Skor: $score%)");
    }

    public function submitQuiz(Request $request)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'answers' => 'required|array'
        ]);

        $materialId = $request->material_id;
        $answers = $request->answers;
        
        $totalQuestions = count($answers);
        $correctAnswers = 0;
        $results = [];
        
        // Check each answer
        foreach ($answers as $questionId => $answerId) {
            $question = Question::with('answers')->findOrFail($questionId);
            $selectedAnswer = $question->answers->find($answerId);
            
            $isCorrect = $selectedAnswer && $selectedAnswer->is_correct;
            $correctAnswer = $question->answers->where('is_correct', true)->first();
            
            // Hitung attempt_number
            $attemptNumber = Progress::where('user_id', auth()->id())
                ->where('question_id', $questionId)
                ->count() + 1;
            
            // Save progress regardless of correctness
            Progress::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'material_id' => $materialId,
                    'question_id' => $questionId
                ],
                [
                    'is_correct' => $isCorrect,
                    'is_answered' => true,
                    'attempt_number' => $attemptNumber
                ]
            );
            
            if ($isCorrect) {
                $correctAnswers++;
            }
            
            // Store result for feedback
            $results[$questionId] = [
                'is_correct' => $isCorrect,
                'question_text' => $question->question_text,
                'selected_answer' => $selectedAnswer ? $selectedAnswer->answer_text : null,
                'correct_answer' => $isCorrect ? null : ($correctAnswer->answer_text ?? null),
                'explanation' => $correctAnswer->explanation ?? null,
                'selected_explanation' => $selectedAnswer ? $selectedAnswer->explanation : null
            ];
        }
        
        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
        
        // Sisa kode tetap sama
    }
}