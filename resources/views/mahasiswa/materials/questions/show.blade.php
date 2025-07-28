@extends('mahasiswa.layouts.app')

@section('title', 'Latihan Soal - ' . $material->title)

@push('css')
<link rel="stylesheet" href="{{ asset('css/material-show.css') }}">
<link rel="stylesheet" href="{{ asset('css/question-review.css') }}">
<style>
    .materi-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }
    
    .materi-card-body {
        padding: 25px;
    }
    
    .question-text {
        font-size: 16px;
        line-height: 1.5;
        background-color: #f8f9fa;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    .answer-option {
        transition: all 0.2s ease;
        cursor: pointer;
        background-color: #f8f9fa;
        padding: 10px !important;
        margin-bottom: 8px !important;
    }
    
    .answer-option:hover {
        background-color: #e9ecef;
    }
    
    /* Enhanced feedback styling */
    .exercise-feedback {
        max-width: 600px;
        margin: 0 auto;
        background-color: #1e2a3a;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        color: white;
    }
    
    .feedback-container {
        padding: 30px;
        text-align: center;
    }
    
    .feedback-icon {
        margin: 0 auto 20px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .feedback-icon.success {
        background-color: #00c07f;
    }
    
    .feedback-icon.error {
        background-color: #ff5a5a;
    }
    
    .feedback-icon i {
        font-size: 50px;
        color: white;
    }
    
    .feedback-status h3 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .text-success {
        color: #00c07f !important;
    }
    
    .text-danger {
        color: #ff5a5a !important;
    }
    
    .explanation-box {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: #e0e0e0;
        margin-top: 20px;
        padding: 15px;
    }
    
    .explanation-box h5 {
        color: white;
        font-size: 18px;
        margin-bottom: 10px;
    }
    
    .feedback-actions {
        margin-top: 25px;
    }
    
    .feedback-actions .btn {
        padding: 10px 25px;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .feedback-actions .btn:hover {
        transform: translateY(-2px);
    }
    
    .btn-outline-light {
        color: white;
        border-color: rgba(255, 255, 255, 0.5);
    }
    
    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: white;
    }
    
    .btn-success {
        background-color: #00c07f;
        border-color: #00c07f;
    }
    
    .btn-success:hover {
        background-color: #00a06a;
        border-color: #00a06a;
    }
    
    /* Filter buttons */
    .filter-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .filter-buttons .btn {
        border-radius: 8px;
        padding: 8px 20px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .filter-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    /* Button colors */
    .btn-primary, .btn-outline-primary:hover {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .btn-success, .btn-outline-success:hover {
        background-color: #198754;
        border-color: #198754;
    }
    
    .btn-warning, .btn-outline-warning:hover {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }
    
    .btn-danger, .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    /* Trophy styles */
    .trophy-circle {
        background-color: #444;
        transition: all 0.3s ease;
    }
    
    .trophy-icon-disabled {
        color: #777;
        font-size: 20px;
    }
    
    .trophy-icon {
        color: #FFD700;
        font-size: 20px;
        text-shadow: 0 0 10px #FFD700;
        animation: glow 1.5s infinite alternate;
    }
    
    @keyframes glow {
        from {
            text-shadow: 0 0 5px #FFD700, 0 0 10px #FFD700;
        }
        to {
            text-shadow: 0 0 10px #FFD700, 0 0 20px #FFD700, 0 0 30px #FFD700;
        }
    }
    
    .trophy.completed .trophy-circle {
        background-color: #444;
        border: 2px solid #FFD700;
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.5);
    }
    
    #tryAgainBtn.btn-warning:hover {
        background-color: #ffc107; /* Same as the default */
        color: #212529;
        border-color: #ffc107;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 215, 0, 0.3);
    }
    
    /* Improved code block formatting with less spacing */
    .question-text pre,
    .question-text code {
        background-color: #f1f3f5;
        border-radius: 4px;
        padding: 10px;
        font-family: "Courier New", Courier, monospace;
        overflow-x: auto;
        margin: 8px 0;
        white-space: pre-wrap;
        font-size: 14px; /* Slightly smaller font for code */
    }
    
    /* Make sure all images display properly */
    .question-text img,
    .answer-text img {
        max-width: 100%;
        height: auto;
        margin: 0.5rem 0;
    }
    
    /* Reduce spacing in paragraphs */
    .question-text p,
    .answer-text p {
        margin-bottom: 0.75rem;
    }
    
    /* Fix whitespace in content with reduced spacing */
    .whitespace-pre-wrap {
        white-space: pre-wrap !important;
    }
    
    /* Tighten headings */
    h5.mb-3 {
        margin-bottom: 10px !important;
    }
    
    /* More compact question header */
    .question-header {
        margin-bottom: 15px !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <h1 class="materi-heading">Latihan Soal: {{ $material->title }}</h1>
    <div class="heading-underline mb-4"></div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(auth()->check() && auth()->user()->role_id === 4)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Mode Tamu Aktif!</strong> 
            Anda hanya dapat melihat sebagian dari soal latihan ini. Untuk akses penuh, silakan 
            <a href="{{ route('login') }}" class="alert-link" onclick="event.preventDefault(); document.getElementById('guest-logout-login-form').submit();">login</a> 
            atau 
            <a href="{{ route('register') }}" class="alert-link" onclick="event.preventDefault(); document.getElementById('guest-logout-register-form').submit();">daftar</a> 
            sebagai mahasiswa.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <!-- Hidden forms for guest logout and redirect -->
        <form id="guest-logout-login-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="redirect" value="{{ route('login') }}">
        </form>

        <form id="guest-logout-register-form" action="{{ route('guest.logout') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="redirect" value="{{ route('register') }}">
        </form>
    @endif

    @if($currentQuestion)
        @include('mahasiswa.partials.question')
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
            </div>
            <h3 class="mb-3">Selamat! Semua Soal Telah Terjawab</h3>
            <p class="text-muted mb-4">
                Anda telah menyelesaikan semua soal pada materi ini.
            </p>
            <div class="mt-4">
                <a href="{{ route('mahasiswa.materials.questions.levels', [
                    'material' => $material->id,
                    'difficulty' => $difficulty
                ]) }}" class="btn btn-success me-2">
                    <i class="fas fa-list-ol me-2"></i>Kembali ke Level
                </a>
                <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn btn-primary me-2">
                    <i class="fas fa-book me-2"></i>Kembali ke Materi
                </a>
                <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function redirectAfterCompletion() {
    window.location.href = "{{ route('mahasiswa.materials.show', $material->id) }}";
}

