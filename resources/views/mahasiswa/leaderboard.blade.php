@extends('mahasiswa.layouts.app')

@section('title', 'Leaderboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4 leaderboard-card">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between px-3">
                            <div class="d-flex align-items-center">
                                <div class="trophy-icon-container me-3">
                                    <i class="fas fa-trophy trophy-icon"></i>
                                </div>
                                <div>
                                    <h4 class="text-dark text-capitalize mb-0 leaderboard-title">Leaderboard</h4>
                                    <p class="text-dark text-sm mb-0 opacity-8">Peringkat Terbaik Mahasiswa</p>
                                </div>
                            </div>
                            <div class="leaderboard-decoration">
                                <span class="medal-badge medal-gold">🥇</span>
                                <span class="medal-badge medal-silver">🥈</span>
                                <span class="medal-badge medal-bronze">🥉</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body px-0 pb-2">    
                    <div class="podium-wrapper mb-5">
                        <div class="podium-display">
                            <div class="podium-item second-place">
                                @if(isset($leaderboardData[1]) && $leaderboardData[1]->total_correct_questions > 0)
                                    <div class="player-avatar">
                                        <span class="medal-badge">🥈</span>
                                        <h5 class="player-name">{{ $leaderboardData[1]->name }}</h5>
                                        <span class="level-badge level-{{ $leaderboardData[1]->badge_color }}">{{ $leaderboardData[1]->badge }}</span>
                                        <div class="score-display">{{ $leaderboardData[1]->formatted_score }} poin</div>
                                    </div>
                                    <div class="podium-base second">2</div>
                                @endif
                            </div>

                            <div class="podium-item first-place">
                                @if(isset($leaderboardData[0]) && $leaderboardData[0]->total_correct_questions > 0)
                                    <i class="fas fa-crown crown-icon"></i>
                                    <div class="player-avatar">
                                        <span class="medal-badge">🥇</span>
                                        <h5 class="player-name">{{ $leaderboardData[0]->name }}</h5>
                                        <span class="level-badge level-{{ $leaderboardData[0]->badge_color }}">{{ $leaderboardData[0]->badge }}</span>
                                        <div class="score-display">{{ $leaderboardData[0]->formatted_score }} poin</div>
                                    </div>
                                    <div class="podium-base first">1</div>
                                @endif
                            </div>

                            <div class="podium-item third-place">
                                @if(isset($leaderboardData[2]) && $leaderboardData[2]->total_correct_questions > 0)
                                    <div class="player-avatar">
                                        <span class="medal-badge">🥉</span>
                                        <h5 class="player-name">{{ $leaderboardData[2]->name }}</h5>
                                        <span class="level-badge level-{{ $leaderboardData[2]->badge_color }}">{{ $leaderboardData[2]->badge }}</span>
                                        <div class="score-display">{{ $leaderboardData[2]->formatted_score }} poin</div>
                                    </div>
                                    <div class="podium-base third">3</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive p-0 mx-3">
                        <div class="animated-border-table">
                            <table class="table leaderboard-table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                            <i class="fas fa-medal me-2"></i>PERINGKAT
                                        </th>
                                        <th class="text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                            <i class="fas fa-user me-2"></i>MAHASISWA
                                        </th>
                                        <th class="text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                            <i class="fas fa-star me-2"></i>LEVEL
                                        </th>
                                        <th class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">
                                            <i class="fas fa-calendar-check me-2"></i>TANGGAL SELESAI
                                        </th>
                                        <th class="text-center text-uppercase text-xxs font-weight-bolder opacity-7">
                                            <i class="fas fa-chart-line me-2"></i>PROGRESS
                                        </th>
                                        <th class="text-uppercase text-xxs font-weight-bolder opacity-7 ps-3">
                                            <i class="fas fa-dollar-sign me-2"></i>SKOR
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaderboardData as $data)
                                    @if($data->total_correct_questions > 0)
                                    <tr class="leaderboard-row @if($data->id === auth()->id()) highlight-row @endif">
                                        <td>
                                            <div class="d-flex px-3 py-2 justify-content-center">
                                                @if($data->rank <= 3)
                                                    <div class="top-rank rank-{{ $data->rank }}">{{ $data->rank }}</div>
                                                @else
                                                    <span class="font-weight-bold">{{ $data->rank }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-2">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $data->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="px-2 py-2">
                                                <span class="level-badge level-{{ $data->badge_color }}">{{ $data->badge }}</span>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="completion-date {{ $data->completion_date ? 'completed' : 'not-completed' }}">
                                                <i class="fas fa-{{ $data->completion_date ? 'calendar-check' : 'hourglass-half' }}"></i>
                                                {{ $data->completion_date ? date('d M Y', strtotime($data->completion_date)) : 'Belum selesai' }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="progress-wrapper mx-auto">
                                                <div class="progress leaderboard-progress">
                                                    <div class="progress-bar bg-gradient-{{ $data->badge_color }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $data->percentage }}%" 
                                                         aria-valuenow="{{ $data->percentage }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <div class="text-sm text-center mt-1">{{ $data->percentage }}%</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="px-2 py-2">
                                                <span class="score-badge">{{ $data->formatted_score }} poin</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
<style>
    /* Reset default badge styles */
    .level-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.375rem;
        text-align: center;
    }
    
    /* Define specific badge colors */
    .level-secondary {
        background-color: #6c757d !important;
        color: white !important;
    }
    
    .level-success {
        background-color: #28a745 !important;
        color: white !important;
    }
    
    .level-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }
    
    .level-danger {
        background-color: #dc3545 !important;
        color: white !important;
    }
    
    /* Override any conflicting styles */
    .podium-item .level-badge {
        margin-top: 5px;
    }
    
    /* Style untuk badge skor */
    .score-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 700;
        border-radius: 0.375rem;
        background-color: #3498db;
        color: white;
    }
    
    /* Style untuk skor di podium */
    .score-display {
        margin-top: 5px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #333;
        background-color: rgba(255, 255, 255, 0.7);
        padding: 3px 8px;
        border-radius: 12px;
        display: inline-block;
    }
    
    /* Skor pada peringkat pertama */
    .first-place .score-display {
        background-color: rgba(255, 215, 0, 0.3);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Konfeti untuk peringkat teratas
    document.addEventListener('DOMContentLoaded', function() {
        @if($currentUserRank && $currentUserRank->rank <= 3)
            // Konfeti untuk peringkat 1-3
            const colors = [
                ['#004e98', '#0074d9'], // Dark blue - peringkat 1
                ['#0074d9', '#3498db'], // Medium blue - peringkat 2
                ['#3498db', '#4fc3f7']  // Light blue - peringkat 3
            ];
            
            const selectedColors = colors[{{ $currentUserRank->rank - 1 }}];
            
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: selectedColors,
                startVelocity: 30,
                gravity: 0.5,
                ticks: 200,
                shapes: ['square', 'circle'],
                zIndex: 1000
            });
        @endif
    });

    function showFeedback(result, score, attemptNumber) {
        // Kode yang sudah dimodifikasi di atas
    }
</script>
@endpush 