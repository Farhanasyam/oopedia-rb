@props(['activePage', 'userName', 'userRole'])

<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark"
    id="sidenav-main">
    <br>
    <div class="sidenav-header d-flex flex-column align-items-center justify-content-center py-3">
        @php
            $dashboardRoute = auth()->user()->role_id === 3 ? 'mahasiswa.dashboard' : 'admin.dashboard';
        @endphp
        <a class="navbar-brand w-100 text-center" href="{{ route($dashboardRoute) }}">
            <img src="{{ asset('images/logo.png') }}" alt="OOPEDIA" class="img-fluid" style="max-height: 130px; width: auto;">
        </a>
    </div>
    <br>
    <hr class="horizontal light mt-0 mb-2">
    <div class="d-flex align-items-center mx-3">
        <i class="material-icons opacity-10 me-2">person</i>
        <div class="flex-grow-1 text-center">
            <span class="font-weight-bold text-white">{{ $userName }}</span>
        </div>
        <span class="text-white ms-2">{{ $userRole }}</span>
    </div>
    <hr class="horizontal light mt-2 mb-2">
    <div class="collapse navbar-collapse w-auto max-height-vh-100" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            {{-- Menu Dashboard untuk Semua Role --}}
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'dashboard' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route($dashboardRoute) }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            {{-- Menu Pembelajaran --}}
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Kelola Pembelajaran</h6>
            </li>
            
            {{-- Menu Materi --}}
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'materials' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('admin.materials.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">library_books</i>
                    </div>
                    <span class="nav-link-text ms-1">Kelola Materi</span>
                </a>
            </li>

            {{-- Menu Soal --}}
            <li class="nav-item">
                <a class="nav-link text-white" 
                   data-bs-toggle="collapse" 
                   href="#questionsMenu" 
                   role="button" 
                   aria-expanded="{{ str_contains($activePage, 'questions') ? 'true' : 'false' }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">quiz</i>
                    </div>
                    <span class="nav-link-text ms-1">Kelola Soal</span>
                    <i class="material-icons ms-auto">keyboard_arrow_down</i>
                </a>
                <div class="collapse {{ str_contains($activePage, 'questions') ? 'show' : '' }}" id="questionsMenu">
                    <ul class="nav">
                        @forelse($materials ?? [] as $material)
                            <li class="nav-item">
                                <a class="nav-link text-white {{ $activePage == 'questions-'.$material->id ? 'active bg-gradient-primary' : '' }}"
                                    href="{{ route('admin.materials.questions.index', $material->id) }}">
                                    <span class="sidenav-mini-icon">
                                        <i class="material-icons opacity-10">article</i>
                                    </span>
                                    <span class="sidenav-normal ms-2">{{ $material->title }}</span>
                                </a>
                            </li>
                        @empty
                            <li class="nav-item">
                                <span class="nav-link text-white-50">
                                    <i class="material-icons opacity-10">info</i>
                                    <span class="ms-2">Belum ada materi</span>
                                </span>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </li>

            {{-- Menu Bank Soal hanya untuk Admin dan Superadmin --}}
            @if(auth()->user()->role_id <= 2)
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'question-banks' ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.question-banks.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">quiz</i>
                    </div>
                    <span class="nav-link-text ms-1">Bank Soal</span>
                </a>
            </li>
            @endif

            {{-- Menu Progress Mahasiswa --}}
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Data Mahasiswa</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'students' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('admin.students.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">school</i>
                    </div>
                    <span class="nav-link-text ms-1">Data Mahasiswa</span>
                </a>
            </li>

            {{-- Menu Admin hanya untuk Superadmin --}}
            @if(auth()->user()->role_id == 1)
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Data Dosen</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'users' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('admin.users.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">person</i>
                    </div>
                    <span class="nav-link-text ms-1">Data Dosen</span>
                </a>
            </li>
            
            {{-- Menu Admin Pending hanya untuk Superadmin --}}
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'pending-users' ? 'active bg-gradient-primary' : '' }}" 
                   href="{{ route('admin.pending-admins') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">person_add</i>
                    </div>
                    <span class="nav-link-text ms-1">Dosen Pending</span>
                    @php
                        $pendingAdminsCount = \App\Models\User::where('role_id', 2)->where('is_approved', false)->count();
                    @endphp
                    @if($pendingAdminsCount > 0)
                        <span class="badge bg-danger ms-auto">{{ $pendingAdminsCount }}</span>
                    @endif
                </a>
            </li>
            @endif

            {{-- Menu UEQ Survey Results hanya untuk Admin dan Superadmin --}}
            @if(auth()->user()->role_id <= 2)
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Feedback</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'ueq' ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('admin.ueq.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">poll</i>
                    </div>
                    <span class="nav-link-text ms-1">UEQ Survey Results</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</aside>

<style>
/* Styling untuk dropdown menu */
#questionsMenu {
    margin-left: 1rem;
    transition: all 0.3s ease;
}

#questionsMenu .nav-link {
    padding: 0.5rem 1rem;
    margin: 0.25rem 0;
    border-radius: 0.375rem;
}

