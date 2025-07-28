@extends('mahasiswa.layouts.app')

@section('title', 'Profil Mahasiswa')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <!-- Header background dengan gambar -->
    <div class="page-header min-height-300 border-radius-xl mt-4"
        style="background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');">
        <span class="mask bg-gradient-primary opacity-6"></span>
    </div>

    <div class="card card-body mx-3 mx-md-4 mt-n6">
        <!-- Profile info section -->
        <div class="row gx-4 mb-2">
            <div class="col-auto">
                <div class="avatar avatar-xl position-relative">
                    <img src="{{ asset('images/accountinfo.gif') }}" alt="Profile Avatar"
                        class="w-100 border-radius-lg shadow-sm">
                </div>
            </div>
            <div class="col-auto my-auto">
                <div class="h-100">
                    <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                    <p class="mb-0 font-weight-normal text-sm">Mahasiswa</p>
                </div>
            </div>
        </div>

        <!-- Alert section -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('mahasiswa.profile.update') }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Nama</label>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       name="name" 
                       value="{{ old('name', auth()->user()->name) }}"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       name="email" 
                       value="{{ old('email', auth()->user()->email) }}"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="password-section">
                <h5 class="mb-4">Ubah Password</h5>
                
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           name="password">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" 
                           class="form-control" 
                           name="password_confirmation">
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn-update">
                    <i class="fas fa-save me-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection