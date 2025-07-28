<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="question-banks" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Kelola Soal Bank" />
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
                        
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3">Kelola Soal: {{ $questionBank->name }}</h6>
                                <a href="{{ route('admin.question-banks.show', $questionBank) }}" class="btn btn-sm btn-light me-3">
                                    <i class="material-icons text-sm">arrow_back</i>
                                    <span>Kembali</span>
                                </a>
                            </div>
                        </div>
                        
                        <div class="card-body pt-4">
                            <!-- Filter and search -->
                            <div class="row mb-4">
                                <div class="col-md-5">
                                    <div class="input-group input-group-outline">
                                        <input type="text" name="search" class="form-control" placeholder="Cari soal..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group input-group-outline">
                                        <select name="difficulty" class="form-control">
                                            <option value="">Semua Tingkat Kesulitan</option>
                                            <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                            <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="material-icons text-sm">search</i>
                                        <span>Filter</span>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Questions list -->
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Soal</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Materi</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kesulitan</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tipe Soal</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($questions as $question)
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
                                                {{ $question->material->title ?? 'Tidak ada materi' }}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $question->difficulty == 'beginner' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($question->difficulty) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $question->formatted_type ?? ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                            </td>
                                            <td class="align-middle text-center">
                                                <form action="{{ route('admin.question-banks.add-question', ['questionBank' => $questionBank, 'question' => $question]) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="material-icons text-sm">add</i>
                                                        <span>Tambahkan ke Bank</span>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <p class="text-sm mb-0">Tidak ada soal yang tersedia untuk ditambahkan.</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-center mt-4">
                                {{ $questions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />

</x-layout> 