#questionsMenu .nav-link:hover {
    background-color: rgba(199, 199, 199, 0.2);
}

#questionsMenu .nav-link.active {
    background: linear-gradient(195deg, #EC407A 0%, #D81B60 100%);
}

/* Animasi untuk icon dropdown */
[aria-expanded="true"] .material-icons.ms-auto {
    transform: rotate(180deg);
    transition: transform 0.3s ease;
}

[aria-expanded="false"] .material-icons.ms-auto {
    transform: rotate(0deg);
    transition: transform 0.3s ease;
}

/* Styling untuk item dropdown */
.sidenav-mini-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
}

.sidenav-normal {
    font-size: 0.875rem;
    font-weight: 400;
}

.material-icons.ms-auto::after {
    display: none !important;
    content: none !important;
}

.nav-link::after {
    display: none !important; /* Menghilangkan kotak */
}

.nav-item h6 {
    margin: 0;
    padding: 1rem 0;
}

#exerciseMenu {
    margin-left: 1rem;
    transition: all 0.3s ease;
}

#exerciseMenu .nav-link {
    padding: 0.5rem 1rem;
    margin: 0.25rem 0;
    border-radius: 0.375rem;
}

#exerciseMenu .nav-link:hover {
    background-color: rgba(199, 199, 199, 0.2);
}

#exerciseMenu .nav-link.active {
    background: linear-gradient(195deg, #EC407A 0%, #D81B60 100%);
}

.sidenav {
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
    padding-bottom: 100px; /* Menambah padding bottom */
}

/* Menyembunyikan scrollbar tapi tetap bisa scroll */
.sidenav::-webkit-scrollbar {
    width: 0;  /* Untuk Chrome, Safari, dan Opera */
    display: none;
}

.sidenav {
    -ms-overflow-style: none;  /* Untuk Internet Explorer dan Edge */
    scrollbar-width: none;  /* Untuk Firefox */
}

/* Memastikan konten sidebar memiliki ruang yang cukup */
.sidenav .navbar-collapse {
    height: auto;
    min-height: calc(100vh - 100px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle all dropdown toggles
    const dropdownToggles = document.querySelectorAll('[data-bs-toggle="collapse"]');
    
    dropdownToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetMenu = document.querySelector(targetId);
            
            // Tutup semua dropdown yang terbuka kecuali yang sedang di-klik
            dropdownToggles.forEach(function(otherToggle) {
                if (otherToggle !== toggle) {
                    const otherId = otherToggle.getAttribute('href');
                    const otherMenu = document.querySelector(otherId);
                    otherToggle.setAttribute('aria-expanded', 'false');
                    otherMenu?.classList.remove('show');
                }
            });

            // Toggle dropdown yang di-klik
            const willExpand = !targetMenu.classList.contains('show');
            this.setAttribute('aria-expanded', willExpand);
            targetMenu.classList.toggle('show');
        });
    });
});
</script>

{{-- Tambahkan script tutorial di bagian bawah file --}}
@push('js')
@endpush
