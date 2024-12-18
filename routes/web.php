<?php

use App\Http\Controllers\AdminRegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataSiswaController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KelasMapelController;
use App\Http\Controllers\LoginRegistController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\PengajarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\UjianController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::controller(AdminRegisterController::class)->group(function () {
    Route::get('/admin-register', 'viewAdminRegister')->middleware('guest')->name('adminRegister');
    Route::post('/regist-admin', 'registAdmin')->middleware('guest')->name('registAdmin');
    Route::get('/debug', 'debug')->name('debug');
});

Route::controller(LoginRegistController::class)->group(function () {
    // Get
    Route::get('/login', 'viewLogin')->middleware('guest')->name('login');
    Route::get('/register', 'viewRegister')->middleware('guest')->name('register');
    Route::get('/forgot-password', 'viewForgotPassword')->middleware('guest')->name('forgotPassword');

    // Post
    Route::post('/vallidate-register', 'register')->middleware('guest')->name('validate');
    Route::post('/authenticate', 'authenticate')->middleware('guest')->name('authenticate');
    Route::post('/logout', 'logout')->middleware('auth')->name('logout');
});

// Dashboard
Route::controller(DashboardController::class)->group(function () {
    // Get
    Route::get('/dashboard', 'viewDashboard')->middleware('auth')->name('dashboard');
    Route::get('/home', 'viewHome')->middleware('auth')->name('home');
});

// KelasMapel
Route::controller(KelasMapelController::class)->group(function () {
    // Get
    Route::get('/kelas-mapel/{mapel}/{token}', 'viewKelasMapel')->middleware('auth')->name('viewKelasMapel');
    Route::get('/save-image-temp', 'saveImageTemp')->middleware('auth')->name('saveImageTemp');

    Route::get('/export-nilai-tugas', 'exportNilaiTugas')->middleware('pengajar')->name('exportNilaiTugas');
    Route::get('/export-nilai-ujian', 'exportNilaiUjian')->middleware('pengajar')->name('exportNilaiUjian');
});

// Ujian
Route::controller(UjianController::class)->group(function () {
    // Get
    Route::get('/ujian/add/1/{token}', 'viewPilihTipeUjian')->middleware('auth')->name('viewPilihTipeUjian');
    Route::get('/ujian/add/2/{token}', 'viewCreateUjian')->middleware('auth')->name('viewCreateUjian');
    Route::get('/ujian/{token}', 'viewUjian')->middleware('auth')->name('viewUjian');
    Route::get('/ujian/update/{token}', 'viewUpdateUjian')->middleware('auth')->name('viewUpdateUjian');

    Route::post('/store-ujian', 'createUjian')->middleware('auth')->name('createUjian');
    Route::post('/trix-upload', 'uploadImage')->middleware('auth')->name('trix.upload');
    Route::post('/ujian/update-nilai', 'ujianUpdateNilai')->middleware('pengajar')->name('ujianUpdateNilai');
    Route::post('/update-ujian', 'updateUjian')->middleware('pengajar')->name('updateUjian');
    Route::post('/destroy-ujian', 'destroyUjian')->middleware('pengajar')->name('destroyUjian');

    Route::post('/import-soal-ujian', 'import')->middleware('pengajar')->name('importSoalUjian');

    // Import Export
    Route::get('/contoh-essay', 'contohEssay')->middleware('pengajar')->name('contohEssay');
    Route::get('/contoh-multiple', 'contohMultiple')->middleware('pengajar')->name('contohMultiple');

    // Siswa
    Route::get('/ujian-access/{token}', 'ujianAccess')->middleware('auth')->name('ujianAccess');
    Route::post('/start-ujian/{token}', 'startUjian')->middleware('auth')->name('startUjian');
    Route::get('/ujian/{ujian}/{token}', 'userUjian')->middleware('auth')->name('userUjian');

    Route::post('/simpan-jawaban', 'simpanJawaban')->middleware('auth')->name('simpanJawaban');
    Route::post('/simpan-jawaban-multiple', 'simpanJawabanMultiple')->middleware('auth')->name('simpanJawabanMultiple');
    Route::post('/simpan-jawaban-kecermatan', 'simpanJawabanKecermatan')->middleware('auth')->name('simpanJawabanKecermatan');
    Route::post('/selesai-ujian', 'selesaiUjian')->middleware('auth')->name('selesaiUjian');
    Route::post('/retry-kecermatan', 'retryKecermatan')->middleware('auth')->name('retry-kecermatan');
    Route::post('/selesai-ujian-multiple', 'selesaiUjianMultiple')->middleware('auth')->name('selesaiUjianMultiple');
    Route::post('/selesai-ujian-kecermatan', 'selesaiUjianKecermatan')->middleware('auth')->name('selesaiUjianKecermatan');
    Route::get('/get-jawaban', 'getJawaban')->middleware('auth')->name('getJawaban');
    Route::get('/get-jawaban-multiple', 'getJawabanMultiple')->middleware('auth')->name('getJawabanMultiple');
});

