@extends('layout.template.mainTemplate')

@section('container')

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
                <li class="breadcrumb-item">
                    <a href="{{ route('viewPilihTipeUjian', ['token' => encrypt($kelasId), 'mapelId' => $mapel['id']]) }}">
                        Tipe Ujian
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Ujian</li>
            </ol>
        </nav>
    </div>

    {{-- Judul Halaman --}}
    <div class="ps-4 pe-4 mt-4  pt-4">
        <h2 class="display-6 fw-bold">
            <a href="{{ route('viewPilihTipeUjian', ['token' => encrypt($kelasId), 'mapelId' => $mapel['id']]) }}">
                <button type="button" class="btn btn-outline-secondary rounded-circle">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
            </a>
            Tambah Ujian
        </h2>

        {{-- Breadcrumb --}}
        <nav style="" aria-label="breadcrumb">
            <ol class="breadcrumb bg-light">
                <li class="breadcrumb-item text-info" aria-current="page">Step 1</li>
                <li class="breadcrumb-item text-info">Step 2</li>
            </ol>
        </nav>
    </div>

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Formulir Tambah Ujian --}}
    <div class="">
        <div class="row p-4">
            <div class="col-12 col-lg-12">
                <form action="{{ route('createUjian') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h4 class="fw-bold text-primary"><i class="fa-solid fa-pen"></i> Data Ujian</h4>
                    <div class=" row ">
                        {{-- Section Left --}}
                        <div class="p-4 col-lg-12 col-12  bg-white rounded-2">

                            {{-- Status Open / Close --}}
                            <div class="mb-3 row">
                                <div class="col-8 col-lg-4">
                                    <label for="opened" class="form-label d-block">Aktif<span class="small">(apakah
                                            sudah bisa diakses?)</span></label>
                                </div>
                                <div class="col-4 col-lg form-check form-switch">
                                    <input class="form-check-input" name="opened" type="checkbox" role="switch"
                                        id="opened" checked>
                                </div>
                            </div>

                            {{-- Nama Ujian --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Judul Ujian <span
                                        class="text-danger">*</span></label>
                                <input type="hidden" name="kelasId" value="{{ encrypt($kelasId) }}" readonly>
                                <input type="hidden" name="mapelId" value="{{ $mapel['id'] }}" readonly>
                                @if (session()->has('info'))
                                    <input type="text" class="form-control" id="inputName" name="name"
                                        placeholder="Inputkan judul Tugas..." value="{{ old('name', session('info')[0]) }}"
                                        required>
                                @else
                                    <input type="text" class="form-control" id="inputName" name="name"
                                        placeholder="Inputkan judul Tugas..." value="{{ old('name') }}" required>
                                @endif
                                @error('name')
                                    <div class="text-danger small">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Reserved grid --}}
                            <div class="row mb-3">
                                <div class="col-12 col-lg-12">
                                    <label class="form-label">Tipe Ujian <span class="text-danger">*</span></label>
                                    <input type="text" name="tipe" class="form-control"
                                        value="@if ($tipe == 'multiple') multiple @elseif($tipe == 'essay') essay @else kecermatan @endif"
                                        readonly>
                                </div>
                            </div>

                            {{-- Waktu Ujian (Timer) --}}
                            <div class="mb-3">
                                <div class="">
                                    <label for="time" class="form-label">Waktu Ujian <span class="small">
                                            @if ($tipe == 'kecermatan')
                                                Per soal (Detik)
                                            @else
                                                dalam
                                                menit
                                            @endif
                                            <span class="text-danger">*</span>
                                        </span></label>
                                    @if (session()->has('info'))
                                        <input type="number" class="form-control" id="inputTime" name="time"
                                            placeholder="0" value="{{ old('time', session('info')[1]) }}" required>
                                    @else
                                        <input type="number" class="form-control" id="inputTime" name="time"
                                            placeholder="0" value="{{ old('time') }}" required>
                                    @endif
                                    @error('time')
                                        <div class="text-danger small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Due Date Picker --}}
                            <div class="mb-3">
                                <label for="due" class="form-label">Due Date <span
                                        class="text-danger">*</span></label>
                                @if (session()->has('info'))
                                    <input class="form-control" id="inputDue" name="due" autocomplete="off"
                                        placeholder="Pilih tanggal jatuh tempo..." required
                                        value="{{ old('due', session('info')[2]) }}">
                                @else
                                    <input class="form-control" id="inputDue" name="due" autocomplete="off"
                                        placeholder="Pilih tanggal jatuh tempo..." required value="{{ old('due') }}">
                                @endif
                                @error('due')
                                    <div class="text-danger small">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div>

                        <div class="mt-4">
                            <h4 class="fw-bold text-primary"><i class="fa-solid fa-pen"></i>
                                @if ($tipe == 'multiple')
                                    Data Soal Pilihan Ganda
                                    <button class="btn btn-success animate-btn-small" data-bs-toggle="modal"
                                        data-bs-target="#importModal" type="button"><i
                                            class="fa-solid fa-file-import"></i>
                                        Import<span class="small">(.xls, .xlsx)</span>
                                    </button>
                                @elseif($tipe == 'essay')
                                    Data Soal Essay
                                    <button class="btn btn-success animate-btn-small" data-bs-toggle="modal"
                                        data-bs-target="#importModal" type="button"><i
                                            class="fa-solid fa-file-import"></i>
                                        Import<span class="small">(.xls, .xlsx)</span>
                                    </button>
                                @else
                                    Data Kolom Kecermatan
                                @endif

                            </h4>
                            {{-- Container untuk Pertanyaan-Pertanyaan --}}
                            <div class="mt-4 bg-white p-4" id="containerPertanyaan">
                                @if (session()->has('soalEssay'))
                                    @foreach (session('soalEssay') as $key)
                                        <div class="bg-white border border-dark-subtle rounded-2 p-4 mt-4 pertanyaan">
                                            <div class="">
                                                <h3>Soal <span class="badge badge-primary">{{ $loop->iteration }}</span>
                                                    <button type="button"
                                                        class="btn btn-outline-danger btnKurangi">X</button>
                                                </h3>
                                                <div class="mb-3">
                                                    <label for="pertanyaan${nomorPertanyaan}"
                                                        class="form-label">Pertanyaan <span
                                                            class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="pertanyaan${nomorPertanyaan}" name="pertanyaan[]" rows="3" required>{{ $key[1] }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @elseif (session()->has('soalMultiple'))
                                    @foreach (session('soalMultiple') as $key)
                                        <div class="bg-white border border-dark-subtle rounded-2 p-4 mt-4 pertanyaan">
                                            <div class="">
                                                <h3>Soal <span class="badge badge-primary">{{ $loop->iteration }}</span>
                                                    <button type="button"
                                                        class="btn btn-outline-danger btnKurangi">X</button>
                                                </h3>
                                                <div class="mb-3 row">
                                                    <div class="col-lg-7 col-12">
                                                        <label for="pertanyaan${nomorPertanyaan}"
                                                            class="form-label">Pertanyaan <span
                                                                class="text-danger">*</span></label>
                                                        <textarea class="form-control" id="pertanyaan${nomorPertanyaan}" name="pertanyaan[]" rows="3" required>{{ $key[1] }}</textarea>
                                                    </div>
                                                    <div class="col-lg-5 col-12 row">
                                                        <div class="col-5 m-1">
                                                            <label for="pertanyaan${nomorPertanyaan}" class="form-label">A
                                                                <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="a[]"
                                                                required id="" value="{{ $key[2] }}">
                                                        </div>
                                                        <div class="col-5 m-1">
                                                            <label for="pertanyaan${nomorPertanyaan}" class="form-label">B
                                                                <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="b[]"
                                                                required id="" value="{{ $key[3] }}">
                                                        </div>
                                                        <div class="col-5 m-1">
                                                            <label for="pertanyaan${nomorPertanyaan}" class="form-label">C
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control" name="c[]"
                                                                required id="" value="{{ $key[4] }}">
                                                        </div>
                                                        <div class="col-5 m-1">
                                                            <label for="pertanyaan${nomorPertanyaan}" class="form-label">D
                                                            </label>
                                                            <input type="text" class="form-control" name="d[]"
                                                                id="" value="{{ $key[5] }}">
                                                        </div>
                                                        <div class="col-5 m-1">
                                                            <label for="pertanyaan${nomorPertanyaan}" class="form-label">E
                                                            </label>
                                                            <input type="text" class="form-control" name="e[]"
                                                                id="" value="{{ $key[6] }}">
                                                        </div>
                                                        <div class="col-5 m-1">
                                                            <label for="pertanyaan${nomorPertanyaan}"
                                                                class="form-label text-primary fw-bold">Jawaban</label>
                                                            <select name="jawaban[]" class="form-select" id="">
                                                                <option value="a"
                                                                    @if ($key[7] == 'a') selected @endif>A
                                                                </option>
                                                                <option value="b"
                                                                    @if ($key[7] == 'b') selected @endif>B
                                                                </option>
                                                                <option value="c"
                                                                    @if ($key[7] == 'c') selected @endif>C
                                                                </option>
                                                                <option value="d"
                                                                    @if ($key[7] == 'd') selected @endif>D
                                                                </option>
                                                                <option value="e"
                                                                    @if ($key[7] == 'e') selected @endif>E
                                                                </option>
                                                            </select>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            {{-- Tombol Tambah Pertanyaan --}}
                            <div class="mt-3 mb-4">
                                <button type="button" class="btn btn-outline-success w-100 btn-lg"
                                    id="btnTambahPertanyaan">Tambah
                                    Pertanyaan</button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-lg btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.tiny.cloud/1/jqqif5psx8ajdrpos129cpypqbqy3qmzk0lxwwxdu9s2lsn7/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>;
    <script src="{{ url('/asset/js/rich-text-editor.js') }}"></script>

    {{-- Modal Import --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Excel<span class="small">(.xls, .xlsx)</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <p class="text-left">
                            File yang tidak mengikuti
                            ketentuan
                            format akan
                            menyebabkan Error pada Perintah Import.
                        </p>
                    </div>
                    <div class="mt-2 col-12 bg-body-secondary rounded-2 p-4">
                        <div class="mb-4">
                            @if ($tipe == 'essay')
                                <a href="{{ route('contohEssay') }}"><button type="button" class="btn btn-warning"><i
                                            class="fa-solid fa-download"></i> Download
                                        Contoh<span class="small">(.xls)</span></button></a>
                            @elseif ($tipe == 'multiple' || $tipe == 'kecermatan')
                                <a href="{{ route('contohMultiple') }}"><button type="button"
                                        class="btn btn-warning"><i class="fa-solid fa-download"></i> Download
                                        Contoh<span class="small">(.xls)</span></button></a>
                            @endif
                        </div>
                        <div>
                            <form method="POST" action="{{ route('importSoalUjian') }}" enctype="multipart/form-data">
                                @csrf
                                <label for="file">Upload File<span class="small">(.xls, .xlsx)</span></label>
                                <input type="file" name="file" accept=".xlsx, .xls" required>
                                <input type="hidden" name="tipe" value="{{ $tipe }}">
                                <input type="hidden" name="name" id="importName">
                                <input type="hidden" name="time" id="importTime">
                                <input type="hidden" name="due" id="importDue">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-upload"></i> Import
                        Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            // Aktifkan date picker dengan format tanggal dan jam
            $(function() {
                $('#inputDue').datetimepicker({
                    format: 'Y-m-d H:i',
                    locale: 'id',
                });
            });

            // Import Hidden Input Builder
            $('#inputName').on('change', function() {
                $('#importName').val($('#inputName').val());
            });
            $('#inputTime').on('change', function() {
                $('#importTime').val($('#inputTime').val());
            });
            $('#inputDue').on('change', function() {
                $('#importDue').val($('#inputDue').val());
            });

            // Tombol Tambah Pertanyaan diklik
            $('#btnTambahPertanyaan').click(function() {
                // Mengambil jumlah pertanyaan saat ini
                const jumlahPertanyaan = $('.pertanyaan').length;

                // Membuat nomor pertanyaan yang akan digunakan
                var nomorPertanyaan = jumlahPertanyaan + 1;
                @if ($tipe == 'essay')
                    // Buat formulir pertanyaan baru Essay
                    const formulirPertanyaanBaru = `
                    <div class="bg-white border border-dark-subtle rounded-2 p-4 mt-4 pertanyaan">
                        <div>
                            <h3>Soal <span class="badge badge-primary">${nomorPertanyaan}</span>
                                <button type="button" class="btn btn-outline-danger btnKurangi">X</button>
                            </h3>
                            <div class="mb-3">
                                <label class="form-label">Jenis Pertanyaan <span class="text-danger">*</span></label>
                                <select class="form-select jenisPertanyaan" id="jenisPertanyaan${nomorPertanyaan}">
                                    <option value="" disabled selected>Pilih jenis pertanyaan</option>
                                    <option value="teks">Teks</option>
                                    <option value="gambar">Gambar</option>
                                </select>
                            </div>
                            <div class="mb-3 pertanyaanTeks" style="display: none;">
                                <label for="pertanyaan${nomorPertanyaan}" class="form-label">Pertanyaan Teks <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="pertanyaan${nomorPertanyaan}"
                                        name="pertanyaan[]" rows="3" placeholder="Masukkan pertanyaan teks"></textarea>
                            </div>
                            <div class="mb-3 pertanyaanGambar" style="display: none;">
                                <label for="pertanyaan${nomorPertanyaan}" class="form-label">Unggah Pertanyaan Gambar <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="pertanyaan${nomorPertanyaan}"
                                    name="pertanyaan[]" accept="image/*" />
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
                                        <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                                        <div class="mb-2">
                                            <input type="radio" id="textOption${nomorPertanyaan}" name="pertanyaanType${nomorPertanyaan}" value="text" checked>
                                            <label for="textOption${nomorPertanyaan}">Teks</label>
                                            <input type="radio" id="imageOption${nomorPertanyaan}" name="pertanyaanType${nomorPertanyaan}" value="image">
                                            <label for="imageOption${nomorPertanyaan}">Gambar</label>
                                        </div>
                                        <!-- Input untuk teks -->
                                        <textarea class="form-control pertanyaan-text" id="pertanyaanText${nomorPertanyaan}" name="pertanyaan[]" rows="3"></textarea>
                                        <!-- Input untuk gambar -->
                                        <input type="file" class="form-control d-none pertanyaan-image" id="pertanyaanImage${nomorPertanyaan}" name="pertanyaan[]" accept="image/*">
                                    </div>
                                    <div class="col-lg-5 col-12 row">
                                        <div class="col-5 m-1">
                                            <label class="form-label">A <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="a[]" required>
                                        </div>
                                        <div class="col-5 m-1">
                                            <label class="form-label">B <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="b[]" required>
                                        </div>
                                        <div class="col-5 m-1">
                                            <label class="form-label">C <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="c[]" required>
                                        </div>
                                        <div class="col-5 m-1">
                                            <label class="form-label">D</label>
                                            <input type="text" class="form-control" name="d[]">
                                        </div>
                                        <div class="col-5 m-1">
                                            <label class="form-label">E</label>
                                            <input type="text" class="form-control" name="e[]">
                                        </div>
                                        <div class="col-5 m-1">
                                            <label class="form-label text-primary fw-bold">Jawaban</label>
                                            <select name="jawaban[]" class="form-select">
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
                @elseif ($tipe == 'kecermatan')
                    // Buat formulir pertanyaan baru Multiple
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
                                                    <input type="number" value="" required class="form-control" required name="jumlahSoal[]"
                                                        id="">
                                                </div>
                                        </div>
                                    </div>
                                </div>
            `;
                @endif


                // Tambahkan formulir pertanyaan baru ke dalam container
                $('#containerPertanyaan').append(formulirPertanyaanBaru);
                tinymce.remove(".tinymce");
                tinymce.init({
                    selector: ".tinymce",
                    plugins: "image link lists media imageupload",
                    toolbar: "undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat",
                    menubar: false,
                    paste_data_images: true,
                    statusbar: false,
                    file_picker_types: 'image',
                    images_file_types: 'jpg,jpeg,png,gif,webp',
                    image_upload_url: '/update-ujian', // URL endpoint untuk meng-handle upload
                    file_picker_callback: (cb, value, meta) => {
                        const input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');

                        input.addEventListener('change', (e) => {
                            const file = e.target.files[0];

                            const reader = new FileReader();
                            reader.addEventListener('load', () => {
                                /*
                                Note: Now we need to register the blob in TinyMCEs image blob
                                registry. In the next release this part hopefully won't be
                                necessary, as we are looking to handle it internally.
                                */
                                const id = 'blobid' + (new Date()).getTime();
                                const blobCache = tinymce.activeEditor
                                    .editorUpload.blobCache;
                                const base64 = reader.result.split(',')[1];
                                const blobInfo = blobCache.create(id, file,
                                    base64);
                                blobCache.add(blobInfo);

                                /* call the callback and populate the Title field with the file name */
                                cb(blobInfo.blobUri(), {
                                    title: file.name
                                });
                            });
                            reader.readAsDataURL(file);
                        });

                        input.click();
                    },
                    // images_upload_handler: function(blobInfo, success, failure) {
                    //     // Fungsi penanganan unggah gambar, dapat diisi sesuai kebutuhan.
                    //     // Di sini, kami mengembalikan false untuk menonaktifkan unggah gambar.
                    //     return true;
                    // },
                    ai_request: (request, respondWith) =>
                        respondWith.string(() =>
                            Promise.reject("See docs to implement AI Assistant")
                        ),
                });
                // Aktifkan tombol Kurangi pada pertanyaan sebelumnya (jika ada)
                $('.pertanyaan:last').prev().find('.btnKurangi').show();
            });


            // Tombol Kurangi diklik
            $('#containerPertanyaan').on('click', '.btnKurangi', function() {
                // Hapus formulir pertanyaan yang terkait
                $(this).closest('.pertanyaan').remove();

                // Update nomor pertanyaan pada pertanyaan yang tersisa
                $('.pertanyaan').each(function(index) {
                    // Menggunakan $(this) untuk merujuk pada elemen pertanyaan saat ini
                    const nomorPertanyaan = index + 1;
                    $(this).find('h3 span.badge').text(nomorPertanyaan);
                });


            });
        });

        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('jenisPertanyaan')) {
                const parent = e.target.closest('.pertanyaan');
                const teksField = parent.querySelector('.pertanyaanTeks');
                const gambarField = parent.querySelector('.pertanyaanGambar');

                if (e.target.value === 'teks') {
                    teksField.style.display = 'block';
                    gambarField.style.display = 'none';
                } else if (e.target.value === 'gambar') {
                    teksField.style.display = 'none';
                    gambarField.style.display = 'block';
                } else {
                    teksField.style.display = 'none';
                    gambarField.style.display = 'none';
                }
            }
        });

        document.addEventListener('change', (e) => {
            if (e.target.type === 'file') {
                const file = e.target.files[0];
                if (file) {
                    // Validasi tipe file (opsional)
                    if (!file.type.startsWith('image/')) {
                        alert('Harap unggah file gambar yang valid!');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (event) => {
                        // Hapus preview lama jika ada
                        const existingPreview = e.target.parentNode.querySelector('.img-preview-container');
                        if (existingPreview) {
                            existingPreview.remove();
                        }

                        // Bungkus elemen preview gambar dalam div
                        const previewContainer = document.createElement('div');
                        previewContainer.classList.add('img-preview-container');
                        previewContainer.style.position = 'relative';
                        previewContainer.style.display = 'inline-block';
                        previewContainer.style.marginTop = '20px';

                        // Elemen gambar
                        const imgPreview = document.createElement('img');
                        imgPreview.src = event.target.result;
                        imgPreview.alt = 'Preview Gambar';
                        imgPreview.style.maxWidth = '300px';
                        imgPreview.style.height = '200px';
                        imgPreview.style.borderRadius = '8px';
                        imgPreview.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
                        imgPreview.style.cursor = 'pointer';

                        // Tombol hapus
                        const removeButton = document.createElement('button');
                        removeButton.textContent = '✕';
                        removeButton.classList.add('btn', 'btn-danger', 'btn-sm');
                        removeButton.style.position = 'absolute';
                        removeButton.style.top = '5px';
                        removeButton.style.right = '5px';
                        removeButton.style.borderRadius = '50%';
                        removeButton.style.padding = '5px 10px';
                        removeButton.style.border = 'none';
                        removeButton.style.cursor = 'pointer';

                        removeButton.addEventListener('click', () => {
                            previewContainer.remove();
                            e.target.value = ''; // Reset input file
                        });

                        // Tambahkan elemen ke dalam container
                        previewContainer.appendChild(imgPreview);
                        previewContainer.appendChild(removeButton);
                        e.target.parentNode.appendChild(previewContainer);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });


        // Event listener untuk toggle antara input teks dan gambar
        document.addEventListener('change', function(e) {
            if (e.target.matches(`input[name^="pertanyaanType"]`)) {
                const pertanyaanId = e.target.name.replace('pertanyaanType', '');
                const textInput = document.getElementById(`pertanyaanText${pertanyaanId}`);
                const imageInput = document.getElementById(`pertanyaanImage${pertanyaanId}`);

                if (e.target.value === 'text') {
                    textInput.classList.remove('d-none');
                    imageInput.classList.add('d-none');
                } else {
                    textInput.classList.add('d-none');
                    imageInput.classList.remove('d-none');
                }
            }
        });
    </script>
@endsection