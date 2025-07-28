@extends('mahasiswa.layouts.app')

@section('title', 'Level Soal - ' . $material->title)

@section('content')
<div class="container-fluid">
    <div class="dashboard-header text-center">
        <h1 class="main-title">Level Soal: {{ $material->title }}</h1>
        <div class="title-underline"></div>
        
        <!-- Add difficulty selector -->
        <div class="difficulty-selector mb-4">
            <form method="GET" action="{{ route('mahasiswa.materials.questions.levels', $material) }}" class="d-flex justify-content-center align-items-center">
                <label class="me-2">Tingkat Kesulitan:</label>
                <select name="difficulty" class="form-select" onchange="this.form.submit()" style="width: auto;">
                    <option value="beginner" {{ $difficulty == 'beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="medium" {{ $difficulty == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="hard" {{ $difficulty == 'hard' ? 'selected' : '' }}>Hard</option>
                </select>
            </form>
        </div>

        <!-- Display current difficulty if filtered -->
        @if($difficulty != 'all')
        <div class="difficulty-badge mb-4">
            <i class="fas fa-signal me-2"></i>
            <span>Menampilkan Soal: {{ ucfirst($difficulty) }}</span>
        </div>
        @endif
    </div>

    <div class="level-container">
        <!-- Tambahkan peringatan tentang sistem penilaian hanya untuk user mahasiswa (bukan tamu) -->
        @if(auth()->check() && auth()->user()->role_id === 3)
            <div class="alert alert-info mb-4" role="alert">
                <h5><i class="fas fa-info-circle"></i> Sistem Penilaian Pada Leaderboard</h5>
                <p>Perhatikan bahwa nilai Anda di leaderboard bergantung pada jumlah percobaan yang dibutuhkan untuk menjawab soal dengan benar:</p>
                
                <div class="mt-2 fw-bold text-danger">
                    <i class="fas fa-exclamation-triangle"></i> Pastikan jawaban Anda sudah benar sebelum mengirim untuk mendapatkan nilai maksimal!
                </div>
            </div>
        @endif

        <div class="level-legend mb-4">
            <div class="legend-title mb-3">Keterangan:</div>
            <div class="legend-items">
                <div class="legend-item">
                    <div class="legend-icon" style="background: #2196F3;">
                        <span class="text-white"></span>
                    </div>
                    <div class="legend-text">Soal yang bisa dikerjakan</div>
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: #4CAF50;">
                        <i class="fas fa-check text-white"></i>
                    </div>
                    <div class="legend-text">Soal yang sudah dijawab benar</div>
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background: #e9ecef;">
                        <span style="color: #6c757d;"></span>
                    </div>
                    <div class="legend-text">Soal yang belum bisa diakses</div>
                </div>
                <div class="legend-item">
                    <div class="legend-icon trophy-circle" style="background: #e9ecef;">
                        <i class="fas fa-trophy" style="color: #adb5bd;"></i>
                    </div>
                    <div class="legend-text">Penghargaan setelah menyelesaikan semua soal</div>
                </div>
            </div>
        </div>

        <div class="level-header text-center mb-5">
            <div class="start-text">
                <span>START</span>
                <div class="start-line"></div>
            </div>
        </div>
        
        <div class="level-map">
            <!-- SVG untuk jalur -->
            <svg class="level-paths" width="100%" height="100%" style="position: absolute; top: 0; left: 0; z-index: 0;">
                <!-- Jalur akan ditambahkan secara dinamis dengan JavaScript -->
            </svg>
            
            @foreach($levels as $index => $level)
                <div class="level-row {{ $index % 3 == 0 ? 'center' : ($index % 3 == 1 ? 'left' : 'right') }}">
                    <div class="level-item {{ $level['status'] }}" data-level="{{ $level['level'] }}" data-question-id="{{ $level['question_id'] }}" {{ $level['status'] === 'unlocked' ? 'id=unlockedLevel' : '' }}>
                        @if($level['status'] === 'locked')
                            <div class="level-circle">
                                <span class="level-number">{{ $level['level'] }}</span>
                            </div>
                        @elseif($level['status'] === 'completed')
                            <div class="level-circle completed">
                                <span class="level-number">{{ $level['level'] }}</span>
                                <i class="fas fa-check-circle completed-icon"></i>
                            </div>
                        @else
                            <a href="{{ route('mahasiswa.materials.questions.show', [
                                'material' => $material->id,
                                'question' => $level['question_id'],
                                'difficulty' => $difficulty
                            ]) }}" class="level-link">
                                <div class="level-circle unlocked">
                                    <span class="level-number">{{ $level['level'] }}</span>
                                </div>
                            </a>
                        @endif
                        <div class="level-difficulty {{ $level['difficulty'] }}">
                            {{ ucfirst($level['difficulty']) }}
                        </div>
                    </div>
                </div>
            @endforeach
            
            <div class="level-row center">
                <div class="level-item trophy {{ count(array_filter($levels, function($level) { return $level['status'] !== 'completed'; })) === 0 ? 'completed' : 'locked' }}">
                    <div class="level-circle trophy-circle">
                        <i class="fas fa-trophy trophy-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="level-actions mt-4">
            <a href="{{ route('mahasiswa.materials.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Materi
            </a>
        </div>
    </div>
</div>

<style>
    .level-container {
        position: relative;
        background-color: #f8f9fa;
        background-image: radial-gradient(#e9ecef 1px, transparent 1px);
        background-size: 20px 20px;
        border-radius: 15px;
        padding: 30px;
        box-shadow: inset 0 0 20px rgba(0,0,0,0.05);
        max-width: 800px;
        margin: 0 auto;
    }
    
    .level-header text-center mb-4 {
        background-color: #333;
        color: #4CAF50;
        padding: 10px 20px;
        border-radius: 10px;
        display: inline-block;
        font-weight: bold;
    }
    
    .level-map {
        position: relative;
        padding: 40px 20px;
        background-image: 
            radial-gradient(#e9ecef 1px, transparent 1px),
            radial-gradient(#e9ecef 1px, transparent 1px);
        background-size: 20px 20px;
        background-position: 0 0, 10px 10px;
    }
    
    .level-row {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        margin: 40px 0;
    }
    
    .level-row::before {
        content: none;
        border: none;
    }
    
    /* Garis tengah (vertikal) */
    .level-row.center::before {
        width: 2px;
        height: 150px;
        border: none;
        border-left: 2px dashed #4CAF50;
        left: 50%;
        top: 100%;
        z-index: 1;
    }
    
    /* Garis kiri (berbelok) */
    .level-row.left::before {
        content: none;
        border: none;
    }
    
    /* Garis kanan (berbelok) */
    .level-row.right::before {
        content: none;
        border: none;
    }
    
    /* Posisi level-item */
    .level-row.center {
        justify-content: center;
        margin-bottom: 150px;
        position: relative;
    }
    
    .level-row.left {
        justify-content: flex-start;
        padding-left: 20%;
        margin-bottom: 100px;
        position: relative;
    }
    
    .level-row.right {
        justify-content: flex-end;
        padding-right: 20%;
        margin-bottom: 100px;
        position: relative;
    }
    
    /* Kotak garis putus-putus dekoratif */
    .level-row.left::after {
        content: none;
        border: none;
    }

    .level-row.right::after {
        content: none;
        border: none;
    }

    /* Garis penghubung tambahan untuk level 1 ke 2 */
    .level-row.center + .level-row.left::before {
        content: none;
        border: none;
    }

    /* Garis penghubung tambahan untuk level 2 ke 3 */
    .level-row.left + .level-row.right::before {
        content: none;
        border: none;
    }

    /* Titik-titik dekoratif */
    .level-dots {
        position: absolute;
        font-size: 24px;
        color: #4CAF50;
        letter-spacing: 8px;
        z-index: 2;
    }

    .level-row.right .level-dots {
        left: 35%;
        top: 50%;
        transform: translateY(-50%);
    }

    .level-row.left .level-dots {
        right: 35%;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .level-item {
        position: relative;
        z-index: 2;
    }
    
    .level-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: bold;
        transition: all 0.3s ease;
        position: relative;
        margin: 5px 0;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        border: 5px solid #fff;
    }
    
    .completed .level-circle {
        background: linear-gradient(145deg, #4CAF50, #45a049);
        color: white;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }
    
    .completed-icon {
        position: absolute;
        top: -10px;
        right: -10px;
        background: white;
        border-radius: 50%;
        padding: 5px;
        font-size: 22px;
        color: #4CAF50;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .locked .level-circle {
        background: linear-gradient(145deg, #e9ecef, #dee2e6);
        color: #adb5bd;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .unlocked .level-circle {
        background: linear-gradient(145deg, #2196F3, #1976D2);
        color: white;
        box-shadow: 0 4px 15px rgba(33, 150, 243, 0.2);
        cursor: pointer;
        position: relative;
    }
    
    /* Hapus animasi bola yang berputar */
    .unlocked .level-circle::before,
    .unlocked .level-circle::after {
        content: none;
    }
    
    /* Tambahkan border berputar */
    .unlocked .level-circle {
        position: relative;
    }
    
    .unlocked .level-circle::before {
        content: '';
        position: absolute;
        width: calc(100% + 20px);
        height: calc(100% + 20px);
        top: -10px;
        left: -10px;
        border: 2px solid transparent;
        border-top-color: #90CAF9;
        border-right-color: #90CAF9;
        border-radius: 50%;
        animation: rotate 2s linear infinite;
    }
    
    .unlocked .level-circle::after {
        content: '';
        position: absolute;
        width: calc(100% + 30px);
        height: calc(100% + 30px);
        top: -15px;
        left: -15px;
        border: 2px dashed transparent;
        border-bottom-color: #64B5F6;
        border-left-color: #64B5F6;
        border-radius: 50%;
        animation: rotate 3s linear infinite reverse;
    }
    
    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    
    .level-circle:hover {
        transform: scale(1.1);
        transition: transform 0.3s ease;
    }
    
    .level-actions {
        text-align: center;
        margin-top: 30px;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
    }
    
    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .trophy-circle {
        width: 90px;
        height: 90px;
        margin-top: -5px;
    }

    .trophy.locked .trophy-circle {
        background: #e9ecef;
        border: 2px solid #dee2e6;
    }

    .trophy.locked .trophy-icon {
        color: #adb5bd;
        font-size: 24px;
    }

    .trophy.completed .trophy-circle {
        background: linear-gradient(145deg, #FFD700, #FFA500);
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.6);
        animation: pulseTrophy 2s infinite;
    }

    .trophy-icon {
        font-size: 35px;
        color: #adb5bd;
    }

    .trophy.completed .trophy-icon {
        color: white;
        animation: rotateTrophy 3s infinite;
    }

    @keyframes pulseTrophy {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    @keyframes rotateTrophy {
        0% { transform: rotate(-15deg); }
        50% { transform: rotate(15deg); }
        100% { transform: rotate(-15deg); }
    }

    .trophy {
        margin-top: 10px;
    }

    .level-legend {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    .legend-items {
        display: flex;
        justify-content: center;
        gap: 25px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .legend-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .difficulty-badge {
        display: inline-block;
        background: linear-gradient(135deg, #2196F3, #1976D2);
        color: white;
        padding: 8px 20px;
        border-radius: 25px;
        margin-top: 15px;
        box-shadow: 0 4px 15px rgba(33, 150, 243, 0.2);
        font-weight: 500;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .difficulty-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.3);
    }

    .start-container {
        display: inline-block;
        padding: 10px 40px;
        background: linear-gradient(135deg, #4CAF50, #45a049);
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        transition: all 0.3s ease;
    }

    .start-title {
        color: white;
        font-size: 24px;
        font-weight: bold;
        margin: 0;
        letter-spacing: 2px;
    }

    .start-underline {
        width: 50%;
        height: 3px;
        background: rgba(255, 255, 255, 0.5);
        margin: 5px auto 0;
        border-radius: 2px;
    }

    .start-container:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
    }

    .start-button {
        background: #4CAF50;
        padding: 15px 60px;
        border-radius: 50px;
        display: inline-block;
        position: relative;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.25);
        cursor: pointer;
        overflow: hidden;
    }

    .start-text {
        color: #0D47A1;
        font-size: 32px;
        font-weight: 800;
        letter-spacing: 6px;
        text-transform: uppercase;
        position: relative;
        display: inline-block;
        padding: 0 15px;
        text-shadow: 
            2px 2px 0 #1976D2,
            -2px -2px 0 #1976D2,
            2px -2px 0 #1976D2,
            -2px 2px 0 #1976D2;
        animation: startGlow 2s ease-in-out infinite;
    }

    .start-line {
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, 
            transparent 0%,
            #0D47A1 50%,
            transparent 100%
        );
        margin-top: 8px;
        opacity: 0.9;
    }

    @keyframes startGlow {
        0% {
            text-shadow: 
                2px 2px 0 #1976D2,
                -2px -2px 0 #1976D2,
                2px -2px 0 #1976D2,
                -2px 2px 0 #1976D2,
                0 0 10px rgba(25, 118, 210, 0.5);
        }
        50% {
            text-shadow: 
                2px 2px 0 #1976D2,
                -2px -2px 0 #1976D2,
                2px -2px 0 #1976D2,
                -2px 2px 0 #1976D2,
                0 0 20px rgba(25, 118, 210, 0.8);
        }
        100% {
            text-shadow: 
                2px 2px 0 #1976D2,
                -2px -2px 0 #1976D2,
                2px -2px 0 #1976D2,
                -2px 2px 0 #1976D2,
                0 0 10px rgba(25, 118, 210, 0.5);
        }
    }

    @keyframes gentlePulse {
        0% {
            opacity: 0.7;
            transform: scaleX(0.95);
        }
        50% {
            opacity: 1;
            transform: scaleX(1);
        }
        100% {
            opacity: 0.7;
            transform: scaleX(0.95);
        }
    }

    .start-line {
        animation: gentlePulse 3s ease-in-out infinite;
    }

    .start-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.2),
            transparent
        );
        transition: 0.5s;
    }

    .start-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.35);
        background: #43A047;
    }

    .start-button:hover::before {
        left: 100%;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.25);
        }
        50% {
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
        }
        100% {
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.25);
        }
    }

    .start-button {
        animation: pulse 2s infinite;
    }

    .start-label {
        background: #4CAF50;
        padding: 12px 50px;
        border-radius: 50px;
        display: inline-block;
        position: relative;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        pointer-events: none;
    }

    .start-text {
        color: white;
        font-size: 22px;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    @keyframes gentlePulse {
        0% {
            transform: scale(1);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        }
        50% {
            transform: scale(1.02);
            box-shadow: 0 4px 20px rgba(76, 175, 80, 0.25);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        }
    }

    .start-label {
        animation: gentlePulse 3s infinite ease-in-out;
    }

    .difficulty-selector {
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .difficulty-selector select {
        min-width: 150px;
        padding: 8px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }

    .difficulty-badge {
        display: inline-block;
        padding: 8px 15px;
        background: #f8f9fa;
        border-radius: 20px;
        color: #666;
        font-size: 0.9rem;
    }

    @keyframes pulseAndFloat {
        0% {
            transform: translateY(0) scale(1);
        }
        50% {
            transform: translateY(-5px) scale(1.05);
        }
        100% {
            transform: translateY(0) scale(1);
        }
    }

    @keyframes glowPulse {
        0% {
            opacity: 0.3;
            transform: scale(1.1);
        }
        50% {
            opacity: 0.5;
            transform: scale(1.2);
        }
        100% {
            opacity: 0.3;
            transform: scale(1.1);
        }
    }

    @keyframes ripple {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.5;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes orbitDouble {
        from {
            transform: rotate(0deg) translateX(60px) rotate(0deg);
        }
        to {
            transform: rotate(360deg) translateX(60px) rotate(-360deg);
        }
    }

    /* Efek glow untuk jalur yang sudah selesai */
    .map-path.completed-path {
        filter: drop-shadow(0 0 3px rgba(76, 175, 80, 0.5));
    }

    /* Efek glow untuk titik yang sudah selesai */
    .map-dot.completed-dot {
        filter: drop-shadow(0 0 3px rgba(76, 175, 80, 0.5));
    }
    
    /* Animasi untuk titik */
    @keyframes pulseDot {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.8;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Tambahkan style untuk level-difficulty */
    .level-difficulty {
        margin-top: 10px;
        font-size: 14px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 15px;
        display: inline-block;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Style untuk setiap tingkat kesulitan */
    .level-difficulty.beginner {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #a5d6a7;
    }

    .level-difficulty.medium {
        background-color: #fff3e0;
        color: #e65100;
        border: 1px solid #ffcc80;
    }

    .level-difficulty.hard {
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #ef9a9a;
    }

    /* Tambahkan ikon untuk setiap tingkat kesulitan */
    .level-difficulty.beginner::before {
        content: "\f005"; /* Ikon bintang */
        font-family: "Font Awesome 5 Free";
        margin-right: 5px;
    }

    .level-difficulty.medium::before {
        content: "\f005\f005"; /* Dua ikon bintang */
        font-family: "Font Awesome 5 Free";
        margin-right: 5px;
    }

    .level-difficulty.hard::before {
        content: "\f005\f005\f005"; /* Tiga ikon bintang */
        font-family: "Font Awesome 5 Free";
        margin-right: 5px;
    }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk menggambar jalur peta
    function drawTreasureMap() {
        const svg = document.querySelector('.level-paths');
        const levelItems = document.querySelectorAll('.level-item');
        const svgNS = "http://www.w3.org/2000/svg";
        
        // Hapus semua path yang ada
        while (svg.firstChild) {
            svg.removeChild(svg.firstChild);
        }
        
        // Dapatkan posisi setiap level
        const positions = [];
        levelItems.forEach(item => {
            const rect = item.getBoundingClientRect();
            const svgRect = svg.getBoundingClientRect();
            
            // Hanya tambahkan item yang bukan trophy
            if (!item.classList.contains('trophy')) {
                positions.push({
                    x: rect.left + rect.width/2 - svgRect.left,
                    y: rect.top + rect.height/2 - svgRect.top,
                    status: item.classList.contains('completed') ? 'completed' : 
                            item.classList.contains('unlocked') ? 'unlocked' : 'locked',
                    level: parseInt(item.getAttribute('data-level') || '0'),
                    questionId: item.getAttribute('data-question-id'),
                    position: item.closest('.level-row').classList.contains('center') ? 'center' : 
                              item.closest('.level-row').classList.contains('left') ? 'left' : 'right'
                });
            } else {
                // Tambahkan trophy sebagai level terakhir
                positions.push({
                    x: rect.left + rect.width/2 - svgRect.left,
                    y: rect.top + rect.height/2 - svgRect.top,
                    status: item.classList.contains('completed') ? 'completed' : 'locked',
                    level: 'trophy',
                    position: 'center' // Trophy selalu di tengah
                });
            }
        });
        
        // Gambar jalur untuk semua level
        for (let i = 0; i < positions.length - 1; i++) {
            const start = positions[i];
            const end = positions[i + 1];
            
            // Jika level saat ini dan level berikutnya berada di tengah, gunakan jalur lurus vertikal
            if (start.position === 'center' && end.position === 'center') {
                // Jalur vertikal lurus tanpa lengkungan
                createStraightVerticalPath(svg, start.x, start.y + 60, end.x, end.y - 60,
                             start.status, end.status, end.status === 'completed');
            }
            // Jika level saat ini di tengah dan berikutnya di kiri
            else if (i % 3 === 0) { // Dari center ke left
                const padding = 40; // Padding untuk belokan tumpul
                
                // Jalur vertikal dari start ke belokan pertama
                createDotPath(svg, start.x, start.y + 60, start.x, start.y + 150 - padding, 
                             start.status, end.status, end.status === 'completed');
                
                // Belokan tumpul pertama (dari vertikal ke horizontal)
                createCurvedCorner(svg, start.x, start.y + 150 - padding, start.x - padding, start.y + 150, 'bottom-right',
                                  start.status, end.status, end.status === 'completed');
                
                // Jalur horizontal
                createDotPath(svg, start.x - padding, start.y + 150, end.x + padding, start.y + 150, 
                             start.status, end.status, end.status === 'completed');
                
                // Belokan tumpul kedua (dari horizontal ke vertikal)
                createCurvedCorner(svg, end.x + padding, start.y + 150, end.x, start.y + 150 + padding, 'top-left',
                                  start.status, end.status, end.status === 'completed');
                
                // Jalur vertikal ke end
                createDotPath(svg, end.x, start.y + 150 + padding, end.x, end.y - 60, 
                             start.status, end.status, end.status === 'completed');
                
            } else if (i % 3 === 1) { // Dari left ke right
                const padding = 40; // Padding untuk belokan tumpul
                
                // Jalur vertikal dari start ke belokan pertama
                createDotPath(svg, start.x, start.y + 60, start.x, start.y + 150 - padding, 
                             start.status, end.status, end.status === 'completed');
                
                // Belokan tumpul pertama (dari vertikal ke horizontal)
                createCurvedCorner(svg, start.x, start.y + 150 - padding, start.x + padding, start.y + 150, 'bottom-right',
                                  start.status, end.status, end.status === 'completed');
                
                // Jalur horizontal
                createDotPath(svg, start.x + padding, start.y + 150, end.x - padding, start.y + 150, 
                             start.status, end.status, end.status === 'completed');
                
                // Belokan tumpul kedua (dari horizontal ke vertikal)
                createCurvedCorner(svg, end.x - padding, start.y + 150, end.x, start.y + 150 + padding, 'top-left',
                                  start.status, end.status, end.status === 'completed');
                
                // Jalur vertikal ke end
                createDotPath(svg, end.x, start.y + 150 + padding, end.x, end.y - 60, 
                             start.status, end.status, end.status === 'completed');
                
            } else if (i % 3 === 2) { // Dari right ke center
                const padding = 40; // Padding untuk belokan tumpul
                
                // Jalur vertikal dari start ke belokan pertama
                createDotPath(svg, start.x, start.y + 60, start.x, start.y + 150 - padding, 
                             start.status, end.status, end.status === 'completed');
                
                // Belokan tumpul pertama (dari vertikal ke horizontal)
                createCurvedCorner(svg, start.x, start.y + 150 - padding, start.x - padding, start.y + 150, 'bottom-left',
                                  start.status, end.status, end.status === 'completed');
                
                // Jalur horizontal
                createDotPath(svg, start.x - padding, start.y + 150, end.x + padding, start.y + 150, 
                             start.status, end.status, end.status === 'completed');
                
                // Belokan tumpul kedua (dari horizontal ke vertikal)
                createCurvedCorner(svg, end.x + padding, start.y + 150, end.x, start.y + 150 + padding, 'top-right',
                                  start.status, end.status, end.status === 'completed');
                
                // Jalur vertikal ke end
                createDotPath(svg, end.x, start.y + 150 + padding, end.x, end.y - 60, 
                             start.status, end.status, end.status === 'completed');
            }
        }
        
        // Tambahkan jalur ke trophy jika ada
        if (positions.length >= 2) {
            const lastLevel = positions[positions.length - 2];
            const trophy = positions[positions.length - 1];
            
            if (trophy.level === 'trophy') {
                // Cek apakah semua soal sudah selesai
                const allCompleted = lastLevel.status === 'completed';
                
                // Jika level terakhir di tengah, buat jalur langsung ke bawah tanpa lengkungan
                if (lastLevel.position === 'center') {
                    createStraightVerticalPath(svg, lastLevel.x, lastLevel.y + 60, trophy.x, trophy.y - 60, 
                                 lastLevel.status, trophy.status, allCompleted);
                } 
                else {
                    // Kode untuk jalur yang tidak di tengah (tetap seperti sebelumnya)
                    // ...
                }
            }
        }
    }
    
    // Buat fungsi baru khusus untuk jalur vertikal yang benar-benar lurus
    function createStraightVerticalPath(svg, x1, y1, x2, y2, startStatus, endStatus, allCompleted) {
        const svgNS = "http://www.w3.org/2000/svg";
        const isCompleted = startStatus === 'completed' && (endStatus === 'completed' || endStatus === 'unlocked');
        
        // Pastikan x koordinatnya sama untuk jalur lurus
        const x = x1; // Atau bisa juga x2, karena keduanya seharusnya sama untuk jalur vertikal
        
        // Hitung jarak dan jumlah titik
        const distance = Math.abs(y2 - y1);
        const dotCount = Math.floor(distance / 20); // Titik setiap 20px
        
        for (let i = 0; i <= dotCount; i++) {
            // Posisi titik (pastikan x tetap sama untuk jalur lurus)
            const ratio = i / dotCount;
            const y = y1 + (y2 - y1) * ratio;
            
            // Buat titik
            const dot = document.createElementNS(svgNS, "circle");
            dot.setAttribute("cx", x);
            dot.setAttribute("cy", y);
            
            // Tentukan warna dan ukuran berdasarkan status
            if (isCompleted) {
                // Gunakan warna emas hanya jika semua soal selesai
                if (allCompleted && endStatus === 'completed') {
                    dot.setAttribute("r", "4");
                    dot.setAttribute("fill", "#FFD700");
                    dot.setAttribute("class", "map-dot trophy-dot");
                } else {
                    // Gunakan warna hijau untuk soal yang sudah dikerjakan
                    dot.setAttribute("r", "4");
                    dot.setAttribute("fill", "#4CAF50");
                    dot.setAttribute("class", "map-dot completed-dot");
                }
            } else {
                // Titik abu-abu untuk soal yang belum dikerjakan
                dot.setAttribute("r", "3");
                dot.setAttribute("fill", "#adb5bd");
                dot.setAttribute("class", "map-dot locked-dot");
            }
            
            svg.appendChild(dot);
        }
    }
    
    // Fungsi untuk membuat belokan tumpul dengan titik-titik
    function createCurvedCorner(svg, x1, y1, x2, y2, cornerType, startStatus, endStatus, allCompleted) {
        const svgNS = "http://www.w3.org/2000/svg";
        const isCompleted = startStatus === 'completed' && (endStatus === 'completed' || endStatus === 'unlocked');
        
        // Tentukan titik kontrol untuk kurva berdasarkan jenis belokan
        let cx, cy;
        
        // Perbaikan titik kontrol berdasarkan jenis belokan yang spesifik
        if (cornerType === 'top-right') {
            cx = x2;
            cy = y1;
        } else if (cornerType === 'top-left') {
            cx = x2;
            cy = y1;
        } else if (cornerType === 'bottom-right') {
            cx = x1;
            cy = y2;
        } else if (cornerType === 'bottom-left') {
            cx = x1;
            cy = y2;
        } else {
            cx = (x1 + x2) / 2;
            cy = (y1 + y2) / 2;
        }
        
        // Buat titik-titik sepanjang kurva
        const steps = 15;
        for (let i = 0; i <= steps; i++) {
            const t = i / steps;
            const x = Math.pow(1-t, 2) * x1 + 2 * (1-t) * t * cx + Math.pow(t, 2) * x2;
            const y = Math.pow(1-t, 2) * y1 + 2 * (1-t) * t * cy + Math.pow(t, 2) * y2;
            
            const dot = document.createElementNS(svgNS, "circle");
            dot.setAttribute("cx", x);
            dot.setAttribute("cy", y);
            
            if (isCompleted) {
                if (allCompleted && endStatus === 'completed') {
                    dot.setAttribute("r", "4");
                    dot.setAttribute("fill", "#FFD700");
                    dot.setAttribute("class", "map-dot trophy-dot");
                } else {
                    dot.setAttribute("r", "4");
                    dot.setAttribute("fill", "#4CAF50");
                    dot.setAttribute("class", "map-dot completed-dot");
                }
            } else {
                dot.setAttribute("r", "3");
                dot.setAttribute("fill", "#adb5bd");
                dot.setAttribute("class", "map-dot locked-dot");
            }
            
            svg.appendChild(dot);
        }
    }
    
    // Fungsi untuk membuat jalur titik-titik
    function createDotPath(svg, x1, y1, x2, y2, startStatus, endStatus, allCompleted) {
        const svgNS = "http://www.w3.org/2000/svg";
        const isCompleted = startStatus === 'completed' && (endStatus === 'completed' || endStatus === 'unlocked');
        
        // Hitung jarak dan jumlah titik
        const distance = Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
        const dotCount = Math.floor(distance / 20); // Titik setiap 20px
        
        for (let i = 0; i <= dotCount; i++) {
            // Posisi titik
            const ratio = i / dotCount;
            const x = x1 + (x2 - x1) * ratio;
            const y = y1 + (y2 - y1) * ratio;
            
            // Buat titik
            const dot = document.createElementNS(svgNS, "circle");
            dot.setAttribute("cx", x);
            dot.setAttribute("cy", y);
            
            // Tentukan warna dan ukuran berdasarkan status
            if (isCompleted) {
                // Gunakan warna emas hanya jika semua soal selesai
                if (allCompleted && endStatus === 'completed') {
                    dot.setAttribute("r", "4");
                    dot.setAttribute("fill", "#FFD700");
                    dot.setAttribute("class", "map-dot trophy-dot");
                } else {
                    // Gunakan warna hijau untuk soal yang sudah dikerjakan
                    dot.setAttribute("r", "4");
                    dot.setAttribute("fill", "#4CAF50");
                    dot.setAttribute("class", "map-dot completed-dot");
                }
            } else {
                // Titik abu-abu untuk soal yang belum dikerjakan
                dot.setAttribute("r", "3");
                dot.setAttribute("fill", "#adb5bd");
                dot.setAttribute("class", "map-dot locked-dot");
            }
            
            svg.appendChild(dot);
        }
    }
    
    // Panggil fungsi saat halaman dimuat
    drawTreasureMap();
    
    // Panggil ulang saat ukuran window berubah
    window.addEventListener('resize', drawTreasureMap);
    
    // Cek parameter URL untuk scroll
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('scroll') === 'true') {
        // Cari level yang unlocked
        const unlockedLevel = document.querySelector('.unlocked');
        
        if (unlockedLevel) {
            // Scroll ke elemen dengan delay
            setTimeout(() => {
                unlockedLevel.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                
                // Tambahkan efek highlight
                unlockedLevel.style.transition = 'all 0.3s ease';
                unlockedLevel.style.boxShadow = '0 0 20px rgba(33, 150, 243, 0.8)';
                
                setTimeout(() => {
                    unlockedLevel.style.boxShadow = '';
                }, 2000);
            }, 500);
            
            // Hapus parameter scroll dari URL
            const newUrl = window.location.pathname + 
                window.location.search.replace('scroll=true', '').replace('?&', '?').replace('&&', '&');
            window.history.replaceState({}, '', newUrl);
        }
    }
    
    // Handle navigation for guest users
    const isGuest = {{ $isGuest ? 'true' : 'false' }};
    
    if (isGuest) {
        // For guest users, check local storage for completed questions
        const questionCompleted = localStorage.getItem('questionCompleted');
        
        if (questionCompleted === 'true') {
            // Clear the flag
            localStorage.removeItem('questionCompleted');
            
            // Check if scroll=true is in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('scroll') === 'true') {
                // Force redraw of map by calling drawTreasureMap again
                setTimeout(function() {
                    drawTreasureMap();
                }, 100);
            }
        }
    }
});
</script>
@endpush
@endsection 