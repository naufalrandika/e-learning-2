@extends('layout.template.mainTemplate')

@section('container')
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Main Section --}}
    <div class="row">
        {{-- Question Section --}}
        <div class="col-lg-12 col-12">
            <div class="bg-white p-4 rounded-2 row" id="question-section">
                <div class="alert alert-secondary" role="alert">
                    Jangan <strong>Refresh / Meninggalkan</strong> Ujian Kecermatan ini!. Ujian Kecermatan tidak bisa
                    diulang!.
                </div>
                <div class="text-center mb-3">
                    <span class="badge badge-danger p-2 fs-2 rounded" id="question-seconds">{{ $ujian->time }}</span>
                </div>
                {{-- Kolom --}}
                <div class="border border-primary rounded-2 p-4 mb-4 col-12" id="kolom-container">
                    <h1 class="text-primary fw-bold text-center" id="kolom-title">Kolom</h1>
                    <hr>
                    <div class="d-flex justify-content-center">
                        <div class="text-center p-4 border  d-none" id="kolom-a">
                            <h1 id="kolom-text-a" class="mb-2"></h1>
                            <h3>A</h3>
                        </div>
                        <div class="text-center p-4 border  d-none" id="kolom-b">
                            <h1 id="kolom-text-b" class="mb-2"></h1>
                            <h3>B</h3>
                        </div>
                        <div class="text-center p-4 border  d-none" id="kolom-c">
                            <h1 id="kolom-text-c" class="mb-2"></h1>
                            <h3>C</h3>
                        </div>
                        <div class="text-center p-4 border  d-none" id="kolom-d">
                            <h1 id="kolom-text-d" class="mb-2"></h1>
                            <h3>D</h3>
                        </div>
                        <div class="text-center p-4 border  d-none" id="kolom-e">
                            <h1 id="kolom-text-e" class="mb-2"></h1>
                            <h3>E</h3>
                        </div>
                    </div>
                </div>
                {{-- Soal --}}
                <div class="border border-primary rounded-2 p-4 mb-4 col-12" id="soal-container">
                    <h1 class="text-primary fw-bold text-center" id="soal-title">Soal 1</h1>
                    <hr>
                    <div class="text-center" id="soal-text"></div>
                </div>



                {{-- Jawaban --}}
                <form id="ujianForm">
                    <div class="rounded-2 mb-4 col-12">
                        <div class="rounded-2 mb-4 col-12">
                            <h2 class="text-primary fw-bold">Pilihan Jawaban</h2>
                            <hr>
                            <div class="row justify-content-center" id="jawaban-container"
                                style="display: flex; justify-content: space-between;">
                                @foreach (['a', 'b', 'c', 'd', 'e'] as $letter)
                                    <div class="col-lg-2 col-md-2 col-sm-12 form-check mb-2" id="soal-{{ $letter }}"
                                        style="text-align: center;">
                                        <input class="form-check-input" type="radio" name="jawaban"
                                            id="pilihan-{{ $letter }}" value="{{ strtoupper($letter) }}"
                                            style="display: none;">
                                        <label class="form-check-label w-100 btn" for="pilihan-{{ $letter }}"
                                            style="display: block; background-color: white; color: black; border: 1px solid #a22020; padding: 15px; border-radius: 8px;">
                                            {{ strtoupper($letter) }}. <span id="label-pilihan-{{ $letter }}"></span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="col-lg-4 col-12">
            <div class="mt-4">
                {{-- Navigation content here --}}
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
                    <form action="{{ route('selesaiUjianKecermatan') }}" method="post">
                        @csrf
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <input type="hidden" name="userCommit" id="userCommit" value="{{ encrypt($userCommit['id']) }}">
                        <button type="submit" class="btn btn-danger">Selesai</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4 p-4 bg-white rounded-4">
        <div class="p-4">
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
                        Waktu Total :
                        <span class="badge badge-danger p-2">
                            <span id="total-seconds">{{ $diffInSeconds }}</span>
                            <span id="" class="badge badge-danger">{{ $ujian->time }} detik / soal</span>
                        </span>
                    </div>
                </div>

                <div class="col-12 border p-3 col-lg-3">
                    <span class="fw-bold">Jumlah Soal :</span>
                    {{ count($ujian->soalUjianMultiple) }}
                    <button class="btn btn-outline-danger w-100" data-bs-toggle="modal"
                        data-bs-target="#modalSelesai">Selesai
                        Mengerjakan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var soalIndex = 0;
        var soalData = @json($userJawabanKecermatan); // Pass UserJawabanKecermatan data from controller
        var kecermatanData = @json($ujian->Kecermatan); // Pass Kecermatan data from controller
        var totalCountdown;
        var questionCountdown;
        var isTimeUp = false;
        var currentKecermatanIndex = 0;
        var currentKecermatan = kecermatanData[currentKecermatanIndex];
        var questionsAnswered = 0;

        function getQuestionsForCurrentKecermatan() {
            return soalData.filter(q => q.kecermatan_id === currentKecermatan.id);
        }

        var currentQuestions = getQuestionsForCurrentKecermatan();
        var totalQuestions = currentQuestions.length;

        function displayQuestion(index) {
            if (index < totalQuestions) {
                var currentQuestion = currentQuestions[index];
                document.getElementById('soal-title').textContent = `Soal ${questionsAnswered + 1}`;
                document.getElementById('soal-text').innerHTML = `<h1 class='fw-bold display-1'>` + currentQuestion.soal +
                    `</h1>`;
                document.getElementById('kolom-title').textContent = `Kolom ke-${currentKecermatanIndex + 1}`;

                // Display ABCDE from currentKecermatan in kolom-text
                ['a', 'b', 'c', 'd', 'e'].forEach(function(letter) {
                    var kolomTextElement = document.getElementById('kolom-text-' + letter);
                    var kolomContainer = document.getElementById('kolom-' + letter);

                    if (currentKecermatan[letter]) {
                        kolomTextElement.textContent = currentKecermatan[letter];
                        kolomContainer.classList.remove('d-none');
                    } else {
                        kolomContainer.classList.add('d-none');
                    }
                });

                ['a', 'b', 'c', 'd', 'e'].forEach(function(letter) {
                    var answerElement = document.getElementById('label-pilihan-' + letter);
                    var answerContainer = document.getElementById('soal-' + letter);
                    var inputElement = document.getElementById('pilihan-' + letter);

                    if (currentKecermatan[letter]) {
                        answerElement.textContent = currentKecermatan[letter];
                        answerContainer.classList.remove('d-none');
                        inputElement.disabled = false;
                        inputElement.checked = false; // Reset the checked status
                    } else {
                        answerContainer.classList.add('d-none');
                        inputElement.disabled = true;
                    }
                });
            } else {
                moveToNextKecermatan();
            }
        }

        function moveToNextKecermatan() {
            clearInterval(questionCountdown);
            currentKecermatanIndex++;
            if (currentKecermatanIndex < kecermatanData.length) {
                currentKecermatan = kecermatanData[currentKecermatanIndex];
                currentQuestions = getQuestionsForCurrentKecermatan();
                totalQuestions = currentQuestions.length;
                soalIndex = 0;
                questionsAnswered = 0;
                startQuestionCountdown(); // Reset the timer for the new Kecermatan
                displayQuestion(soalIndex);
            } else {
                endExam();
            }

        }

        function goToNextQuestion() {
            questionsAnswered++;
            if (questionsAnswered >= totalQuestions) {
                moveToNextKecermatan(); // Reset timer and move to the next Kecermatan if questions are completed
                return;
            }
            soalIndex++;
            displayQuestion(soalIndex);

        }

        function endExam() {
            clearInterval(totalCountdown);
            clearInterval(questionCountdown);
            document.getElementById('question-section').style.display = 'none';
            $('#modalSelesai').modal('show');
        }

        document.addEventListener('DOMContentLoaded', function() {

            startCountdown({{ $ujian->time }});
            displayQuestion(soalIndex);
            startQuestionCountdown();

            document.querySelectorAll('.form-check-input').forEach(function(inputElement) {
                inputElement.addEventListener('change', function(event) {
                    var selectedValue = event.target.value;
                    var currentQuestion = currentQuestions[soalIndex];

                    // if (document.getElementById('total-seconds').textContent === 'Waktu Habis') {
                    //     alert('Waktu ujian telah habis. Anda tidak dapat memilih jawaban lagi.');
                    //     event.target.checked = false;
                    //     return;
                    // }

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    });

                    $.ajax({
                        type: 'POST',
                        url: '/simpan-jawaban-kecermatan',
                        data: {
                            soal_id: currentQuestion.id,
                            jawaban: selectedValue
                        },
                        success: function(response) {
                            goToNextQuestion();
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                });
            });
        });

        function startCountdown(seconds) {
            var displaySeconds = document.getElementById('total-seconds');
            var remainingSeconds = seconds;
            console.log("Starting total countdown with seconds:", remainingSeconds);
            clearInterval(totalCountdown);
            totalCountdown = setInterval(function() {
                if (remainingSeconds <= 0 || isTimeUp) {
                    clearInterval(totalCountdown);
                    clearInterval(questionCountdown);
                    displaySeconds.textContent = 'Waktu Habis';
                    document.getElementById('ujianForm').disabled = true;
                    if (currentKecermatanIndex >= kecermatanData.length - 1) {
                        isTimeUp = true;
                        endExam();
                    } else {
                        moveToNextKecermatan();
                    }
                } else {
                    displaySeconds.textContent = remainingSeconds;
                    console.log("Total Countdown:", remainingSeconds);
                    remainingSeconds--;
                }

            }, 1000); // 1 detik
        }

        function startQuestionCountdown() {
            var displaySeconds = document.getElementById('question-seconds');
            var remainingQuestionSeconds = {{ $ujian->time }}; // Total time for all questions in the current Kecermatan
            console.log("Starting question countdown with seconds:", remainingQuestionSeconds);
            clearInterval(questionCountdown);
            questionCountdown = setInterval(function() {
                if (remainingQuestionSeconds <= 0 || isTimeUp) {
                    clearInterval(questionCountdown);
                    console.log("XX:", currentKecermatanIndex);
                    console.log("ZZ:", isTimeUp);
                    console.log("YY:", kecermatanData.length - 1);
                    if (currentKecermatanIndex >= kecermatanData.length - 1) {
                        endExam();
                    } else {
                        moveToNextKecermatan();
                    }
                } else {
                    displaySeconds.textContent = remainingQuestionSeconds;
                    console.log("Question Countdown:", remainingQuestionSeconds);
                    remainingQuestionSeconds--;
                }

            }, 1000); // 1 detik
        }
    </script>
@endsection
