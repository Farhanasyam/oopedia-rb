@extends('mahasiswa.layouts.app')

@section('title', 'UEQ Survey')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>User Experience Questionnaire (UEQ)</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any() || session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Perhatian!</h5>
                                    <p class="mb-0">
                                        Ada {{ count(session('missingFields', [])) ?: $errors->count() }} pertanyaan yang belum dijawab. Silakan isi semua pertanyaan.
                                    </p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <p class="mb-4">Silakan berikan penilaian Anda terhadap aplikasi pembelajaran OOPEDIA dengan memilih nilai pada skala berikut:</p>
                    
                    <form id="ueqForm" method="POST" action="{{ route('mahasiswa.ueq.store') }}">
                        @csrf
                        
                        <!-- Tambahkan bagian form identitas mahasiswa -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nim" class="form-label">NIM <span class="text-danger fw-bold">*</span></label>
                                    <input type="text" class="form-control @error('nim') is-invalid @enderror" 
                                        id="nim" name="nim" value="{{ old('nim') }}" required>
                                    @error('nim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Lengkap <span class="text-danger fw-bold">*</span></label>
                                    <input type="text" class="form-control bg-light"
                                        id="name" value="{{ auth()->check() ? auth()->user()->name : '' }}" readonly
                                        style="cursor: not-allowed; opacity: 0.7;">
                                    <small class="text-muted">Nama diambil dari data profil</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="class" class="form-label">Kelas <span class="text-danger fw-bold">*</span></label>
                                    <input type="text" class="form-control @error('class') is-invalid @enderror" 
                                        id="class" name="class" value="{{ old('class') }}" 
                                        placeholder="contoh: SIB2A" required>
                                    @error('class')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <!-- Header table tidak berubah -->
                                <thead>
                                    <tr>
                                        <th width="30%">Aspek</th>
                                        <th colspan="7" class="text-center">Penilaian <span class="text-danger fw-bold">*</span></th>
                                        <th width="30%">Aspek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Untuk masing-masing baris pertanyaan, tambahkan logika untuk memarkah pertanyaan yang belum dijawab -->
                                    @foreach([
                                        ['name' => 'annoying_enjoyable', 'left' => 'Menyebalkan', 'right' => 'Menyenangkan'],
                                        ['name' => 'not_understandable_understandable', 'left' => 'Tidak dapat dipahami', 'right' => 'Dapat dipahami'],
                                        ['name' => 'creative_dull', 'left' => 'Kreatif', 'right' => 'Monoton'],
                                        ['name' => 'easy_difficult', 'left' => 'Mudah', 'right' => 'Sulit'],
                                        ['name' => 'valuable_inferior', 'left' => 'Bermanfaat', 'right' => 'Kurang bermanfaat'],
                                        ['name' => 'boring_exciting', 'left' => 'Membosankan', 'right' => 'Menarik'],
                                        ['name' => 'not_interesting_interesting', 'left' => 'Tidak menarik', 'right' => 'Menarik'],
                                        ['name' => 'unpredictable_predictable', 'left' => 'Tidak dapat diprediksi', 'right' => 'Dapat diprediksi'],
                                        ['name' => 'fast_slow', 'left' => 'Cepat', 'right' => 'Lambat'],
                                        ['name' => 'inventive_conventional', 'left' => 'Inovatif', 'right' => 'Konvensional'],
                                        ['name' => 'obstructive_supportive', 'left' => 'Menghambat', 'right' => 'Mendukung'],
                                        ['name' => 'good_bad', 'left' => 'Baik', 'right' => 'Buruk'],
                                        ['name' => 'complicated_easy', 'left' => 'Rumit', 'right' => 'Sederhana'],
                                        ['name' => 'unlikable_pleasing', 'left' => 'Tidak disukai', 'right' => 'Menyenangkan'],
                                        ['name' => 'usual_leading_edge', 'left' => 'Biasa saja', 'right' => 'Terdepan'],
                                        ['name' => 'unpleasant_pleasant', 'left' => 'Tidak menyenangkan', 'right' => 'Menyenangkan'],
                                        ['name' => 'secure_not_secure', 'left' => 'Aman', 'right' => 'Tidak aman'],
                                        ['name' => 'motivating_demotivating', 'left' => 'Memotivasi', 'right' => 'Tidak memotivasi'],
                                        ['name' => 'meets_expectations_does_not_meet', 'left' => 'Memenuhi ekspektasi', 'right' => 'Tidak memenuhi ekspektasi'],
                                        ['name' => 'inefficient_efficient', 'left' => 'Tidak efisien', 'right' => 'Efisien'],
                                        ['name' => 'clear_confusing', 'left' => 'Jelas', 'right' => 'Membingungkan'],
                                        ['name' => 'impractical_practical', 'left' => 'Tidak praktis', 'right' => 'Praktis'],
                                        ['name' => 'organized_cluttered', 'left' => 'Terorganisir', 'right' => 'Berantakan'],
                                        ['name' => 'attractive_unattractive', 'left' => 'Menarik', 'right' => 'Tidak menarik'],
                                        ['name' => 'friendly_unfriendly', 'left' => 'Ramah', 'right' => 'Tidak ramah'],
                                        ['name' => 'conservative_innovative', 'left' => 'Konservatif', 'right' => 'Inovatif'],
                                    ] as $question)
                                    <tr class="ueq-row {{ in_array($question['name'], session('missingFields', [])) || $errors->has($question['name']) ? 'unanswered' : '' }}">
                                        <td class="aspect-left">{{ $question['left'] }}</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td class="text-center radio-cell">
                                                <div class="radio-wrapper">
                                                    <input type="radio" 
                                                        name="{{ $question['name'] }}" 
                                                        value="{{ $i }}" 
                                                        {{ old($question['name']) == $i ? 'checked' : '' }} 
                                                        required>
                                                    <label>{{ $i }}</label>
                                                </div>
                                            </td>
                                        @endfor
                                        <td class="aspect-right">{{ $question['right'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Bagian komentar dan saran -->
                        <div class="mb-3 mt-4">
                            <label for="comments" class="form-label">Komentar <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('comments') is-invalid @enderror" 
                                id="comments" 
                                name="comments" 
                                rows="3" 
                                required
                                placeholder="Tulis komentar Anda mengenai pengalaman menggunakan web ini...">{{ old('comments') }}</textarea>
                            @error('comments')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="suggestions" class="form-label">Saran <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('suggestions') is-invalid @enderror" 
                                id="suggestions" 
                                name="suggestions" 
                                rows="3" 
                                required
                                placeholder="Tulis saran Anda untuk pengembangan atau perbaikan web ini...">{{ old('suggestions') }}</textarea>
                            @error('suggestions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary" id="submitButton">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styling untuk alert kustom */
    .alert-danger {
        border-left: 4px solid #dc3545;
        background-color: #fff5f5;
    }
    
    /* Styling untuk pertanyaan yang belum dijawab */
    .unanswered {
        background-color: #fff3f3 !important;
        border-left: 4px solid #dc3545;
        animation: pulse-error 2s infinite;
    }
    
    @keyframes pulse-error {
        0% { background-color: #fff3f3; }
        50% { background-color: #ffe0e0; }
        100% { background-color: #fff3f3; }
    }
    
    /* Styling untuk radio cell */
    .radio-cell:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    
    /* Buat seluruh cell bisa diklik */
    .radio-wrapper {
        display: block;
        width: 100%;
        height: 100%;
        padding: 10px 0;
    }
    
    /* Wajib diisi */
    .form-label:after, th .text-danger {
        content: " *";
        color: #dc3545;
        font-weight: bold;
    }

    /* Memastikan tombol logout selalu menampilkan teks "Logout" */
    #logout-button::after,
    #logout-button.dropdown-item::after,
    button.logout-button::after {
        content: "Logout" !important;
        position: absolute;
        display: none;
    }
    
    #logout-button,
    button.logout-button {
        position: relative;
    }
    
    #logout-button span,
    button.logout-button span {
        position: relative;
        z-index: 2;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const form = document.getElementById('ueqForm');
    const submitButton = document.querySelector('button[type="submit"]');
    const rows = document.querySelectorAll('tr.ueq-row');
    
    // Save answers to localStorage
    function saveAnswers() {
        const answers = {};
        rows.forEach(function(row) {
            const radios = row.querySelectorAll('input[type="radio"]');
            radios.forEach(function(radio) {
                if (radio.checked) {
                    answers[radio.name] = radio.value;
                }
            });
        });
        localStorage.setItem('ueq_survey_answers', JSON.stringify(answers));
    }
    
    // Load answers from localStorage
    function loadAnswers() {
        const savedAnswers = localStorage.getItem('ueq_survey_answers');
        if (savedAnswers) {
            try {
                const answers = JSON.parse(savedAnswers);
                Object.keys(answers).forEach(function(name) {
                    const value = answers[name];
                    const radio = document.querySelector(`input[name="${name}"][value="${value}"]`);
                    if (radio) {
                        radio.checked = true;
                    }
                });
            } catch (e) {
                console.error('Error loading saved answers:', e);
            }
        }
    }
    
    // Check for unanswered questions
    function checkUnansweredQuestions() {
        const unanswered = [];
        
        rows.forEach(function(row, index) {
            const name = row.querySelector('input[type="radio"]')?.name;
            if (!name) return;
            
            const radios = row.querySelectorAll('input[type="radio"]');
            let anyChecked = false;
            
            radios.forEach(function(radio) {
                if (radio.checked) {
                    anyChecked = true;
                }
            });
            
            if (!anyChecked) {
                unanswered.push(index);
                row.classList.add('unanswered');
            } else {
                row.classList.remove('unanswered');
            }
        });
        
        // Show warning if there are unanswered questions
        if (unanswered.length > 0) {
            if (!document.getElementById('unansweredWarning')) {
                const warning = document.createElement('div');
                warning.id = 'unansweredWarning';
                warning.className = 'alert alert-danger sticky-top mt-2 mb-3';
                warning.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <strong>Perhatian!</strong>
                            <p class="mb-0">Ada ${unanswered.length} pertanyaan yang belum dijawab. Silakan isi semua pertanyaan.</p>
                        </div>
                        <div class="ms-auto">
                            <button id="btnScrollToNext" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-arrow-down me-1"></i> Lihat Pertanyaan
                            </button>
                        </div>
                    </div>
                `;
                const formElement = document.getElementById('ueqForm');
                formElement.insertAdjacentElement('beforebegin', warning);
                
                // Add event listener to scroll to next unanswered question
                document.getElementById('btnScrollToNext').addEventListener('click', function() {
                    if (unanswered.length > 0) {
                        scrollToRow(unanswered[0]);
                    }
                });
            } else {
                document.getElementById('unansweredWarning').querySelector('p').textContent = 
                    `Ada ${unanswered.length} pertanyaan yang belum dijawab. Silakan isi semua pertanyaan.`;
            }
        } else {
            const warning = document.getElementById('unansweredWarning');
            if (warning) {
                warning.remove();
            }
        }
        
        return unanswered;
    }
    
    // Update submit button status
    function updateSubmitButtonStatus() {
        const unanswered = checkUnansweredQuestions();
        if (unanswered.length > 0) {
            submitButton.disabled = true;
            submitButton.textContent = `Masih ada ${unanswered.length} pertanyaan belum dijawab`;
        } else {
            submitButton.disabled = false;
            submitButton.textContent = 'Kirim';
        }
    }
    
    // Scroll to specific row with animation
    function scrollToRow(rowIndex) {
        if (rowIndex >= 0 && rowIndex < rows.length) {
            const row = rows[rowIndex];
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            row.classList.add('flash-highlight');
            setTimeout(() => {
                row.classList.remove('flash-highlight');
            }, 2000);
        }
    }
    
    // Helper function to find row index by field name
    function findRowIndexByFieldName(fieldName) {
        for (let i = 0; i < rows.length; i++) {
            const radio = rows[i].querySelector(`input[name="${fieldName}"]`);
            if (radio) {
                return i;
            }
        }
        return -1;
    }
    
    // Add event listeners to all radio buttons and cells
    rows.forEach(function(row) {
        const radios = row.querySelectorAll('input[type="radio"]');
        radios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                row.classList.remove('unanswered');
                checkUnansweredQuestions();
                updateSubmitButtonStatus();
                saveAnswers(); // Save changes immediately
            });
        });
        
        // Make entire cell clickable
        const cells = row.querySelectorAll('.radio-cell');
        cells.forEach(function(cell, index) {
            cell.addEventListener('click', function(e) {
                // Prevent clicking if already clicking on the radio button itself
                if (e.target.tagName !== 'INPUT') {
                    const radio = cell.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        // Trigger change event
                        const event = new Event('change');
                        radio.dispatchEvent(event);
                    }
                }
            });
        });
    });
    
    // Handle form submission
    form.addEventListener('submit', function(e) {
        const unanswered = checkUnansweredQuestions();
        if (unanswered.length > 0) {
            e.preventDefault();
            scrollToRow(unanswered[0]);
            return false;
        }
        
        // Optional: Clear localStorage after successful submission
        // localStorage.removeItem('ueq_survey_answers');
        return true;
    });
    
    // Add CSS for flash highlight animation
    const style = document.createElement('style');
    style.textContent = `
        .flash-highlight {
            animation: flash 0.5s 3;
        }
        
        @keyframes flash {
            0%, 100% { background-color: inherit; }
            50% { background-color: #ffe066; }
        }
    `;
    document.head.appendChild(style);
    
    // Initialize on page load
    loadAnswers();
    checkUnansweredQuestions();
    updateSubmitButtonStatus();
    
    // Scroll to first error if any
    @if($errors->any() || session('missingFields'))
        setTimeout(function() {
            const firstUnanswered = document.querySelector('.unanswered');
            if (firstUnanswered) {
                firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstUnanswered.classList.add('flash-highlight');
                setTimeout(() => {
                    firstUnanswered.classList.remove('flash-highlight');
                }, 2000);
            }
        }, 500);
    @endif

    // Memastikan teks tombol logout tidak berubah
    const logoutButton = document.getElementById('logout-button');
    if (logoutButton) {
        logoutButton.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i> Logout';
    }
    
    // Mencegah form UEQ memengaruhi tombol logout
    if (form) {
        form.addEventListener('submit', function(e) {
            const logoutButton = document.getElementById('logout-button');
            if (logoutButton) {
                logoutButton.innerHTML = '<i class="fas fa-sign-out-alt mr-2"></i> Logout';
            }
        });
    }
});
</script>
@endpush