function initializeQuestionForm() {
    const questionForm = document.getElementById('questionForm');
    const checkAnswerBtn = document.getElementById('checkAnswerBtn');
    const feedbackElement = document.querySelector('.exercise-feedback');
    const feedbackIcon = document.getElementById('feedbackIcon');
    const feedbackStatus = document.getElementById('feedbackStatus');
    const tryAgainBtn = document.getElementById('tryAgainBtn');
    const nextQuestionBtn = document.getElementById('nextQuestionBtn');
    const explanationBox = document.getElementById('explanationBox');
    const explanationText = document.getElementById('explanationText');
    const isGuest = {{ auth()->check() ? (auth()->user()->role_id === 4 ? 'true' : 'false') : 'true' }};
    const currentQuestionNumber = {{ $currentQuestionNumber ?? 1 }};
    
    // Fungsi untuk menampilkan pesan semua soal telah terjawab
    function showAllQuestionsCompleted() {
        // Sembunyikan form soal dan feedback
        if (questionForm) {
            questionForm.style.display = 'none';
        }
        if (feedbackElement) {
            feedbackElement.style.display = 'none';
        }
        
        // Tampilkan pesan semua soal telah terjawab
        const questionContainer = document.getElementById('questionContainer');
        if (questionContainer) {
            questionContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h3 class="mb-3">Selamat! Semua Soal Telah Terjawab</h3>
                    <p class="text-muted mb-4">Anda telah menyelesaikan semua soal pada materi ini.</p>
                    <div class="mt-4">
                        <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn btn-primary me-2">
                            <i class="fas fa-book me-2"></i>Kembali ke Materi
                        </a>
                        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            `;
        }
    }
    
    if (questionForm) {
        questionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (checkAnswerBtn) {
                checkAnswerBtn.disabled = true;
                checkAnswerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memeriksa...';
            }
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                showFeedback(data);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memeriksa jawaban. Silakan coba lagi.');
                if (checkAnswerBtn) {
                    checkAnswerBtn.disabled = false;
                    checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                }
            });
        });
    }
    
    // Make the entire answer option clickable
    const answerOptions = document.querySelectorAll('.answer-option');
    answerOptions.forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        });
    });
}

// Cek apakah tidak ada soal yang ditampilkan (berarti semua sudah terjawab)
document.addEventListener('DOMContentLoaded', function() {
    // Jika tidak ada form soal, berarti semua soal sudah terjawab
    const questionForm = document.getElementById('questionForm');
    const noQuestionsMessage = document.querySelector('.alert.alert-info');
    
    if (!questionForm && noQuestionsMessage) {
        // Ganti pesan default dengan tampilan yang lebih menarik
        const materiCardBody = document.querySelector('.materi-card-body');
        if (materiCardBody) {
            materiCardBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h3 class="mb-3">Selamat! Semua Soal Telah Terjawab</h3>
                    <p class="text-muted mb-4">Anda telah menyelesaikan semua soal pada materi ini.</p>
                    <div class="mt-4">
                        <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn btn-primary me-2">
                            <i class="fas fa-book me-2"></i>Kembali ke Materi
                        </a>
                        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            `;
        }
    }
    
    // Initialize form
    initializeQuestionForm();
});

