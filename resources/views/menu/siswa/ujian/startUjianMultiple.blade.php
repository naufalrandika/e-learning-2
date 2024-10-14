@extends('layout.template.mainTemplate')

@section('container')
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Informasi Tugas --}}
    <div class="mb-4 p-4 bg-white rounded-4">
        <div class=" p-4">
            <h2 class="fw-bold mb-2 text-primary">{{ $ujian->name }}</h2>
            <hr>
            <div class="row">
                @php
                    $end_time = Carbon\Carbon::parse($userCommit->end_time);
                    $now = Carbon\Carbon::now();
                    if ($now > $end_time) {
                        $diffInSeconds = 0;
                    } else {
                        $diffInSeconds = $end_time->diffInSeconds($now);
                    }
                @endphp

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
                    {{ count($ujian->soalUjianMultiple) }}
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
                    <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Aliquid atque quos inventore nesciunt
                        voluptates ipsam molestias non quasi incidunt dolor!</p>
                </div>

                {{-- Jawaban --}}
                <form id="ujianForm">
                    <div class="rounded-2 mb-4 col-12">
                        <div class="rounded-2 mb-4 col-12">
                            <h6 class="text-primary fw-bold">Pilihan Jawaban</h6>
                            <div class="form-check flex flex-col">
                                <input class="form-check-input flex items-center" type="radio" name="jawaban" id="pilihan-a" value="A">
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
                        @foreach ($ujian->soalUjianMultiple as $index => $soal)
                            <button class="btn btn-outline-primary nomor-soal-btn"
                                data-soal="{{ $soal->id }}">{{ $index + 1 }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalSelesai">Selesai
                    Mengerjakan</button>
            </div>
        </div>

    </div>

    {{-- Modal --}}
    <div class="modal fade" id="modalSelesai" tabindex="-1" aria-labelledby="modalSelesai" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Konfirmasi Selesai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin mengakhiri ujian?
                </div>
                <div class="modal-footer">
                    <form action="{{ route('selesaiUjianMultiple') }}" method="post">
                        @csrf
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <input type="hidden" name="userCommit" id="userCommit"
                            value="{{ encrypt($userCommit['id']) }}">
                        {{-- <input type="hidden" name="idMateri" id="idMateri" value="{{ $materi['id'] }}"> --}}
                        <button type="submit" class="btn btn-danger">Selesai</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.tiny.cloud/1/jqqif5psx8ajdrpos129cpypqbqy3qmzk0lxwwxdu9s2lsn7/tinymce/6/tinymce.min.js"
    referrerpolicy="origin"></script>;

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
                        e.preventDefault();  // Mencegah interaksi keyboard
                    });
                    editor.on('cut', function (e) {
                        e.preventDefault();  // Mencegah aksi cut (potong konten)
                    });
                    editor.on('drop', function (e) {
                        e.preventDefault();  // Mencegah drop file atau konten
                    });
                }
        });

        // Variabel untuk melacak apakah pengguna telah menjawab setiap soal atau belum
        var jawabanTersimpan = [];
        var ujianForm = document.getElementById('ujianForm');
        var isTimeUp = false;
        // Fungsi untuk mengatur countdown
        function startCountdown(seconds) {
            var displayMinutes = document.getElementById('minutes');
            var displaySeconds = document.getElementById('seconds');
            var remainingSeconds = seconds;

            var countdown = setInterval(function() {
                if (remainingSeconds <= 0) {
                    clearInterval(countdown);
                    displayMinutes.textContent = 'Waktu Habis';
                    displaySeconds.textContent = '';
                    // Jika waktu habis, nonaktifkan elemen form
                    ujianForm.disabled = true;
                    // Nonaktifkan kotak input jawaban
                    isTimeUp = true;
                } else {
                    var minutes = Math.floor(remainingSeconds / 60);
                    var seconds = remainingSeconds % 60;
                    displayMinutes.textContent = minutes + ' menit';
                    displaySeconds.textContent = seconds + ' detik';
                    remainingSeconds--;
                    isTimeUp = false;
                    if (remainingSeconds < 0) {
                        clearInterval(countdown);
                        displayMinutes.textContent = 'Waktu Habis';
                        displaySeconds.textContent = '';
                        // Jika waktu habis, nonaktifkan elemen form
                        ujianForm.disabled = true;
                        // Nonaktifkan kotak input jawaban
                        isTimeUp = true;
                    }
                }
            }, 1000); // 1 detik
        }


        // Memulai countdown dengan waktu dari PHP (dalam detik)
        startCountdown({{ $diffInSeconds }});

        var soalUjianMultiple = @json($ujian->soalUjianMultiple);

        // Variabel untuk melacak nomor soal saat ini
        var currentSoal = 0;

        // Fungsi untuk menampilkan soal sesuai dengan nomor yang dipilih
        function tampilkanSoal(nomor) {
            if (nomor >= 1 && nomor <= soalUjianMultiple.length) {
                currentSoal = nomor;
                var selectedSoal = soalUjianMultiple[nomor - 1];
                // Tampilkan nomor soal dan teks soal
                // document.getElementById('soal-title').textContent = "Soal " + nomor;
                document.getElementById('soal-container').innerHTML = `
            <h1 class="text-primary fw-bold">Soal ${nomor}</h1>
            <hr>
            <p>${selectedSoal.soal}</p>
        `;
                // Cek apakah pengguna telah menjawab soal saat ini
                var soalId = soalUjianMultiple[nomor - 1].id;
                if (jawabanTersimpan[soalId]) {
                    // Jika pengguna telah menjawab, tandai tombol nomor soal sebagai "btn-primary"
                    document.querySelector(`button[data-soal="${soalId}"]`).classList.add('btn-primary');
                    document.querySelector(`button[data-soal="${soalId}"]`).classList.remove('btn-outline-primary');
                } else {
                    // Jika pengguna belum menjawab, tandai tombol nomor soal sebagai "btn-outline-primary"
                    document.querySelector(`button[data-soal="${soalId}"]`).classList.add('btn-outline-primary');
                    document.querySelector(`button[data-soal="${soalId}"]`).classList.remove('btn-primary');
                }

                // Setel label pilihan jawaban
                document.getElementById('label-pilihan-a').innerHTML = 
                `
                        ${selectedSoal.a}
                `;

                document.getElementById('label-pilihan-b').innerHTML = 
                `
                        ${selectedSoal.b}
                `;

                document.getElementById('label-pilihan-c').innerHTML = 
                `
                        ${selectedSoal.c}
                `;

                if (selectedSoal.d == null) {
                    // console.log('d = null');
                    // document.getElementById('label-pilihan-d').textContent = selectedSoal.d;
                    // document.getElementById('label-pilihan-d').classList.add('d-none');
                    document.getElementById('soal-d').classList.add('d-none');
                } else {
                    document.getElementById('label-pilihan-d').innerHTML =
                    `
                            ${selectedSoal.d}
                    `;
                    document.getElementById('soal-d').classList.remove('d-none');
                }
                if (selectedSoal.e == null) {
                    // console.log('d = null');
                    // document.getElementById('label-pilihan-d').textContent = selectedSoal.d;
                    // document.getElementById('label-pilihan-d').classList.add('d-none');
                    document.getElementById('soal-e').classList.add('d-none');
                } else {
                    document.getElementById('label-pilihan-e').innerHTML = 
                    `
                            ${selectedSoal.e}
                    `;
                    document.getElementById('soal-e').classList.remove('d-none');
                }


                // document.getElementById('label-pilihan-d').textContent = selectedSoal.d;
                document.getElementById('label-pilihan-e').innerHTML = 
                `
                        ${selectedSoal.e}
                `;

                // Reset pilihan jawaban
                document.querySelectorAll('input[name="jawaban"]').forEach(function(input) {
                    input.checked = false;
                    // Tambahkan atribut 'disabled' jika waktu habis
                    if (isTimeUp) {
                        input.disabled = true;
                    } else {
                        input.disabled = false;
                    }
                });
            }
        }

        // Inisialisasi tampilan soal pertama
        tampilkanSoal(1);
        getJawaban();

        // Tambahkan event listener untuk tombol "Next"
        document.getElementById('nextBtn').addEventListener('click', function() {
            if (currentSoal < soalUjianMultiple.length) {
                tampilkanSoal(currentSoal + 1);
                getJawaban();
                // Aktifkan tombol "Previous"
                document.getElementById('prevBtn').removeAttribute('disabled');
                console.log(currentSoal);
                console.log(soalUjianMultiple.length - 1);
                // Jika pengguna sekarang di soal terakhir, nonaktifkan tombol "Next"
                if (currentSoal > soalUjianMultiple.length - 1) {
                    document.getElementById('nextBtn').setAttribute('disabled', 'true');
                }
            }
            updateRaguRaguCheckbox(currentSoal - 1);
        });

        // Tambahkan event listener untuk tombol "Previous"
        document.getElementById('prevBtn').addEventListener('click', function() {
            tampilkanSoal(currentSoal - 1);
            getJawaban();
            if (currentSoal === 1) {
                // Nonaktifkan tombol "Previous" jika kembali ke soal pertama
                document.getElementById('nextBtn').removeAttribute('disabled');

                if (currentSoal <= soalUjianMultiple.length - 1) {
                    document.getElementById('prevBtn').setAttribute('disabled', 'true');
                }
            }
            updateRaguRaguCheckbox(currentSoal - 1);
        });

        function updateRaguRaguCheckbox(currentSoalId) {
            var raguRaguCheckbox = document.getElementById('raguRaguCheckbox');
            raguRaguCheckbox.setAttribute('data-soal', currentSoalId);
        }

        // Tambahkan event listener untuk tombol nomor soal
        var nomorSoalButtons = document.querySelectorAll('.nomor-soal-btn');
        nomorSoalButtons.forEach(function(button, index) {
            button.addEventListener('click', function() {
                tampilkanSoal(index + 1);

                // Nonaktifkan tombol "Previous" jika memilih soal dari nomor pertama
                if (index === 0) {
                    document.getElementById('prevBtn').setAttribute('disabled', 'true');
                } else {
                    // Aktifkan tombol "Previous"
                    document.getElementById('prevBtn').removeAttribute('disabled');
                }

                // Periksa jika pengguna berada di ujung nomor soal, maka nonaktifkan tombol "Next"
                if (index === soalUjianMultiple.length - 1) {
                    document.getElementById('nextBtn').setAttribute('disabled', 'true');
                } else {
                    // Aktifkan tombol "Next" jika bukan di ujung nomor soal
                    document.getElementById('nextBtn').removeAttribute('disabled');
                }

                // Periksa nomor soal saat ini dan mengambil jawaban dari database jika ada
                var currentSoalId = soalUjianMultiple[index].id;
                getJawaban(currentSoalId);

                // Mengatur warna tombol nomor soal
                // setButtonColors(index);
            });
        });




        // Tambahkan token CSRF ke header permintaan
        function getJawaban() {
            var soalId = soalUjianMultiple[currentSoal - 1].id;
            var data = {
                soal_id: soalId,
            };

            // Kirim permintaan AJAX untuk mengambil jawaban dari database
            $.ajax({
                type: 'GET',
                url: "{{ route('getJawabanMultiple') }}",
                data: data,
                success: function(response) {
                    console.log(soalId);
                    console.log(response.jawaban);

                    // Periksa apakah ada jawaban yang tersimpan untuk soal ini
                    if (response.jawaban) {
                        // Cari elemen radio button yang sesuai dengan jawaban yang diterima
                        var selectedRadioButton = $(`input[value='${response.jawaban}']`);

                        // Periksa apakah elemen radio button ditemukan
                        if (selectedRadioButton.length > 0) {
                            selectedRadioButton.prop('checked', true);
                        }

                        jawabanTersimpan[soalId] = true; // tandai soal sebagai telah dijawab
                    } else {
                        // Jika tidak ada jawaban tersimpan, reset elemen radio button
                        $("input[name='jawaban']").prop('checked', false);
                        jawabanTersimpan[soalId] = false; // tandai soal sebagai belum dijawab
                    }
                },
                error: function(xhr, status, error) {
                    // Tangani kesalahan jika terjadi
                    console.error(error);
                }
            });
        }

        $(document).ready(function() {
            // Cek jawaban yang telah disimpan di Local Storage
            for (var i = 0; i < soalUjianMultiple.length; i++) {
                var soalId = soalUjianMultiple[i].id;
                var storedJawaban = localStorage.getItem('jawaban_' + soalId);

                if (storedJawaban) {
                    // Jika jawaban telah tersimpan, tandai tombol sebagai "btn-primary"
                    var nomorSoalButton = $('#nomorSoalContainer button[data-soal="' + soalId + '"]');
                    nomorSoalButton.addClass('btn-primary');
                    nomorSoalButton.removeClass('btn-outline-primary');
                }
            }

            // Tambahkan event listener untuk elemen-elemen input dalam form
            var inputElements = document.querySelectorAll('.form-check-input');

            inputElements.forEach(function(inputElement) {
                inputElement.addEventListener('change', function(event) {
                    var selectedValue = event.target.value;
                    var soalId = soalUjianMultiple[currentSoal - 1].id;

                    // Periksa apakah waktu telah habis
                    var displayMinutes = document.getElementById('minutes');
                    var displaySeconds = document.getElementById('seconds');
                    var waktuHabis = displayMinutes.textContent === 'Waktu Habis' && displaySeconds
                        .textContent === '';

                    if (waktuHabis) {
                        // Jika waktu telah habis, tidak perbolehkan memilih jawaban
                        alert('Waktu ujian telah habis. Anda tidak dapat memilih jawaban lagi.');
                        // Kembalikan radio button ke status sebelumnya
                        event.target.checked = false;
                        return;
                    }

                    // Simpan jawaban di Local Storage
                    localStorage.setItem('jawaban_' + soalId, selectedValue);

                    // Tangai tombol nomor soal sebagai "btn-primary" jika ada jawaban
                    var nomorSoalButton = $('#nomorSoalContainer button[data-soal="' + soalId +
                        '"]');

                    if (selectedValue !== '') {
                        nomorSoalButton.addClass('btn-primary');
                        nomorSoalButton.removeClass('btn-outline-primary');
                    } else {
                        nomorSoalButton.addClass('btn-outline-primary');
                        nomorSoalButton.removeClass('btn-primary');
                    }

                    // Kirim jawaban yang dipilih ke server dengan AJAX (jika diperlukan)
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
                        url: '/simpan-jawaban-multiple', // Ganti dengan URL yang sesuai
                        data: data,
                        success: function(response) {
                            // Tangani respon dari server
                            console.log(response.message);
                            // Di sini Anda dapat menambahkan notifikasi atau tindakan lain sesuai kebutuhan.
                        },
                        error: function(xhr, status, error) {
                            // Tangani kesalahan jika terjadi
                            console.error(error);
                        }
                    });
                });
            });

        });
    </script>
@endsection
