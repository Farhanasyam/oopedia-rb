<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="users" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Manajemen Admin" />
        <div class="container-fluid py-4">
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3">
                <div class="input-group input-group-outline my-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama atau email..." value="{{ request('search') }}" style="height: 50px;">
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

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3 mb-0">
                                    @if(auth()->user()->role_id == 1)
                                        Daftar Pengguna
                                    @else
                                        Daftar Dosen
                                    @endif
                                </h6>
                                @if(auth()->user()->role_id == 1)
                                <div class="d-flex me-3">
                                    <a href="{{ route('admin.pending-admins') }}" class="btn btn-warning btn-sm me-2 d-flex align-items-center">
                                        <i class="material-icons text-sm me-1">pending</i>
                                        <span>Dosen Pending</span>
                                        @php
                                            $pendingAdminsCount = \App\Models\User::where('role_id', 2)->where('is_approved', false)->count();
                                        @endphp
                                        @if($pendingAdminsCount > 0)
                                            <span class="badge bg-danger ms-1">{{ $pendingAdminsCount }}</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.users.import') }}" class="btn btn-sm btn-success me-2">
                                        <i class="material-icons text-sm">upload_file</i>
                                        <span>Tambah dengan Excel</span>
                                    </a>
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-sm d-flex align-items-center">
                                        <i class="material-icons text-sm me-1">add</i>
                                        <span>Tambah Pengguna</span>
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                                    <strong>Error:</strong> {{ session('error') }}
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
                            
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(auth()->user()->role_id == 1)
                                            <!-- Tampilkan superadmin (role_id = 1) -->
                                            @php
                                                $superadmins = \App\Models\User::where('role_id', 1)->get();
                                            @endphp
                                            @foreach($superadmins as $superadmin)
                                            <tr class="bg-light">
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $superadmin->name }}</h6>
                                                            <span class="badge bg-gradient-dark">Superadmin</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $superadmin->email }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $superadmin->role->role_name }}</p>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">Aktif</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    @if(auth()->id() == $superadmin->id)
                                                        <a href="{{ route('admin.users.edit', $superadmin->id) }}" class="btn btn-sm btn-info">Edit</a>
                                                        <button class="btn btn-sm btn-secondary" disabled>Akun Anda</button>
                                                    @else
                                                        <a href="{{ route('admin.users.edit', $superadmin->id) }}" class="btn btn-sm btn-info">Edit</a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif

                                        <!-- Tampilkan dosen (role_id = 2) -->
                                        @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $user->email }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $user->role->role_name }}</p>
                                            </td>
                                            <td>
                                                @if($user->is_approved)
                                                    <span class="badge bg-success">Disetujui</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-info">Edit</a>
                                                @if(auth()->user()->role_id == 1 && auth()->id() != $user->id)
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus dosen ini?')">Hapus</button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-center mt-3">
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />

</x-layout>

@push('scripts')
@endpush 