<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="students" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Progress Mahasiswa" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                    <br><br>
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3">Progress Mahasiswa: {{ $student->name }}</h6>
                                <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-light me-3">
                                    <span class="btn-inner--icon"><i class="material-icons">arrow_back</i></span>
                                    <span class="btn-inner--text">Kembali</span>
                                </a>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Materi</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Progress</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Terakhir Dikerjakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($materials as $material)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $material->title }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="height: 8px; width: 200px;">
                                                        <div class="progress-bar bg-gradient-success"
                                                             role="progressbar"
                                                             style="width: {{ is_numeric($material->progress) ? $material->progress : 0 }}%;"
                                                             aria-valuenow="{{ is_numeric($material->progress) ? $material->progress : 0 }}"
                                                             aria-valuemin="0"
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="text-xs font-weight-bold">{{ is_numeric($material->progress) ? $material->progress : 0 }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if(is_numeric($material->progress) && $material->progress == 100)
                                                    <span class="badge bg-gradient-success">Selesai</span>
                                                @elseif(is_numeric($material->progress) && $material->progress > 0)
                                                    <span class="badge bg-gradient-warning">Sedang Dikerjakan</span>
                                                @else
                                                    <span class="badge bg-gradient-secondary">Belum Dimulai</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-xs font-weight-bold">
                                                    {{ $material->last_accessed ? $material->last_accessed->format('d M Y H:i') : '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <p class="text-sm mb-0">Belum ada data materi</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(count($missingQuestionsByMaterial) > 0)
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <br><br>
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-warning shadow-warning border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Soal yang Belum Dijawab</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Materi</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Jumlah Soal Belum Dijawab Benar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($missingQuestionsByMaterial as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $item['material_title'] }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-gradient-danger">{{ $item['missing_count'] }} soal</span>
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
        @endif
    </main>
    <x-admin.tutorial />

</x-layout>

@push('js')
@endpush