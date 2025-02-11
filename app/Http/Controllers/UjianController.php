<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Ujian;
use App\Models\Kecermatan;
use App\Models\KelasMapel;
use App\Models\UserCommit;
use App\Models\UserJawaban;
use Illuminate\Http\Request;
use App\Models\SoalUjianEssay;
use App\Imports\SoalUjianImport;
use App\Models\SoalUjianMultiple;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserJawabanKecermatan;

class UjianController extends Controller
{
    public function viewPilihTipeUjian(Request $request)
    {
        $id = decrypt($request->token);
        $roles = DashboardController::getRolesName();
        $mapel = Mapel::where('id', $request->mapelId)->first();
        $kelasMapel = KelasMapel::where('mapel_id', $request->mapelId)->where('kelas_id', $id)->first();
        $assignedKelas = DashboardController::getAssignedClass();

        return view('menu.pengajar.ujian.viewPilihTipeUjian', ['assignedKelas' => $assignedKelas, 'title' => 'Tambah Ujian', 'roles' => $roles, 'kelasId' => $id, 'mapel' => $mapel]);
    }

    public function destroyUjian(Request $request)
    {
        $ujianId = $request->hapusId;
        $tipe = $request->tipe;

        foreach (Auth()->User()->EditorAccess as $key) {
            if ($key->kelas_mapel_id == $request->kelasMapelId) {
                Ujian::where('id', $ujianId)->delete();

                if ($tipe == 'multiple') {
                    SoalUjianMultiple::where('ujian_id', $ujianId)->delete();
                    UserJawaban::where('multiple_id', $ujianId)->delete();
                } else {
                    SoalUjianEssay::where('ujian_id', $ujianId)->delete();
                    UserJawaban::where('essay_id', $ujianId)->delete();
                }

                return redirect()->back()->with('success', 'Ujian Berhasil dihapus');
            }
        }
    }

    public function viewCreateUjian(Request $request)
    {
        $id = decrypt($request->token);
        $roles = DashboardController::getRolesName();
        $mapel = Mapel::where('id', $request->mapelId)->first();
        $assignedKelas = DashboardController::getAssignedClass();

        return view('menu.pengajar.ujian.viewTambahUjian', ['assignedKelas' => $assignedKelas, 'tipe' => $request->type, 'title' => 'Tambah Ujian', 'roles' => $roles, 'kelasId' => $id, 'mapel' => $mapel]);
    }

    public function createUjian(Request $request)
    {
        $name = $request->name ?? 'Ujian';
        $id = decrypt($request->kelasId);
        $kelasMapel = KelasMapel::where('mapel_id', $request->mapelId)->where('kelas_id', $id)->first();
        $isHidden = $request->opened ? 0 : 1;
        $tanggalWaktuIndonesia = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->due);

        $data = [
            'name' => $name,
            'isHidden' => $isHidden,
            'kelas_mapel_id' => $kelasMapel['id'],
            'tipe' => $request->tipe,
            'time' => $request->time,
            'due' => $tanggalWaktuIndonesia,
        ];

        $ujian = Ujian::create($data);

        if ($request->tipe == 'essay') {
            foreach ($request->pertanyaan as $key) {
                if ($key) {
                    SoalUjianEssay::create([
                        'ujian_id' => $ujian->id,
                        'soal' => $key,
                    ]);
                }
            }
        } elseif ($request->tipe == 'multiple') {
            for ($i = 0; $i < count($request->pertanyaan); $i++) {
                $d = $request->d[$i] ?? null;
                $e = $request->e[$i] ?? null;

                SoalUjianMultiple::create([
                    'ujian_id' => $ujian->id,
                    'soal' => $request->pertanyaan[$i],
                    'a' => $request->a[$i],
                    'b' => $request->b[$i],
                    'c' => $request->c[$i],
                    'd' => $d,
                    'e' => $e,
                    'jawaban' => $request->jawaban[$i],
                ]);
            }
        } else {
            for ($i = 0; $i < count($request->jumlahSoal); $i++) {
                $d = $request->d[$i] ?? null;
                $e = $request->e[$i] ?? null;

                Kecermatan::create([
                    'ujian_id' => $ujian->id,
                    'a' => $request->a[$i],
                    'b' => $request->b[$i],
                    'c' => $request->c[$i],
                    'd' => $d,
                    'e' => $e,
                    'jumlah_soal' => $request->jumlahSoal[$i],
                ]);

                $ujian->update([
                    'jumlah_kolom' => count($request->jumlahSoal)
                ]);
            }
        }

