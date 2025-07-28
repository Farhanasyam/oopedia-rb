<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="pending-users" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Admin Pending" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                    <br><br>

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Dosen Menunggu Persetujuan</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="p-4">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                @if(auth()->user()->role_id == 1)
                                <!-- Tampilan untuk superadmin -->
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal Daftar</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pendingAdmins as $admin)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $admin->name }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $admin->email }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $admin->created_at->format('d/m/Y H:i') }}</p>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <form action="{{ route('admin.users.approve', $admin->id) }}" method="POST" class="mx-1">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm px-3">
                                                                Setujui
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('admin.users.reject', $admin->id) }}" method="POST" class="mx-1">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-sm px-3" 
                                                                    onclick="return confirm('Apakah Anda yakin ingin menolak admin ini?')">
                                                                Tolak
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <p class="text-sm mb-0">Tidak ada admin yang menunggu persetujuan</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <!-- Tampilan untuk admin yang menunggu persetujuan -->
                                <div class="container py-5">
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div class="card">
                                                <div class="card-header bg-warning text-white">Menunggu Persetujuan</div>
                                                <div class="card-body">
                                                    <p>Akun admin Anda sedang menunggu persetujuan dari superadmin.</p>
                                                    <p>Silakan coba login kembali nanti.</p>
                                                    
                                                    <form method="POST" action="{{ route('admin.logout') }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger">Logout</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto refresh setelah form submit
            const approveForms = document.querySelectorAll('form[action*="approve"]');
            const rejectForms = document.querySelectorAll('form[action*="reject"]');
            
            const handleFormSubmit = function(e) {
                const form = e.target;
                const originalButton = form.querySelector('button[type="submit"]');
                
                if (originalButton) {
                    originalButton.disabled = true;
                    originalButton.innerHTML = 'Memproses...';
                }
            };
            
            approveForms.forEach(form => {
                form.addEventListener('submit', handleFormSubmit);
            });
            
            rejectForms.forEach(form => {
                form.addEventListener('submit', handleFormSubmit);
            });
            
            // Jika ada pesan sukses, refresh halaman setelah 2 detik
            @if(session('success'))
            setTimeout(function() {
                window.location.reload();
            }, 2000);
            @endif
        });
    </script>
    <x-admin.tutorial />

</x-layout>