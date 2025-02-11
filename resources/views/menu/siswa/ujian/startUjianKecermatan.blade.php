@extends('layout.template.mainTemplate')

@section('container')
    {{-- Add CSRF Token meta --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                {{-- Connection Status --}}
                <div id="connection-status" class="alert alert-info mb-3" style="display: none;">
                    <span id="connection-text">Status Koneksi: Online</span>
                </div>

                {{-- Ganti bagian Sync Status yang lama dengan yang baru --}}
{{-- Sync Status - Posisikan di pojok kanan atas --}}
<div id="sync-status" class="position-fixed top-0 end-0 p-3" style="z-index: 1000; display: none;">
    <div class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="polite" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <small>
                    <i class="fas fa-sync-alt fa-spin me-1"></i>
                    <span id="sync-text">Menyinkronkan...</span>
                </small>
            </div>
        </div>
    </div>
</div>

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
                        @foreach(['a', 'b', 'c', 'd', 'e'] as $letter)
                            <div class="text-center p-4 border d-none" id="kolom-{{ $letter }}">
                                <h1 id="kolom-text-{{ $letter }}" class="mb-2" style="font-size: 7rem;"></h1>
                                <h3>{{ strtoupper($letter) }}</h3>
                            </div>
                        @endforeach
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
                                @foreach(['a', 'b', 'c', 'd', 'e'] as $letter)
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
                    <div id="modal-sync-warning" class="alert alert-warning mb-3" style="display: none;">
                        Masih ada jawaban yang belum tersinkronisasi. Mohon tunggu...
                    </div>
                    <p>Apakah Anda yakin mengakhiri ujian?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('selesaiUjianKecermatan') }}" method="post" id="formSelesai">
                        @csrf
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <input type="hidden" name="userCommit" id="userCommit" value="{{ encrypt($userCommit['id']) }}">
                        <button type="submit" class="btn btn-danger" id="btnSelesai">Selesai</button>
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
                    $diffInSeconds = $now > $end_time ? 0 : $end_time->diffInSeconds($now);
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
                            <span class="badge badge-danger">{{ $ujian->time }} detik / soal</span>
                        </span>
                    </div>
                </div>
                <div class="col-12 border p-3 col-lg-3">
                    <span class="fw-bold">Jumlah Soal :</span>
                    {{ count($ujian->soalUjianMultiple) }}
                    <button class="btn btn-outline-danger w-100" data-bs-toggle="modal"
                        data-bs-target="#modalSelesai">Selesai Mengerjakan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Offline Storage Handler
        class OfflineStorage {
            constructor(userCommitId) {
                this.storageKey = `exam_answers_${userCommitId}`;
                this.setupConnectionListeners();
            }

            setupConnectionListeners() {
                window.addEventListener('online', () => this.handleOnline());
                window.addEventListener('offline', () => this.handleOffline());
                this.updateConnectionStatus(navigator.onLine);
            }

            handleOnline() {
                this.updateConnectionStatus(true);
                this.syncPendingAnswers();
            }

            handleOffline() {
                this.updateConnectionStatus(false);
            }

            updateConnectionStatus(isOnline) {
                const statusDiv = document.getElementById('connection-status');
                const statusText = document.getElementById('connection-text');
                
                statusDiv.style.display = 'block';
                statusDiv.className = `alert ${isOnline ? 'alert-success' : 'alert-warning'} mb-3`;
                statusText.textContent = `Status Koneksi: ${isOnline ? 'Online' : 'Offline (Jawaban disimpan lokal)'}`;
            }

            saveAnswer(soalId, jawaban) {
                const answers = this.getStoredAnswers();
                answers[soalId] = {
                    jawaban,
                    timestamp: new Date().getTime(),
                    synced: false
                };
                localStorage.setItem(this.storageKey, JSON.stringify(answers));
                
                if (navigator.onLine) {
                    this.syncPendingAnswers();
                }
            }

            getStoredAnswers() {
                const stored = localStorage.getItem(this.storageKey);
                return stored ? JSON.parse(stored) : {};
            }

            async syncPendingAnswers() {
                const answers = this.getStoredAnswers();
                const pendingAnswers = Object.entries(answers).filter(([_, data]) => !data.synced);
                
                if (pendingAnswers.length === 0) {
                    document.getElementById('sync-status').style.display = 'none';
                    return;
                }

                const syncStatus = document.getElementById('sync-status');
                syncStatus.style.display = 'block';

                for (const [soalId, data] of pendingAnswers) {
                    try {
                        const response = await fetch('/simpan-jawaban-kecermatan', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                soal_id: soalId,
                                jawaban: data.jawaban
                            })
                        });

                        if (response.ok) {
                            answers[soalId].synced = true;
                            localStorage.setItem(this.storageKey, JSON.stringify(answers));
                        }
                    } catch (error) {
                        console.error('Sync failed:', error);
                    }
                }

                this.updateSyncStatus();
            }

            updateSyncStatus() {
                const answers = this.getStoredAnswers();
                const pendingCount = Object.values(answers).filter(data => !data.synced).length;
                const syncStatus = document.getElementById('sync-status');
                const modalWarning = document.getElementById('modal-sync-warning');
                
                if (pendingCount > 0) {
                    syncStatus.style.display = 'block';
                    modalWarning.style.display = 'block';
                    document.getElementById('btnSelesai').disabled = true;
                    syncStatus.querySelector('#sync-text').textContent = `${pendingCount} jawaban belum tersinkronisasi`;
                } else {
                    syncStatus.style.display = 'none';
                    modalWarning.style.display = 'none';
                    document.getElementById('btnSelesai').disabled = false;
                }
            }

            hasPendingAnswers() {
                const answers = this.getStoredAnswers();
                return Object.values(answers).some(data => !data.synced);
            }
        }

        // Inisialisasi variabel
        let soalIndex = 0;
        let soalData = @json($userJawabanKecermatan);
        let kecermatanData = @json($ujian->Kecermatan);
        let totalCountdown;
        let questionCountdown;
        let isTimeUp = false;
        let currentKecermatanIndex = 0;
        let currentKecermatan = kecermatanData[currentKecermatanIndex];
        let questionsAnswered = 0;
        let offlineStorage;
        let currentQuestions = getQuestionsForCurrentKecermatan();
        let totalQuestions = currentQuestions.length;

        function getQuestionsForCurrentKecermatan() {
            return soalData.filter(q => q.kecermatan_id === currentKecermatan.id);
        }

        function displayQuestion(index) {
            if (index < totalQuestions) {
                const currentQuestion = currentQuestions[index];
                const savedAnswers = offlineStorage.getStoredAnswers();
                
                document.getElementById('soal-title').textContent = `Soal ${questionsAnswered + 1}`;
                document.getElementById('soal-text').innerHTML = `<h1 class='fw-bold display-1'>${currentQuestion.soal}</h1>`;
                document.getElementById('kolom-title').textContent = `Kolom ke-${currentKecermatanIndex + 1}`;

                // Reset radio buttons
                document.querySelectorAll('input[type="radio"]').forEach(input => input.checked = false);

                ['a', 'b', 'c', 'd', 'e'].forEach(letter => {
                    const kolomTextElement = document.getElementById(`kolom-text-${letter}`);
                    const kolomContainer = document.getElementById(`kolom-${letter}`);
                    const answerElement = document.getElementById(`label-pilihan-${letter}`);
                    const answerContainer = document.getElementById(`soal-${letter}`);
                    const inputElement = document.getElementById(`pilihan-${letter}`);

                    if (currentKecermatan[letter]) {
                        kolomTextElement.textContent = currentKecermatan[letter];
                        kolomContainer.classList.remove('d-none');
                        answerElement.textContent = currentKecermatan[letter];
                        answerContainer.classList.remove('d-none');
                        inputElement.disabled = false;

                        // Restore saved answer if exists
                        // Restore saved answer if exists
                        const savedAnswer = savedAnswers[currentQuestion.id];
                        if (savedAnswer && savedAnswer.jawaban === letter.toUpperCase()) {
                            inputElement.checked = true;
                        }
                    } else {
                        kolomContainer.classList.add('d-none');
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
        // Reset untuk kolom baru
        currentKecermatan = kecermatanData[currentKecermatanIndex];
        currentQuestions = getQuestionsForCurrentKecermatan();
        totalQuestions = currentQuestions.length;
        soalIndex = 0;
        questionsAnswered = 0;
        
        // Reset timer untuk kolom baru
        document.getElementById('question-seconds').textContent = {{ $ujian->time }};
        startQuestionCountdown();
        displayQuestion(soalIndex);
    } else {
        endExam();
    }
}

        function goToNextQuestion() {
            questionsAnswered++;
            if (questionsAnswered >= totalQuestions) {
                moveToNextKecermatan();
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

        function startCountdown(seconds) {
    var displaySeconds = document.getElementById('total-seconds');
    var remainingSeconds = seconds;
    
    clearInterval(totalCountdown);
    totalCountdown = setInterval(function() {
        if (remainingSeconds <= 0) {
            clearInterval(totalCountdown);
            displaySeconds.textContent = 'Waktu Total Habis';
            // Tetap biarkan user melanjutkan ke kolom berikutnya
            if (currentKecermatanIndex < kecermatanData.length - 1) {
                moveToNextKecermatan();
            } else {
                endExam();
            }
        } else {
            displaySeconds.textContent = remainingSeconds;
            remainingSeconds--;
        }
    }, 1000);
}

        function startQuestionCountdown() {
    var displaySeconds = document.getElementById('question-seconds');
    var remainingQuestionSeconds = parseInt(document.getElementById('question-seconds').textContent);
    
    clearInterval(questionCountdown);
    questionCountdown = setInterval(function() {
        if (remainingQuestionSeconds <= 0) {
            clearInterval(questionCountdown);
            // Cek apakah ini kolom terakhir
            if (currentKecermatanIndex >= kecermatanData.length - 1) {
                endExam();
            } else {
                // Jika bukan kolom terakhir, pindah ke kolom berikutnya
                moveToNextKecermatan();
            }
        } else {
            displaySeconds.textContent = remainingQuestionSeconds;
            remainingQuestionSeconds--;
        }
    }, 1000);
}

        // Initialize everything when document is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize offline storage
            const userCommitId = document.getElementById('userCommit').value;
            offlineStorage = new OfflineStorage(userCommitId);

            // Start countdowns
            startCountdown(parseInt(document.getElementById('total-seconds').textContent));
            startQuestionCountdown();
            displayQuestion(soalIndex);

            // Add event listeners for answer selection
            document.querySelectorAll('.form-check-input').forEach(function(inputElement) {
                inputElement.addEventListener('change', function(event) {
                    const selectedValue = event.target.value;
                    const currentQuestion = currentQuestions[soalIndex];

                    // Save answer locally
                    offlineStorage.saveAnswer(currentQuestion.id, selectedValue);
                    
                    // Move to next question
                    goToNextQuestion();
                });
            });

            // Handle form submission
            document.querySelector('#formSelesai').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const submitButton = this.querySelector('#btnSelesai');
                const originalText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyinkronkan...';

                try {
                    // Final sync attempt
                    await offlineStorage.syncPendingAnswers();

                    if (offlineStorage.hasPendingAnswers()) {
                        alert('Beberapa jawaban belum tersinkronisasi. Mohon tunggu hingga koneksi internet stabil.');
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                        return;
                    }

                    // If everything is synced, submit the form
                    this.submit();
                } catch (error) {
                    console.error('Error during final sync:', error);
                    alert('Terjadi kesalahan saat menyinkronkan jawaban. Mohon coba lagi.');
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            });

            // Check connection status on load
            offlineStorage.updateConnectionStatus(navigator.onLine);
        });
    </script>
@endsection