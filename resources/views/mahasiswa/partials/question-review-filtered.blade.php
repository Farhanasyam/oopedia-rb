<div class="question-review-container">
    <div class="review-header mb-4">
        <h3>Review Soal {{ $difficulty !== 'all' ? ucfirst($difficulty) : 'Semua Tingkat' }}</h3>
        <p class="text-muted">Berikut adalah review dari soal-soal yang telah Anda kerjakan.</p>
    </div>
    
    <div class="questions-list">
        @if(count($questions) > 0)
            @foreach($questions as $question)
                <div class="question-card mb-4 p-4 bg-white rounded shadow-sm">
                    <div class="question-content">
                        <div class="question-header d-flex justify-content-between mb-3">
                            <span class="badge bg-{{ $question->difficulty == 'beginner' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                {{ ucfirst($question->difficulty) }}
                            </span>
                            <span class="question-type text-muted">
                                <i class="fas fa-question-circle me-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                            </span>
                        </div>

                        <h5 class="mb-3"><i class="fas fa-question me-2"></i>Pertanyaan</h5>
                        <div class="question-text p-3 bg-light rounded mb-4">
                            {!! $question->question_text !!}
                        </div>

                        <h5 class="mt-4 mb-3"><i class="fas fa-list-ul me-2"></i>Pilihan Jawaban</h5>
                        <div class="answers-container">
                            @foreach($question->answers as $answer)
                                <div class="answer-option p-3 mb-2 rounded d-flex align-items-center {{ $answer->is_correct ? 'border-success bg-success bg-opacity-10' : '' }}">
                                    <div class="answer-text">
                                        @if($answer->is_correct)
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                        @endif
                                        {!! $answer->answer_text !!}
                                    </div>
                                </div>
                                @if($answer->is_correct && $answer->explanation)
                                    <div class="answer-explanation p-3 mb-3 bg-light rounded">
                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                        <strong>Penjelasan:</strong> {!! $answer->explanation !!}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Tidak ada soal yang tersedia untuk ditampilkan.
            </div>
        @endif
    </div>
</div>

<style>
    .question-review {
        transition: all 0.3s ease;
    }
    
    .question-review:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    
    .answer-option {
        position: relative;
        transition: all 0.2s ease;
    }
    
    .answer-option.border-success {
        border: 1px solid #00c07f;
    }
    
    .answer-explanation {
        font-size: 0.95rem;
        color: #555;
    }
</style> 