// Materi
Route::controller(MateriController::class)->group(function () {
    // Get
    Route::get('/materi/add/{token}', 'viewCreateMateri')->middleware('pengajar')->name('viewCreateMateri');
    Route::get('/materi/update/{token}', 'viewUpdateMateri')->middleware('pengajar')->name('viewUpdateMateri');
    Route::post('/store-materi', 'createMateri')->middleware('pengajar')->name('createMateri');
    Route::post('/update-materi', 'updateMateri')->middleware('pengajar')->name('updateMateri');
    Route::post('/destroy-materi', 'destroyMateri')->middleware('pengajar')->name('destroyMateri');

    Route::get('/materi', 'viewMateri')->middleware('auth')->name('viewMateri');

    Route::post('/upload-materi-file', 'uploadFileMateri')->middleware('auth')->name('uploadFileMateri');
    Route::post('/destroy-materi-file', 'destroyFileMateri')->middleware('auth')->name('destroyFileMateri');
    Route::get('/redirect-after', 'redirectBack')->middleware('auth')->name('redirectBack');
});

// Tugas
Route::controller(TugasController::class)->group(function () {
    // Get
    Route::get('/tugas/add/{token}', 'viewCreateTugas')->middleware('pengajar')->name('viewCreateTugas');
    Route::get('/tugas', 'viewTugas')->middleware('auth')->name('viewTugas');
    Route::get('/tugas/update/{token}', 'viewUpdateTugas')->middleware('pengajar')->name('viewUpdateTugas');

    Route::post('/tugas/update-nilai/{token}', 'siswaUpdateNilai')->middleware('pengajar')->name('siswaUpdateNilai');
    Route::post('/destroy-tugas', 'destroyTugas')->middleware('pengajar')->name('destroyTugas');
    Route::post('/store-tugas', 'createTugas')->middleware('pengajar')->name('createTugas');
    Route::post('/update-tugas', 'updateTugas')->middleware('pengajar')->name('updateTugas');
    Route::post('/destroy-tugas-file', 'destroyFileTugas')->middleware('pengajar')->name('destroyFileTugas');
    Route::post('/destroy-tugas-submit-file', 'destroyFileSubmit')->middleware('auth')->name('destroyFileSubmit');

    Route::post('/upload-tugas-file', 'uploadFileTugas')->middleware('auth')->name('uploadFileTugas');
    Route::post('/submit-tugas-file', 'submitFileTugas')->middleware('auth')->name('submitFileTugas');

    Route::post('/submit-tugas/{token}', 'submitTugas')->middleware('auth')->name('submitTugas');
});