function showQuestionReview(difficulty) {
    const mainContainer = document.querySelector('.container-fluid');
    
    // Show loading state
    mainContainer.innerHTML = `
        <div class="text-center my-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Memuat review soal...</p>
        </div>
    `;
    
    // Fetch the review content
    const url = `{{ route('mahasiswa.materials.questions.review', $material->id) }}${difficulty && difficulty !== 'all' ? `?difficulty=${difficulty}` : ''}`;
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        mainContainer.innerHTML = `
            <h1 class="materi-heading">Review Soal: {{ $material->title }}</h1>
            <div class="heading-underline mb-4"></div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="review-content">
                                ${html}
                            </div>
                            <div class="mt-4 text-center">
                                <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Materi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    })
    .catch(error => {
        console.error('Error:', error);
        mainContainer.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Terjadi kesalahan saat memuat review soal. 
                <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn btn-sm btn-primary ms-3">
                    Kembali ke Materi
                </a>
            </div>
        `;
    });
}

// Only include question-specific JavaScript if there is a current question
@if($currentQuestion)
function submitAnswer() {
    const selectedOption = document.querySelector('input[name="answer"]:checked');
    if (!selectedOption) {
        showAlert('Silakan pilih jawaban terlebih dahulu', 'warning');
        return;
    }

    // Disable submit button
    const submitButton = document.getElementById('checkAnswerBtn');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memeriksa...';
    }

    // Get attempts count first
    fetch(`{{ route('mahasiswa.materials.questions.attempts', ['material' => $material->id, 'question' => $currentQuestion->id]) }}`)
    .then(response => response.json())
    .then(data => {
        const attemptCount = data.attempts;
        
        // Calculate potential score based on attempts
        let potentialScore = 0;
        const difficulty = '{{ $currentQuestion->difficulty }}';
        
        if (difficulty === 'beginner') {
            potentialScore = attemptCount === 0 ? 3 : (attemptCount === 1 ? 2 : 1);
        } else if (difficulty === 'medium') {
            potentialScore = attemptCount === 0 ? 6 : (attemptCount === 1 ? 4 : 2);
        } else if (difficulty === 'hard') {
            potentialScore = attemptCount === 0 ? 9 : (attemptCount === 1 ? 6 : (attemptCount === 2 ? 4 : 2));
        }

        // Submit answer
        fetch('{{ route('questions.check-answer') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                question_id: '{{ $currentQuestion->id }}',
                material_id: '{{ $material->id }}',
                answer: selectedOption.value,
                attempts: attemptCount + 1,
                potential_score: potentialScore,
                difficulty: '{{ request()->query('difficulty') }}'
            })
        })
        .then(response => response.json())
        .then(result => {
            showFeedback(result, potentialScore, attemptCount + 1);
            
            if (submitButton && result.status !== 'success') {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan saat memeriksa jawaban', 'error');
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
            }
        });
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Terjadi kesalahan saat mengambil data percobaan', 'error');
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
        }
    });
}
@endif

