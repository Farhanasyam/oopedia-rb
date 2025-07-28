<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="ueq" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Detail UEQ Survey" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <br><br>
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3">Detail Survey UEQ - {{ $user->name }}</h6>
                                <a href="{{ route('admin.ueq.index') }}" class="btn btn-sm btn-light me-3">
                                    <span class="btn-inner--icon"><i class="material-icons">arrow_back</i></span>
                                    <span class="btn-inner--text">Kembali</span>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Informasi Mahasiswa</h5>
                                    <table class="table">
                                        <tr>
                                            <th>Nama</th>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>NIM</th>
                                            <td>{{ $survey->nim }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kelas</th>
                                            <td>{{ $survey->class }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Survey</th>
                                            <td>{{ $survey->created_at->format('d M Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <h5 class="mt-4">Hasil Survey UEQ</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Aspek</th>
                                            <th>Kiri</th>
                                            <th>Skor</th>
                                            <th>Kanan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $aspects = [
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
                                        ];
                                        @endphp

                                        @foreach($aspects as $aspect)
                                            <tr>
                                                <td>{{ ucwords(str_replace('_', ' ', $aspect['name'])) }}</td>
                                                <td>{{ $aspect['left'] }}</td>
                                                <td>{{ $survey->{$aspect['name']} }}</td>
                                                <td>{{ $aspect['right'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5>Komentar</h5>
                                    <div class="card">
                                        <div class="card-body">
                                            {{ $survey->comments ?: 'Tidak ada komentar' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Saran</h5>
                                    <div class="card">
                                        <div class="card-body">
                                            {{ $survey->suggestions ?: 'Tidak ada saran' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php
                            $dimensions = [
                                'Daya Tarik (Attractiveness)' => [
                                    'annoying_enjoyable', 
                                    'good_bad', 
                                    'unlikable_pleasing', 
                                    'unpleasant_pleasant', 
                                    'attractive_unattractive', 
                                    'friendly_unfriendly'
                                ],
                                'Kejelasan (Perspicuity)' => [
                                    'not_understandable_understandable', 
                                    'clear_confusing', 
                                    'easy_difficult', 
                                    'complicated_easy'
                                ],
                                'Efisiensi (Efficiency)' => [
                                    'fast_slow', 
                                    'inefficient_efficient', 
                                    'organized_cluttered', 
                                    'supportive_obstructive'
                                ],
                                'Ketepatan (Dependability)' => [
                                    'secure_not_secure', 
                                    'predictable_unpredictable', 
                                    'meets_expectations_does_not_meet'
                                ],
                                'Stimulasi (Stimulation)' => [
                                    'boring_exciting', 
                                    'not_interesting_interesting', 
                                    'motivating_demotivating', 
                                    'creative_dull'
                                ],
                                'Kebaruan (Novelty)' => [
                                    'usual_leading_edge', 
                                    'conservative_innovative', 
                                    'inventive_conventional'
                                ]
                            ];

                            $dimensionScores = [];
                            foreach ($dimensions as $dimensionName => $aspectNames) {
                                $scores = collect($aspectNames)->map(function($aspectName) use ($survey) {
                                    return $survey->{$aspectName};
                                });
                                $dimensionScores[$dimensionName] = $scores->avg();
                            }
                            @endphp

                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Rangkuman Dimensi UEQ</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Dimensi</th>
                                                    <th>Skor Rata-rata</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($dimensionScores as $dimensionName => $score)
                                                    <tr>
                                                        <td>{{ $dimensionName }}</td>
                                                        <td>{{ number_format($score, 2) }}/7</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Rangkuman 6 Dimensi UEQ</h5>
                                    <div class="table-responsive">
                                        <table class="table align-items-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Attractiveness</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Perspicuity</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Efficiency</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dependability</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stimulation</th>
                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Novelty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        {{ number_format(
                                                            ($survey->annoying_enjoyable + $survey->good_bad + $survey->unlikable_pleasing + $survey->unpleasant_pleasant + $survey->attractive_unattractive + $survey->friendly_unfriendly) / 6, 2) }}
                                                    </td>
                                                    <td>
                                                        {{ number_format(
                                                            ($survey->not_understandable_understandable + $survey->easy_difficult + $survey->complicated_easy + $survey->clear_confusing) / 4, 2) }}
                                                    </td>
                                                    <td>
                                                        {{ number_format(
                                                            ($survey->fast_slow + $survey->inefficient_efficient + $survey->impractical_practical + $survey->organized_cluttered) / 4, 2) }}
                                                    </td>
                                                    <td>
                                                        {{ number_format(
                                                            ($survey->unpredictable_predictable + $survey->obstructive_supportive + $survey->secure_not_secure + $survey->meets_expectations_does_not_meet) / 4, 2) }}
                                                    </td>
                                                    <td>
                                                        {{ number_format(
                                                            ($survey->valuable_inferior + $survey->boring_exciting + $survey->not_interesting_interesting + $survey->motivating_demotivating) / 4, 2) }}
                                                    </td>
                                                    <td>
                                                        {{ number_format(
                                                            ($survey->creative_dull + $survey->inventive_conventional + $survey->usual_leading_edge + $survey->conservative_innovative) / 4, 2) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
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