<x-layout bodyClass="g-sidenav-show bg-gray-200">
    @push('head')
        <x-head.tinymce-config />
    @endpush

    <x-navbars.sidebar activePage="questions" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Tambah Soal" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <br><br>

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Tambah Soal Baru</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            @if (isset($material))
                                <form method="POST" action="{{ route('admin.materials.questions.store', $material) }}"
                                    class="p-4" id="questionForm">
                                @else
                                    <form method="POST" action="{{ route('admin.questions.store') }}" class="p-4"
                                        id="questionForm">
                            @endif
                            @csrf

                            @if ($errors->any())
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}<br>
                                    @endforeach
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('warning'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    {{ session('warning') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Material</label>
                                        <div class="input-group input-group-outline">
                                            @if (isset($material))
                                                <input type="hidden" name="material_id" value="{{ $material->id }}">
                                                <input type="text" class="form-control"
                                                    value="{{ $material->title }}" disabled>
                                            @else
                                                <select name="material_id" id="material_id" class="form-control"
                                                    required>
                                                    <option value="">Pilih Material</option>
                                                    @foreach ($materials as $material)
                                                        <option value="{{ $material->id }}">{{ $material->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Pertanyaan</label>
                                        <div class="my-3">
                                            <textarea id="content-editor" name="question_text">{{ old('question_text') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Tipe Soal</label>
                                        <div class="input-group input-group-outline">
                                            <select name="question_type" class="form-control" required>
                                                <option value="fill_in_the_blank">Fill in the Blank</option>
                                                <option value="radio_button">Radio Button</option>
                                                <option value="drag_and_drop">Drag and Drop</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Tingkat Kesulitan</label>
                                        <div class="input-group input-group-outline">
                                            <select name="difficulty" class="form-control" required>
                                                <option value="beginner">Beginner</option>
                                                <option value="medium">Medium</option>
                                                <option value="hard">Hard</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="answers-container">
                                <h6 class="mb-3">Jawaban</h6>
                                <div class="answer-entry mb-3">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="input-group input-group-outline">
                                                <input type="text" name="answers[0][answer_text]"
                                                    class="form-control" placeholder="Jawaban" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="correct_answer"
                                                    value="0">
                                                <label class="form-check-label">Jawaban Benar</label>
                                                <input type="hidden" name="answers[0][is_correct]" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addAnswer()">
                                Tambah Jawaban
                            </button>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">Simpan Soal</button>
                                    @if (isset($material))
                                        <a href="{{ route('admin.materials.questions.index', $material) }}"
                                            class="btn btn-outline-secondary">Batal</a>
                                    @else
                                        <a href="{{ route('admin.questions.index') }}"
                                            class="btn btn-outline-secondary">Batal</a>
                                    @endif
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('js')
        <script>
            let answerCount = 1;

            function handleQuestionTypeChange() {
                const questionType = document.querySelector('[name="question_type"]').value;
                const answerContainer = document.getElementById('answers-container');
                const addAnswerBtn = document.getElementById('add-answer-btn');

                // Reset container
                while (answerContainer.firstChild) {
                    answerContainer.removeChild(answerContainer.firstChild);
                }

                // Add initial answers based on question type
                if (questionType === 'fill_in_the_blank') {
                    addAnswer(); // Only add one answer for fill in the blank
                    // Optionally hide the add answer button for fill in the blank
                    if (addAnswerBtn) {
                        addAnswerBtn.style.display = 'none';
                    }
                } else {
                    // For other question types, add two answers and show the add button
                    addAnswer();
                    addAnswer();
                    if (addAnswerBtn) {
                        addAnswerBtn.style.display = 'block';
                    }
                }

                // Update UI based on question type
                updateAnswerUI(questionType);
            }

            function addAnswer() {
                const container = document.getElementById('answers-container');
                const answerCount = container.getElementsByClassName('answer-entry').length;

                const newAnswer = document.createElement('div');
                newAnswer.className = 'answer-entry mb-3';
                newAnswer.innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-group input-group-outline">
                            <input type="text" name="answers[${answerCount}][answer_text]" class="form-control" placeholder="Jawaban" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="correct_answer" value="${answerCount}">
                            <label class="form-check-label">Jawaban Benar</label>
                            <input type="hidden" name="answers[${answerCount}][is_correct]" value="0">
                        </div>
                    </div>
                </div>
            `;

                container.appendChild(newAnswer);
            }

            function updateAnswerUI(questionType) {
                const answerEntries = document.querySelectorAll('.answer-entry');

                answerEntries.forEach((entry, index) => {
                    const radioInput = entry.querySelector('input[type="radio"]');
                    const isCorrectInput = entry.querySelector('input[name$="[is_correct]"]');

                    if (questionType === 'fill_in_the_blank' && index === 0) {
                        // For fill in the blank, automatically set the first answer as correct
                        if (radioInput) radioInput.checked = true;
                        if (isCorrectInput) isCorrectInput.value = '1';
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                const questionTypeSelect = document.querySelector('[name="question_type"]');
                const form = document.getElementById('questionForm');

                // Event listener untuk perubahan tipe soal
                questionTypeSelect.addEventListener('change', handleQuestionTypeChange);

                // Event listener untuk perubahan jawaban benar
                document.addEventListener('change', function(e) {
                    if (e.target.type === 'radio' && e.target.name === 'correct_answer') {
                        const container = document.getElementById('answers-container');
                        const answers = container.getElementsByClassName('answer-entry');

                        Array.from(answers).forEach((answer, index) => {
                            const hiddenInput = answer.querySelector('input[name$="[is_correct]"]');
                            if (hiddenInput) {
                                hiddenInput.value = (index.toString() === e.target.value) ? '1' : '0';
                            }
                        });
                    }
                });

                // Validasi form sebelum submit
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Ambil nilai dari TinyMCE
                    const questionText = tinymce.get('content-editor').getContent();

                    if (!questionText) {
                        alert('Pertanyaan tidak boleh kosong!');
                        return;
                    }

                    const questionType = questionTypeSelect.value;
                    if (questionType === 'radio_button') {
                        const selectedRadio = document.querySelector('input[name="correct_answer"]:checked');
                        if (!selectedRadio) {
                            alert('Pilih satu jawaban yang benar untuk tipe soal Radio Button');
                            return;
                        }

                        const correctAnswers = document.querySelectorAll(
                            'input[name$="[is_correct]"][value="1"]');
                        if (correctAnswers.length !== 1) {
                            alert('Harus ada tepat satu jawaban yang benar untuk tipe soal Radio Button');
                            return;
                        }
                    }

                    // Jika semua validasi passed, submit form
                    this.submit();
                });

                // Disable tombol submit setelah diklik untuk mencegah double submit
                document.getElementById('submitBtn').addEventListener('click', function() {
                    setTimeout(() => {
                        this.disabled = true;
                        this.innerHTML =
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
                    }, 0);
                });

                // Inisialisasi awal
                handleQuestionTypeChange();
            });
        </script>
    @endpush
    <x-admin.tutorial />
</x-layout>