function showFeedback(data) {
    const feedbackElement = document.querySelector('.exercise-feedback');
    const feedbackStatus = document.getElementById('feedbackStatus');
    const feedbackIcon = document.getElementById('feedbackIcon');
    const tryAgainBtn = document.getElementById('tryAgainBtn');
    const nextQuestionBtn = document.getElementById('nextQuestionBtn');
    const questionForm = document.getElementById('questionForm');
    
    // Set status dan icon
    feedbackStatus.innerHTML = `<h3 class="${data.status === 'success' ? 'text-success' : 'text-danger'}">${data.message}</h3>`;
    feedbackIcon.className = `feedback-icon ${data.status === 'success' ? 'success' : 'error'}`;
    feedbackIcon.innerHTML = `<i class="fas ${data.status === 'success' ? 'fa-check-circle' : 'fa-times-circle'} fa-3x"></i>`;

    if (data.status === 'success') {
        // Penanganan khusus untuk guest dengan redirect langsung
        if (data.redirect_url) {
            // Tampilkan feedback sebentar lalu redirect
            feedbackElement.style.display = 'block';
            feedbackElement.classList.remove('alert-danger');
            feedbackElement.classList.add('alert-success');
            feedbackIcon.className = 'fas fa-check-circle';
            feedbackStatus.textContent = data.message;
            
            // Redirect setelah 1.5 detik
            setTimeout(() => {
                window.location.href = data.redirect_url;
            }, 1500);
            
            return; // Penting: hentikan eksekusi di sini
        }
        
        // Kode penanganan normal lainnya
        tryAgainBtn.style.display = 'none';
        nextQuestionBtn.style.display = 'inline-block';
        nextQuestionBtn.innerHTML = '<i class="fas fa-list-ol me-2"></i>Kembali ke Level';
        nextQuestionBtn.onclick = () => {
            const currentDifficulty = '{{ request()->query('difficulty') }}';
            const levelUrl = data.levelUrl || '{{ route('mahasiswa.materials.questions.levels', ['material' => $material->id, 'difficulty' => request()->query('difficulty')]) }}';
            redirectToLevelWithScroll(levelUrl);
        };
    } else {
        tryAgainBtn.style.display = 'inline-block';
        nextQuestionBtn.style.display = 'none';
        
        tryAgainBtn.onclick = () => {
            feedbackElement.style.display = 'none';
            questionForm.style.display = 'block';
            
            const checkAnswerBtn = document.getElementById('checkAnswerBtn');
            if (checkAnswerBtn) {
                checkAnswerBtn.disabled = false;
                checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
            }
        };
    }

    feedbackElement.style.display = 'block';
    questionForm.style.display = 'none';
}

