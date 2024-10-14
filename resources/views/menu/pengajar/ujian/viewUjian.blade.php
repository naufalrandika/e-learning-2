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
                        {{ $ujian->time }}
                        @if ($tipe == 'quiz')
                            detik / soal
                        @else
                            Menit
                        @endif
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
                            {{ $ujian->time }} @if ($tipe == 'kecermatan')
                                detik / soal
                            @else
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
    {{-- Baris utama --}}
    <div class="col-12 ps-4 pe-4 mb-4">
        <div class="row">
            @if ($ujian->tipe == 'essay')
                <form action="{{ route('ujianUpdateNilai') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ encrypt($ujian['id']) }}">
                    @foreach ($ujian->SoalUjianEssay as $soal)
                        <div class="col-lg-12 col-12 bg-white rounded-2 mb-4 p-4 ">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class=" border border border-primary shadow-sm  p-4 mt-4 pertanyaan">
                                        <div class="">
                                            <h3>Soal <span class="badge badge-primary">{{ $loop->iteration }}</span>
                                            </h3>
                                            <div class="mb-3">
                                                <label for="pertanyaan${nomorPertanyaan}"
                                                    class="form-label">Pertanyaan</label>
                                                <div class="border border-secondary p-4 rounded-2"
                                                    id="pertanyaan${nomorPertanyaan}" name="pertanyaan[]" rows="3"
                                                    disabled>{!! $soal->soal !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="accordion mb-4 mt-4" id="ujian{{ $loop->iteration }}">
                                        <div class="accordion-item ">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button bg-outline-danger  collapsed fw-bold"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#ujian{{ $loop->iteration }}-collapseOne"
                                                    aria-controls="ujian{{ $loop->iteration }}-collapseOne">
                                                    Submittion Siswa
                                                </button>
                                            </h2>
                                            <div id="ujian{{ $loop->iteration }}-collapseOne"
                                                class="accordion-collapse collapse">
                                                <div class="accordion-body table-responsive"
                                                    style="max-height: 300px; overflow-y: auto;">
                                                    <table id="table" class="table table-striped table-hover table-lg ">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">#</th>
                                                                <th scope="col">Nama</th>
                                                                <th scope="col">Submittion</th>
                                                                <th scope="col">Nilai</th>
                                                                <th scope="col">Input Nilai</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            @foreach ($kelas->User as $key)
                                                                @php
                                                                    // Mencari UserTugas sesuai dengan ID tugas yang Anda inginkan
                                                                    $userTugas = $key->UserJawaban
                                                                        ->where('essay_id', $soal->id)
                                                                        ->first();
                                                                    $submition = App\Models\UserJawaban::where(
                                                                        'user_id',
                                                                        $key->id,
                                                                    )
                                                                        ->where('essay_id', $soal->id)
                                                                        ->first();
                                                                    $nilai =
                                                                        $userTugas && is_numeric($userTugas->nilai)
                                                                            ? intval($userTugas->nilai)
                                                                            : null;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $key->name }}</td>
                                                                    <td>
                                                                        @if ($submition)
                                                                            {{ $submition['user_jawaban'] }}
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($userTugas)
                                                                            @if ($nilai !== null && $nilai >= 0)
                                                                                {{ $nilai }}
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <input type="hidden" name="siswaId[]"
                                                                        value="{{ $key->id }}">
                                                                    <input type="hidden" name="soalId[]"
                                                                        value="{{ $soal->id }}">
                                                                    @if ($tipe == 'essay')
                                                                        <td class="w-25">
                                                                            <input type="number" class="form-control w-100"
                                                                                placeholder="-" aria-label="nilai"
                                                                                name="nilai[]"
                                                                                value="{{ $nilai !== null ? $nilai : '' }}">
                                                                        </td>
                                                                    @endif
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
                        </div>
                        <hr>
                    @endforeach
                    <button class="btn btn-primary btn-lg w-100" type="submit">Simpan</button>
                </form>
            @elseif($ujian->tipe == 'multiple')
                @foreach ($ujian->SoalUjianMultiple as $soal)
                    <div class="col-lg-12 col-12 bg-white rounded-2 mb-4 p-4 ">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-6">
                                        <div class=" border border border-primary shadow-sm  p-4 mt-4 pertanyaan">
                                            <div class="">
                                                <h3>Soal <span class="badge badge-primary">{{ $loop->iteration }}</span>
                                                </h3>
                                                <div class="mb-3">
                                                    <label for="pertanyaan${nomorPertanyaan}"
                                                        class="form-label">Pertanyaan</label>
                                                    <div class="border border-secondary p-4 rounded-2"
                                                        id="pertanyaan${nomorPertanyaan}" name="pertanyaan[]"
                                                        rows="3" disabled>
                                                        {!! $soal->soal !!}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row">
                                            <div class="col-6 mb-1">
                                                <div class="flex flex-row">
                                                    <label class="form-label">A</label>
                                                    @if($soal->jawaban == 'a')
                                                        <span style="font-size; color: green">
                                                            <i class="fa fa-check-square " aria-hidden="true"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                                {{-- <input type="text"
                                                    class="form-control @if ($soal->jawaban == 'a') text-white fw-bold bg-success @endif"
                                                    disabled value="{{ $soal->a }}"> --}}
                                                <textarea class="tinymce form-control" id="" name="a[]" rows="2" disabled>{!! $soal->a !!}</textarea>
                                            </div>
                                            <div class="col-6 mb-1">
                                                <div class="flex flex-row">
                                                    <label class="form-label">B</label>
                                                    @if($soal->jawaban == 'b')
                                                        <span style="font-size; color: green">
                                                            <i class="fa fa-check-square " aria-hidden="true"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                                {{-- <input type="text"
                                                    class="form-control @if ($soal->jawaban == 'a') text-white fw-bold bg-success @endif"
                                                    disabled value="{{ $soal->a }}"> --}}
                                                <textarea class="tinymce form-control" id="" name="b[]" rows="2" disabled>{!! $soal->b !!}</textarea>
                                            </div>
                                            <div class="col-6 mb-1">
                                                <div class="flex flex-row">
                                                    <label class="form-label">C</label>
                                                    @if($soal->jawaban == 'c')
                                                        <span style="font-size; color: green">
                                                            <i class="fa fa-check-square " aria-hidden="true"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                                {{-- <input type="text"
                                                    class="form-control @if ($soal->jawaban == 'a') text-white fw-bold bg-success @endif"
                                                    disabled value="{{ $soal->a }}"> --}}
                                                <textarea class="tinymce form-control" id="" name="c[]" rows="2" disabled>{!! $soal->c !!}</textarea>
                                            </div>
                                            @if ($soal->d)
                                                <div class="col-6 mb-1">
                                                    <div class="flex flex-row">
                                                        <label class="form-label">D</label>
                                                        @if($soal->jawaban == 'd')
                                                            <span style="font-size; color: green">
                                                                <i class="fa fa-check-square " aria-hidden="true"></i>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    {{-- <input type="text"
                                                        class="form-control @if ($soal->jawaban == 'a') text-white fw-bold bg-success @endif"
                                                        disabled value="{{ $soal->a }}"> --}}
                                                    <textarea class="tinymce form-control" id="" name="d[]" rows="2" disabled>{!! $soal->d !!}</textarea>
                                                </div>
                                            @endif
                                            @if ($soal->e)
                                                <div class="col-6 mb-1">
                                                    <div class="flex flex-row">
                                                        <label class="form-label">E</label>
                                                        @if($soal->jawaban == 'e')
                                                            <span style="font-size; color: green">
                                                                <i class="fa fa-check-square " aria-hidden="true"></i>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    {{-- <input type="text"
                                                        class="form-control @if ($soal->jawaban == 'a') text-white fw-bold bg-success @endif"
                                                        disabled value="{{ $soal->a }}"> --}}
                                                    <textarea class="tinymce form-control" id="" name="e[]" rows="2" disabled>{!! $soal->e !!}</textarea>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-12">
                                <div class="accordion mb-4 mt-4" id="ujian{{ $loop->iteration }}">
                                    <div class="accordion-item ">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button bg-outline-danger  collapsed fw-bold"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#ujian{{ $loop->iteration }}-collapseOne"
                                                aria-controls="ujian{{ $loop->iteration }}-collapseOne">
                                                Submittion Siswa
                                            </button>
                                        </h2>

                                        <div id="ujian{{ $loop->iteration }}-collapseOne"
                                            class="accordion-collapse collapse">
                                            <div class="accordion-body table-responsive"
                                                style="max-height: 300px; overflow-y: auto;">
                                                <table id="table" class="table table-striped table-hover table-lg ">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">Nama</th>
                                                            <th scope="col">Submittion</th>
                                                            <th scope="col">Nilai</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($kelas->User as $key)
                                                            @php
                                                                // Mencari UserTugas sesuai dengan ID tugas yang Anda inginkan
                                                                $userTugas = $key->UserJawaban
                                                                    ->where('multiple_id', $soal->id)
                                                                    ->first();
                                                                $submition = App\Models\UserJawaban::where(
                                                                    'user_id',
                                                                    $key->id,
                                                                )
                                                                    ->where('multiple_id', $soal->id)
                                                                    ->first();
                                                                $nilai =
                                                                    $userTugas && is_numeric($userTugas->nilai)
                                                                        ? intval($userTugas->nilai)
                                                                        : null;
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $key->name }}</td>
                                                                <td>
                                                                    @if ($submition)
                                                                        {{ $submition['user_jawaban'] }}.
                                                                        @if ($submition['user_jawaban'] == 'A')
                                                                            {{ $soal->a }}
                                                                        @elseif ($submition['user_jawaban'] == 'B')
                                                                            {{ $soal->b }}
                                                                        @elseif ($submition['user_jawaban'] == 'C')
                                                                            {{ $soal->c }}
                                                                        @elseif ($submition['user_jawaban'] == 'D')
                                                                            {{ $soal->d }}
                                                                        @elseif ($submition['user_jawaban'] == 'E')
                                                                            {{ $soal->e }}
                                                                        @endif
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{ $nilai }}
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
                    </div>
                    <hr>
                @endforeach
            @elseif($ujian->tipe == 'kecermatan')
                @foreach ($ujian->Kecermatan as $soal)
                    <div class="col-lg-12 col-12 bg-white rounded-2 mb-4 p-4 ">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-6">
                                        <div class=" border border border-primary shadow-sm  p-4 mt-4 pertanyaan">
                                            <div class="">
                                                <h3>Kolom <span class="badge badge-primary">{{ $loop->iteration }}</span>
                                                </h3>
                                                <div class="mb-3">
                                                    <label for="pertanyaan${nomorPertanyaan}"
                                                        class="form-label">Pertanyaan</label>
                                                    <div class="border border-secondary p-4 rounded-2"
                                                        id="pert anyaan${nomorPertanyaan}" name="pertanyaan[]"
                                                        rows="3" disabled>
                                                        {{ $soal->a }}
                                                        {{ $soal->b }}
                                                        {{ $soal->c }}
                                                        @if ($soal->d)
                                                            {{ $soal->d }}
                                                        @endif
                                                        @if ($soal->e)
                                                            {{ $soal->e }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row">
                                            <div class="col-6 mb-1">
                                                <label class="form-label">A
                                                </label>
                                                <input type="text"
                                                    class="form-control @if ($soal->jawaban == 'a') text-white fw-bold bg-success @endif"
                                                    disabled value="{{ $soal->a }}">
                                            </div>
                                            <div class="col-6 mb-1">
                                                <label class="form-label">B
                                                </label>
                                                <input type="text"
                                                    class="form-control @if ($soal->jawaban == 'b') text-white fw-bold bg-success @endif"
                                                    disabled value="{{ $soal->b }}">
                                            </div>
                                            <div class="col-6 mb-1">
                                                <label class="form-label">C
                                                </label>
                                                <input type="text"
                                                    class="form-control @if ($soal->jawaban == 'c') text-white fw-bold bg-success @endif"
                                                    disabled value="{{ $soal->c }}">
                                            </div>
                                            @if ($soal->d)
                                                <div class="col-6 mb-1">
                                                    <label class="form-label">D
                                                    </label>
                                                    <input type="text"
                                                        class="form-control @if ($soal->jawaban == 'd') text-white fw-bold bg-success @endif"
                                                        disabled value="{{ $soal->d }}">
                                                </div>
                                            @endif
                                            @if ($soal->e)
                                                <div class="col-6 mb-1">
                                                    <label class="form-label">E
                                                    </label>
                                                    <input type="text"
                                                        class="form-control @if ($soal->jawaban == 'e') text-white fw-bold bg-success @endif"
                                                        disabled value="{{ $soal->e }}">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-12">
                                <div class="accordion mb-4 mt-4" id="ujian{{ $loop->iteration }}">
                                    <div class="accordion-item ">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button bg-outline-danger  collapsed fw-bold"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#ujian{{ $loop->iteration }}-collapseOne"
                                                aria-controls="ujian{{ $loop->iteration }}-collapseOne">
                                                Submittion Siswa
                                            </button>
                                        </h2>

                                        <div id="ujian{{ $loop->iteration }}-collapseOne"
                                            class="accordion-collapse collapse">
                                            <div class="accordion-body table-responsive"
                                                style="max-height: 300px; overflow-y: auto;">
                                                <table id="table" class="table table-striped table-hover table-lg ">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">Nama</th>
                                                            <th scope="col">Benar</th>
                                                            <th scope="col">Salah</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($kelas->User as $key)
                                                            @php
                                                                // Mencari UserTugas sesuai dengan ID tugas yang Anda inginkan
                                                                $userTugas = $key->UserJawabanKecermatan
                                                                    ->where('kecermatan_id', $soal->id)
                                                                    ->first();
                                                                $submition = App\Models\UserJawabanKecermatan::where(
                                                                    'user_id',
                                                                    $key->id,
                                                                )
                                                                    ->where('kecermatan_id', $soal->id)
                                                                    ->get();
                                                                $benar = 0;
                                                                $salah = 0;
                                                                foreach ($submition as $key2) {
                                                                    if (
                                                                        strtolower($key2->jawaban_user) ==
                                                                        $key2->jawaban
                                                                    ) {
                                                                        $benar++;
                                                                    } else {
                                                                        $salah++;
                                                                    }
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $key->name }}</td>
                                                                <td>
                                                                    {{ $benar }}
                                                                </td>
                                                                <td>
                                                                    {{ $salah }}
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
                    </div>
                    <hr>
                @endforeach
            @endif

        </div>
    </div>

    {{-- Tombol Submit --}}

    <script src="https://cdn.tiny.cloud/1/jqqif5psx8ajdrpos129cpypqbqy3qmzk0lxwwxdu9s2lsn7/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>;

    <script src="{{ url('/asset/js/rich-text-editor.js') }}"></script>

    <script>
        $(document).ready(function() {
            tinymce.remove('.tinymce');
            tinymce.init({
                selector: ".tinymce",
                toolsbar: false,
                menubar: false,
                content_css: false
            });
            
            // Aktifkan date picker dengan format tanggal dan jam
            $(function() {
                $('#due').datetimepicker({
                    format: 'Y-m-d H:i',
                    locale: 'id',
                });
            });


            // Tombol Tambah Pertanyaan diklik
            $('#btnTambahPertanyaan').click(function() {
                // Mengambil jumlah pertanyaan saat ini
                const jumlahPertanyaan = $('.pertanyaan').length;

                // Membuat nomor pertanyaan yang akan digunakan
                const nomorPertanyaan = jumlahPertanyaan + 1;
                @if ($tipe == 'essay')
                    // Buat formulir pertanyaan baru Essay
                    const formulirPertanyaanBaru = `
                 <div class="bg-white border border-dark-subtle rounded-2 p-4 mt-4 pertanyaan">
                  <div class="">
                                <h3>Soal <span class="badge badge-primary">${nomorPertanyaan}</span>
                                      <button type="button" class="btn btn-outline-danger btnKurangi">X</button>
                                </h3>
                                <div class="mb-3">
                                    <label for="pertanyaan${nomorPertanyaan}" class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="pertanyaan${nomorPertanyaan}" name="pertanyaan[]" rows="3" required></textarea>
                                </div>
                            </div>
                            </div>
            `;
                @elseif ($tipe == 'multiple')
                    // Buat formulir pertanyaan baru Multiple
                    const formulirPertanyaanBaru = `
                 <div class="bg-white border border-dark-subtle rounded-2 p-4 mt-4 pertanyaan">
                                    <div class="">
                                        <h3>Soal <span class="badge badge-primary">${nomorPertanyaan}</span>
                                            <button type="button" class="btn btn-outline-danger btnKurangi">X</button>
                                        </h3>
                                        <div class="mb-3 row">
                                            <div class="col-lg-7 col-12">
                                                <label for="pertanyaan${nomorPertanyaan}"
                                                    class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                                                <textarea class="form-control" id="pertanyaan${nomorPertanyaan}" name="pertanyaan[]" rows="3" required></textarea>
                                            </div>
                                            <div class="col-lg-5 col-12 row">
                                                <div class="col-5 m-1">
                                                    <label for="pertanyaan${nomorPertanyaan}" class="form-label">A
                                                          <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="a[]" required
                                                        id="">
                                                </div>
                                                <div class="col-5 m-1">
                                                    <label for="pertanyaan${nomorPertanyaan}" class="form-label">B
                                                          <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="b[]" required
                                                        id="">
                                                </div>
                                                <div class="col-5 m-1">
                                                    <label for="pertanyaan${nomorPertanyaan}" class="form-label">C
                                                        <span class="text-danger">*</span>
                                                        </label>
                                                    <input type="text" class="form-control" name="c[]" required
                                                        id="">
                                                </div>
                                                <div class="col-5 m-1">
                                                    <label for="pertanyaan${nomorPertanyaan}" class="form-label">D <span class="text-secondary small">(opsi)</span></label>
                                                    <input type="text" class="form-control" name="d[]"
                                                        id="">
                                                </div>
                                                <div class="col-5 m-1">
                                                    <label for="pertanyaan${nomorPertanyaan}" class="form-label">E <span class="text-secondary small">(opsi)</span></label>
                                                    <input type="text" class="form-control" name="e[]"
                                                        id="">
                                                </div>
                                                <div class="col-5 m-1">
                                                    <label for="pertanyaan${nomorPertanyaan}"
                                                        class="form-label text-primary fw-bold">Jawaban</label>
                                                    <select name="jawaban[]" class="form-select" id="">
                                                        <option value="a">A</option>
                                                        <option value="b">B</option>
                                                        <option value="c">C</option>
                                                        <option value="d">D</option>
                                                        <option value="e">E</option>
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
            `;
                @else
                    const formulirPertanyaanBaru = `
                 <div class="bg-white border border-dark-subtle rounded-2 p-4 mt-4 pertanyaan">
                                    <div class="">
                                        <h3>Soal <span class="badge badge-primary">${nomorPertanyaan}</span>
                                            <button type="button" class="btn btn-outline-danger btnKurangi">X</button>
                                        </h3>
                                        <div class="mb-3 row">

                                                <div class="col-5 m-1">
                                                    <label for="pertanyaan${nomorPertanyaan}" class="form-label">A
                                                          <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="a[]" required
                                                        id="">
                                                </div>
                                                <div class="col-5 m-1">
                                                    <label for="pertanyaan${nomorPertanyaan}" class="form-label">B
                                                          <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="b[]" required
                                                        id="">
                                                </div>
                                                <div class="col-5 m-1">
                                                    <label for="pertanyaan${nomorPertanyaan}" class="form-label">C
                                                        <span class="text-danger">*</span>
                                                        </label>
                                                    <input type="text" class="form-control" name="c[]" required
                                                        id="">
                                                </div>
                                                <div class="col-5 m-1">
                                                    <label for="pertanyaan${nomorPertanyaan}" class="form-label">D <span class="text-secondary small">(opsi)</span></label>
                                                    <input type="text" class="form-control" name="d[]"
                                                        id="">
                                                </div>
                                                <div class="col-5 m-1">
                                                    <label for="pertanyaan${nomorPertanyaan}" class="form-label">E <span class="text-secondary small">(opsi)</span></label>
                                                    <input type="text" class="form-control" name="e[]"
                                                        id="">
                                                </div>
                                                <div class="col-5 m-1">
                                                    <label for="jumlah${nomorPertanyaan}" class="form-label">Jumlah Soal<span class="text-secondary small"></span></label>
                                                    <input type="text" class="form-control" name="jumlahSoal[]"
                                                        id="">
                                                </div>



                                        </div>
                                    </div>
                                </div>
            `;
                @endif


                // Tambahkan formulir pertanyaan baru ke dalam container
                $('#containerPertanyaan').append(formulirPertanyaanBaru);

                // Aktifkan tombol Kurangi pada pertanyaan sebelumnya (jika ada)
                $('.pertanyaan:last').prev().find('.btnKurangi').show();
            });


            // Tombol Kurangi diklik
            $('#containerPertanyaan').on('click', '.btnKurangi', function() {
                // Hapus formulir pertanyaan yang terkait
                $(this).closest('.pertanyaan').remove();

                // Update nomor pertanyaan pada pertanyaan yang tersisa
                $('.pertanyaan').each(function(index) {
                    `Soal ${index + 1} <span class="badge badge-secondary">${nomorPertanyaan}</span> <button type="button" class="btn btn-outline-danger btnKurangi">X</button>`
                });

                // Sembunyikan tombol Kurangi jika hanya ada satu pertanyaan
                if ($('.pertanyaan').length === 1) {
                    $('.pertanyaan .btnKurangi').hide();
                }
            });
        });
    </script>
@endsection
