@php
    use App\Models\Progress;
@endphp

<div class="materi-card shadow-sm rounded">
    <div class="materi-card-body p-4">
        <div id="questionContainer">
            <form id="questionForm" action="{{ route('questions.check-answer', [
                'material' => $material->id,
                'question' => $currentQuestion->id,
                'difficulty' => $difficulty
            ]) }}" method="POST">
                @csrf
                <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                <input type="hidden" name="material_id" value="{{ $material->id }}">
                <input type="hidden" name="difficulty" value="{{ $difficulty }}">
                
                <div class="question-header mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="badge bg-gradient-primary p-2 px-3">
                            <i class="fas fa-question-circle me-2"></i>
                            @php
                                $difficulty = request()->query('difficulty', 'all');
                                $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
                                
                                // Hitung jumlah soal berdasarkan konfigurasi
                                if ($difficulty !== 'all') {
                                    $difficultyQuestions = $material->questions->where('difficulty', $difficulty);
                                    
                                    // Ambil konfigurasi
                                    if ($isGuest) {
                                        // Untuk guest, maksimal 3 soal
                                        $configuredTotal = min(3, $difficultyQuestions->count());
                                    } else {
                                        // Untuk pengguna terdaftar, gunakan konfigurasi dari admin
                                        $config = App\Models\QuestionBankConfig::where('material_id', $material->id)
                                            ->where('is_active', true)
                                            ->first();
                                        
                                        if ($config) {
                                            $countField = $difficulty . '_count';
                                            $configuredTotal = $config->$countField;
                                        } else {
                                            $configuredTotal = $difficultyQuestions->count();
                                        }
                                    }
                                } else {
                                    // Jika all difficulty, hitung total dari semua tingkat kesulitan
                                    if ($isGuest) {
                                        // Untuk guest, maksimal 3 soal per tingkat
                                        $beginnerCount = min(3, $material->questions->where('difficulty', 'beginner')->count());
                                        $mediumCount = min(3, $material->questions->where('difficulty', 'medium')->count());
                                        $hardCount = min(3, $material->questions->where('difficulty', 'hard')->count());
                                        $configuredTotal = $beginnerCount + $mediumCount + $hardCount;
                                    } else {
                                        // Untuk pengguna terdaftar, gunakan konfigurasi dari admin
                                        $config = App\Models\QuestionBankConfig::where('material_id', $material->id)
                                            ->where('is_active', true)
                                            ->first();
                                        
                                        if ($config) {
                                            $configuredTotal = $config->beginner_count + $config->medium_count + $config->hard_count;
                                        } else {
                                            $configuredTotal = $material->questions->count();
                                        }
                                    }
                                }
                                
                                // Calculate the current question number 
                                $answeredInDifficulty = App\Models\Progress::where('user_id', auth()->id() ?? session()->getId())
                                    ->where('material_id', $material->id)
                                    ->where('is_correct', true);
                                    
                                if ($difficulty !== 'all') {
                                    $answeredInDifficulty = $answeredInDifficulty->whereIn('question_id', $difficultyQuestions->pluck('id'));
                                }
                                
                                $answeredCount = $answeredInDifficulty->count();
                                $currentNumberInDifficulty = $answeredCount + 1;
                                
                                if ($currentNumberInDifficulty > $configuredTotal) {
                                    $currentNumberInDifficulty = $configuredTotal;
                                }
                            @endphp
                            Soal {{ $currentNumberInDifficulty }} dari {{ $configuredTotal }}
                        </span>
                        <span class="badge bg-{{ $currentQuestion->difficulty == 'beginner' ? 'success' : ($currentQuestion->difficulty == 'medium' ? 'warning' : 'danger') }} p-2 px-3">
                            {{ ucfirst($currentQuestion->difficulty) }}
                        </span>
                    </div>
                </div>
                
                <div class="question-content mb-4">
                    <h5 class="mb-3"><i class="fas fa-question me-2"></i>Pertanyaan</h5>
                    <div class="question-text whitespace-pre-wrap">
                        {!! $currentQuestion->question_text !!}
                    </div>
                </div>
                
                <div class="answers-container">
                    <!-- Tampilkan input teks jika tipe soal adalah fill_in_the_blank -->
                    @if($currentQuestion->question_type === 'fill_in_the_blank')
                        <div class="fill-in-blank-container p-3 mb-3 rounded">
                            <label for="fill_in_the_blank_answer" class="form-label">Jawaban Anda:</label>
                            <input type="text" name="fill_in_the_blank_answer" id="fill_in_the_blank_answer" class="form-control" placeholder="Ketik jawaban Anda di sini..." required>
                        </div>
                    @else
                        <!-- Tampilkan radio button untuk tipe soal lainnya -->
                        @foreach($currentQuestion->answers as $answer)
                            <div class="answer-option p-3 mb-3 rounded d-flex align-items-center">
                                <input type="radio" name="answer" id="answer{{ $answer->id }}" value="{{ $answer->id }}" class="me-3" required>
                                <label for="answer{{ $answer->id }}" class="mb-0 w-100">{{ $answer->answer_text }}</label>
                            </div>
                        @endforeach
                    @endif
                </div>
                
                <div class="d-grid">
                    <button type="submit" id="checkAnswerBtn" class="btn btn-primary py-2">
                        <i class="fas fa-check-circle me-2"></i>Periksa Jawaban
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Feedback container (initially hidden) -->
        <div class="exercise-feedback" style="display: none;">
            <div class="feedback-container">
                <div id="feedbackIcon" class="feedback-icon">
                    <!-- Icon will be inserted here by JS -->
                </div>
                <div id="feedbackStatus">
                    <!-- Status will be inserted here by JS -->
                </div>
                <div id="explanationBox" style="display: none;" class="explanation-box mt-4 p-3 bg-light rounded">
                    <h5><i class="fas fa-info-circle me-2"></i>Penjelasan</h5>
                    <p id="explanationText" class="mb-0"></p>
                </div>
                <div class="feedback-actions mt-4">
                    <button id="tryAgainBtn" class="btn btn-outline-light px-4 py-2">
                        <i class="fas fa-redo me-2"></i>Coba Lagi
                    </button>
                    <button id="nextQuestionBtn" class="btn btn-success px-4 py-2" style="display: none;">
                        Lanjut ke Soal Berikutnya <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>