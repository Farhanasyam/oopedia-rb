<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="question-banks" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Bank Soal" />
        <div class="container-fluid py-4">
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.question-banks.index') }}" class="mb-3">
                <div class="input-group input-group-outline my-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama..." value="{{ request('search') }}" style="height: 50px;">
                    <button class="btn btn-icon btn-3 btn-primary" type="submit" style="height: 50px;">
                        <span class="btn-inner--icon"><i class="material-icons">search</i></span>
                        <span class="btn-inner--text">Cari</span>
                    </button>
                </div>
            </form>

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
                                <h6 class="text-white text-capitalize ps-3 mb-0">Daftar Bank Soal</h6>
                                <a href="{{ route('admin.question-banks.create') }}" class="btn btn-sm btn-light me-3">
                                    <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Tambah Bank Soal
                                </a>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Bank Soal</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Materi</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dibuat Oleh</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal Dibuat</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($questionBanks as $bank)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $bank->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ Str::limit($bank->description, 50) }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm mb-0">{{ $bank->material->title ?? 'Tidak ada materi' }}</p>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $bank->creator ? $bank->creator->name : 'Unknown' }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $bank->created_at->format('d/m/Y') }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('admin.question-banks.show', $bank) }}" class="btn btn-sm btn-info">
                                                    <i class="material-icons text-sm">visibility</i>
                                                    <span>Detail</span>
                                                </a>
                                                <a href="{{ route('admin.question-banks.manage-questions', $bank) }}" class="btn btn-sm btn-success">
                                                    <i class="material-icons text-sm">question_answer</i>
                                                    <span>Kelola Soal</span>
                                                </a>
                                                <a href="{{ route('admin.question-banks.configure', $bank) }}" class="btn btn-sm btn-warning">
                                                    <i class="material-icons text-sm">settings</i>
                                                    <span>Konfigurasi</span>
                                                </a>
                                                <a href="{{ route('admin.question-banks.edit', $bank) }}" class="btn btn-sm btn-primary">
                                                    <i class="material-icons text-sm">edit</i>
                                                    <span>Edit</span>
                                                </a>
                                                <form action="{{ route('admin.question-banks.destroy', $bank) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus bank soal ini?')">
                                                        <i class="material-icons text-sm">delete</i>
                                                        <span>Hapus</span>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <p class="text-sm mb-0">Belum ada bank soal</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $questionBanks->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />

</x-layout>

@push('js')
@endpush 