@extends('mahasiswa.layouts.app')

@section('title', 'Materi Sedang Dipelajari')

@section('content')
<div class="dashboard-header text-center">
    <h1 class="main-title">Materi Sedang Dipelajari</h1>
    <div class="title-underline"></div>
</div>

<div class="dashboard-content">
    @if(count($materialsWithStats) == 0)
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <h3 class="empty-state-title">Belum Ada Materi yang Sedang Dipelajari</h3>
            <p class="empty-state-description">
                Anda belum memulai belajar materi apapun atau semua materi sudah selesai.
            </p>
            <a href="{{ route('mahasiswa.materials.index') }}" class="btn btn-primary">
                <i class="fas fa-book me-2"></i>Lihat Daftar Materi
            </a>
        </div>
    @else
        <div class="row g-4">
            @foreach($materialsWithStats as $materialData)
                @php
                    $material = $materialData['material'];
                    $stats = $materialData['stats'];
                @endphp
                <div class="col-md-12 col-lg-6">
                    <div class="material-card">
                        <div class="material-card-header">
                            <div class="material-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h4 class="material-title">{{ $material->title }}</h4>
                        </div>
                        <div class="material-card-body">
                            <!-- Overall Progress -->
                            <div class="progress-section">
                                <div class="progress-info d-flex justify-content-between">
                                    <span class="progress-text">Progress Keseluruhan</span>
                                    <span class="progress-percentage">{{ $stats['overall']['percentage'] }}%</span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar" style="width: {{ $stats['overall']['percentage'] }}%"></div>
                                </div>
                                <div class="progress-details">
                                    <small>{{ $stats['overall']['correct'] }} dari {{ $stats['overall']['total'] }} soal selesai</small>
                                </div>
                            </div>
                            
                            <div class="difficulty-progress-container">
                                <!-- Beginner Progress -->
                                <div class="difficulty-progress beginner">
                                    <div class="difficulty-label">
                                        <i class="fas fa-battery-quarter"></i>
                                        <span>Beginner</span>
                                    </div>
                                    <div class="difficulty-bar-container">
                                        <div class="difficulty-bar" style="width: {{ $stats['beginner']['percentage'] }}%"></div>
                                    </div>
                                    <div class="difficulty-percentage">{{ $stats['beginner']['percentage'] }}%</div>
                                    <div class="difficulty-details">
                                        <small>{{ $stats['beginner']['correct'] }}/{{ $stats['beginner']['configured_total'] }} soal</small>
                                    </div>
                                </div>
                                
                                <!-- Medium Progress -->
                                <div class="difficulty-progress medium">
                                    <div class="difficulty-label">
                                        <i class="fas fa-battery-half"></i>
                                        <span>Medium</span>
                                    </div>
                                    <div class="difficulty-bar-container">
                                        <div class="difficulty-bar" style="width: {{ $stats['medium']['percentage'] }}%"></div>
                                    </div>
                                    <div class="difficulty-percentage">{{ $stats['medium']['percentage'] }}%</div>
                                    <div class="difficulty-details">
                                        <small>{{ $stats['medium']['correct'] }}/{{ $stats['medium']['configured_total'] }} soal</small>
                                    </div>
                                </div>
                                
                                <!-- Hard Progress -->
                                <div class="difficulty-progress hard">
                                    <div class="difficulty-label">
                                        <i class="fas fa-battery-full"></i>
                                        <span>Hard</span>
                                    </div>
                                    <div class="difficulty-bar-container">
                                        <div class="difficulty-bar" style="width: {{ $stats['hard']['percentage'] }}%"></div>
                                    </div>
                                    <div class="difficulty-percentage">{{ $stats['hard']['percentage'] }}%</div>
                                    <div class="difficulty-details">
                                        <small>{{ $stats['hard']['correct'] }}/{{ $stats['hard']['configured_total'] }} soal</small>
                                    </div>
                                </div>
                            </div>

                            <div class="material-actions">
                                <a href="{{ route('mahasiswa.materials.show', $material->id) }}" class="btn-view-material">
                                    <i class="fas fa-book me-2"></i>
                                    <span>Lihat Materi</span>
                                </a>
                                <a href="{{ route('mahasiswa.materials.questions.show', $material->id) }}" class="btn-read-material">
                                    <i class="fas fa-question-circle me-2"></i>
                                    <span>Lanjut Latihan</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('css')
