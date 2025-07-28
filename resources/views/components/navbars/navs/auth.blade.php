@props(['titlePage'])

<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">{{ $titlePage }}</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">{{ $titlePage }}</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item d-flex align-items-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link px-3">
                            <i class="material-icons opacity-10">logout</i>
                            <span class="nav-link-text ms-1">Logout</span>
                        </button>
                    </form>
                </li>
                <li class="nav-item px-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" onclick="resetAllTutorials()">
                        <i class="fa fa-redo me-sm-1"></i>
                        <span class="d-sm-inline d-none">Reset Tutorial</span>
                    </a>
                </li>
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
function resetAllTutorials() {
    // Hapus semua tutorial keys dari localStorage
    for (let key in localStorage) {
        if (key.includes('tutorial_complete') || key === 'skip_admin_tour') {
            localStorage.removeItem(key);
        }
    }
    
    Swal.fire({
        title: 'Tutorial Direset',
        text: 'Tutorial akan dimulai ulang',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then(() => {
        // Langsung jalankan tutorial setelah reset
        const currentPage = '{{ request()->route()->getName() }}';
        startAdminTutorial(); // Menggunakan fungsi yang sudah ada di tutorial.blade.php
    });
}
</script>
