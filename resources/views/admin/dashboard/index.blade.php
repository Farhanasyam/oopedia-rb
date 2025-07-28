<x-layout bodyClass="dashboard-layout g-sidenav-show">
    <x-navbars.sidebar activePage="dashboard" :userName="$userName" :userRole="$userRole"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Dashboard Admin"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <!-- Statistics Cards Row -->
            <div class="row">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl">
                                <i class="material-icons opacity-10">group</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Mahasiswa</p>
                                <h4 class="mb-0">{{ $totalStudents }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl">
                                <i class="material-icons opacity-10">person_outline</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Mahasiswa Aktif</p>
                                <h4 class="mb-0">{{ $activeStudents }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl">
                                <i class="material-icons opacity-10">library_books</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Materi</p>
                                <h4 class="mb-0">{{ $totalMaterials }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl">
                                <i class="material-icons opacity-10">quiz</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Soal</p>
                                <h4 class="mb-0">{{ $totalQuestions }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Students Section -->
            <div class="row mt-4">
                <div class="col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Mahasiswa dengan Performa Terbaik</h6>
                            <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-info">
                                <i class="material-icons text-sm">visibility</i>
                                Lihat Semua
                            </a>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mahasiswa</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Soal Diselesaikan</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Progress Materi</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Terakhir Aktif</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($studentProgress as $student)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="avatar avatar-sm me-3 bg-gradient-primary rounded-circle">
                                                        <span class="text-white text-xs">{{ substr($student->name, 0, 1) }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $student->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $student->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">{{ $student->completed_questions }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span class="me-2 text-xs font-weight-bold">{{ $student->materials_progress }}%</span>
                                                    <div class="progress" style="width: 100px; height: 5px;">
                                                        <div class="progress-bar bg-gradient-info" role="progressbar" 
                                                             aria-valuenow="{{ $student->materials_progress }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100" 
                                                             style="width: {{ $student->materials_progress }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">{{ $student->last_active ? $student->last_active->diffForHumans() : 'Belum pernah' }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('admin.students.progress', $student->id) }}" class="btn btn-sm btn-info">
                                                    <i class="material-icons text-sm">assessment</i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Material Completion Stats -->
            <div class="row mt-4">
                <div class="col-lg-6 mb-lg-0 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Statistik Penyelesaian Materi</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="material-completion-chart" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Materi Paling Populer</h6>
                        </div>
                        <div class="card-body p-3">
                            <ul class="list-group">
                                @foreach($popularMaterials as $material)
                                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3 bg-gradient-primary shadow text-center">
                                            <i class="material-icons opacity-10 text-white">book</i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">{{ $material->title }}</h6>
                                            <span class="text-xs">{{ $material->students_count }} mahasiswa aktif</span>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <span class="text-success text-sm font-weight-bolder">{{ $material->completion_rate }}% selesai</span>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Aktivitas Penyelesaian Terbaru</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="timeline timeline-one-side">
                                @foreach($recentProgress as $progress)
                                <div class="timeline-block mb-3">
                                    <span class="timeline-step">
                                        @if($progress->is_correct)
                                            <i class="material-icons text-success">check_circle</i>
                                        @else
                                            <i class="material-icons text-warning">error_outline</i>
                                        @endif
                                    </span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">
                                            {{ optional($progress->user)->name ?? 'unknown' }} 
                                            {{ $progress->is_correct ? 'berhasil menyelesaikan' : 'mencoba' }} soal
                                        </h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                            {{ optional($progress->material)->title ?? '-' }} - 
                                            <span class="badge bg-gradient-{{ $progress->is_correct ? 'success' : 'warning' }}">
                                                {{ ucfirst($progress->question->difficulty ?? 'unknown') }}
                                            </span>
                                        </p>
                                        <p class="text-sm mt-3 mb-0">
                                            {{ $progress->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>
    <x-admin.tutorial />
</x-layout>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Material Completion Chart
        var ctx = document.getElementById('material-completion-chart').getContext('2d');
        var materialChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($materialStats->pluck('title')) !!},
                datasets: [{
                    label: 'Tingkat Penyelesaian (%)',
                    data: {!! json_encode($materialStats->pluck('completion_rate')) !!},
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(255, 99, 132, 0.7)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw + '%';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
