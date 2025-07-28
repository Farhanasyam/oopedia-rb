@extends('mahasiswa.layouts.app')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Dashboard')

@section('content')
<div id="dashboard-container">
    <div class="dashboard-header text-center compact-header">
        <h1 class="main-title">Dashboard</h1>
        <div class="title-underline"></div>
    </div>

    <div class="welcome-banner">
        <div class="welcome-content">
            <div class="welcome-icon">
                <i class="fas fa-hand-sparkles"></i>
            </div>
            <div class="welcome-text">
                <h2 class="welcome-title">Selamat Datang Kembali,</h2>
                <h3 class="welcome-name">{{ auth()->user()->name }}</h3>
                <p class="welcome-message">Lanjutkan perjalanan belajar Anda hari ini!</p>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row g-4">
            <!-- Material Overview (Modified) -->
            <div class="col-md-6">
                <div class="materi-card">
                    <div class="materi-card-body">
                        <h3 class="materi-title">Materi Pembelajaran</h3>
                        <div class="materi-overview">
                            <div class="materi-count text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <img src="{{ asset('images/book-icon.png') }}" alt="Materi" class="dashboard-icon-large mb-2">
                                    <div class="count-number-large">{{ $totalMaterials }}</div>
                                </div>
                            </div>
                            <p class="materi-description">Total materi tersedia untuk dipelajari</p>
                            <div class="button-container">
                                <a href="{{ route('mahasiswa.materials.index') }}" class="btn btn-primary w-100">
                                    <i class="fas fa-book me-2"></i>Lihat Semua Materi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Question Progress Overview -->
            <div class="col-md-6">
                <div class="materi-card">
                    <div class="materi-card-body">
                        <h3 class="materi-title">Latihan Soal</h3>
                        <div class="materi-overview">
                            <div class="materi-count text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <img src="{{ asset('images/question-icon.png') }}" alt="Soal" class="dashboard-icon-large mb-2">
                                    <div class="count-number-large">{{ $totalQuestions }}</div>
                                </div>
                            </div>
                            <p class="materi-description">Total soal tersedia untuk latihan</p>
                            <div class="difficulty-breakdown">
                                <div class="difficulty-item">
                                    <span class="badge bg-success">Beginner</span>
                                    <span class="difficulty-count">{{ $easyQuestions }}</span>
                                </div>
                                <div class="difficulty-item">
                                    <span class="badge bg-warning">Medium</span>
                                    <span class="difficulty-count">{{ $mediumQuestions }}</span>
                                </div>
                                <div class="difficulty-item">
                                    <span class="badge bg-danger">Hard</span>
                                    <span class="difficulty-count">{{ $hardQuestions }}</span>
                                </div>
                            </div>
                            <div class="button-container">
                                <a href="{{ route('mahasiswa.materials.questions.index') }}" class="btn btn-primary w-100">
                                    <i class="fas fa-question-circle me-2"></i>Latihan Soal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="col-12">
                <div class="materi-card">
                    <div class="materi-card-body">
                        <h3 class="materi-title">Aktivitas Terbaru</h3>
                        <div class="activity-timeline">
                            @forelse($recentActivities as $activity)
                                <div class="activity-item">
                                    <div class="activity-icon 
                                        @if($activity->type === 'achievement') bg-success
                                        @elseif($activity->type === 'milestone') bg-warning
                                        @else bg-info @endif">
                                        @if($activity->type === 'achievement')
                                            <i class="fas fa-trophy" style="color: white;"></i>
                                        @elseif($activity->type === 'milestone')
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="fas fa-tasks"></i>
                                        @endif
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">
                                            @if($activity->type === 'achievement')
                                                Pencapaian Baru!
                                            @elseif($activity->type === 'milestone')
                                                Milestone Tercapai!
                                            @else
                                                Progress Pembelajaran
                                            @endif
                                        </div>
                                        <div class="activity-details">
                                            @if($activity->type === 'achievement')
                                                Menyelesaikan {{ $activity->total_correct }} soal di materi 
                                                <span class="fw-bold">{{ $activity->material_title }}</span>
                                            @elseif($activity->type === 'milestone')
                                                Berhasil menyelesaikan soal level hard di materi 
                                                <span class="fw-bold">{{ $activity->material_title }}</span>
                                            @else
                                                Mengerjakan soal {{ $activity->difficulty }} di materi 
                                                <span class="fw-bold">{{ $activity->material_title }}</span>
                                            @endif
                                        </div>
                                        <div class="activity-time">
                                            {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-3">
                                    Belum ada aktivitas
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
<style>
    /* Override khusus untuk dashboard header - versi lebih ketat */
    body .dashboard-header.compact-header {
        padding: 0 0 0.5rem !important;
        margin-top: -0.5rem !important;
        margin-bottom: 1rem !important;
    }
    
    /* Kurangi margin atas welcome banner */
    .welcome-banner {
        margin-top: -0.5rem;
    }
    
    /* Pastikan tidak mempengaruhi navbar */
    .navbar {
        margin-bottom: 0 !important;
    }
    
    /* Fixes spesifik untuk halaman dashboard */
    #dashboard-container .main-title {
        margin-top: 0 !important; 
    }
    
    /* Atur ulang padding container */
    #dashboard-container .container-fluid {
        padding-top: 1rem !important;
    }

    /* Custom Tour Styling */
    .introjs-tooltip {
        border-radius: 12px !important;
        padding: 20px !important;
        max-width: 400px !important;
        box-shadow: 0 8px 25px rgba(0, 78, 152, 0.15) !important;
        border: 1px solid rgba(0, 78, 152, 0.1) !important;
        background: white !important;
    }

    .introjs-tooltip-header {
        padding-bottom: 10px !important;
        border-bottom: 1px solid rgba(0, 78, 152, 0.1) !important;
        margin-bottom: 15px !important;
    }

    .introjs-tooltiptext {
        font-size: 15px !important;
        line-height: 1.6 !important;
        color: #2c3e50 !important;
    }

    .introjs-tooltipbuttons {
        border-top: 1px solid rgba(0, 78, 152, 0.1) !important;
        padding-top: 15px !important;
        margin-top: 15px !important;
        text-align: right !important;
    }

    .introjs-button {
        padding: 8px 16px !important;
        border-radius: 8px !important;
        font-weight: 500 !important;
        transition: all 0.3s ease !important;
        margin-left: 8px !important;
        font-size: 14px !important;
    }

    .introjs-skipbutton {
        background-color: #f8f9fa !important;
        color: #3498db !important;
        border: 1px solid rgba(0, 78, 152, 0.2) !important;
        float: left !important;
        margin-left: 0 !important;
    }

    .introjs-skipbutton:hover {
        background-color: #e9ecef !important;
    }

    .introjs-nextbutton {
        background: linear-gradient(135deg, #3498db, #2c3e50) !important;
        color: white !important;
        border: none !important;
    }

    .introjs-nextbutton:hover {
        background: linear-gradient(135deg, #2980b9, #1a252f) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 8px rgba(0, 78, 152, 0.2) !important;
    }

    .introjs-prevbutton {
        background-color: white !important;
        color: #3498db !important;
        border: 1px solid rgba(0, 78, 152, 0.2) !important;
    }

    .introjs-prevbutton:hover {
        background-color: #f8f9fa !important;
    }

    .introjs-bullets {
        bottom: -25px !important;
    }

    .introjs-bullets ul li a {
        background: rgba(0, 78, 152, 0.2) !important;
    }

    .introjs-bullets ul li a.active {
        background: #3498db !important;
    }

    .custom-highlight {
        border-radius: 8px !important;
        box-shadow: 0 0 0 9999px rgba(0,0,0,0.5), 0 0 15px rgba(0,0,0,0.5) !important;
    }

    /* Tour content styling */
    .tour-step-title {
        color: #3498db;
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .tour-step-content {
        color: #2c3e50;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .dashboard-icon {
        width: 24px;
        height: 24px;
        object-fit: contain;
        vertical-align: middle;
    }

    .dashboard-icon-large {
        width: 80px;
        height: 80px;
        display: block;
        margin: 0 auto 10px;
    }

    .count-number-large {
        font-size: 2.5rem;
        font-weight: bold;
        color: #4e73df;
    }

    .materi-count {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 15px 0;
    }

    .dashboard-icon-large {
        width: 64px;
        height: 64px;
        max-width: 100%;
        object-fit: contain;
    }

    /* Responsive sizing untuk layar kecil */
    @media (max-width: 768px) {
        .dashboard-icon-large {
            width: 48px;
            height: 48px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!sessionStorage.getItem('dashboard_tour_complete')) {
            setTimeout(startDashboardTour, 800);
        }
    });

    function startDashboardTour() {
        const steps = [
            {
                intro: `
                    <div class="text-center">
                        <h4 class="tour-step-title">Selamat Datang di Dashboard</h4>
                        <p class="tour-step-content">Temukan semua fitur pembelajaran OOP di satu tempat!</p>
                    </div>
                `,
                position: 'center'
            },
            {
                element: document.querySelector('.col-md-6:first-child .materi-card'),
                intro: `
                    <div>
                        <h5 class="tour-step-title">Materi Pembelajaran</h5>
                        <p class="tour-step-content">Lihat jumlah materi yang tersedia dan akses konten pembelajaran.</p>
                    </div>
                `,
                position: 'auto'
            },
            {
                element: document.querySelector('.col-md-6:nth-child(2) .materi-card'),
                intro: `
                    <div>
                        <h5 class="tour-step-title">Latihan Soal</h5>
                        <p class="tour-step-content">Temukan berbagai level soal untuk menguji pemahaman Anda.</p>
                    </div>
                `,
                position: 'auto'
            },
            {
                element: document.querySelector('.activity-timeline'),
                intro: `
                    <div>
                        <h5 class="tour-step-title">Aktivitas Terbaru</h5>
                        <p class="tour-step-content">Pantau perkembangan belajar Anda melalui aktivitas terkini.</p>
                    </div>
                `,
                position: 'auto'
            },
            {
                intro: `
                    <div class="text-center">
                        <h4 class="tour-step-title">Mulai Petualangan Belajar!</h4>
                        <p class="tour-step-content">Anda siap menjelajahi dunia OOP. Selamat belajar!</p>
                    </div>
                `,
                position: 'center'
            }
        ];

        introJs().setOptions({
            steps: steps,
            showProgress: true,
            exitOnOverlayClick: true,
            showBullets: true,
            scrollToElement: true,
            nextLabel: 'Berikutnya →',
            prevLabel: '← Sebelumnya',
            skipLabel: 'X',
            doneLabel: 'Mulai Belajar',
            tooltipClass: 'custom-tour',
            highlightClass: 'custom-highlight',
            hidePrev: true,
            exitOnEsc: true
        }).oncomplete(function() {
            sessionStorage.setItem('dashboard_tour_complete', 'true');
        }).onexit(function() {
            sessionStorage.setItem('dashboard_tour_complete', 'true');
        }).start();
    }
</script>
@endpush
@endsection