//Admin Only
Route::controller(PengajarController::class)->group(function () {
    // Get
    Route::get('/data-pengajar', 'viewPengajar')->middleware('admin')->name('viewPengajar');
    Route::get('/data-pengajar/new-pengajar-1', 'viewNewPengajar')->middleware('admin')->name('viewTambahPengajar');
    Route::get('/data-pengajar/new-pengajar-2', 'tambahKelasPengajar')->middleware('admin')->name('tambahKelasPengajar');
    Route::get('/data-pengajar/debug', 'debugRoute')->middleware('auth')->name('debugRoute');
    Route::get('/data-pengajar/success', 'dataPengajarSuccess')->middleware('admin')->name('dataPengajarSuccess');

    Route::get('/data-pengajar/update/{token}', 'viewUpdatePengajar')->middleware('admin')->name('viewUpdatePengajar');

    Route::post('/validate-pengajar', 'validateDataPengajar')->middleware('admin')->name('validateDataPengajar');
    Route::post('/validate-pengajar-2', 'validateDataPengajarKelas')->middleware('admin')->name('validateDataPengajarKelas');
    Route::post('/update-pengajar', 'updatePengajar')->middleware('admin')->name('updatePengajar');
    Route::post('/destroy-pengajar', 'destroyPengajar')->middleware('admin')->name('destroyPengajar');
    Route::post('/catch', 'catch')->middleware('admin')->name('catch');

    Route::get('/export-pengajar', 'export')->middleware('admin')->name('exportPengajar');
    Route::get('/contoh-pengajar', 'fileContoh')->middleware('admin')->name('contohPengajar');
    Route::post('/import-pengajar', 'import')->middleware('admin')->name('importPengajar');

    // API routes
    Route::get('/search-pengajar', 'searchPengajar')->middleware('admin')->name('searchPengajar');
});

// All Roles
Route::controller(ProfileController::class)->group(function () {
    // Get
    Route::get('/data-pengajar/profile/{token}', 'viewProfilePengajar')->middleware('admin')->name('viewProfileAdmin');
    Route::get('/profile-pengajar/{token}', 'viewProfilePengajar')->middleware('auth')->name('viewProfilePengajar');
    Route::get('/profile/{token}', 'viewProfileSiswa')->middleware('auth')->name('viewProfileSiswa');
    Route::get('/user-setting/{token}', 'viewProfileSetting')->middleware('auth')->name('viewProfileSetting');

    Route::post('/crop-photo-user', 'cropImageUser')->middleware('auth')->name('cropImageUser');
});

// Kelas
Route::controller(KelasController::class)->group(function () {
    Route::get('/data-kelas', 'viewKelas')->middleware('admin')->name('viewKelas');
    Route::get('/data-kelas/tambah-kelas', 'viewTambahKelas')->middleware('admin')->name('viewTambahKelas');
    Route::get('/data-kelas/success', 'dataKelasSuccess')->middleware('admin')->name('dataKelasSuccess');
    Route::get('/data-kelas/update-kelas/{kelas:id}', 'viewUpdateKelas')->middleware('admin')->name('viewUpdateKelas');
    Route::get('/data-kelas/get-kelas', 'viewDetailKelas')->middleware('admin')->name('viewDetailKelas');

    Route::post('/validate-kelas', 'validateNamaKelas')->middleware('admin')->name('validateNamaKelas');
    Route::post('/destroy-kelas', 'destroyKelas')->middleware('admin')->name('destroyKelas');
    Route::post('/update-kelas', 'updateKelas')->middleware('admin')->name('updateKelas');

    Route::get('/export-Kelas', 'export')->middleware('admin')->name('exportKelas');
    Route::get('/contoh-Kelas', 'contohKelas')->middleware('admin')->name('contohKelas');
    Route::post('/import-Kelas', 'import')->middleware('admin')->name('importKelas');

    // API routes
    Route::get('/search-kelas', 'searchKelas')->middleware('admin')->name('searchKelas');
});

