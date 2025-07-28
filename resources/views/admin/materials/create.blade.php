<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <!-- Add TinyMCE configuration component in the head section -->
    @push('head')
        <x-head.tinymce-config />
    @endpush

    <x-navbars.sidebar activePage="materials" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Tambah Materi" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                    <br><br>

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Tambah Materi Baru</h6>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-2">
                            <form method="POST" action="{{ route('admin.materials.store') }}" id="materialForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="created_by" value="{{ auth()->id() }}">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Judul Materi</label>
                                            <div class="input-group input-group-outline">
                                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" required value="{{ old('title') }}">
                                            </div>
                                            @error('title')
                                                <div class="text-danger text-xs">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Isi Materi</label>
                                            <div class="my-3">
                                                <textarea id="content-editor" name="content" class="@error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                                            </div>
                                            @error('content')
                                                <div class="text-danger text-xs">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Gambar Cover (Untuk Tampilan Card Mahasiswa)</label>
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Rekomendasi Ukuran Gambar:</strong>
                                                <ul class="mb-0 mt-1">
                                                    <li><b>Rasio Aspek:</b> 16:9 (widescreen) atau 4:3 (standar)</li>
                                                    <li><b>Ukuran Optimal:</b> 1280×720px (16:9) atau 1024×768px (4:3)</li>
                                                    <li><b>Ukuran Minimum:</b> 640×360px (16:9) atau 800×600px (4:3)</li>
                                                    <li><b>Format:</b> JPG, PNG, GIF (maks 2MB)</li>
                                                </ul>
                                                <div class="mt-2">Gambar akan tampil penuh pada card materi dan question tanpa terpotong.</div>
                                            </div>
                                            <div class="input-group input-group-outline">
                                                <input type="file" name="cover_image" class="form-control" accept="image/*" 
                                                       onchange="previewImage(this, 'imagePreview')">
                                            </div>
                                            <div id="imagePreview" class="mt-3 text-center d-none">
                                                <p class="text-muted mb-1">Preview:</p>
                                                <img src="" class="img-thumbnail" style="max-height: 200px; max-width: 100%;">
                                            </div>
                                            @error('cover_image')
                                                <div class="text-danger text-xs">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn bg-gradient-primary">Simpan</button>
                                <a href="{{ route('admin.materials.index') }}" class="btn btn-outline-secondary">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-admin.tutorial />
</x-layout>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const previewImg = preview.querySelector('img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('d-none');
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewImg.src = '';
        preview.classList.add('d-none');
    }
}
</script>
