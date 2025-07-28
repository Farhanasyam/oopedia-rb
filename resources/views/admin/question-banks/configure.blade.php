<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="question-banks" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Konfigurasi Bank Soal" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                    <br><br>
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3">Konfigurasi Bank Soal: {{ $questionBank->name }}</h6>
                                <a href="{{ route('admin.question-banks.show', $questionBank) }}" class="btn btn-sm btn-light me-3">
                                    <i class="material-icons text-sm">arrow_back</i>
                                    <span>Kembali</span>
                                </a>
                            </div>
                        </div>
                        
                        <div class="card-body pt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Form untuk menambah atau mengedit konfigurasi -->
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">{{ isset($editConfig) ? 'Edit Konfigurasi' : 'Tambah Konfigurasi Baru' }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.question-banks.store-config', $questionBank) }}" method="POST">
                                                @csrf
                                                
                                                @if(isset($editConfig))
                                                    <input type="hidden" name="config_id" value="{{ $editConfig->id }}">
                                                @endif
                                                
                                                <div class="mb-3">
                                                    <label for="material_id" class="form-label">Materi</label>
                                                    <div class="input-group input-group-outline @if(!isset($editConfig)) focused is-focused @endif">
                                                        <select name="material_id" id="material_id" class="form-control" {{ isset($editConfig) ? 'disabled' : 'required' }}>
                                                            <option value="">-- Pilih Materi --</option>
                                                            @foreach($materials as $material)
                                                                <option value="{{ $material->id }}" 
                                                                    {{ (isset($editConfig) && $editConfig->material_id == $material->id) || old('material_id') == $material->id ? 'selected' : '' }}>
                                                                    {{ $material->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @if(isset($editConfig))
                                                        <input type="hidden" name="material_id" value="{{ $editConfig->material_id }}">
                                                    @endif
                                                    @error('material_id')
                                                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <label for="beginner_count" class="form-label">Jumlah Soal Beginner</label>
                                                        <div class="input-group input-group-outline focused is-focused">
                                                            <input type="number" min="0" name="beginner_count" id="beginner_count" class="form-control" 
                                                                value="{{ old('beginner_count', isset($editConfig) ? $editConfig->beginner_count : 0) }}" required>
                                                        </div>
                                                        @error('beginner_count')
                                                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="medium_count" class="form-label">Jumlah Soal Medium</label>
                                                        <div class="input-group input-group-outline focused is-focused">
                                                            <input type="number" min="0" name="medium_count" id="medium_count" class="form-control" 
                                                                value="{{ old('medium_count', isset($editConfig) ? $editConfig->medium_count : 0) }}" required>
                                                        </div>
                                                        @error('medium_count')
                                                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="hard_count" class="form-label">Jumlah Soal Hard</label>
                                                        <div class="input-group input-group-outline focused is-focused">
                                                            <input type="number" min="0" name="hard_count" id="hard_count" class="form-control" 
                                                                value="{{ old('hard_count', isset($editConfig) ? $editConfig->hard_count : 0) }}" required>
                                                        </div>
                                                        @error('hard_count')
                                                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3 form-check">
                                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                                        {{ (isset($editConfig) && $editConfig->is_active) || old('is_active') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_active">Aktifkan Konfigurasi</label>
                                                </div>
                                                
                                                <div id="totalQuestions" class="alert alert-info">
                                                    Total: <span id="totalCount">{{ old('beginner_count', isset($editConfig) ? $editConfig->beginner_count : 0) + old('medium_count', isset($editConfig) ? $editConfig->medium_count : 0) + old('hard_count', isset($editConfig) ? $editConfig->hard_count : 0) }}</span> soal
                                                </div>
                                                
                                                <div class="mb-0">
                                                    <button type="submit" class="btn btn-primary">
                                                        {{ isset($editConfig) ? 'Perbarui Konfigurasi' : 'Tambah Konfigurasi' }}
                                                    </button>
                                                    <a href="{{ route('admin.question-banks.configure', $questionBank) }}" class="btn btn-outline-secondary">Batal</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <!-- Daftar konfigurasi yang sudah ada -->
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Konfigurasi yang Ada</h5>
                                        </div>
                                        <div class="card-body">
                                            @if($configs->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Materi</th>
                                                                <th>Beginner</th>
                                                                <th>Medium</th>
                                                                <th>Hard</th>
                                                                <th>Status</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($configs as $config)
                                                                <tr>
                                                                    <td>{{ $config->material ? $config->material->title : 'Tidak ada' }}</td>
                                                                    <td>{{ $config->beginner_count }}</td>
                                                                    <td>{{ $config->medium_count }}</td>
                                                                    <td>{{ $config->hard_count }}</td>
                                                                    <td>
                                                                        <span class="badge bg-{{ $config->is_active ? 'success' : 'danger' }}">
                                                                            {{ $config->is_active ? 'Aktif' : 'Nonaktif' }}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <a href="{{ route('admin.question-banks.configure', ['questionBank' => $questionBank, 'edit' => $config->id]) }}" class="btn btn-sm btn-info mb-1">
                                                                            <i class="material-icons text-sm">edit</i>
                                                                        </a>
                                                                        <form action="{{ route('admin.question-bank-configs.delete', $config) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Apakah Anda yakin ingin menghapus konfigurasi ini?')">
                                                                                <i class="material-icons text-sm">delete</i>
                                                                            </button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="alert alert-info">
                                                    Belum ada konfigurasi untuk bank soal ini.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to calculate total questions
        function calculateTotal() {
            const beginnerCount = parseInt(document.getElementById('beginner_count').value) || 0;
            const mediumCount = parseInt(document.getElementById('medium_count').value) || 0;
            const hardCount = parseInt(document.getElementById('hard_count').value) || 0;
            
            const total = beginnerCount + mediumCount + hardCount;
            document.getElementById('totalCount').textContent = total;
            
            // Visual feedback
            const totalEl = document.getElementById('totalQuestions');
            if (total <= 0) {
                totalEl.classList.remove('alert-info');
                totalEl.classList.add('alert-danger');
            } else {
                totalEl.classList.remove('alert-danger');
                totalEl.classList.add('alert-info');
            }
        }
        
        // Add event listeners to all count inputs
        document.getElementById('beginner_count').addEventListener('input', calculateTotal);
        document.getElementById('medium_count').addEventListener('input', calculateTotal);
        document.getElementById('hard_count').addEventListener('input', calculateTotal);
        
        // Calculate on page load
        calculateTotal();
        
        // Handle edit mode material selection
        const materialSelect = document.getElementById('material_id');
        if (materialSelect && materialSelect.disabled) {
            // Add text showing the selected material name
            const selectedOption = materialSelect.options[materialSelect.selectedIndex];
            const materialName = selectedOption.textContent;
            const materialInfo = document.createElement('div');
            materialInfo.className = 'form-text text-info mt-1';
            materialInfo.textContent = 'Materi terpilih: ' + materialName;
            materialSelect.parentNode.appendChild(materialInfo);
        }
    });
</script>
@endpush