        $assignedKelas = DashboardController::getAssignedClass();
        return redirect(route('viewKelasMapel', ['assignedKelas' => $assignedKelas, 'mapel' => $request->mapelId, 'token' => encrypt($id), 'mapel_id' => $request->mapelId]))->with('success', 'Data Berhasil di tambah');
    }

    public function viewUjian($token, Request $request)
    {
        $ujianId = decrypt($token);
        $kelasMapelId = decrypt($request->kelasMapelId);
        $mapel = Mapel::where('id', $request->mapelId)->first();
        $kelasMapel = KelasMapel::where('id', $kelasMapelId)->first();
        $roles = DashboardController::getRolesName();
        $ujian = Ujian::where('id', $ujianId)->first();
        $kelas = Kelas::where('id', $kelasMapel['kelas_id'])->first();
        $assignedKelas = DashboardController::getAssignedClass();

        return view('menu.pengajar.ujian.viewUjian', ['assignedKelas' => $assignedKelas, 'title' => 'Tambah Ujian', 'ujian' => $ujian, 'roles' => $roles, 'kelas' => $kelas, 'mapel' => $mapel, 'tipe' => $ujian['tipe']]);
    }

    public function ujianUpdateNilai(Request $request)
    {
        $id = decrypt($request->token);
        for ($i = 0; $i < count($request->nilai); $i++) {
            if ($request->nilai[$i] !== null && $request->nilai[$i] !== '') {
                $exist = UserJawaban::where('user_id', $request->siswaId[$i])->where('essay_id', $request->soalId[$i])->first();
                $nilai = $request->nilai[$i];
                $nilai = max(0, min($nilai, 100));

                if ($exist) {
                    $exist->update(['nilai' => $nilai]);
                } else {
                    UserJawaban::create([
                        'multiple_id' => null,
                        'essay_id' => $request->soalId[$i],
                        'user_id' => $request->siswaId[$i],
                        'nilai' => $nilai,
                    ]);
                }
            }
        }
        return redirect()->back()->with('success', 'Nilai Telah diPerbaharui');
    }

    public function viewUpdateUjian($token, Request $request)
    {
        $ujianId = decrypt($token);
        $mapel = Mapel::where('id', $request->mapelId)->first();
        $kelasMapel = KelasMapel::where('kelas_id', $request->kelasId)->where('mapel_id', $request->mapelId)->first();
        $roles = DashboardController::getRolesName();
        $ujian = Ujian::where('id', $ujianId)->first();
        $kelas = Kelas::where('id', $kelasMapel['kelas_id'])->first();
        $assignedKelas = DashboardController::getAssignedClass();

        return view('menu.pengajar.ujian.viewUpdateUjian', ['assignedKelas' => $assignedKelas, 'tipe' => $request->type, 'title' => 'Tambah Ujian', 'ujian' => $ujian, 'roles' => $roles, 'kelas' => $kelas, 'mapel' => $mapel]);
    }

    public function updateUjian(Request $request)
    {
        $name = $request->name ?? 'Ujian';
        $id = decrypt($request->kelasId);
        $kelasMapel = KelasMapel::where('mapel_id', $request->mapelId)->where('kelas_id', $id)->first();
        $isHidden = $request->opened ? 0 : 1;
        $ujianId = decrypt($request->token);
        $tanggalWaktuIndonesia = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->due);

        $data = [
            'name' => $name,
            'isHidden' => $isHidden,
            'time' => $request->time,
            'due' => $tanggalWaktuIndonesia,
        ];

        Ujian::where('id', $ujianId)->update($data);

        $temp = null;
        if ($request->tipe == 'essay') {
            $temp = SoalUjianEssay::where('ujian_id', $ujianId)->get();
        } elseif ($request->tipe == 'multiple') {
            $temp = SoalUjianMultiple::where('ujian_id', $ujianId)->get();
        } elseif ($request->tipe == 'kecermatan') {
            $temp = Kecermatan::where('ujian_id', $ujianId)->get();
        }

        $soalIdRequest = $request->pertanyaanId;
        $soalUjianIds = [];

        foreach ($temp as $key) {
            $soalUjianIds[] = $key->id;
        }
        $idToDelete = array_diff($soalUjianIds, $soalIdRequest);

        if ($request->tipe == 'essay') {
            if ($idToDelete) {
                SoalUjianEssay::whereIn('id', $idToDelete)->delete();
            }
            foreach ($request->pertanyaan as $i => $pertanyaan) {
                $exist = SoalUjianEssay::find($request->pertanyaanId[$i]);

                $data = [
                    'ujian_id' => $ujianId,
                    'soal' => $pertanyaan,
                ];

                if ($exist) {
                    $exist->update($data);
                } elseif ($pertanyaan) {
                    SoalUjianEssay::create($data);
                }
            }
        } elseif ($request->tipe == 'multiple') {
            if ($idToDelete) {
                SoalUjianMultiple::whereIn('id', $idToDelete)->delete();
            }
            foreach ($request->pertanyaan as $i => $pertanyaan) {
                $exist = SoalUjianMultiple::find($request->pertanyaanId[$i]);

                $data = [
                    'ujian_id' => $ujianId,
                    'soal' => $pertanyaan,
                    'a' => $request->a[$i],
                    'b' => $request->b[$i],
                    'c' => $request->c[$i],
                    'd' => $request->d[$i] ?? null,
                    'e' => $request->e[$i] ?? null,
                    'jawaban' => $request->jawaban[$i],
                ];

                if ($exist) {
                    $exist->update($data);
                } elseif ($pertanyaan) {
                    SoalUjianMultiple::create($data);
                }
            }
        } elseif ($request->tipe == 'kecermatan') {
            if ($idToDelete) {
                Kecermatan::whereIn('id', $idToDelete)->delete();
            }
            foreach ($request->pertanyaanId as $i => $pertanyaanId) {
                $exist = Kecermatan::find($pertanyaanId);

                $data = [
                    'ujian_id' => $ujianId,
                    'a' => $request->a[$i],
                    'b' => $request->b[$i],
                    'c' => $request->c[$i],
                    'd' => $request->d[$i] ?? null,
                    'e' => $request->e[$i] ?? null,
                    'jumlah_soal' => $request->jumlahSoal[$i],
                ];

                if ($exist) {
                    $exist->update($data);
                } else {
                    Kecermatan::create($data);
                }
            }
        }

        $assignedKelas = DashboardController::getAssignedClass();
        return redirect(route('viewKelasMapel', ['assignedKelas' => $assignedKelas, 'mapel' => $request->mapelId, 'token' => encrypt($id), 'mapel_id' => $request->mapelId]))->with('success', 'Data Berhasil di Update');
    }

    public function contohEssay()
    {
        $file = public_path() . '/examples/contoh-data-essay.xls';
        return response()->download($file, 'contoh-essay.xls');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
        session()->forget('soal', []);
        session()->forget('info', []);

        $info = session('info', []);
        array_push($info, $request->name);
        array_push($info, $request->time);
        array_push($info, $request->due);
        session(['info' => $info]);

        try {
            if ($request->tipe == 'essay') {
                Excel::import(new SoalUjianImport, $request->file('file'));
                return redirect()->back()->with('soalEssay', session('soal'))->with('info', session('info'));
            } elseif ($request->tipe == 'multiple' || $request->tipe == 'quiz') {
                Excel::import(new SoalUjianImport, $request->file('file'));
                return redirect()->back()->with('soalMultiple', session('soal'))->with('info', session('info'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'action gagal')->with('info', session('info'));
        }
    }

    public function contohMultiple()
    {
        $file = public_path() . '/examples/contoh-data-multiple.xls';
        return response()->download($file, 'contoh-multiple.xls');
    }

    public function ujianAccess($token, Request $request)
    {
        $id = decrypt($token);
        $ujian = Ujian::where('id', $id)->first();
        $userCommit = UserCommit::where('user_id', Auth()->User()->id)->where('ujian_id', $ujian['id'])->first();
        $mapel = Mapel::where('id', $request->mapelId)->first();
        $kelas = Kelas::where('id', $request->kelasId)->first();
        $data = [
            'content' => $ujian,
            'tipe' => $ujian['tipe'],
        ];
        $roles = DashboardController::getRolesName();
        $assignedKelas = DashboardController::getAssignedClass();
        $quizCommit = $ujian['tipe'] == 'quiz' && $userCommit ? 1 : 0;

        if ($userCommit && $ujian['tipe'] != 'quiz' && $userCommit['status'] == 'active') {
            return redirect(route('userUjian', ['ujian' => $ujian['name'], 'token' => encrypt($ujian['id'])]));
        }

        return view('menu.siswa.ujian.ujianAccess', ['quizCommit' => $quizCommit, 'ujian' => $ujian, 'userCommit' => $userCommit, 'assignedKelas' => $assignedKelas, 'tipe' => $data['tipe'], 'kelas' => $kelas, 'mapel' => $mapel, 'title' => 'Tambah Ujian', 'ujian' => $ujian, 'roles' => $roles]);
    }

    // public function startUjian($token)
    // {
    //     $ujianId = decrypt($token);
    //     $ujian = Ujian::find($ujianId);

    //     UserCommit::create([
    //         'user_id' => auth()->user()->id,
    //         'ujian_id' => $ujianId,
    //         'start_time' => now()->format('Y-m-d H:i:s'),
    //         'end_time' => now()->addMinutes($ujian->time)->format('Y-m-d H:i:s'),
    //         'due' => $ujian->due,
    //     ]);

    //     return redirect(route('userUjian', ['ujian' => $ujian->name, 'token' => encrypt($ujian->id)]));
    // }

    public function startUjian($token)
    {
        $ujianId = decrypt($token);
        $ujian = Ujian::find($ujianId);
        $userId = auth()->user()->id;

        // Hapus jawaban lama untuk soal multiple choice (jika ada)
        if ($ujian->tipe == 'multiple') {
            // Ambil semua ID soal untuk ujian tersebut
            $soalIds = $ujian->soalUjianMultiple->pluck('id');
            UserJawaban::where('user_id', $userId)
                ->whereIn('multiple_id', $soalIds)
                ->delete();

            // Hapus juga session penyimpanan urutan soal acak
            $sessionKey = 'shuffledSoal_' . $ujian->id . '_' . $userId;
            session()->forget($sessionKey);
        }

        // (Untuk tipe lain, lakukan hal serupa jika diperlukan)

        // Jika tipe ujian multiple, simpan urutan soal acak ke session (fresh)
        if ($ujian->tipe == 'multiple') {
            $sessionKey = 'shuffledSoal_' . $ujian->id . '_' . $userId;
            $shuffled = $ujian->soalUjianMultiple->shuffle()->toArray();
            session([$sessionKey => $shuffled]);
        }

        // Buat record baru untuk commit ujian
        UserCommit::create([
            'user_id'    => $userId,
            'ujian_id'   => $ujianId,
            'start_time' => now()->format('Y-m-d H:i:s'),
            'end_time'   => now()->addMinutes($ujian->time)->format('Y-m-d H:i:s'),
            'due'        => $ujian->due,
        ]);

        return redirect(route('userUjian', [
            'ujian' => $ujian->name,
            'token' => encrypt($ujian->id)
        ]));
    }


    // public function userUjian($ujian, $token)
    // {
    //     $ujianId = decrypt($token);

    //     try {
    //         $userCommit = UserCommit::where('user_id', Auth()->user()->id)->where('ujian_id', $ujianId)->orderBy('created_at', 'desc')->first();
    //         $userCommitFirst = UserCommit::where('user_id', Auth()->user()->id)->where('ujian_id', $ujianId)->orderBy('created_at', 'asc')->first();
    //     } catch (Exception $e) {
    //         abort(404);
    //     }

    //     $ujian = Ujian::find($ujianId);
    //     $roles = DashboardController::getRolesName();
    //     $assignedKelas = DashboardController::getAssignedClass();

    //     if ($ujian->tipe == 'multiple') {
    //         $soalUjianMultiple = $ujian->soalUjianMultiple;

    //         if ($ujian->tipe == 'multiple' && $userCommitFirst['status'] == 'selesai') {
    //             foreach ($soalUjianMultiple as $key) {
    //                 UserJawaban::where('user_id', auth()->user()->id)
    //                     ->where('multiple_id', $key->id)
    //                     ->delete();
    //             }
    //             $userCommitFirst->delete();
    //         }

    //         return view('menu.siswa.ujian.startUjianMultiple', [
    //             'userCommit' => $userCommit,
    //             'ujian' => $ujian,
    //             'soalUjianMultiple' => $soalUjianMultiple,
    //             'title' => $ujian->name,
    //             'roles' => $roles,
    //             'assignedKelas' => $assignedKelas,
    //         ]);
    //     } elseif ($ujian->tipe == 'kecermatan') {
    //         $soalUjianMultiple = $ujian->soalUjianMultiple;

    //         if ($userCommit->status == 'active' || $userCommit->status == 'selesai') {
    //             foreach ($ujian->Kecermatan as $key) {
    //                 UserJawabanKecermatan::where('kecermatan_id', $key->id)->delete();
    //             }
    //         }

    //         foreach ($ujian->Kecermatan as $key) {
    //             for ($i = 0; $i < $key->jumlah_soal; $i++) {
    //                 $letters = [$key->a, $key->b, $key->c];

    //                 if ($key->d !== null) {
    //                     $letters[] = $key->d;
    //                 }

    //                 if ($key->e !== null) {
    //                     $letters[] = $key->e;
    //                 }

    //                 shuffle($letters);
    //                 $answer = array_pop($letters);

    //                 if ($key->a == $answer) {
    //                     $answer = "a";
    //                 } else if ($key->b == $answer) {
    //                     $answer = "b";
    //                 } else if ($key->c == $answer) {
    //                     $answer = "c";
    //                 } else if ($key->d == $answer) {
    //                     $answer = "d";
    //                 } else if ($key->e == $answer) {
    //                     $answer = "e";
    //                 }

    //                 $soal = implode('', $letters);

    //                 UserJawabanKecermatan::create([
    //                     "user_id" => auth()->user()->id,
    //                     "soal" => $soal,
    //                     "jawaban" => $answer,
    //                     "kecermatan_id" => $key->id,
    //                 ]);
    //             }
    //         }

    //         $userCommit->update(["status" => "selesai"]);

    //         $userJawabanKecermatan = UserJawabanKecermatan::where('user_id', Auth()->user()->id)
    //             ->whereIn('kecermatan_id', $ujian->Kecermatan->pluck('id'))
    //             ->get();

    //         return view('menu.siswa.ujian.startUjianKecermatan', [
    //             'userCommit' => $userCommit,
    //             'ujian' => $ujian,
    //             'userJawabanKecermatan' => $userJawabanKecermatan,
    //             'title' => $ujian->name,
    //             'roles' => $roles,
    //             'assignedKelas' => $assignedKelas,
    //         ]);
    //     } else {
    //         $soalUjianEssay = $ujian->SoalUjianEssay;

    //         return view('menu.siswa.ujian.startUjian', [
    //             'userCommit' => $userCommit,
    //             'ujian' => $ujian,
    //             'soalUjianEssay' => $soalUjianEssay,
    //             'title' => $ujian->name,
    //             'roles' => $roles,
    //             'assignedKelas' => $assignedKelas,
    //         ]);
    //     }
    // }

    // public function userUjian($ujian, $token)
    // {
    //     $ujianId = decrypt($token);

    //     try {
    //         $userCommit = UserCommit::where('user_id', Auth()->user()->id)->where('ujian_id', $ujianId)->orderBy('created_at', 'desc')->first();
    //         $userCommitFirst = UserCommit::where('user_id', Auth()->user()->id)->where('ujian_id', $ujianId)->orderBy('created_at', 'asc')->first();
    //     } catch (Exception $e) {
    //         abort(404);
    //     }

    //     $ujian = Ujian::find($ujianId);

    //     $roles = DashboardController::getRolesName();
    //     $assignedKelas = DashboardController::getAssignedClass();

    //     if ($ujian->tipe == 'multiple') {
    //         $soalUjianMultiple = $ujian->soalUjianMultiple; // Ambil semua soal dari ujian


    //         if ($ujian->tipe == 'multiple' && $userCommitFirst['status'] == 'selesai') {
    //             foreach ($soalUjianMultiple as $key) {
    //                 UserJawaban::where('user_id', auth()->user()->id)
    //                     ->where('multiple_id', $key->id)
    //                     ->delete();
    //             }
    //             $userCommitFirst->delete();
    //             // dd('here');
    //         }

    public function userUjian($ujian, $token)
    {
        $ujianId = decrypt($token);

        try {
            $userCommit = UserCommit::where('user_id', Auth()->user()->id)
                ->where('ujian_id', $ujianId)
                ->orderBy('created_at', 'desc')
                ->first();
        } catch (Exception $e) {
            abort(404);
        }

        $ujian = Ujian::find($ujianId);
        $roles = DashboardController::getRolesName();
        $assignedKelas = DashboardController::getAssignedClass();

        if ($ujian->tipe == 'multiple') {
            $sessionKey = 'shuffledSoal_' . $ujian->id . '_' . auth()->user()->id;
            if (session()->has($sessionKey)) {
                $soalUjianMultiple = session($sessionKey);
            } else {
                $soalUjianMultiple = $ujian->soalUjianMultiple->shuffle()->toArray();
                session([$sessionKey => $soalUjianMultiple]);
            }
            // Jika status UserCommit sudah 'selesai', kita aktifkan mode review
            $reviewMode = $userCommit->status == 'selesai';

            return view('menu.siswa.ujian.startUjianMultiple', [
                'userCommit'        => $userCommit,
                'ujian'             => $ujian,
                'soalUjianMultiple' => $soalUjianMultiple,
                'title'             => $ujian->name,
                'roles'             => $roles,
                'assignedKelas'     => $assignedKelas,
                'reviewMode'        => $reviewMode,  // Flag untuk menandai mode review/kunci jawaban
            ]);
        } elseif ($ujian->tipe == 'kecermatan') {

            $soalUjianMultiple = $ujian->soalUjianMultiple;

            if ($userCommit->status == 'active' || $userCommit->status == 'selesai') {
                foreach ($ujian->Kecermatan as $key) {
                    UserJawabanKecermatan::where('kecermatan_id', $key->id)->where('user_id', Auth()->user()->id)->delete();
                }
                // dd($ujian->Kecermatan);
                // dd("here2");
                // userCommit::where('ujian_id', $ujian->id)->delete();
            }
            // dd($userCommit->status);
            foreach ($ujian->Kecermatan as $key) {
                for ($i = 0; $i < $key->jumlah_soal; $i++) {
                    $letters = [$key->a, $key->b, $key->c];

                    if ($key->d !== null) { // Include D if it's not null
                        $letters[] = $key->d;
                    }

                    if ($key->e !== null) { // Include E if it's not null
                        $letters[] = $key->e;
                    }

                    shuffle($letters);
                    $answer = array_pop($letters); // Remove one letter to be the answer

                    if ($key->a == $answer) {
                        $answer = "a";
                    } else if ($key->b == $answer) {
                        $answer = "b";
                    } else if ($key->c == $answer) {
                        $answer = "c";
                    } else if ($key->d == $answer) {
                        $answer = "d";
                    } else if ($key->e == $answer) {
                        $answer = "e";
                    }

                    $soal = implode('', $letters); // Convert the array to a string

                    $data = [
                        "user_id" => auth()->user()->id,
                        "soal" => $soal,
                        "jawaban" => $answer,
                        "kecermatan_id" => $key->id,
                    ];

                    UserJawabanKecermatan::create($data);
                }
            }

            $data = ["status" => "selesai"];
            $userCommit->update($data);

            $userJawabanKecermatan = UserJawabanKecermatan::where('user_id', Auth()->user()->id)
                ->whereIn('kecermatan_id', $ujian->Kecermatan->pluck('id'))
                ->get();

            return view('menu.siswa.ujian.startUjianKecermatan', [
                'userCommit' => $userCommit,
                'ujian' => $ujian,
                'userJawabanKecermatan' => $userJawabanKecermatan, // Kirim data soal ke view
                'title' => $ujian->name,
                'roles' => $roles,
                'assignedKelas' => $assignedKelas,
            ]);
        } else {
            $soalUjianEssay = $ujian->SoalUjianEssay; // Ambil semua soal dari ujian

            return view('menu.siswa.ujian.startUjian', [
                'userCommit' => $userCommit,
                'ujian' => $ujian,
                'soalUjianEssay' => $soalUjianEssay, // Kirim data soal ke view
                'title' => $ujian->name,
                'roles' => $roles,
                'assignedKelas' => $assignedKelas,
            ]);
        }
    }

    public function simpanJawaban(Request $request)
    {
        $soalId = $request->input('soal_id');
        $jawaban = $request->input('jawaban');

        $existingJawaban = UserJawaban::where('user_id', auth()->user()->id)
            ->where('essay_id', $soalId)
            ->first();

        if ($existingJawaban) {
            $existingJawaban->update(['user_jawaban' => $jawaban]);
        } else {
            UserJawaban::create([
                'user_id' => auth()->user()->id,
                'essay_id' => $soalId,
                'user_jawaban' => $jawaban,
            ]);
        }

        return response()->json(['message' => 'Jawaban berhasil disimpan.']);
    }

    public function simpanJawabanKecermatan(Request $request)
    {
        $soalId = $request->input('soal_id');
        $jawaban = $request->input('jawaban');

        $existingJawaban = UserJawabanKecermatan::where('user_id', auth()->user()->id)
            ->where('id', $soalId)
            ->first();

        if ($existingJawaban) {
            $existingJawaban->update(['jawaban_user' => $jawaban]);
        } else {
            UserJawabanKecermatan::create([
                'user_id' => auth()->user()->id,
                'essay_id' => $soalId,
                'jawaban_user' => $jawaban,
            ]);
        }

        return response()->json(['message' => 'Jawaban berhasil disimpan.']);
    }

    public function getJawaban(Request $request)
    {
        $jawaban = UserJawaban::where('essay_id', $request->soal_id)->where('user_id', Auth()->User()->id)->first();

        if ($jawaban) {
            return response()->json(['jawaban' => $jawaban->user_jawaban]);
        } else {
            return response()->json(['jawaban' => null]);
        }
    }

    public function getJawabanMultiple(Request $request)
    {
        $jawaban = UserJawaban::where('multiple_id', $request->soal_id)->where('user_id', Auth()->User()->id)->first();

        if ($jawaban) {
            return response()->json(['jawaban' => $jawaban->user_jawaban]);
        } else {
            return response()->json(['jawaban' => null]);
        }
    }

    public function selesaiUjian(Request $request)
    {
        $id = decrypt($request->userCommit);
        $userCommit = UserCommit::where('id', $id)->first();
        $userCommit->update(['status' => 'selesai']);
        return redirect('home')->with('success', 'Ujian berhasil di submit');
    }

    // public function selesaiUjianMultiple(Request $request)
    // {
    //     try {
    //         $id = decrypt($request->userCommit);
    //         $userCommit = UserCommit::where('id', $id)->first();
    //         $userCommit->update(['status' => 'selesai']);
    //         $ujian = Ujian::where('id', $userCommit['ujian_id'])->first();
    //         $countUjian = count($ujian->SoalUjianMultiple);
    //         $nilaiPerSoal = 100 / $countUjian;

    //         foreach ($ujian->SoalUjianMultiple as $key) {
    //             $jawabanUser = UserJawaban::where('multiple_id', $key->id)->where('user_id', Auth()->User()->id)->first();
    //             if ($jawabanUser) {
    //                 if (strcasecmp($key->jawaban, $jawabanUser['user_jawaban']) === 0) {
    //                     $jawabanUser->update(['nilai' => $nilaiPerSoal]);
    //                 } else {
    //                     $jawabanUser->update(['nilai' => 0]);
    //                 }
    //             }
    //         }
    //         return redirect('home')->with('success', 'Ujian berhasil di submit');
    //     } catch (Exception $e) {
    //         return redirect('home')->with('success', 'Ujian berhasil di submit');
    //     }
    // }

    public function selesaiUjianMultiple(Request $request)
    {
        try {
            $id = decrypt($request->userCommit);
            $userCommit = UserCommit::find($id);
            $userCommit->update(['status' => 'selesai']);

            $ujian = Ujian::find($userCommit->ujian_id);
            $soalCount = $ujian->soalUjianMultiple->count();
            $nilaiPerSoal = $soalCount > 0 ? (100 / $soalCount) : 0;

            foreach ($ujian->soalUjianMultiple as $soal) {
                $jawabanUser = UserJawaban::where('multiple_id', $soal->id)
                    ->where('user_id', Auth()->user()->id)
                    ->first();
                if ($jawabanUser) {
                    // Perbandingan tanpa memperhatikan kapital dan spasi ekstra
                    if (strcasecmp(trim($soal->jawaban), trim($jawabanUser->user_jawaban)) === 0) {
                        $jawabanUser->update(['nilai' => $nilaiPerSoal]);
                    } else {
                        $jawabanUser->update(['nilai' => 0]);
                    }
                }
            }

            // Redirect langsung ke halaman utama setelah submit ujian
            return redirect('home')->with('success', 'Ujian berhasil disubmit!');
        } catch (Exception $e) {
            return redirect('home')->with('error', 'Terjadi kesalahan saat submit ujian');
        }
    }




    public function selesaiUjianKecermatan(Request $request)
    {
        return redirect('home')->with('success', 'Ujian berhasil di submit');
    }

    public function simpanJawabanMultiple(Request $request)
    {
        $soalId = $request->input('soal_id');
        $jawaban = $request->input('jawaban');

        $existingJawaban = UserJawaban::where('user_id', auth()->user()->id)
            ->where('multiple_id', $soalId)
            ->first();

        if ($existingJawaban) {
            $existingJawaban->update(['user_jawaban' => $jawaban]);
        } else {
            UserJawaban::create([
                'user_id' => auth()->user()->id,
                'multiple_id' => $soalId,
                'user_jawaban' => $jawaban,
            ]);
        }

        return response()->json(['message' => 'Jawaban berhasil disimpan.']);
    }
}
