<x-layout bodyClass="bg-gray-200">
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                <!-- Navbar -->
                <x-navbars.navs.guest signin='login' signup='register'></x-navbars.navs.guest>
                <!-- End Navbar -->
            </div>
        </div>
    </div>

    @if(session('error')) <!-- Modal hanya muncul jika ada error -->
    <div class="modal fade show" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true" style="display: block;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">Error 422</h5>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ asset('images/error-422.png') }}" alt="Error 422" class="img-fluid mb-3">
                    <h2>Unprocessable Entity</h2>
                    <p>Oops! The request was well-formed but couldn't be processed.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="location.reload();">Coba Lagi</button>
                    <a href="{{ url('/') }}" class="btn btn-primary">Go Home</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Backdrop untuk modal -->
    <div class="modal-backdrop fade show"></div>
    @endif

    <x-footers.guest></x-footers.guest>

    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Jika modal ada, atur backdrop agar tampil
            if (document.getElementById('errorModal')) {
                document.body.classList.add('modal-open');
            }
        });
    </script>
    @endpush
</x-layout>
