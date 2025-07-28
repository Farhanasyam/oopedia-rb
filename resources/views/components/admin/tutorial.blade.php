@push('js')
<script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>
<link href="https://unpkg.com/intro.js/minified/introjs.min.css" rel="stylesheet">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = '{{ request()->route()->getName() }}';
    const tutorialKey = `admin_${currentPage}_tutorial_complete`;
    
    if (!localStorage.getItem(tutorialKey) && !localStorage.getItem('skip_admin_tour')) {
        setTimeout(startAdminTutorial, 500);
    }
});

function startAdminTutorial() {
    const currentPage = '{{ request()->route()->getName() }}';
    const steps = getTutorialSteps(currentPage);
    
    if (!steps) return;

    introJs().setOptions({
        steps: steps,
        showProgress: true,
        exitOnOverlayClick: true,
        showBullets: false,
        scrollToElement: true,
        nextLabel: 'Berikutnya',
        prevLabel: 'Sebelumnya',
        doneLabel: 'Selesai',
        skipLabel: '×',
        showSkipButton: true,
        tooltipClass: 'customTooltip'
    }).oncomplete(function() {
        localStorage.setItem(`admin_${currentPage}_tutorial_complete`, 'true');
    }).onexit(function() {
        localStorage.setItem(`admin_${currentPage}_tutorial_complete`, 'true');
    }).onskip(function() {
        localStorage.setItem('skip_admin_tour', 'true');
    }).start();
}

