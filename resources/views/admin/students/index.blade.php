<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="students" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Data Mahasiswa" />
        <div class="container-fluid py-4">
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.students.index') }}" class="mb-3">
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
                        
                        @if(session('importErrors'))
                            <div class="alert alert-warning alert-dismissible fade show mx-4" role="alert">
                                <p>Beberapa baris tidak dapat diimpor:</p>
                                <ul>
                                    @foreach(session('importErrors') as $error)
                                        <li>Baris {{ $error['row'] }}: {{ implode(', ', $error['errors']) }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3">Data Mahasiswa</h6>
                                <div class="d-flex me-3">
                                    <a href="{{ route('admin.students.import') }}" class="btn btn-sm btn-success me-2">
                                        <i class="material-icons text-sm">upload_file</i>
                                        <span>Tambah dengan Excel</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Soal Dijawab</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Progress Keseluruhan</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($students as $student)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $student->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $student->email }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold">{{ $student->total_answered_questions ?? 0 }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="progress" style="height: 8px; width: 80%; margin: 0 auto;">
                                                    <div class="progress-bar bg-gradient-info" role="progressbar" 
                                                         style="width: {{ $student->overall_progress }}%" 
                                                         aria-valuenow="{{ $student->overall_progress }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <span class="text-xs font-weight-bold">{{ $student->overall_progress }}%</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('admin.students.progress', $student) }}" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="material-icons text-sm">visibility</i>
                                                        <span>Detail</span>
                                                    </a>
                                                    <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini?')">
                                                            <i class="material-icons text-sm">delete</i>
                                                            <span>Hapus</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <p class="text-sm mb-0">Belum ada data mahasiswa</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $students->links() }}
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