<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="question-banks" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Detail Bank Soal" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                    <br><br>
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3">Detail Bank Soal</h6>
                                <div>
                                    <a href="{{ route('admin.question-banks.manage-questions', $questionBank) }}" class="btn btn-sm btn-success me-2">
                                        <i class="material-icons text-sm">question_answer</i>
                                        <span>Kelola Soal</span>
                                    </a>
                                    <a href="{{ route('admin.question-banks.configure', $questionBank) }}" class="btn btn-sm btn-warning me-2">
                                        <i class="material-icons text-sm">settings</i>
                                        <span>Konfigurasi</span>
                                    </a>
                                    <a href="{{ route('admin.question-banks.index') }}" class="btn btn-sm btn-light me-3">
                                        <i class="material-icons text-sm">arrow_back</i>
                                        <span>Kembali</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body px-4 py-3">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h4>{{ $questionBank->name }}</h4>
                                    <p class="text-muted">{{ $questionBank->description }}</p>
                                    <p><strong>Dibuat oleh:</strong> {{ $questionBank->creator->name ?? 'Unknown' }}</p>
                                    <p><strong>Tanggal dibuat:</strong> {{ $questionBank->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            
                            <!-- Statistik Soal -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="mb-3">Statistik Soal</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Beginner</h6>
                                            <h3 class="mb-0">{{ $questionCounts['beginner'] }}</h3>
                                            <p class="text-muted">soal</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Medium</h6>
                                            <h3 class="mb-0">{{ $questionCounts['medium'] }}</h3>
                                            <p class="text-muted">soal</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Hard</h6>
                                            <h3 class="mb-0">{{ $questionCounts['hard'] }}</h3>
                                            <p class="text-muted">soal</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Daftar Soal -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="mb-3">Daftar Soal ({{ $questionBank->questions->count() }} soal)</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Soal</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kesulitan</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipe Soal</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($questionBank->questions as $question)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex px-2 py-1">
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <div class="mb-0 text-sm">
                                                                    {!! Str::limit(strip_tags($question->question_text), 100) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $question->difficulty == 'beginner' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($question->difficulty) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ $question->formatted_type ?? ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('admin.question-banks.remove-question', ['questionBank' => $questionBank, 'question' => $question]) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus soal ini dari bank soal?')">
                                                                <i class="material-icons text-sm">delete</i>
                                                                <span>Hapus</span>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">
                                                        <p>Belum ada soal dalam bank soal ini.</p>
                                                        <a href="{{ route('admin.question-banks.manage-questions', $questionBank) }}" class="btn btn-sm btn-primary mt-2">
                                                            <i class="material-icons text-sm">add</i>
                                                            <span>Tambahkan Soal</span>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />

</x-layout> 