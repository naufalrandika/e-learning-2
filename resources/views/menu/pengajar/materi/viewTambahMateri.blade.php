@extends('layout.template.mainTemplate')

@section('container')
    {{-- Cek peran pengguna --}}
    @if (Auth()->user()->roles_id == 1)
        @include('menu.admin.adminHelper')
    @endif

    {{-- Navigasi Breadcrumb --}}
    <div class="col-12 ps-4 pe-4 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">
                    <a
                        href="{{ route('viewKelasMapel', ['mapel' => $mapel['id'], 'token' => encrypt($kelasId), 'mapel_id' => $mapel['id']]) }}">
                        {{ $mapel['name'] }}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Materi</li>
            </ol>
        </nav>
    </div>

    {{-- Judul Halaman --}}
    <div class="ps-4 pe-4 mt-4  pt-4">
        <h2 class="display-6 fw-bold">
            <a
                href="{{ route('viewKelasMapel', ['mapel' => $mapel['id'], 'token' => encrypt($kelasId), 'mapel_id' => $mapel['id']]) }}">
                <button type="button" class="btn btn-outline-secondary rounded-circle">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
            </a> Tambah Materi
        </h2>
    </div>

    {{-- Formulir Tambah Materi --}}
    <div class="">
        <div class="row p-4">
            <h4 class="fw-bold text-primary"><i class="fa-solid fa-pen"></i> Data Materi</h4>
            <div class="col-12 col-lg-12 bg-white rounded-2">
                <div class="mt-4">
                    <div class="p-4">
                        <form action="{{ route('createMateri') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            {{-- Status Open / Close --}}
                            <div class="mb-3 row">
                                <div class="col-8 col-lg-4">
                                    <label for="opened" class="form-label d-block">Aktif<span class="small">(apakah
                                            sudah bisa diakses siswa?)</span></label>
                                </div>
                                <div class="col-4 col-lg form-check form-switch">
                                    <input class="form-check-input" name="opened" type="checkbox" role="switch"
                                        id="opened" checked>
                                </div>
                            </div>
                            {{-- Nama Materi --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Judul Materi</label>
                                <input type="hidden" name="kelasId" value="{{ encrypt($kelasId) }}" readonly>
                                <input type="hidden" name="mapelId" value="{{ $mapel['id'] }}" readonly>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Inputkan judul materi..." value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="text-danger small">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            {{-- Konten Materi --}}
                            <div class="mb-3">
                                <label for="nama" class="form-label">Konten <span
                                        class="small text-info">(Opsional)</span></label>
                                <textarea id="tinymce" id="content2" name="content"></textarea>
                            </div>
                            {{-- Konten Materi --}}
                            <div class="mb-3">
                                <label for="uploadFile" class="form-label">Upload <span
                                        class="small text-info">(Opsional)</span></label>
                                <!-- Dropzone -->
                                <div id="my-dropzone" class="dropzone"></div>
                            </div>
                            {{-- Tombol Submit --}}
                            <div class="">
                                <button type="submit" class="btn-lg btn btn-primary w-100" id="btnSimpan">Simpan dan
                                    Lanjutkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script yang dibutuhkan --}}
    <script src="https://cdn.tiny.cloud/1/jqqif5psx8ajdrpos129cpypqbqy3qmzk0lxwwxdu9s2lsn7/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>;

    <script src="{{ url('/asset/js/rich-text-editor.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Menangkap submit form
            $('form').submit(function(e) {
                e.preventDefault(); // Mencegah form melakukan submit default

                // Mengambil data form
                var formData = new FormData(this);

                // Menggunakan AJAX untuk mengirim data ke server
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Berhasil, lakukan sesuatu dengan respons dari server jika diperlukan
                        console.log(response);
                        uploadFiles();
                    },
                    error: function(error) {
                        // Terjadi kesalahan, tangani kesalahan jika diperlukan
                        console.log(error);
                        // Di sini Anda dapat menambahkan logika lain atau menampilkan pesan kesalahan kepada pengguna.
                    }
                });
            });
        });
    </script>
    <script>
        // Inisialisasi Dropzone
        Dropzone.autoDiscover = false; // Untuk menghindari Dropzone menginisialisasi dirinya sendiri secara otomatis

        // Fungsi untuk memeriksa apakah konten TinyMCE tidak kosong
        function validateTinyMCE() {
            var content = tinymce.get("tinymce").getContent();
            if (!content.trim()) {
                alert("Konten tidak boleh kosong.");
                return false; // Membatalkan pengiriman formulir jika konten kosong
            }
            return true; // Lanjutkan pengiriman formulir jika konten tidak kosong
        }
        $("form").on("submit", function() {
            return validateTinyMCE();
        });

        var totalFilesToUpload = 0; // Total file yang diharapkan diunggah
        var completedFiles = 0; // Jumlah file yang sudah selesai diunggah

        // Konfigurasi Dropzone
        var myDropzone = new Dropzone("#my-dropzone", {
            url: "{{ route('uploadFileMateri', ['action' => 'tambah']) }}", // Ganti dengan rute yang sesuai untuk menangani unggahan file
            paramName: "file", // Nama parameter untuk mengirim file
            maxFilesize: 10, // Batasan ukuran file (dalam MB)
            acceptedFiles: ".jpg, .jpeg, .png, .gif, .mp4, .pdf, .doc, .docx, .ppt, .pptx, .xls, .xlsx, .txt, .mp3, .avi, .mov",
            addRemoveLinks: true, // Menampilkan tautan untuk menghapus file yang diunggah
            timeout: 60000, // Menampilkan tautan untuk menghapus file yang diunggah
            dictDefaultMessage: "Seret file ke sini atau klik untuk mengunggah", // Pesan default
            autoProcessQueue: false,

            parallelUploads: 100,
            init: function() {
                this.on("addedfile", function(file) {
                    if (file.size <=
                        10485760
                    ) { // Misalnya, hanya mengunggah file yang berukuran kurang dari atau sama dengan 10MB
                        totalFilesToUpload++; // Menambah total file yang diharapkan saat file ditambahkan
                    } else {
                        // Jika file tidak memenuhi ketentuan, Anda dapat memberikan pesan kesalahan kepada pengguna atau tindakan lain yang sesuai.
                        this.removeFile(file);
                        alert("File terlalu besar! Maksimal ukuran file adalah 10MB.");
                    }
                });

                this.on("sending", function(file, xhr, formData) {
                    // Tambahkan token CSRF ke dalam header permintaan
                    xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");
                });

                this.on("success", function(file, response) {

                    console.log("completedFiles : " + completedFiles);
                    console.log("totalFilesToUpload : " + totalFilesToUpload);
                });

                this.on("complete", function(file, response) {
                    if (file.size <=
                        10485760) { // Hanya menambahkan file yang memenuhi ketentuan ke completedFiles
                        completedFiles++; // Menambah jumlah file yang selesai diunggah
                        console.log("completedFiles : " + completedFiles);
                        console.log("totalFilesToUpload : " + totalFilesToUpload);
                        if (completedFiles === totalFilesToUpload) {
                            // Semua file yang memenuhi ketentuan sudah selesai diunggah, lakukan pengalihan
                            window.location.href =
                                "{{ route('redirectBack', ['kelasId' => $kelasId, 'mapelId' => $mapel['id'], 'message' => 'Tambah']) }}";
                        }
                    }
                });

                this.on("removedfile", function(file) {

                    totalFilesToUpload--;

                    // Pastikan completedFiles tidak kurang dari 0
                    if (completedFiles < 0) {
                        completedFiles = 0;
                    }
                });

                // Tambahkan event lain yang Anda perlukan di sini
            }
        });



        function uploadFiles() {
            if (myDropzone.getQueuedFiles().length === 0) {
                // Tidak ada file yang diunggah, lakukan pengalihan (redirect)
                window.location.href =
                    "{{ route('redirectBack', ['kelasId' => $kelasId, 'mapelId' => $mapel['id'], 'message' => 'Tambah']) }}";
            } else {
                // Ada file yang diunggah, proses antrian Dropzone
                myDropzone.processQueue();
            }
        }
    </script>
@endsection
