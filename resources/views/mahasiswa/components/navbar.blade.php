@push('css')
<link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
<link href="https://unpkg.com/intro.js/minified/introjs.min.css" rel="stylesheet">
<style>
    /* Perbaikan responsif navbar */
    @media (max-width: 767.98px) {
        .navbar .container-fluid {
            padding-left: 8px;
            padding-right: 8px;
        }
        
        /* Profile dropdown style untuk mobile */
        .profile-dropdown {
            position: static;  /* Penting: membuat dropdown muncul relatif terhadap navbar */
        }
        
        .profile-dropdown .dropdown-menu {
            position: absolute;
            right: 10px;
            left: auto;
            width: auto;
            min-width: 200px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        /* Animasi dropdown yang lebih halus */
        .dropdown-menu.show {
            transform: translateY(0);
            opacity: 1;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }
        
        .dropdown-menu {
            transform: translateY(-10px);
            opacity: 0;
        }
    }
    
    /* Perbaikan umum */
    .navbar {
        position: sticky;
        top: 0;
        z-index: 1030;
    }
    
    .dropdown-item {
        display: flex;
        align-items: center;
        padding: 8px 16px;
    }
    
    .profile-image {
        border-radius: 50%;
        object-fit: cover;
    }
    
    .mobile-header-links {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
        margin-bottom: 0.5rem;
    }
    
    .mobile-header-links .btn-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: white;
        color: #333;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .mobile-header-links .btn-icon.active {
        background: #007bff;
        color: white;
    }
    
    @media (max-width: 359.98px) {
        .profile-image {
            width: 25px;
            height: 25px;
        }
        
        .navbar .container-fluid {
            padding-left: 4px;
            padding-right: 4px;
        }
        
        .btn-icon {
            padding: 0.25rem;
            font-size: 0.875rem;
        }
    }
</style>
@endpush


<nav class="navbar">
    <div class="container-fluid">
        <!-- Left side group -->
        <div class="d-flex align-items-center h-100">
            <!-- Sidebar Toggle Button - hanya muncul di mobile -->
            <button id="sidebarToggleBtn" class="btn btn-icon d-lg-none me-2">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Navigation links -->
            <div class="nav-links">
                <ul class="nav-menu">
                    @auth
                    <li>
                        <a href="{{ route('mahasiswa.dashboard') }}" 
                           class="nav-link {{ request()->routeIs('mahasiswa.dashboard*') ? 'active' : '' }}"
                           data-bs-toggle="tooltip" 
                           data-bs-placement="bottom" 
                           title="Dashboard pengguna">
                            <i class="fas fa-home me-2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    @endauth
                    <li>
                        <a href="{{ route('mahasiswa.materials.index') }}" 
                           class="nav-link {{ request()->routeIs('mahasiswa.materials*') && !request()->routeIs('mahasiswa.materials.questions*') ? 'active' : '' }}"
                           data-bs-toggle="tooltip" 
                           data-bs-placement="bottom" 
                           title="@auth Kumpulan materi pembelajaran @else Kumpulan materi pembelajaran @endauth">
                            <i class="fas fa-book me-2"></i>
                            <span>Materi</span>
                            @guest
                                <small class="badge bg-warning text-dark ms-1">Terbatas</small>
                            @endguest
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mahasiswa.materials.questions.index') }}" 
                           class="nav-link {{ request()->routeIs('mahasiswa.materials.questions*') ? 'active' : '' }}"
                           data-bs-toggle="tooltip" 
                           data-bs-placement="bottom" 
                           title="@auth Latihan soal untuk menguji pemahaman pengguna @else Latihan soal untuk menguji pemahaman @endauth">
                            <i class="fas fa-clipboard-check me-2"></i>
                            <span>Latihan Soal</span>
                            @guest
                                <small class="badge bg-warning text-dark ms-1">Terbatas</small>
                            @endguest
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mahasiswa.leaderboard') }}" 
                           class="nav-link {{ request()->routeIs('mahasiswa.leaderboard*') ? 'active' : '' }}"
                           data-bs-toggle="tooltip" 
                           data-bs-placement="bottom" 
                           title="Papan peringkat pengguna berdasarkan skor">
                            <i class="fas fa-trophy me-2"></i>
                            <span>Peringkat</span>
                            @guest
                                <small class="badge bg-danger text-white ms-1">Perlu Login</small>
                            @endguest
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right side - Profile/Logout/Login/Register -->
        <div class="d-flex align-items-center">
            @guest
                <div class="auth-buttons me-3 d-none d-md-flex">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm me-2" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="bottom" 
                       title="Login untuk akses semua soal latihan tanpa batasan">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm"
                       data-bs-toggle="tooltip" 
                       data-bs-placement="bottom" 
                       title="Buat akun baru untuk akses semua soal latihan tanpa batasan">
                        <i class="fas fa-user-plus me-1"></i> Register
                    </a>
                </div>
                <!-- Tampilkan tombol kecil untuk login di mobile -->
                <div class="d-md-none">
                    <a href="{{ route('login') }}" class="btn btn-sm btn-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </a>
                </div>
            @endguest
            
            @auth
            <div class="dropdown profile-dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('images/profile.gif') }}" alt="Profile" class="profile-image me-1" width="30" height="30">
                    <span class="profile-name d-none d-sm-inline">
                        {{ auth()->user()->name }}
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            @endauth
        </div>
    </div>
