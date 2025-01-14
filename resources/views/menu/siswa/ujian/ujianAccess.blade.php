@extends('layout.template.mainTemplate')

@section('container')

    {{-- Navigasi Breadcrumb --}}
    <div class="col-12 ps-4 pe-4 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">
                    <a
                        href="{{ route('viewKelasMapel', ['mapel' => $mapel['id'], 'token' => encrypt($kelas['id']), 'mapel_id' => $mapel['id']]) }}">
                        {{ $mapel['name'] }}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page"> Ujian</li>
            </ol>
        </nav>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Judul Halaman --}}
    <div class="ps-4 pe-4 mt-4  pt-4">
        <h2 class="display-6 fw-bold">
            <a
                href="{{ route('viewKelasMapel', ['mapel' => $mapel['id'], 'token' => encrypt($kelas['id']), 'mapel_id' => $mapel['id']]) }}">
                <button type="button" class="btn btn-outline-secondary rounded-circle">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
            </a>
            Ujian
        </h2>
    </div>

    {{-- Informasi Tugas --}}
    <div class="mb-4 p-4 bg-white rounded-4">
        <div class=" p-4">
            <h4 class="fw-bold mb-2">Informasi</h4>
            <hr>
            <h3 class="fw-bold text-primary">{{ $ujian->name }} @if ($ujian->isHidden == 1)
                    <i class="fa-solid fa-lock fa-bounce text-danger"></i>
                @endif
            </h3>
            <div class="row">
                @php
                    $dueDateTime = \Carbon\Carbon::parse($ujian->due); // Mengatur timezone ke Indonesia (ID)
                    $localTime = \Carbon\Carbon::parse($ujian->due)->setTimeZone('asia/jakarta'); // Mengatur timezone ke Indonesia (ID)
                    $now = \Carbon\Carbon::now(); // Mengatur timezone ke Indonesia (ID)
                    $timeUntilDue = $dueDateTime->diff($now); // Perbedaan waktu antara sekarang dan waktu jatuh tempo
                    // dd($dueDateTime, $now, $timeUntilDue);
                    $daysUntilDue = $timeUntilDue->days; // Jumlah hari hingga jatuh tempo
                    $hoursUntilDue = $timeUntilDue->h; // Jumlah jam hingga jatuh tempo
                    $minutesUntilDue = $timeUntilDue->i; // Jumlah menit hingga jatuh tempo
                @endphp
                @if ($dueDateTime < $now)
                    <div class="border p-3 fw-bold col-lg-3 col-12">
                        Status : <span class="badge badge-danger p-2">Ditutup</span>
                    </div>
                    <div class="col-12 border p-3 col-lg-3">
                        <span class="fw-bold">Time : </span>
                        {{ $ujian->time }} Menit
                    </div>
                    <div class="border p-3 fw-bold col-lg-3 col-12">
                        Waktu : <span class="badge badge-danger p-2">
                            -
                        </span>
                    </div>
                @else
                    @if ($daysUntilDue >= 0 || ($daysUntilDue == 0 && $hoursUntilDue >= 0 && $minutesUntilDue >= 0))
                        <div class="border p-3 fw-bold col-lg-3 col-12">
                            Status : <span class="badge badge-primary p-2">Dibuka</span>
                        </div>
                        <div class="col-12 border p-3 col-lg-3">
                            <span class="fw-bold">Time : </span>
                            {{ $ujian->time }}
                            @if ($ujian->tipe == 'kecermatan')
                                Detik / Kolom
                            @elseif($ujian->tipe == 'multiple' || $ujian->tipe == 'essay')
                                Menit
                            @endif
                        </div>
                        <div class="border p-3 fw-bold col-lg-3 col-12">
                            Waktu : <span class="badge badge-primary p-2">
                                {{ $daysUntilDue }} hari, {{ $hoursUntilDue }} jam, {{ $minutesUntilDue }}
                                menit lagi
                            </span>
                        </div>
                    @endif
                @endif
                <div class="col-12 border p-3 col-lg-3">
                    <span class="fw-bold">Deadline :</span>
                    {{ $localTime->formatLocalized('%d %B %Y %H:%M') }}
                </div>
            </div>
        </div>
    </div>
    <hr>

    @if ($userCommit)
        {{-- Box prompt --}}
        <div class="mb-4 p-4 bg-white rounded-4 text-center h-100">
            <h6 class="fw-bold display-6 text-primary">
                @if ($tipe == 'kecermatan')
                    Ujian Kecermatan
                @else
                    Ujian
                @endif Selesai
            </h6>

            {{-- Score --}}
            @if ($tipe == 'kecermatan')
                <div class="accordion-body table-responsive p-4">
                    <table id="table" class="table table-striped table-hover table-lg">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Kolom</th>
                                <th scope="col">Result</th>
                            </tr>
                        </thead>
                        {{-- {{ dd($ujian->Kecermatan) }} --}}
                        <tbody>
                            @php
                                $totalBenar = 0;
                                $totalSalah = 0;
                                $totalTidakDijawab = 0;
                            @endphp
                            @foreach ($ujian->Kecermatan as $key)
                                @php
                                    $benar = 0;
                                    $salah = 0;
                                    $tidakDijawab = 0;
                        
                                    foreach ($key->UserJawabanKecermatan as $key2) {
                                        if ($key2->user_id == Auth()->user()->id) {
                                            if (is_null($key2->jawaban_user)) {
                                                $tidakDijawab++;
                                            } elseif ($key2->jawaban == strtolower($key2->jawaban_user)) {
                                                $benar++;
                                            } else {
                                                $salah++;
                                            }
                                        }
                                    }
                        
                                    $totalBenar += $benar;
                                    $totalSalah += $salah;
                                    $totalTidakDijawab += $tidakDijawab;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>Kolom {{ $loop->iteration }}</td>
                                    <td>Benar: {{ $benar }}, Salah: {{ $salah }}, Tidak Dijawab: {{ $tidakDijawab }}</td>
                                </tr>
                            @endforeach
                        </tbody>                        
                    </table>

                    {{-- Grafik Hasil Ujian --}}
                    <div class="text-center mt-4">
                        <canvas id="resultChart" width="400" height="200"></canvas>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var ctx = document.getElementById('resultChart').getContext('2d');
                            var labels = [];
                            var dataBenar = [];

                            @foreach ($ujian->Kecermatan as $key)
                                labels.push("Kolom {{ $loop->iteration }}");
                                @php
                                    $benar = 0;
                                    foreach ($key->UserJawabanKecermatan as $key2) {
                                        if ($key2->user_id == Auth()->user()->id && $key2->jawaban == strtolower($key2->jawaban_user)) {
                                            $benar++;
                                        }
                                    }
                                    echo "dataBenar.push($benar);";
                                @endphp
                            @endforeach

                            var resultChart = new Chart(ctx, {
                                type: 'bar', // Tipe grafik
                                data: {
                                    labels: labels, // Nama-nama kolom sebagai label sumbu X
                                    datasets: [{
                                        label: 'Jumlah Jawaban Benar per Kolom',
                                        data: dataBenar, // Data jumlah benar dari setiap kolom
                                        backgroundColor: 'rgba(128, 0, 0, 0.6)',
                                        borderColor: 'rgba(128, 0, 0, 1)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            suggestedMin: 0, // Minimum value with padding
                                            suggestedMax: Math.max(...dataBenar) + 2, // Maximum value with padding
                                            title: {
                                                display: true,
                                                text: 'Jumlah Jawaban Benar'
                                            },

                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Kolom'
                                            }
                                        }
                                    }
                                }
                            });
                        });
                    </script>

                    <div class="text-center">
                        <div class="fs-5">
                            <span class="text-primary fw-bold">Total: </span>
                            <span class="badge badge-danger">Benar : {{ $totalBenar }}</span>
                            <span class="badge badge-danger">Salah : {{ $totalSalah }}</span>
                            <span class="badge badge-danger">Tidak Dijawab : {{ $totalTidakDijawab }}</span>
                        </div>
                        <h6 class="fw-bold display-6 text-primary mt-4">Ulangi Ujian Kecermatan?</h6>
                        <p>ingin mulai kecermatan sekarang? anda memiliki <span
                                class="fw-bold text-danger">{{ $ujian->time }}
                                @if ($ujian->tipe == 'essay' || $ujian->tipe == 'multiple')
                                    menit
                                @elseif($ujian->tipe == 'kecermatan')
                                    detik / Kolom
                                @endif
                            </span>
                            untuk
                            mengerjakan, dan tidak bisa
                            diberhentikan
                            setelah anda memilih untuk melanjutkan.</p>
                        <p class="text-danger">*Pastikan anda memiliki koneksi internet yang bagus / cukup. Test Kecermatan
                            tidak bisa
                            diulang!</p>
                        <div class=" text-center">
                            <form action="{{ route('startUjian', ['token' => encrypt($ujian->id)]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary">Lanjutkan</button>
                                <a
                                    href="{{ route('viewKelasMapel', ['mapel' => $mapel['id'], 'token' => encrypt($kelas['id']), 'mapel_id' => $mapel['id']]) }}">
                                    <button type="button" class="btn btn-primary">Kembali</button>
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            @elseif ($tipe == 'essay')
                <div class="accordion-body table-responsive p-4">
                    <table id="table" class="table table-striped table-hover table-lg ">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Soal</th>
                                <th scope="col">Jawaban anda</th>
                                <th scope="col">Nilai</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $nilaiTotal = 0;
                            @endphp
                            @foreach ($ujian->SoalUjianEssay as $key)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{!! $key->soal !!}</td>
                                    <td>
                                        @php
                                            $jawaban = App\Models\UserJawaban::where('user_id', Auth()->User()->id)
                                                ->where('essay_id', $key->id)
                                                ->first();
                                            if ($jawaban) {
                                                $nilaiTotal += $jawaban['nilai'];
                                            }
                                        @endphp
                                        @if ($jawaban)
                                            {{ $jawaban['user_jawaban'] }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($jawaban)
                                            {{ $jawaban['nilai'] }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @php
                        if ($nilaiTotal >= 100) {
                            $nilaiTotal = 100;
                        } elseif ($nilaiTotal <= 0) {
                            $nilaiTotal = 0;
                        }

                    @endphp
                    <div class="text-center ">
                        <div class=" fs-5">
                            <span class="text-primary fw-bold">Nilai : </span><span class="badge badge-danger">
                                {{ $nilaiTotal }}</span>
                        </div>
                    </div>
                </div>
            @else
                @if ($dueDateTime < $now || $tipe == 'multiple')
                    <div class="accordion-body table-responsive p-4">
                        <table id="table" class="table table-striped table-hover table-lg ">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Soal</th>
                                    <th scope="col">Jawaban anda</th>
                                    <th scope="col">Kunci Jawaban</th>
                                    <th scope="col">Nilai</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $nilaiTotal = 0;
                                @endphp
                                @foreach ($ujian->SoalUjianMultiple as $key)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{!! $key->soal !!}</td>
                                        <td>
                                            @php
                                                $jawaban = App\Models\UserJawaban::where('user_id', Auth()->User()->id)
                                                    ->where('multiple_id', $key->id)
                                                    ->first();
                                                if ($jawaban) {
                                                    $nilaiTotal += $jawaban['nilai'];
                                                    $temp = $jawaban['user_jawaban'];
                                                }
                                            @endphp
                                            @if ($jawaban)
                                                {{ $jawaban['user_jawaban'] }}.

                                                @if ($jawaban['user_jawaban'] == 'A')
                                                    {{ $key->a }}
                                                @elseif ($jawaban['user_jawaban'] == 'B')
                                                    {{ $key->b }}
                                                @elseif ($jawaban['user_jawaban'] == 'C')
                                                    {{ $key->c }}
                                                @elseif ($jawaban['user_jawaban'] == 'D')
                                                    {{ $key->d }}
                                                @elseif ($jawaban['user_jawaban'] == 'E')
                                                    {{ $key->e }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {!! strtoupper($key->jawaban) !!}.
                                            @if ($key->jawaban == 'a')
                                                {{ $key->a }}
                                            @elseif ($key->jawaban == 'b')
                                                {{ $key->b }}
                                            @elseif ($key->jawaban == 'c')
                                                {{ $key->c }}
                                            @elseif ($key->jawaban == 'd')
                                                {{ $key->d }}
                                            @elseif ($key->jawaban == 'e')
                                                {{ $key->e }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($jawaban)
                                                {{ substr($jawaban['nilai'], 0, 4) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @php
                            if ($nilaiTotal >= 100) {
                                $nilaiTotal = 100;
                            } elseif ($nilaiTotal <= 0) {
                                $nilaiTotal = 0;
                            }

                        @endphp
                        <div class="text-center mt-2 fs-5 ">
                            <div class=" fs-5">
                                <span class="text-primary fw-bold">Nilai : </span><span class="badge badge-danger">
                                    {{ ceil($nilaiTotal) }}</span>
                            </div>
                        </div>
                    </div>
                    {{-- Here ulangi --}}
                    <div class=" text-center">
                        <form action="{{ route('startUjian', ['token' => encrypt($ujian->id)]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary">Ulangi</button>
                            <a
                                href="{{ route('viewKelasMapel', ['mapel' => $mapel['id'], 'token' => encrypt($kelas['id']), 'mapel_id' => $mapel['id']]) }}">
                                <button type="button" class="btn btn-primary">Kembali</button>
                            </a>
                        </form>
                    </div>
                @else
                    @php
                        $nilaiTotal = 0;
                    @endphp
                    @foreach ($ujian->SoalUjianMultiple as $key)
                        @php
                            $jawaban = App\Models\UserJawaban::where('user_id', Auth()->User()->id)
                                ->where('multiple_id', $key->id)
                                ->first();
                            if ($jawaban) {
                                $nilaiTotal += $jawaban['nilai'];
                                $temp = $jawaban['user_jawaban'];
                            }
                        @endphp
                    @endforeach
                    <div class="text-center fs-5 mt-2">
                        Detail Jawaban anda akan ditampilkan ketika Deadline sudah selesai.
                        <div class="">
                            <span class="text-primary fw-bold">Nilai : </span><span class="badge badge-danger">
                                {{ ceil($nilaiTotal) }}</span>
                        </div>
                    </div>
                @endif

            @endif


        </div>
    @else
        @if ($dueDateTime > $now)
            {{-- Box prompt --}}
            <div class="mb-4 p-4 bg-white rounded-4 text-center h-100">
                @if ($ujian->tipe == 'multiple' || $ujian->tipe == 'essay')
                    <h6 class="fw-bold display-6 text-primary">Mulai Ujian?</h6>
                    <p>ingin mulai ujian sekarang? anda memiliki <span class="fw-bold text-danger">{{ $ujian->time }}
                            @if ($ujian->tipe == 'essay' || $ujian->tipe == 'multiple')
                                menit
                            @elseif($ujian->tipe == 'kecermatan')
                                detik / Kolom
                            @endif
                        </span>
                        untuk
                        mengerjakan, dan tidak bisa
                        diberhentikan
                        setelah anda memilih untuk melanjutkan.</p>
                    <div class=" text-center">
                        <form action="{{ route('startUjian', ['token' => encrypt($ujian->id)]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary">Lanjutkan</button>
                            <a
                                href="{{ route('viewKelasMapel', ['mapel' => $mapel['id'], 'token' => encrypt($kelas['id']), 'mapel_id' => $mapel['id']]) }}">
                                <button type="button" class="btn btn-primary">Kembali</button>
                            </a>
                        </form>
                    </div>
                @elseif ($ujian->tipe == 'kecermatan')
                    <h6 class="fw-bold display-6 text-primary">Mulai Ujian Kecermatan?</h6>
                    <p>ingin mulai kecermatan sekarang? anda memiliki <span
                            class="fw-bold text-danger">{{ $ujian->time }}
                            @if ($ujian->tipe == 'essay' || $ujian->tipe == 'multiple')
                                menit
                            @elseif($ujian->tipe == 'kecermatan')
                                detik / Kolom
                            @endif
                        </span>
                        untuk
                        mengerjakan, dan tidak bisa
                        diberhentikan
                        setelah anda memilih untuk melanjutkan.</p>
                    <p class="text-danger">*Pastikan anda memiliki koneksi internet yang bagus / cukup. Test Kecermatan
                        tidak bisa
                        diulang!</p>
                    <div class=" text-center">
                        <form action="{{ route('startUjian', ['token' => encrypt($ujian->id)]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary">Lanjutkan</button>
                            <a
                                href="{{ route('viewKelasMapel', ['mapel' => $mapel['id'], 'token' => encrypt($kelas['id']), 'mapel_id' => $mapel['id']]) }}">
                                <button type="button" class="btn btn-primary">Kembali</button>
                            </a>
                        </form>
                    </div>
                @endif
            </div>
        @else
            {{-- Box prompt --}}
            <div class="mb-4 p-4 bg-white rounded-4 text-center h-100">
                <h6 class="fw-bold display-6 text-primary">Ujian ditutup</h6>
                <p>Ujian telah ditutup.</p>
            </div>
        @endif

    @endif

@endsection