function getTutorialSteps(page) {
    const tutorials = {
        'admin.dashboard': [
            {
                intro: "Selamat datang di Dashboard Admin OOPEDIA!"
            },
            {
                element: '.card-stats',
                intro: "Di sini Anda dapat melihat statistik penting seperti jumlah mahasiswa, materi, dan soal."
            },
            {
                element: '.chart-container',
                intro: "Grafik ini menunjukkan progres rata-rata mahasiswa untuk setiap materi."
            }
        ],
        'admin.materials.index': [
            {
                intro: "Selamat datang di halaman Manajemen Materi!"
            },
            {
                element: '.input-group',
                intro: "Gunakan form pencarian ini untuk menemukan materi."
            },
            {
                element: 'a[href*="materials/create"]',
                intro: "Klik tombol ini untuk menambahkan materi baru."
            },
            {
                element: '.table',
                intro: "Tabel ini menampilkan semua materi yang tersedia."
            }
        ],
        'admin.materials.create': [
            {
                intro: "Selamat datang di halaman Tambah Materi!"
            },
            {
                element: 'input[name="title"]',
                intro: "Masukkan judul materi di sini."
            },
            {
                element: '.tox-tinymce',
                intro: "Editor ini untuk menulis konten materi. Anda dapat menambahkan teks, gambar, dan kode."
            },
            {
                element: 'button[type="submit"]',
                intro: "Klik tombol ini untuk menyimpan materi baru."
            }
        ],
        'admin.materials.edit': [
            {
                intro: "Selamat datang di halaman Edit Materi!"
            },
            {
                element: 'input[name="title"]',
                intro: "Ubah judul materi di sini jika diperlukan."
            },
            {
                element: '.tox-tinymce',
                intro: "Edit konten materi menggunakan editor ini."
            },
            {
                element: 'button[type="submit"]',
                intro: "Klik untuk menyimpan perubahan materi."
            }
        ],
        'admin.question-banks.index': [
            {
                intro: "Selamat datang di halaman Bank Soal!"
            },
            {
                element: '.input-group',
                intro: "Gunakan form pencarian ini untuk menemukan bank soal."
            },
            {
                element: 'a[href*="question-banks/create"]',
                intro: "Klik tombol ini untuk menambahkan bank soal baru."
            },
            {
                element: '.table',
                intro: "Tabel ini menampilkan semua bank soal yang tersedia."
            }
        ],
        'admin.question-banks.create': [
            {
                intro: "Selamat datang di halaman Tambah Bank Soal!"
            },
            {
                element: '.input-group input[name="name"]',
                intro: "Masukkan nama bank soal di sini."
            },
            {
                element: '.input-group textarea[name="description"]',
                intro: "Berikan deskripsi untuk bank soal ini."
            },
        ],
        'admin.question-banks.configure': [
            {
                intro: "Selamat datang di halaman Konfigurasi Bank Soal!"
            },
            {
                element: 'select[name="material_id"]',
                intro: "Pilih materi yang terkait dengan bank soal ini."
            },
            {
                element: '#beginner_count',
                intro: "Tentukan jumlah soal untuk tingkat Beginner."
            },
            {
                element: '#medium_count',
                intro: "Tentukan jumlah soal untuk tingkat Medium."
            },
            {
                element: '#hard_count',
                intro: "Tentukan jumlah soal untuk tingkat Hard."
            }
        ],
        'admin.question-banks.manage-questions': [
            {
                intro: "Selamat datang di halaman Kelola Soal!"
            },
            {
                element: 'input[name="search"]',
                intro: "Cari soal berdasarkan teks pertanyaan."
            },
            {
                element: 'select[name="difficulty"]',
                intro: "Filter soal berdasarkan tingkat kesulitan."
            },
            {
                element: 'select[name="material_id"]',
                intro: "Filter soal berdasarkan materi."
            },
            {
                element: '.table',
                intro: "Pilih soal yang ingin ditambahkan ke bank soal."
            }
        ],
        'admin.question-banks.show': [
            {
                intro: "Selamat datang di halaman Detail Bank Soal!"
            },
            {
                element: '.card-body h4',
                intro: "Informasi detail bank soal ditampilkan di sini."
            },
            {
                element: '.row.mb-4 .col-md-4',
                intro: "Statistik jumlah soal berdasarkan tingkat kesulitan."
            },
            {
                element: '.table',
                intro: "Daftar soal yang ada dalam bank soal ini."
            }
        ],
        'admin.ueq.index': [
            {
                intro: "Selamat datang di halaman Hasil Survey UEQ!"
            },
            {
                element: '.btn-success',
                intro: "Klik tombol ini untuk mengunduh hasil survey dalam format CSV."
            },
            {
                element: '.table-responsive',
                intro: "Tabel ini menampilkan detail hasil survey dari setiap responden."
            }
        ],
        'admin.users.index': [
            {
                intro: "Selamat datang di halaman Manajemen Pengguna!"
            },
            {
                element: 'form.mb-3',
                intro: "Cari pengguna berdasarkan nama atau email."
            },
            {
                element: '.btn-warning',
                intro: "Lihat daftar dosen yang menunggu persetujuan."
            },
            {
                element: '.btn-success',
                intro: "Tambahkan data dosen menggunakan file Excel."
            },
            {
                element: '.table',
                intro: "Tabel ini menampilkan semua pengguna sistem."
            }
        ],
        'admin.users.create': [
            {
                intro: "Selamat datang di halaman Tambah Pengguna!"
            },
            {
                element: 'input[name="name"]',
                intro: "Masukkan nama pengguna."
            },
            {
                element: 'input[name="email"]',
                intro: "Masukkan email pengguna."
            },
            {
                element: 'select[name="role_id"]',
                intro: "Pilih peran untuk pengguna baru."
            }
        ],
        'admin.users.import': [
            {
                intro: "Selamat datang di halaman Import Data Dosen!"
            },
            {
                element: '.btn-info',
                intro: "Unduh template Excel untuk data dosen."
            },
            {
                element: 'input[name="excel_file"]',
                intro: "Upload file Excel yang berisi data dosen."
            },
            {
                element: 'button[type="submit"]',
                intro: "Klik untuk memproses import data."
            }
        ],
        'admin.students.index': [
            {
                intro: "Selamat datang di halaman Data Mahasiswa!"
            },
            {
                element: 'form.mb-3',
                intro: "Cari mahasiswa berdasarkan nama."
            },
            {
                element: '.btn-primary',
                intro: "Klik tombol ini untuk mengimpor data mahasiswa."
            },
            {
                element: '.table',
                intro: "Tabel ini menampilkan daftar mahasiswa dan progres belajar mereka."
            }
        ],
        'admin.students.import': [
            {
                intro: "Selamat datang di halaman Import Data Mahasiswa!"
            },
            {
                element: '.card-body p',
                intro: "Bagian ini menjelaskan format file yang dibutuhkan."
            },
            {
                element: 'a[href*="template"]',
                intro: "Unduh template Excel untuk data mahasiswa di sini."
            },
            {
                element: 'input[type="file"]',
                intro: "Pilih file Excel/CSV yang berisi data mahasiswa."
            }
        ],
        'admin.students.progress': [
            {
                intro: "Selamat datang di halaman Progress Mahasiswa!"
            },
            {
                element: '.progress-stats',
                intro: "Lihat statistik progress pembelajaran mahasiswa di sini."
            },
            {
                element: '.material-progress',
                intro: "Detail progress per materi ditampilkan di bagian ini."
            }
        ],
        'admin.questions.index': [
            {
                intro: "Selamat datang di halaman Manajemen Soal!"
            },
            {
                element: '.search-form',
                intro: "Cari soal berdasarkan teks pertanyaan atau materi."
            },
            {
                element: '.filter-difficulty',
                intro: "Filter soal berdasarkan tingkat kesulitan."
            },
            {
                element: '.btn-create-question',
                intro: "Klik untuk menambahkan soal baru."
            }
        ],
        'admin.questions.create': [
            {
                intro: "Selamat datang di halaman Tambah Soal!"
            },
            {
                element: 'select[name="question_bank_id"]',
                intro: "Pilih bank soal untuk menyimpan soal ini."
            },
            {
                element: '.tox-tinymce',
                intro: "Tulis pertanyaan di sini. Anda dapat menambahkan teks, gambar, atau kode program."
            },
            {
                element: '.options-container',
                intro: "Tambahkan pilihan jawaban di bagian ini. Pastikan menandai jawaban yang benar."
            },
            {
                element: 'textarea[name="explanation"]',
                intro: "Berikan penjelasan untuk jawaban yang benar."
            }
        ],
        'admin.questions.edit': [
            {
                intro: "Selamat datang di halaman Edit Soal!"
            },
            {
                element: '.tox-tinymce',
                intro: "Edit pertanyaan di sini."
            },
            {
                element: '.options-container',
                intro: "Ubah pilihan jawaban dan jawaban yang benar."
            },
            {
                element: 'textarea[name="explanation"]',
                intro: "Perbarui penjelasan jawaban jika diperlukan."
            }
        ],
        'admin.users.pending': [
            {
                intro: "Selamat datang di halaman Persetujuan Dosen!"
            },
            {
                element: '.table',
                intro: "Daftar dosen yang menunggu persetujuan ditampilkan di sini."
            },
            {
                element: '.btn-approve',
                intro: "Klik untuk menyetujui pendaftaran dosen."
            },
            {
                element: '.btn-reject',
                intro: "Klik untuk menolak pendaftaran dosen."
            }
        ],
        'admin.questions.show': [
            {
                intro: "Selamat datang di halaman Detail Soal!"
            },
            {
                element: '.question-content',
                intro: "Pertanyaan lengkap ditampilkan di sini."
            },
            {
                element: '.options-list',
                intro: "Daftar pilihan jawaban yang tersedia."
            },
            {
                element: '.correct-answer',
                intro: "Jawaban yang benar ditandai dengan warna hijau."
            },
            {
                element: '.explanation',
                intro: "Penjelasan mengapa jawaban tersebut benar."
            }
        ],
        'admin.question-banks.edit': [
            {
                intro: "Selamat datang di halaman Edit Bank Soal!"
            },
            {
                element: 'input[name="name"]',
                intro: "Ubah nama bank soal di sini."
            },
            {
                element: 'textarea[name="description"]',
                intro: "Perbarui deskripsi bank soal jika diperlukan."
            },
            {
                element: 'button[type="submit"]',
                intro: "Simpan perubahan yang telah dilakukan."
            }
        ],
        'admin.users.edit': [
            {
                intro: "Selamat datang di halaman Edit Pengguna!"
            },
            {
                element: 'input[name="name"]',
                intro: "Ubah nama pengguna di sini."
            },
            {
                element: 'input[name="email"]',
                intro: "Perbarui alamat email jika diperlukan."
            },
            {
                element: 'select[name="role_id"]',
                intro: "Ubah peran pengguna jika diperlukan."
            }
        ],
        'admin.materials.show': [
            {
                intro: "Selamat datang di halaman Detail Materi!"
            },
            {
                element: '.material-title',
                intro: "Judul materi ditampilkan di sini."
            },
            {
                element: '.material-content',
                intro: "Konten lengkap materi dapat dilihat di bagian ini."
            },
            {
                element: '.btn-edit',
                intro: "Klik untuk mengedit materi ini."
            }
        ],
        'admin.students.show': [
            {
                intro: "Selamat datang di halaman Detail Mahasiswa!"
            },
            {
                element: '.student-info',
                intro: "Informasi dasar mahasiswa ditampilkan di sini."
            },
            {
                element: '.progress-overview',
                intro: "Ringkasan progress pembelajaran mahasiswa."
            },
            {
                element: '.material-list',
                intro: "Daftar materi yang telah dipelajari dan nilainya."
            }
        ],
        'admin.ueq.show': [
            {
                intro: "Selamat datang di halaman Detail Survey UEQ!"
            },
            {
                element: '.respondent-info',
                intro: "Informasi responden survey."
            },
            {
                element: '.ueq-answers',
                intro: "Jawaban lengkap untuk setiap pertanyaan survey."
            },
            {
                element: '.dimension-scores',
                intro: "Skor untuk setiap dimensi UEQ."
            }
        ]
    };

    return tutorials[page] || null;
}
</script>
@endpush 