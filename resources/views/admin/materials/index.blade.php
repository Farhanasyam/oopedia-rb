<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="materials" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Materi" />
        <div class="container-fluid py-4">
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.materials.index') }}" class="mb-3">
                <div class="input-group input-group-outline my-3">
                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}" style="height: 50px;">
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
                                <h6 class="text-white text-capitalize ps-3">Daftar Materi</h6>
                                <a href="{{ route('admin.materials.create') }}" class="btn btn-sm btn-light me-3">
                                    <i class="material-icons text-sm">add</i> Tambah Materi
                                </a>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Materi</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cover Materi</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dibuat Oleh</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($materials as $material)
                                        <tr>
                                            
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $material->title }}</h6>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ Str::limit(strip_tags($material->content), 50) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($material->media && $material->media->isNotEmpty())
                                                    <div class="material-thumbnail-container">
                                                        <img src="{{ asset($material->media->first()->media_url) }}" 
                                                             alt="{{ $material->title }}" 
                                                             class="material-cover-thumbnail">
                                                    </div>
                                                @else
                                                    <div class="no-image-placeholder">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                        <p class="text-xs font-weight-bold mb-0">
                                                            {{ $material->creator ? $material->creator->name : 'Admin' }}
                                                        </p>
                                                    </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">
                                                    {{ $material->created_at->format('d M Y') }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('admin.materials.edit', $material->id) }}" class="btn btn-sm btn-info">
                                                    <i class="material-icons text-sm">edit</i>
                                                </a>
                                                <form action="{{ route('admin.materials.destroy', $material->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                                        <i class="material-icons text-sm">delete</i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />

</x-layout>

@push('js')
@endpush
<style>
    /* Perbaikan ukuran gambar di daftar materi admin */
    .material-cover-thumbnail {
        width: 120px;
        height: 80px;
        object-fit: cover;
        border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid #e0e6ed;
    }
    
    /* Untuk container gambar */
    .material-thumbnail-container {
        width: 120px;
        height: 80px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 6px;
    }
    
    /* Untuk placeholder jika tidak ada gambar */
    .no-image-placeholder {
        width: 120px;
        height: 80px;
        background-color: #f0f7ff;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0057B8;
        font-size: 24px;
        border: 1px solid #e0e6ed;
    }
</style>