<style>
/* Base styles */
.dashboard-header {
    text-align: center;
    margin-bottom: 2rem;
}

.main-title {
    font-size: 2.2rem;
    font-weight: 700;
    color: #004E98;
    margin-bottom: 0.5rem;
}

.title-underline {
    height: 4px;
    width: 80px;
    background: linear-gradient(to right, #004E98, #0074D9);
    margin: 0 auto;
    border-radius: 2px;
}

/* Material card styles */
.material-card {
    border-radius: 12px;
    background-color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    margin-bottom: 20px;
}

.material-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.material-card-header {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    background: linear-gradient(135deg, #004E98 0%, #0074D9 100%);
    color: white;
}

.material-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.material-title {
    font-weight: 600;
    margin: 0;
    font-size: 1.3rem;
}

.material-card-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Overall progress section */
.progress-section {
    margin-bottom: 1rem;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.progress-text {
    font-weight: 600;
    font-size: 0.95rem;
}

.progress-percentage {
    font-weight: 700;
    color: #004E98;
}

.progress-bar-container {
    height: 10px;
    background-color: #e9ecef;
    border-radius: 5px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #004E98, #0074D9);
    border-radius: 5px;
    transition: width 0.5s ease;
}

.progress-details {
    font-size: 0.85rem;
    color: #6c757d;
}

/* Difficulty progress bars - Perbaikan */
.difficulty-progress-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1rem;
}

.difficulty-progress {
    display: grid;
    grid-template-columns: minmax(110px, 110px) 1fr auto; /* Fixed width for label column */
    grid-template-rows: auto auto;
    column-gap: 1rem;
    align-items: center;
}

.difficulty-label {
    grid-column: 1;
    grid-row: 1;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 0.9rem;
    width: 110px; /* Fixed width for all labels */
}

.difficulty-bar-container {
    grid-column: 2;
    grid-row: 1;
    height: 8px;
    background-color: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.difficulty-percentage {
    grid-column: 3;
    grid-row: 1;
    font-weight: 700;
    font-size: 0.9rem;
}

.difficulty-details {
    grid-column: 2;
    grid-row: 2;
    font-size: 0.75rem;
    color: #6c757d;
}

/* Difficulty-specific colors */
.beginner .difficulty-bar {
    background: linear-gradient(90deg, #28a745, #5cb85c);
}

.beginner .difficulty-label {
    color: #28a745;
}

.medium .difficulty-bar {
    background: linear-gradient(90deg, #fd7e14, #f0ad4e);
}

.medium .difficulty-label {
    color: #fd7e14;
}

.hard .difficulty-bar {
    background: linear-gradient(90deg, #dc3545, #ff6b6b);
}

.hard .difficulty-label {
    color: #dc3545;
}

.difficulty-bar {
    height: 100%;
    border-radius: 4px;
    transition: width 0.5s ease;
}

/* Action buttons */
.material-actions {
    display: flex;
    gap: 1rem;
    margin-top: auto;
}

.btn-view-material, .btn-read-material {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    text-align: center;
    position: relative;
    overflow: hidden;
    gap: 0.5rem;
}

.btn-view-material {
    background-color: #f8f9fa;
    color: #004E98;
    border: 1px solid #dee2e6;
}

.btn-view-material:hover {
    background-color: #e9ecef;
    color: #003d75;
}

.btn-read-material {
    background: linear-gradient(135deg, #004E98 0%, #0074D9 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 78, 152, 0.2);
}

.btn-read-material:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 78, 152, 0.3);
    background: linear-gradient(135deg, #003d75 0%, #005bb0 100%);
    color: white;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    max-width: 600px;
    margin: 0 auto;
}

.empty-state-icon {
    font-size: 4rem;
    color: #e0e0e0;
    margin-bottom: 20px;
}

.empty-state-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
}

.empty-state-description {
    color: #666;
    margin-bottom: 25px;
    font-size: 1.1rem;
}

/* Responsiveness */
@media (max-width: 768px) {
    .material-actions {
        flex-direction: column;
    }
    
    .material-title {
        font-size: 1.1rem;
    }
}
</style>
@endpush
@endsection