// Event handler submit
$(document).ready(function() {
    $('#questionForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const currentDifficulty = '{{ request()->query('difficulty') }}';
        formData.append('difficulty', currentDifficulty);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize() + '&difficulty=' + currentDifficulty,
            success: function(response) {
                console.log("Answer response:", response);
                
                if (response.status === 'success') {
                    // Show success feedback
                    showFeedback(true, response.message, response.selectedAnswerText, 
                                 response.correctAnswerText, response.explanation);
                    
                    // Then redirect after a delay
                    setTimeout(function() {
                        if (response.hasNextQuestion && response.nextUrl) {
                            console.log("Redirecting to next question:", response.nextUrl);
                            // Store in localStorage that we're redirecting
                            localStorage.setItem('redirecting_from_question', 'true');
                            window.location.href = response.nextUrl;
                        } else {
                            console.log("Redirecting to levels page:", response.levelUrl);
                            localStorage.setItem('questionCompleted', 'true');
                            window.location.href = response.levelUrl + (response.levelUrl.includes('?') ? '&' : '?') + 'scroll=true';
                        }
                    }, 2000);
                } else {
                    // Handle incorrect answer
                    showFeedback(false, response.message, response.selectedAnswerText, 
                                 response.correctAnswerText, response.explanation);
                }
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Add console logs for debugging
    console.log("DOM loaded on question page");
    console.log("Tutorial status:", sessionStorage.getItem('question_answer_tutorial_complete'));
    
    // Check if this is first time visiting a question
    if (!sessionStorage.getItem('question_answer_tutorial_complete')) {
        console.log("Starting question tutorial in 1 second");
        setTimeout(startQuestionAnswerTutorial, 1000);
    }
});

function startQuestionAnswerTutorial() {
    // Define the tutorial steps
    console.log("Tutorial function called");
    
    // Check if question elements exist
    const questionText = document.querySelector('.question-text');
    const optionsContainer = document.querySelector('.options-container');
    const checkAnswerBtn = document.getElementById('checkAnswerBtn');
    
    console.log("Found elements:", {
        questionText: !!questionText,
        optionsContainer: !!optionsContainer,
        checkAnswerBtn: !!checkAnswerBtn
    });
    
    // Only proceed if elements exist
    if (!questionText || !optionsContainer || !checkAnswerBtn) {
        console.log("Missing required elements for tutorial");
        return;
    }
    
    const steps = [
        {
            intro: "Sekarang Anda berada di halaman soal. Mari kita pelajari cara menjawab soal."
        },
        {
            element: questionText,
            intro: "Ini adalah teks pertanyaan yang harus Anda jawab."
        },
        {
            element: optionsContainer,
            intro: "Pilih salah satu jawaban yang menurut Anda benar."
        },
        {
            element: checkAnswerBtn,
            intro: "Setelah memilih jawaban, klik tombol ini untuk memeriksa jawaban Anda."
        },
        {
            intro: "Jika jawaban benar, Anda dapat melanjutkan ke soal berikutnya. Jika salah, Anda dapat mencoba lagi."
        }
    ];

    // Start the tutorial
    try {
        const tour = introJs().setOptions({
            steps: steps,
            showProgress: true,
            exitOnOverlayClick: true,
            showBullets: false,
            scrollToElement: true,
            nextLabel: 'Berikutnya',
            prevLabel: 'Sebelumnya',
            doneLabel: 'Mulai Menjawab'
        });
        
        tour.oncomplete(function() {
            // Mark as completed in session storage
            sessionStorage.setItem('question_answer_tutorial_complete', 'true');
            console.log("Tutorial completed and marked in session storage");
        });
        
        tour.start();
        console.log("Tutorial started successfully");
    } catch (error) {
        console.error("Error starting tutorial:", error);
    }
}

// Fix for TinyMCE content display
document.addEventListener('DOMContentLoaded', function() {
    // Process all question-text elements to render HTML properly
    const questionTextElements = document.querySelectorAll('.question-text');
    
    questionTextElements.forEach(element => {
        // Only apply if the content appears to be raw HTML
        if (element.textContent.includes('&lt;') || 
            element.textContent.includes('<p class="whitespace-pre-wrap') || 
            element.textContent.includes('<div class="relative group/copy')) {
            
            // Extract the content and re-render it properly
            let rawHtml = element.textContent;
            element.innerHTML = rawHtml;
        }
        
        // Ensure code blocks have proper styling
        const codeBlocks = element.querySelectorAll('pre');
        codeBlocks.forEach(block => {
            block.classList.add('language-java', 'formatted');
            block.style.backgroundColor = '#f1f3f5';
            block.style.padding = '1rem';
            block.style.borderRadius = '4px';
            block.style.fontFamily = 'monospace';
            block.style.overflow = 'auto';
            block.style.whiteSpace = 'pre-wrap';
        });
    });
});

// Simpan level yang sedang dikerjakan ke localStorage
document.addEventListener('DOMContentLoaded', function() {
    const currentLevel = '{{ $currentQuestion->id }}';
    localStorage.setItem('currentQuestionLevel', currentLevel);
});

// Ketika jawaban benar dan kembali ke halaman level
function redirectToLevelWithScroll(levelUrl) {
    // Save status that question was answered correctly
    localStorage.setItem('questionCompleted', 'true');
    
    // If levelUrl not provided, use default URL
    if (!levelUrl) {
        levelUrl = "{{ route('mahasiswa.materials.questions.levels', ['material' => $material->id, 'difficulty' => request()->query('difficulty')]) }}";
    }
    
    // Add scroll parameter
    const separator = levelUrl.includes('?') ? '&' : '?';
    window.location.href = `${levelUrl}${separator}scroll=true`;
}
</script>
@endpush 