</nav>

@if(request()->routeIs('mahasiswa.dashboard*'))
<div class="container-fluid px-4 pt-3">
    @guest
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Silakan login untuk mengakses semua fitur pembelajaran
        </div>
    @endguest
</div>
@endif
@push('scripts')
<script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>
<script>
    // Simpan URL route
    const routeLogin = "{{ route('login') }}";
    const routeRegister = "{{ route('register') }}";
    const routeDashboard = "{{ route('mahasiswa.dashboard') }}";
    const routeMateri = "{{ route('mahasiswa.materials.index') }}";
    const routeSoal = "{{ route('mahasiswa.materials.questions.index') }}";
    const routeLeaderboard = "{{ route('mahasiswa.leaderboard') }}";
    const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

    // Variabel untuk menandai klik sidebar
    let sidebarClicked = false;

    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi tooltip
        var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltips.map(function(el) {
            return new bootstrap.Tooltip(el);
        });

        // Only show tutorial for authenticated users on dashboard page and only once
        const isMainTutorialCompleted = sessionStorage.getItem('main_tutorial_complete');
        const isDashboardPage = {{ request()->routeIs('mahasiswa.dashboard*') ? 'true' : 'false' }};
        const isQuestionsPage = {{ request()->routeIs('mahasiswa.materials.questions*') ? 'true' : 'false' }};
        
        // Skip tutorial for guests and on question pages
        if (isLoggedIn && !isMainTutorialCompleted && isDashboardPage && !sidebarClicked && !sessionStorage.getItem('skip_tour')) {
            startTutorial();
        }
        
        // SOLUSI BARU: Pendekatan langsung untuk toggle sidebar
        // Dapatkan elemen-elemen yang diperlukan
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        const sidebar = document.querySelector('.sidebar');
        let sidebarBackdrop = document.querySelector('.sidebar-backdrop');
        
        // Periksa apakah backdrop sudah ada
        if (!sidebarBackdrop) {
            // Jika belum ada, buat elemen backdrop baru
            sidebarBackdrop = document.createElement('div');
            sidebarBackdrop.className = 'sidebar-backdrop';
            document.body.appendChild(sidebarBackdrop);
        }
        
        // Fungsi sederhana untuk toggle sidebar
        function toggleSidebar() {
            console.log('Toggle sidebar dipanggil'); // Logging untuk debugging
            sidebar.classList.toggle('show');
            sidebarBackdrop.classList.toggle('show');
        }
        
        // PENTING: Pasang event listener langsung dengan implementasi paling sederhana
        sidebarToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Tombol sidebar diklik'); // Logging untuk debugging
            toggleSidebar();
        });
        
        // Event listener untuk backdrop (untuk menutup sidebar saat klik di luar)
        sidebarBackdrop.addEventListener('click', function() {
            if (sidebar.classList.contains('show')) {
                toggleSidebar();
            }
        });
        
        // Event listener untuk tombol Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                toggleSidebar();
            }
        });
        
        // Tambahkan event listener untuk semua link di sidebar 
        // agar sidebar tertutup saat link diklik (pada tampilan mobile)
        document.querySelectorAll('.sidebar a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 991.98 && sidebar.classList.contains('show')) {
                    toggleSidebar();
                }
            });
        });
        
        // Juga tambahkan event listener untuk tombol tutup di sidebar
        const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
        if (sidebarCloseBtn) {
            sidebarCloseBtn.addEventListener('click', function() {
                if (sidebar.classList.contains('show')) {
                    toggleSidebar();
                }
            });
        }
    });

    // Fungsi untuk memulai tutorial
    function startTutorial() {
        let steps = [
            {
                intro: "Halo! Mari kita mulai dengan mengenal tampilan website ini."
            }
        ];

        if (!isLoggedIn) {
            steps.push(
                {
                    element: document.querySelector('a.btn[href="' + routeLogin + '"]'),
                    intro: "Klik tombol Login ini untuk masuk ke akun Anda"
                },
                {
                    element: document.querySelector('a.btn[href="' + routeRegister + '"]'),
                    intro: "Atau klik tombol Register untuk membuat akun baru"
                }
            );
        }

        // Add dashboard tutorial step only for logged in users
        if (isLoggedIn) {
            steps.push({
                element: document.querySelector('.nav-link[href="' + routeDashboard + '"]'),
                intro: "Ini adalah dashboard. Kamu bisa melihat ringkasan aktivitas di sini."
            });
        }
        
        // These steps are always shown
        steps.push(
            {
                element: document.querySelector('.nav-link[href="' + routeMateri + '"]'),
                intro: "Di sini kamu bisa belajar berbagai materi pembelajaran."
            },
            {
                element: document.querySelector('.nav-link[href="' + routeSoal + '"]'),
                intro: "Cek pemahamanmu di bagian latihan soal ini!"
            },
            {
                element: document.querySelector('.nav-link[href="' + routeLeaderboard + '"]'),
                intro: "Periksa peringkat dan capaian pengguna di leaderboard!"
            },
            {
                intro: "Siap menjelajah? Klik di mana saja untuk menyelesaikan tutorial ini!"
            }
        );

        introJs().setOptions({
            steps: steps,
            showProgress: true,
            exitOnOverlayClick: true,
            showBullets: false,
            scrollToElement: true,
            nextLabel: 'Berikutnya',
            prevLabel: 'Sebelumnya',
            doneLabel: 'Selesai'
        }).oncomplete(function() {
            // Mark main tutorial as completed
            sessionStorage.setItem('main_tutorial_complete', 'true');
        }).start();
    }

    // Tambahkan event listener untuk semua link di sidebar
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', function(event) {
            // Tandai bahwa ini klik dari sidebar
            sidebarClicked = true;
            
            // Untuk materi, biarkan lanjut tanpa tour
            sessionStorage.setItem('skip_tour', 'true');
            
            // Tutup sidebar otomatis di mobile setelah link diklik
            if (window.innerWidth <= 991.98) {
                const sidebar = document.querySelector('.sidebar');
                const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
                
                if (sidebar && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    if (sidebarBackdrop) {
                        sidebarBackdrop.classList.remove('show');
                    }
                }
            }
        });
    });
</script>
@endpush