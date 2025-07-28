@extends('mahasiswa.layouts.app')

@section('title', 'Terima Kasih - UEQ Survey')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="ueq-thankyou-card">
                <div class="ueq-thankyou-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="ueq-thankyou-title">Terima Kasih!</h2>
                <p class="ueq-thankyou-message">
                    Kami sangat menghargai waktu dan masukan yang Anda berikan melalui survey UEQ ini. 
                    Feedback Anda sangat berharga untuk pengembangan aplikasi OOPEDIA ke depannya.
                </p>
                <div class="ueq-thankyou-decoration">
                    <div class="ueq-decoration-item"></div>
                    <div class="ueq-decoration-item"></div>
                    <div class="ueq-decoration-item"></div>
                </div>
                <div class="ueq-thankyou-actions">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 