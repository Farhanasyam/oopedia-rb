@extends('mahasiswa.layouts.app')

@section('title', $material->title)

@push('css')
<link rel="stylesheet" href="{{ asset('css/material-show.css') }}">
<style>
    .materi-media-section {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-top: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .media-heading {
        font-size: 1.5rem;
        color: #2c3e50;
        margin-bottom: 20px;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
    }

    .media-item {
        transition: transform 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .media-item:hover {
        transform: translateY(-5px);
    }

    .media-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .media-caption {
        background-color: #fff;
        padding: 10px;
        font-size: 0.9rem;
        color: #555;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Judul Materi -->
    <h1 class="materi-heading">{{ $material->title }}</h1>
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

    @guest
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Mode Tamu Aktif!</strong> 
            Anda hanya dapat melihat sebagian dari konten materi ini. Untuk akses penuh, silakan 
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
    @endguest

    <!-- Content Section -->
    <div class="materi-card mb-4">
        <div class="materi-card-body">
            <div class="content-text">
                {!! $material->content !!}
            </div>
        </div>
    </div>

    

    <!-- Navigation Buttons -->
    <div class="d-flex justify-content-between mt-4 mb-5">
        <a href="{{ route('mahasiswa.materials.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Materi
        </a>
        <a href="{{ route('mahasiswa.materials.questions.levels', ['material' => $material->id, 'difficulty' => 'beginner']) }}" class="btn btn-primary">
            Latihan Soal<i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
</div>

@push('scripts')
<script>
function initializeQuestionForm() {
    const questionForm = document.getElementById('questionForm');
    const checkAnswerBtn = document.getElementById('checkAnswerBtn');
    const feedbackElement = document.querySelector('.exercise-feedback');
    
    if (questionForm) {
        questionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Disable button to prevent multiple submissions
            if (checkAnswerBtn) {
                checkAnswerBtn.disabled = true;
                checkAnswerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memeriksa...';
            }
            
            // Validasi untuk fill in the blank
            const fillInBlankInput = document.getElementById('fill_in_the_blank_answer');
            if (fillInBlankInput && fillInBlankInput.value.trim() === '') {
                alert('Jawaban tidak boleh kosong');
                if (checkAnswerBtn) {
                    checkAnswerBtn.disabled = false;
                    checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                }
                return;
            }
            
            // Debugging - log form data
            console.log("Form data being sent:");
            const formData = new FormData(this);
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Submit form via AJAX
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(JSON.stringify(errorData));
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log("Response data:", data);
                
                const feedbackStatus = document.getElementById('feedbackStatus');
                const feedbackIcon = document.getElementById('feedbackIcon');
                const explanationBox = document.getElementById('explanationBox');
                const explanationText = document.getElementById('explanationText');
                const tryAgainBtn = document.getElementById('tryAgainBtn');
                const nextQuestionBtn = document.getElementById('nextQuestionBtn');

                // Set status dan icon
                feedbackStatus.innerHTML = `<h3 class="${data.status === 'success' ? 'text-success' : 'text-danger'}">${data.message}</h3>`;
                feedbackIcon.className = `feedback-icon ${data.status === 'success' ? 'success' : 'error'}`;
                feedbackIcon.innerHTML = `<i class="fas ${data.status === 'success' ? 'fa-check-circle' : 'fa-times-circle'} fa-3x"></i>`;

                // Tampilkan penjelasan
                if (data.explanation) {
                    explanationText.innerHTML = data.explanation;
                    explanationBox.style.display = 'block';
                } else if (data.selectedExplanation) {
                    explanationText.innerHTML = data.selectedExplanation;
                    explanationBox.style.display = 'block';
                } else {
                    explanationBox.style.display = 'none';
                }

                // Tampilkan tombol yang sesuai
                if (data.status === 'success') {
                    tryAgainBtn.style.display = 'none';
                    nextQuestionBtn.style.display = 'inline-block';
                    
                    // Check if guest has completed their maximum questions
                    if (isGuest && data.answeredCount >= maxQuestionsForGuest) {
                        nextQuestionBtn.innerHTML = '<i class="fas fa-check me-2"></i>Selesai';
                        nextQuestionBtn.onclick = () => window.location.reload();
                    } else if (isGuest && !data.hasNextQuestion) {
                        // Jika pengguna tamu dan tidak ada soal berikutnya
                        nextQuestionBtn.innerHTML = '<i class="fas fa-check me-2"></i>Selesai';
                        nextQuestionBtn.onclick = () => window.location.reload();
                    } else if (!data.hasNextQuestion) {
                        // Jika tidak ada soal berikutnya untuk user biasa
                        nextQuestionBtn.innerHTML = '<i class="fas fa-check me-2"></i>Selesai';
                        nextQuestionBtn.onclick = () => {
                            window.location.href = "{{ route('mahasiswa.materials.questions.levels', [
                                'material' => $material->id,
                                'difficulty' => request()->query('difficulty', 'all')
                            ]) }}";
                        };
                    } else {
                        nextQuestionBtn.innerHTML = 'Lanjut ke Soal Berikutnya <i class="fas fa-arrow-right ms-2"></i>';
                        nextQuestionBtn.onclick = () => {
                            // Save current scroll position
                            const currentScroll = window.scrollY;
                            
                            // Fetch next question via AJAX
                            fetch(data.nextUrl, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.text())
                            .then(html => {
                                // Find and update only the question container
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newQuestionContainer = doc.querySelector('#questionContainer');
                                const currentQuestionContainer = document.querySelector('#questionContainer');
                                
                                if (newQuestionContainer && currentQuestionContainer) {
                                    currentQuestionContainer.innerHTML = newQuestionContainer.innerHTML;
                                    
                                    // Hide feedback element
                                    const feedbackElement = document.querySelector('.exercise-feedback');
                                    if (feedbackElement) {
                                        feedbackElement.style.display = 'none';
                                    }
                                    
                                    // Show question form
                                    const questionForm = document.querySelector('#questionForm');
                                    if (questionForm) {
                                        questionForm.style.display = 'block';
                                    }
                                    
                                    // Restore scroll position
                                    window.scrollTo(0, currentScroll);
                                    
                                    // Reinitialize event listeners for the new form
                                    initializeQuestionForm();
                                } else {
                                    // Fallback to full page reload if containers not found
                                    window.location.href = data.nextUrl;
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                window.location.href = data.nextUrl; // Fallback to full page reload
                            });
                        };
                    }
                } else {
                    tryAgainBtn.style.display = 'inline-block';
                    nextQuestionBtn.style.display = 'none';
                    tryAgainBtn.onclick = () => {
                        feedbackElement.style.display = 'none';
                        questionForm.style.display = 'block';
                        checkAnswerBtn.disabled = false;
                        checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                    };
                }

                feedbackElement.style.display = 'block';
                questionForm.style.display = 'none';
            })
            .catch(error => {
                console.error('Error:', error);
                
                try {
                    const errorData = JSON.parse(error.message);
                    if (errorData.message) {
                        alert(errorData.message);
                    } else {
                        alert('Terjadi kesalahan saat memeriksa jawaban. Silakan coba lagi.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan saat memeriksa jawaban. Silakan coba lagi.');
                }
                
                if (checkAnswerBtn) {
                    checkAnswerBtn.disabled = false;
                    checkAnswerBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                }
            });
        });
    }
    
    // Initialize clickable answer options
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

document.addEventListener('DOMContentLoaded', function() {
    initializeQuestionForm();
});
</script>
@endpush

@push('js')
<!-- Lightbox untuk Galeri Gambar -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'albumLabel': "Gambar %1 dari %2"
    });
</script>
@endpush
@endsection

