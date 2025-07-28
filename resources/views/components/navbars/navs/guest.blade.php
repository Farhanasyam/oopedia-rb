@props(['signin', 'signup'])

<nav class="navbar navbar-expand-lg position-absolute top-0 z-index-3 w-100 shadow-none my-3 navbar-transparent">
    <div class="container">
        <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 text-white" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" alt="OOPedia" height="50" class="me-2 navbar-logo">
           
            <span class="logo-fallback">OOPedia</span>
        </a>
        <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon mt-2">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
            </span>
        </button>
        <div class="collapse navbar-collapse" id="navigation">
            <ul class="navbar-nav ms-auto">
               @guest
                <!-- <li class="nav-item">
                    <a class="nav-link nav-link-icon me-2" href="{{ route($signin) }}">
                        <i class="fas fa-key me-1"></i>
                        <span class="nav-link-inner--text">Login</span>
                    </a>
                </li> -->
                <!-- <li class="nav-item">
                    <a class="nav-link nav-link-icon me-2 bg-gradient-primary btn-navbar" href="{{ route($signup) }}">
                        <i class="fas fa-user-circle me-1"></i>
                        <span class="nav-link-inner--text">Register</span>
                    </a>
                </li> -->
                @endguest

                @auth
                    @if(Auth::check())
                        <li class="nav-item">
                            <a class="nav-link text-warning fw-bold" href="#">
                                Mode Tamu Aktif
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- Flash message jika login sebagai tamu --}}
<!-- @if(session('info')) -->
    <div class="container mt-5 pt-3">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            'hahahahaahaa'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<!-- @endif -->
 
<style>
    /* Navbar styling */
    .navbar {
        background-color: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.18);
    }
    
    /* Logo styling */
    .logo-container {
        display: flex;
        align-items: center;
        position: relative;
    }
    
    .logo-text {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #004e98, #0066cc);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -0.5px;
    }
    
    .logo-dot {
        width: 8px;
        height: 8px;
        background: linear-gradient(135deg, #004e98, #0066cc);
        border-radius: 50%;
        position: absolute;
        bottom: 0;
        right: -12px;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.5);
            opacity: 0.7;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    /* Navigation buttons */
    .nav-btn {
        color: #344767;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        margin: 0 0.3rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }
    
    .nav-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 0;
        height: 100%;
        background: rgba(0, 78, 152, 0.08);
        transition: all 0.3s ease;
        z-index: -1;
        border-radius: 8px;
    }
    
    .nav-btn:hover {
        color: #004e98;
        transform: translateY(-2px);
    }
    
    .nav-btn:hover::before {
        width: 100%;
    }
    
    .nav-btn-primary {
        background: linear-gradient(135deg, #004e98, #0066cc);
        color: white !important;
        box-shadow: 0 4px 10px rgba(0, 78, 152, 0.3);
    }
    
    .nav-btn-primary:hover {
        color: white !important;
        box-shadow: 0 6px 15px rgba(0, 78, 152, 0.4);
        transform: translateY(-2px);
    }
    
    .nav-btn-primary::before {
        background: rgba(255, 255, 255, 0.1);
    }
    
    /* Custom toggler */
    .custom-toggler {
        border: none;
        padding: 0.5rem;
        border-radius: 8px;
        background: transparent;
    }
    
    .custom-toggler:focus {
        box-shadow: none;
        outline: none;
    }
    
    .toggler-line {
        display: block;
        width: 25px;
        height: 3px;
        margin: 5px 0;
        background: linear-gradient(135deg, #004e98, #0066cc);
        border-radius: 3px;
        transition: all 0.3s ease;
    }
    
    .navbar-toggler[aria-expanded="true"] .toggler-line:nth-child(1) {
        transform: translateY(8px) rotate(45deg);
    }
    
    .navbar-toggler[aria-expanded="true"] .toggler-line:nth-child(2) {
        opacity: 0;
    }
    
    .navbar-toggler[aria-expanded="true"] .toggler-line:nth-child(3) {
        transform: translateY(-8px) rotate(-45deg);
    }
    
    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .navbar {
            padding: 0.5rem 1rem;
        }
        
        .navbar-collapse {
            background-color: white;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 0.5rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .nav-btn {
            margin: 0.3rem 0;
            display: block;
        }
    }
</style>
