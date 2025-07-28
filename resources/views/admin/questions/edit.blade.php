<x-layout bodyClass="g-sidenav-show bg-gray-200">
    @push('head')
        <x-head.tinymce-config />
    @endpush

    <x-navbars.sidebar activePage="questions" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Edit Soal" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                    <br><br>

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Edit Soal</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <form method="POST" action="{{ $material 
                                ? route('admin.materials.questions.update', ['material' => $material, 'question' => $question]) 
                                : route('admin.questions.update', $question) }}" class="p-4" id="questionForm">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Material</label>
                                            <div class="input-group input-group-outline">
                                                @if(isset($material))
                                                    <input type="hidden" name="material_id" value="{{ $material->id }}">
                                                    <input type="text" class="form-control" value="{{ $material->title }}" disabled>
                                                @else
                                                    <select name="material_id" id="material_id" class="form-control" required>
                                                        <option value="">Pilih Material</option>
                                                        @foreach($materials as $mat)
                                                            <option value="{{ $mat->id }}" {{ $question->material_id == $mat->id ? 'selected' : '' }}>
                                                                {{ $mat->title }}
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
                                                <textarea id="content-editor" name="question_text">{{ $question->question_text }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Tipe Soal</label>
                                            <div class="input-group input-group-outline">
                                                <select name="question_type" class="form-control" required>
                                                    <option value="radio_button" {{ $question->question_type == 'radio_button' ? 'selected' : '' }}>Radio Button</option>
                                                    <option value="drag_and_drop" {{ $question->question_type == 'drag_and_drop' ? 'selected' : '' }}>Drag and Drop</option>
                                                    <option value="fill_in_the_blank" {{ $question->question_type == 'fill_in_the_blank' ? 'selected' : '' }}>Fill in the Blank</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Tingkat Kesulitan</label>
                                            <div class="input-group input-group-outline">
                                                <select name="difficulty" class="form-control" required>
                                                    <option value="beginner" {{ $question->difficulty == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                                    <option value="medium" {{ $question->difficulty == 'medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="hard" {{ $question->difficulty == 'hard' ? 'selected' : '' }}>Hard</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="answers-container">
                                    <h6 class="mb-3">Jawaban</h6>
                                    @foreach($question->answers as $index => $answer)
                                        <div class="answer-entry mb-3" data-correct="{{ $answer->is_correct ? '1' : '0' }}">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="input-group input-group-outline">
                                                        <input type="text" name="answers[{{ $index }}][answer_text]" class="form-control" placeholder="Jawaban" required value="{{ $answer->answer_text }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        @if($question->question_type === 'radio_button' || $question->question_type === 'fill_in_the_blank')
                                                            <input class="form-check-input correct-radio" type="radio" name="correct_answer" value="{{ $index }}" {{ $answer->is_correct ? 'checked' : '' }}>
                                                            <label class="form-check-label">Jawaban Benar</label>
                                                            <input type="hidden" name="answers[{{ $index }}][is_correct]" value="{{ $answer->is_correct ? '1' : '0' }}">
                                                        @elseif($question->question_type === 'drag_and_drop')
                                                            <input class="form-check-input" type="checkbox" name="answers[{{ $index }}][is_correct]" value="1" {{ $answer->is_correct ? 'checked' : '' }}>
                                                            <label class="form-check-label">Jawaban Benar</label>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" id="add-answer-btn" class="btn btn-outline-primary btn-sm mb-3" onclick="addAnswer()">
                                    Tambah Jawaban
                                </button>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary" id="submitBtn">Simpan Perubahan</button>
                                        @if($material)
                                            <a href="{{ route('admin.materials.questions.index', $material) }}" class="btn btn-outline-secondary">Batal</a>
                                        @else
                                            <a href="{{ route('admin.questions.index') }}" class="btn btn-outline-secondary">Batal</a>
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
        let answerCount = 0; // Init dengan 0, akan dihitung ulang di handleQuestionTypeChange

        function handleQuestionTypeChange() {
            const questionType = document.querySelector('[name="question_type"]').value;
            const answerContainer = document.getElementById('answers-container');
            const addAnswerBtn = document.getElementById('add-answer-btn');

            // Ambil data jawaban lama dari DOM
            const oldAnswers = Array.from(answerContainer.querySelectorAll('.answer-entry')).map(entry => {
                return {
                    text: entry.querySelector('input[type="text"]').value,
                    isCorrect: entry.getAttribute('data-correct') === '1',
                };
            });

            // Reset container kecuali heading
            const heading = answerContainer.querySelector('h6');
            answerContainer.innerHTML = '';
            answerContainer.appendChild(heading);

            // Reset counter dan bangun ulang jawaban sesuai tipe soal dan data lama
            answerCount = 0;

            if (questionType === 'fill_in_the_blank') {
                // Hanya satu jawaban, ambil dari oldAnswers jika ada, atau buat baru kosong
                const ans = oldAnswers[0] || { text: '', isCorrect: true };
                addAnswer(ans.text, ans.isCorrect, questionType);
                if (addAnswerBtn) addAnswerBtn.style.display = 'none';
            } else {
                // Untuk tipe lain, minimal 2 jawaban
                const countToAdd = Math.max(oldAnswers.length, 2);
                for (let i = 0; i < countToAdd; i++) {
                    const ans = oldAnswers[i] || { text: '', isCorrect: false };
                    addAnswer(ans.text, ans.isCorrect, questionType);
                }
                if (addAnswerBtn) addAnswerBtn.style.display = 'inline-block';
            }
        }

        function addAnswer(answerText = '', isCorrect = false, questionType = null) {
            const container = document.getElementById('answers-container');

            if (!questionType) {
                questionType = document.querySelector('[name="question_type"]').value;
            }

            // Cegah tambah lebih dari 1 jawaban untuk fill_in_the_blank
            if (questionType === 'fill_in_the_blank' && container.getElementsByClassName('answer-entry').length >= 1) {
                return;
            }

            const newAnswer = document.createElement('div');
            newAnswer.className = 'answer-entry mb-3';
            newAnswer.setAttribute('data-correct', isCorrect ? '1' : '0');

            if (questionType === 'radio_button' || questionType === 'fill_in_the_blank') {
                newAnswer.innerHTML = `
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group input-group-outline">
                                <input type="text" name="answers[${answerCount}][answer_text]" class="form-control" placeholder="Jawaban" required value="${answerText}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input correct-radio" type="radio" name="correct_answer" value="${answerCount}" ${isCorrect ? 'checked' : ''}>
                                <label class="form-check-label">Jawaban Benar</label>
                                <input type="hidden" name="answers[${answerCount}][is_correct]" value="${isCorrect ? '1' : '0'}">
                            </div>
                        </div>
                    </div>
                `;
            } else if (questionType === 'drag_and_drop') {
                newAnswer.innerHTML = `
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group input-group-outline">
                                <input type="text" name="answers[${answerCount}][answer_text]" class="form-control" placeholder="Jawaban" required value="${answerText}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="answers[${answerCount}][is_correct]" value="1" ${isCorrect ? 'checked' : ''}>
                                <label class="form-check-label">Jawaban Benar</label>
                            </div>
                        </div>
                    </div>
                `;
            }

            container.appendChild(newAnswer);
            answerCount++;

            // Setup ulang listener radio button supaya sync hidden input
            setupRadioButtonListeners();
        }

        function setupRadioButtonListeners() {
            // Hapus listener dulu supaya gak duplikat
            const radios = document.querySelectorAll('.correct-radio');
            radios.forEach(radio => {
                radio.onchange = null;
                radio.addEventListener('change', function() {
                    updateAllHiddenInputs();
                    updateDataCorrectAttributes();
                });
            });
        }

        function updateAllHiddenInputs() {
            const container = document.getElementById('answers-container');
            const entries = container.getElementsByClassName('answer-entry');
            const selectedRadio = document.querySelector('input[name="correct_answer"]:checked');

            if (!selectedRadio) return;

            const selectedValue = selectedRadio.value;

            Array.from(entries).forEach((entry, index) => {
                const hiddenInput = entry.querySelector('input[type="hidden"]');
                if (hiddenInput) {
                    hiddenInput.value = (index.toString() === selectedValue) ? '1' : '0';
                }
            });
        }

        function updateDataCorrectAttributes() {
            // Update atribut data-correct untuk tiap jawaban berdasarkan radio/checkbox checked
            const container = document.getElementById('answers-container');
            const entries = container.getElementsByClassName('answer-entry');
            const questionType = document.querySelector('[name="question_type"]').value;

            Array.from(entries).forEach((entry, index) => {
                if (questionType === 'drag_and_drop') {
                    const checkbox = entry.querySelector('input[type="checkbox"]');
                    entry.setAttribute('data-correct', checkbox.checked ? '1' : '0');
                } else {
                    const radio = document.querySelector('input[name="correct_answer"]:checked');
                    if (radio) {
                        entry.setAttribute('data-correct', radio.value == index ? '1' : '0');
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const questionTypeSelect = document.querySelector('[name="question_type"]');

            questionTypeSelect.addEventListener('change', handleQuestionTypeChange);

            // Inisialisasi
            handleQuestionTypeChange();
        });
    </script>
    @endpush
    <x-admin.tutorial />
</x-layout>
