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
                                <h6 class="text-white text-capitalize ps-3">Admin Menunggu Persetujuan</h6>
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
                                
                                @if(count($pendingAdmins) > 0)
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
                                                @foreach($pendingAdmins as $admin)
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
                                                        <p class="text-xs font-weight-bold mb-0">{{ $admin->created_at->format('d M Y H:i') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <div class="d-flex justify-content-center">
                                                            <form action="{{ route('admin.users.approve', $admin->id) }}" method="POST" class="me-2">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                                                            </form>
                                                            <form action="{{ route('admin.users.reject', $admin->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak admin ini? Akun akan diubah menjadi mahasiswa.')">Tolak</button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        Tidak ada admin yang menunggu persetujuan.
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