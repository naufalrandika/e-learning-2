@extends('layout.template.mainTemplate')

@section('container')
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <style>
        #nomorSoalContainer {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(50px, 1fr));
    gap: 10px;
    justify-content: center;
}

.nomor-soal-btn {
    width: 50px;
    height: 50px;
    font-weight: bold;
    border-radius: 50%;
    text-align: center;
}

    </style>

    {{-- Acak urutan soal menggunakan metode shuffle() --}}
    @php
        // Acak soal sebelum ditampilkan
        $soalUjianMultiple = $ujian->soalUjianMultiple->shuffle();
        $totalSoal = count($soalUjianMultiple);
        $end_time = Carbon\Carbon::parse($userCommit->end_time);
        $now = Carbon\Carbon::now();
        if ($now > $end_time) {
            $diffInSeconds = 0;
        } else {
            $diffInSeconds = $end_time->diffInSeconds($now);
        }
    @endphp

    {{-- Informasi Tugas --}}
    <div class="mb-4 p-4 bg-white rounded-4">
        <div class="p-4">
            <h2 class="fw-bold mb-2 text-primary">{{ $ujian->name }}</h2>
            <hr>
            <div class="row">
                <div class="border p-3 fw-bold col-lg-3 col-12">
                    Deadline : <span class="badge badge-primary p-2">
                        {{ \Carbon\Carbon::parse($userCommit->end_time)->format('h:i A') }}
                    </span>
                </div>
                <div class="col-12 border p-3 col-lg-3">
                    <span class="fw-bold">Time : </span>
                    {{ $ujian->time }} Menit
                </div>
                <div class="border p-3 fw-bold col-lg-3 col-12">
                    <div id="countdown">
                        Waktu :
                        <span class="badge badge-danger p-2">
                            <span id="minutes">{{ floor($diffInSeconds / 60) }}</span>
                            <span id="seconds">{{ $diffInSeconds % 60 }}</span>
                        </span>
                    </div>
                </div>
                <div class="col-12 border p-3 col-lg-3">
                    <span class="fw-bold">Jumlah Soal :</span>
                    {{ $totalSoal }}
                </div>
            </div>
        </div>
    </div>
    <hr>

    {{-- Main Section --}}
    <div class="row">
        {{-- Question Section --}}
        <div class="col-lg-8 col-12">
            <div class="bg-white p-4 rounded-2 row">
                {{-- Soal --}}
                <div class="border border-primary rounded-2 p-4 mb-4 col-12" id="soal-container">
                    <h1 class="text-primary fw-bold" id="soal-title">Soal 1</h1>
                    <hr>
                    <p>Loading soal...!</p>
                </div>

                {{-- Jawaban --}}
                <form id="ujianForm">
                    <div class="rounded-2 mb-4 col-12">
                        <h6 class="text-primary fw-bold">Pilihan Jawaban</h6>
                        <div class="form-check flex flex-col">
                            <input class="form-check-input" type="radio" name="jawaban" id="pilihan-a" value="A">
                            <label class="form-check-label" for="pilihan-a">
                                A. <span class="tinymce" id="label-pilihan-a"></span>
                            </label>
                        </div>
                        <div class="form-check flex flex-col">
                            <input class="form-check-input" type="radio" name="jawaban" id="pilihan-b" value="B">
                            <label class="form-check-label" for="pilihan-b">
                                B. <span class="tinymce" id="label-pilihan-b"></span>
                            </label>
                        </div>
                        <div class="form-check flex flex-col">
                            <input class="form-check-input" type="radio" name="jawaban" id="pilihan-c" value="C">
                            <label class="form-check-label" for="pilihan-c">
                                C. <span class="tinymce" id="label-pilihan-c"></span>
                            </label>
                        </div>
                        <div class="form-check" id="soal-d">
                            <input class="form-check-input" type="radio" name="jawaban" id="pilihan-d" value="D">
                            <label class="form-check-label" for="pilihan-d">
                                D. <span class="tinymce" id="label-pilihan-d"></span>
                            </label>
                        </div>
                        <div class="form-check" id="soal-e">
                            <input class="form-check-input" type="radio" name="jawaban" id="pilihan-e" value="E">
                            <label class="form-check-label" for="pilihan-e">
                                E. <span class="tinymce" id="label-pilihan-e"></span>
                            </label>
                        </div>
                    </div>
                </form>

                {{-- Next and Prev --}}
                <div class="d-flex justify-content-between align-items-center col-12">
                    <button class="btn btn-primary" id="prevBtn" disabled>Previous</button>
                    <button class="btn btn-primary" id="nextBtn">Next</button>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="col-lg-4 col-12">
            <div class="bg-white p-4 rounded-2">
                <div class="border border-primary rounded-2 p-4">
                    <h5 class="text-primary fw-bold">Nomor Soal</h5>
                    <div class="border border-secondary p-4 rounded-2" id="nomorSoalContainer">
                        @foreach ($soalUjianMultiple as $index => $soal)
                            <button class="btn btn-outline-primary nomor-soal-btn"
                                data-soal="{{ $soal->id }}">{{ $index + 1 }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalSelesai">
                    Selesai Mengerjakan</button>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi --}}
    <div class="modal fade" id="modalSelesai" tabindex="-1" aria-labelledby="modalSelesai" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Selesai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin mengakhiri ujian?
                </div>
                <div class="modal-footer">
                    <form action="{{ route('selesaiUjianMultiple') }}" method="post">
                        @csrf
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <input type="hidden" name="userCommit" id="userCommit" value="{{ encrypt($userCommit['id']) }}">
                        <button type="submit" class="btn btn-danger">Selesai</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk rich text editor --}}
    <script src="https://cdn.tiny.cloud/1/jqqif5psx8ajdrpos129cpypqbqy3qmzk0lxwwxdu9s2lsn7/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script src="{{ url('/asset/js/rich-text-editor.js') }}"></script>

    <script>
        tinymce.init({
            selector: ".tinymce",
            inline: true,
            readonly: 1,
            toolbar: false,
            menubar: false,
            object_resizing: false,
            content_css: false,
            setup: function (editor) {
                editor.on('keydown', function (e) {
                    e.preventDefault();
                });
                editor.on('cut', function (e) {
                    e.preventDefault();
                });
                editor.on('drop', function (e) {
                    e.preventDefault();
                });
            }
        });

        // Variabel untuk menyimpan status jawaban per soal
        var jawabanTersimpan = [];
        var ujianForm = document.getElementById('ujianForm');
        var isTimeUp = false;

        // Fungsi countdown
        function startCountdown(seconds) {
            var displayMinutes = document.getElementById('minutes');
            var displaySeconds = document.getElementById('seconds');
            var remainingSeconds = seconds;

            var countdown = setInterval(function() {
                if (remainingSeconds <= 0) {
                    clearInterval(countdown);
                    displayMinutes.textContent = 'Waktu Habis';
                    displaySeconds.textContent = '';
                    ujianForm.disabled = true;
                    isTimeUp = true;
                } else {
                    var minutes = Math.floor(remainingSeconds / 60);
                    var seconds = remainingSeconds % 60;
                    displayMinutes.textContent = minutes + ' menit';
                    displaySeconds.textContent = seconds + ' detik';
                    remainingSeconds--;
                    isTimeUp = false;
                }
            }, 1000);
        }

        // Memulai countdown dengan waktu dari PHP (dalam detik)
        startCountdown({{ $diffInSeconds }});

        // Mengambil soal yang sudah diacak dari server
        var soalUjianMultiple = @json($soalUjianMultiple);
        // Variabel untuk melacak soal saat ini (1-indexed)
        var currentSoal = 0;

        // Fungsi untuk menampilkan soal
        function tampilkanSoal(nomor) {
            if (nomor >= 1 && nomor <= soalUjianMultiple.length) {
                currentSoal = nomor;
                var selectedSoal = soalUjianMultiple[nomor - 1];
                document.getElementById('soal-container').innerHTML = `
                    <h1 class="text-primary fw-bold">Soal ${nomor}</h1>
                    <hr>
                    <p>${selectedSoal.soal}</p>
                `;
                var soalId = selectedSoal.id;
                if (jawabanTersimpan[soalId]) {
                    document.querySelector(`button[data-soal="${soalId}"]`).classList.add('btn-primary');
                    document.querySelector(`button[data-soal="${soalId}"]`).classList.remove('btn-outline-primary');
                } else {
                    document.querySelector(`button[data-soal="${soalId}"]`).classList.add('btn-outline-primary');
                    document.querySelector(`button[data-soal="${soalId}"]`).classList.remove('btn-primary');
                }

                // Tampilkan pilihan jawaban
                document.getElementById('label-pilihan-a').innerHTML = selectedSoal.a;
                document.getElementById('label-pilihan-b').innerHTML = selectedSoal.b;
                document.getElementById('label-pilihan-c').innerHTML = selectedSoal.c;

                if (selectedSoal.d == null) {
                    document.getElementById('soal-d').classList.add('d-none');
                } else {
                    document.getElementById('label-pilihan-d').innerHTML = selectedSoal.d;
                    document.getElementById('soal-d').classList.remove('d-none');
                }
                if (selectedSoal.e == null) {
                    document.getElementById('soal-e').classList.add('d-none');
                } else {
                    document.getElementById('label-pilihan-e').innerHTML = selectedSoal.e;
                    document.getElementById('soal-e').classList.remove('d-none');
                }

                // Reset pilihan jawaban dan atur disable jika waktu habis
                document.querySelectorAll('input[name="jawaban"]').forEach(function(input) {
                    input.checked = false;
                    input.disabled = isTimeUp;
                });
            }
        }

        // Inisialisasi tampilan soal pertama
        tampilkanSoal(1);
        getJawaban();

        // Event listener untuk tombol Next dan Previous
        document.getElementById('nextBtn').addEventListener('click', function() {
            if (currentSoal < soalUjianMultiple.length) {
                tampilkanSoal(currentSoal + 1);
                getJawaban();
                document.getElementById('prevBtn').removeAttribute('disabled');
                if (currentSoal >= soalUjianMultiple.length) {
                    document.getElementById('nextBtn').setAttribute('disabled', 'true');
                }
            }
        });

        document.getElementById('prevBtn').addEventListener('click', function() {
            tampilkanSoal(currentSoal - 1);
            getJawaban();
            if (currentSoal === 1) {
                document.getElementById('prevBtn').setAttribute('disabled', 'true');
            }
        });

        // Event listener untuk tombol navigasi nomor soal
        var nomorSoalButtons = document.querySelectorAll('.nomor-soal-btn');
        nomorSoalButtons.forEach(function(button, index) {
            button.addEventListener('click', function() {
                tampilkanSoal(index + 1);
                if (index === 0) {
                    document.getElementById('prevBtn').setAttribute('disabled', 'true');
                } else {
                    document.getElementById('prevBtn').removeAttribute('disabled');
                }
                if (index === soalUjianMultiple.length - 1) {
                    document.getElementById('nextBtn').setAttribute('disabled', 'true');
                } else {
                    document.getElementById('nextBtn').removeAttribute('disabled');
                }
                getJawaban();
            });
        });

        // Fungsi AJAX untuk mengambil jawaban dari server
        function getJawaban() {
            var soalId = soalUjianMultiple[currentSoal - 1].id;
            var data = {
                soal_id: soalId,
            };

            $.ajax({
                type: 'GET',
                url: "{{ route('getJawabanMultiple') }}",
                data: data,
                success: function(response) {
                    if (response.jawaban) {
                        var selectedRadioButton = $(`input[value='${response.jawaban}']`);
                        if (selectedRadioButton.length > 0) {
                            selectedRadioButton.prop('checked', true);
                        }
                        jawabanTersimpan[soalId] = true;
                    } else {
                        $("input[name='jawaban']").prop('checked', false);
                        jawabanTersimpan[soalId] = false;
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        // Event listener untuk menyimpan jawaban ketika input berubah
        $(document).ready(function() {
            // Tandai soal yang sudah terjawab dari localStorage (jika ada)
            for (var i = 0; i < soalUjianMultiple.length; i++) {
                var soalId = soalUjianMultiple[i].id;
                var storedJawaban = localStorage.getItem('jawaban_' + soalId);
                if (storedJawaban) {
                    var nomorSoalButton = $('#nomorSoalContainer button[data-soal="' + soalId + '"]');
                    nomorSoalButton.addClass('btn-primary');
                    nomorSoalButton.removeClass('btn-outline-primary');
                }
            }
            
            // Hapus semua key localStorage yang berawalan 'jawaban_'
    Object.keys(localStorage).forEach(function(key) {
        if (key.indexOf('jawaban_') === 0) {
            localStorage.removeItem(key);
        }
    });

            var inputElements = document.querySelectorAll('.form-check-input');
            inputElements.forEach(function(inputElement) {
                inputElement.addEventListener('change', function(event) {
                    var selectedValue = event.target.value;
                    var soalId = soalUjianMultiple[currentSoal - 1].id;
                    var displayMinutes = document.getElementById('minutes');
                    var displaySeconds = document.getElementById('seconds');
                    var waktuHabis = displayMinutes.textContent === 'Waktu Habis' && displaySeconds.textContent === '';

                    if (waktuHabis) {
                        alert('Waktu ujian telah habis. Anda tidak dapat memilih jawaban lagi.');
                        event.target.checked = false;
                        return;
                    }

                    localStorage.setItem('jawaban_' + soalId, selectedValue);

                    var nomorSoalButton = $('#nomorSoalContainer button[data-soal="' + soalId + '"]');
                    if (selectedValue !== '') {
                        nomorSoalButton.addClass('btn-primary');
                        nomorSoalButton.removeClass('btn-outline-primary');
                    } else {
                        nomorSoalButton.addClass('btn-outline-primary');
                        nomorSoalButton.removeClass('btn-primary');
                    }

                    var data = {
                        soal_id: soalId,
                        jawaban: selectedValue,
                    };

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    });

                    $.ajax({
                        type: 'POST',
                        url: '/simpan-jawaban-multiple',
                        data: data,
                        success: function(response) {
                            console.log(response.message);
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                });
            });
        });
        
    </script>
@endsection