// All Mapel
Route::controller(MapelController::class)->group(function () {
    // Get
    Route::get('/data-mapel', 'viewMapel')->middleware('admin')->name('viewMapel');
    Route::get('/data-mapel/tambah-mapel', 'viewTambahMapel')->middleware('admin')->name('viewTambahMapel');
    Route::get('/data-mapel/update-mapel/{mapel:id}', 'viewUpdateMapel')->middleware('admin')->name('viewUpdateMapel');
    Route::get('/data-mapel/success', 'dataMapelSuccess')->middleware('admin')->name('dataMapelSuccess');
    Route::get('/cek-kelas-mapel', 'cekKelasMapel')->middleware('admin')->name('cekKelasMapel');

    Route::post('/validate-mapel', 'validateNamaMapel')->middleware('admin')->name('validateNamaMapel');
    Route::post('/add-change-access', 'addChangeEditorAccess')->middleware('admin')->name('addChangeEditorAccess');
    Route::post('/add-editor-access', 'tambahEditorAccess')->middleware('admin')->name('tambahEditorAccess');
    Route::post('/delete-editor-access', 'deleteEditorAccess')->middleware('admin')->name('deleteEditorAccess');
    Route::post('/update-mapel', 'updateMapel')->middleware('admin')->name('updateMapel');
    Route::post('/destroy-mapel', 'destroyMapel')->middleware('admin')->name('destroyMapel');
    Route::post('/mapel-crop-image', 'mapelTambahGambar')->middleware('admin')->name('mapelTambahGambar');

    Route::get('/export-mapel', 'export')->middleware('admin')->name('exportMapel');
    Route::get('/contoh-mapel', 'contohMapel')->middleware('admin')->name('contohMapel');
    Route::post('/import-mapel', 'import')->middleware('admin')->name('importMapel');

    // API routes
    Route::get('/search-mapel', 'searchMapel')->middleware('admin')->name('searchMapel');
    Route::get('/search-mapel-from-kelas', 'searchKelasMapel')->middleware('admin')->name('searchKelasMapel');
});

// DataSiswa
Route::controller(DataSiswaController::class)->group(function () {
    Route::get('/data-siswa', 'viewSiswa')->middleware('admin')->name('viewSiswa');
    Route::get('/data-siswa/tambah-siswa', 'viewTambahSiswa')->middleware('admin')->name('viewTambahSiswa');
    Route::get('/data-siswa/update-siswa/{data_siswa:id}', 'viewUpdateDataSiswa')->middleware('admin')->name('viewUpdateDataSiswa');
    Route::get('/data-siswa/success', 'dataSiswaSuccess')->middleware('admin')->name('dataSiswaSuccess');
    Route::get('/data-siswa/update/{token}', 'viewUpdateUserSiswa')->middleware('admin')->name('viewUpdateUserSiswa');

    Route::post('/update-user-siswa', 'updateUserSiswa')->middleware('auth')->name('updateUserSiswa');
    Route::post('/validate-data-siswa', 'validateDataSiswa')->middleware('admin')->name('validateDataSiswa');
    Route::post('/destroy-siswa', 'destroyDataSiswa')->middleware('admin')->name('destroyDataSiswa');
    Route::post('/update-siswa', 'updateDataSiswa')->middleware('admin')->name('updateSiswa');

    Route::get('/export-siswa', 'export')->middleware('admin')->name('exportSiswa');
    Route::get('/contoh-siswa', 'contohSiswa')->middleware('admin')->name('contohSiswa');

    Route::post('/import-siswa', 'import')->middleware('admin')->name('importSiswa');

    // API routes
    Route::get('/search-siswa', 'searchSiswa')->middleware('admin')->name('searchSiswa');
    Route::get('/search-siswa-kelas', 'viewSiswaKelas')->middleware('auth')->name('viewSiswaKelas');
});

// File
Route::controller(FileController::class)->group(function () {
    Route::get('/getFile/{namaFile}', 'getFile')->middleware('auth')->name('getFile');
    Route::get('/getFileTugas/{namaFile}', 'getFileTugas')->middleware('auth')->name('getFileTugas');
    Route::get('/getFileUser/{namaFile}', 'getFileUser')->middleware('auth')->name('getFileUser');
});
