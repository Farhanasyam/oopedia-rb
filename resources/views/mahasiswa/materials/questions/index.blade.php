@extends('mahasiswa.layouts.app')

@section('title', 'Latihan Soal PBO')

@section('content')
@if(auth()->check() && auth()->user()->role_id === 4)
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

@if(!auth()->check() || (auth()->check() && auth()->user()->role_id === 4))
<div class="alert alert-warning mb-4">
    <strong>Mode Tamu Aktif!</strong> 
    Anda hanya dapat melihat sebagian materi dan hanya 3 soal latihan dari setiap tingkat kesulitan yang ditampilkan. 
    Untuk akses penuh, silakan 
    <a href="{{ route('login') }}" class="alert-link">login</a> 
    atau 
    <a href="{{ route('register') }}" class="alert-link">daftar</a> 
    sebagai mahasiswa.
</div>
@endif

<div class="dashboard-header text-center">
    <h1 class="main-title">Latihan Soal PBO</h1>
    <div class="title-underline"></div>
    <p class="subtitle mt-3">Uji pemahaman Anda dengan mengerjakan latihan soal untuk setiap materi</p>
</div>

<div class="materials-container">
    <div class="row">
        @foreach($materials as $material)
        <div class="col-md-12 mb-4">
            <a href="{{ route('mahasiswa.materials.questions.levels', $material->id) }}" class="card-link">
                <div class="material-question-card horizontal">
                    <!-- Bagian Gambar Material (Kiri) -->
                    <div class="material-left-section">
                        @if($material->media && $material->media->isNotEmpty())
                            <div class="material-question-image">
                                <img src="{{ $material->media->first()->media_url }}" alt="{{ $material->title }}">
                            </div>
                        @else
                            <div class="material-question-image default-image">
                                <div class="no-image-icon">
                                    <i class="fas fa-code"></i>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Bagian Konten (Kanan) -->
                    <div class="material-right-section">
                        <div class="material-top-section">
                            <div class="material-info">
                                <div class="material-badges">
                                    <div class="material-badge">
                                        <span class="badge-text">Tersedia</span>
                                    </div>
                                </div>
                                <h2 class="material-question-title">{{ $material->title }}</h2>
                                <!-- Material Meta Info dengan jumlah mahasiswa sebenarnya -->
                                <div class="material-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-users"></i>
                                        <span>{{ $material->student_count }} Mahasiswa</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Navigation Icon -->
                            <div class="nav-icon">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                        
                        <div class="material-bottom-section">
                            <!-- Progress Section -->
                            @if(!auth()->check() || (auth()->check() && auth()->user()->role_id === 4))
                                <!-- Guest Mode Display -->
                                <div class="guest-limit-section">
                                    <div class="guest-info-icon">
                                        <i class="fas fa-lock text-warning"></i>
                                    </div>
                                    <div class="guest-limit-text">
                                        <span>Mode Tamu: Akses Terbatas</span>
                                        <small>Hanya 3 soal per tingkat kesulitan. Login untuk akses penuh</small>
                                    </div>
                                </div>
                            @else
                                <!-- Regular Progress Section for Registered Users -->
                                <div class="progress-section">
                                    <div class="progress-header">
                                        <span class="progress-label">Progress</span>
                                        <span class="progress-percentage">{{ $material->progress_percentage }}%</span>
                                    </div>
                                    <div class="progress-bar-wrapper">
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" style="width: {{ $material->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                    <div class="progress-detail">
                                        {{ $material->completed_questions }} dari {{ $material->total_questions }} soal selesai
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Action Button -->
                            <div class="btn-start-exercise">
                                <span>Detail</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>

@push('css')
<style>
/* [All your CSS styles remain exactly the same] */
</style>
@endpush

@push('scripts')<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!sessionStorage.getItem('question_index_tour_complete')) {
            setTimeout(startQuestionsIndexTour, 500);
        }
    });

    function startQuestionsIndexTour() {
        const steps = [
            {
                intro: `
                    <div class="text-center">
                        <h4 style="margin-bottom: 10px; color: var(--color-1);">Selamat datang di Latihan Soal OOPEDIA!</h4>
                        <p>Temukan berbagai latihan soal untuk menguji pemahaman Anda tentang Pemrograman Berorientasi Objek</p>
                    </div>
                `
            },
            {
                element: document.querySelector('.material-question-card'),
                intro: "Ini adalah kartu materi latihan soal. Pilih salah satu materi untuk mulai berlatih."
            },
            {
                element: document.querySelector('.progress-section'),
                intro: "Di sini Anda dapat melihat progres pengerjaan soal untuk setiap materi."
            },
            {
                element: document.querySelector('.btn-start-exercise'),
                intro: "Klik tombol ini untuk melihat detail dan mulai mengerjakan latihan soal."
            },
            {
                element: document.querySelector('.material-card:first-child'),
                intro: `
                    <div>
                        <h5 style="margin-bottom: 8px; color: var(--color-1);">Kartu Materi</h5>
                        <p>Setiap kartu mewakili satu materi yang bisa Anda pelajari. Pilih materi untuk mulai berlatih.</p>
                    </div>
                `,
                position: 'bottom'
            },
            {
                element: document.querySelector('.progress-container'),
                intro: `
                    <div>
                        <h5 style="margin-bottom: 8px; color: var(--color-1);">Progress Belajar</h5>
                        <p>Pantau perkembangan Anda melalui indikator progress ini.</p>
                    </div>
                `,
                position: 'bottom'
            },
            {
                element: document.querySelector('.material-actions .btn-read-material'),
                intro: `
                    <div>
                        <h5 style="margin-bottom: 8px; color: var(--color-1);">Mulai Berlatih</h5>
                        <p>Klik tombol ini untuk mengakses soal-soal latihan dari materi yang dipilih.</p>
                    </div>
                `,
                position: 'top'
            },
            {
                intro: `
                    <div class="text-center">
                        <h4 style="margin-bottom: 10px; color: var(--color-1);">Siap Berlatih!</h4>
                        <p>Selamat mengasah kemampuan Pemrograman Berorientasi Objek Anda!</p>
                    </div>
                `
            }
        ];

        const intro = introJs();
        intro.setOptions({
            steps: steps,
            showProgress: true,
            exitOnOverlayClick: true,
            scrollToElement: true,
            nextLabel: 'Berikutnya',
            prevLabel: 'Sebelumnya', 
            doneLabel: 'Mulai Berlatih',
            skipLabel: 'Lewati Panduan',
            showSkipButton: true,
            tooltipClass: 'custom-introjs-tooltip',
            hidePrev: true
        })
        .oncomplete(function() {
            sessionStorage.setItem('question_index_tour_complete', 'true');
        })
        .onexit(function() {
            sessionStorage.setItem('question_index_tour_complete', 'true'); 
        })
        .start();
    }
</script>
@endpush
@endsection