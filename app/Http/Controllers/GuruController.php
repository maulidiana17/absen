<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiExport;
use App\Exports\AbsensiMingguanExport;
use App\Models\User;
use App\Models\Guru;
use App\Models\QRAbsen;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Siswa;

class GuruController extends Controller
{

    public function index(Request $request)
    {
        $query = Guru::with('user')->orderBy('id');

        if (!empty($request->nama_guru)) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->nama_guru . '%');
            });
        }

        $gurus = $query->paginate(10);

        return view('guruu.index', compact('gurus'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'nip' => 'required|string',
            'mapel' => 'required|string',
            'kode_guru' => 'required|string',
            'alamat' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
             'role' => 'guru',
        ]);

        $guru = new Guru();
        $guru->user_id = $user->id;
        $guru->nip = $request->nip;
        $guru->mapel = $request->mapel;
        $guru->kode_guru = $request->kode_guru;
        $guru->alamat = $request->alamat;
        $guru->save();

        return redirect()->route('guru.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function edit(Guru $guru)
    {
        return view('guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        //dd($request->all());

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $guru->user_id,
            'nip' => 'required|string',
            'mapel' => 'required|string',
            'kode_guru' => 'required|string',
            'alamat' => 'required|string',
            'password' => 'nullable|string|min:6',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if (!empty($request->password)) {
            $userData['password'] = bcrypt($request->password);
        }

        $guru->user->update($userData);

        $guru->update([
            'nip' => $request->nip,
            'mapel' => $request->mapel,
            'kode_guru' => $request->kode_guru,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('guru.index')->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();

        return redirect()->route('guru.index')->with('success', 'Guru berhasil dihapus.');
    }
    //fiks benar
    public function qr()
    {
        $hariini = Carbon::today()->toDateString();

        if (Auth::user()->role !== 'guru') {
            abort(403, 'Akses ditolak.');
        }

        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->first();

        if (!$guru) {
            abort(404, 'Data guru tidak ditemukan.');
        }

        $timestamp = Carbon::now()->timestamp;
        $intervalKey = floor($timestamp / (30 * 60));

        $data = json_encode([
            'nama' => $user->name,
            'nip' => $guru->nip,
            'mapel' => $guru->mapel,
            'token' => $intervalKey,
        ]);

        $qrCode = QrCode::size(250)->generate($data);
       // $siswaAbsenHariIni = QRAbsen::whereDate('waktu', Carbon::today())->get();
        $siswaAbsenHariIni = QRAbsen::whereDate('waktu', Carbon::today())
            ->where('nip', $guru->nip)
            ->get();

          $siswaIzin = DB::table('pengajuan_izin')
            ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
            ->select('siswa.nis', 'siswa.nama_lengkap', 'siswa.kelas', 'pengajuan_izin.tanggal_izin', 'pengajuan_izin.tanggal_izin_akhir')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->where('pengajuan_izin.status', 'i')
            ->get();

        $siswaSakit = DB::table('pengajuan_izin')
            ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
            ->select('siswa.nis', 'siswa.nama_lengkap', 'siswa.kelas', 'pengajuan_izin.tanggal_izin', 'pengajuan_izin.tanggal_izin_akhir')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->where('pengajuan_izin.status', 's')
            ->get();

        $hariMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $hariIndo = $hariMap[date('l')];

        $jadwalMengajarMinggu = DB::connection('mysql_spensa')
            ->table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->where('jadwal.guru_id', $guru->id)
            ->whereIn(DB::raw('LOWER(waktu.hari)'), ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'])
            ->select('mapel.mapel', 'kelas.nama as nama_kelas', 'waktu.jam_mulai', 'waktu.jam_selesai', 'waktu.hari')
            ->orderByRaw("FIELD(LOWER(waktu.hari), 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu')")
            ->orderBy('waktu.jam_mulai')
            ->get()
            ->groupBy('hari');

        $jadwalHariIni = DB::connection('mysql_spensa')
            ->table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->where('jadwal.guru_id', $guru->id)
            ->whereRaw('LOWER(waktu.hari) = ?', [strtolower($hariIndo)])
            ->select('kelas.nama as nama_kelas', 'kelas.id as kelas_id')
            ->get();
        $daftarSiswaKelasHariIni = collect();

        foreach ($jadwalHariIni as $jadwal) {
            $siswaKelas = DB::table('siswa')
                ->where('kelas', $jadwal->nama_kelas)
                ->select('nis', 'nama_lengkap', 'kelas')
                ->get();

            $daftarSiswaKelasHariIni = $daftarSiswaKelasHariIni->merge($siswaKelas);
        }
        $jadwalHariIni = DB::connection('mysql_spensa')
            ->table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->where('jadwal.guru_id', $guru->id)
            ->whereRaw('LOWER(waktu.hari) = ?', [strtolower($hariIndo)])
            //->select('kelas.nama as nama_kelas')
            ->select('kelas.nama as nama_kelas', 'mapel.mapel')
            ->get();

        $absensiHariIni = DB::table('qr_absens')
            // ->whereDate('waktu', $hariini)
            // ->pluck('nis');
                ->whereDate('waktu', $hariini)
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->nis . '|' . $item->mapel => true];
                });

        $izinHariIni = DB::table('pengajuan_izin')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->get()
            ->keyBy('nis');

        $daftarSiswaKelasHariIni = collect();

        foreach ($jadwalHariIni as $jadwal) {
            $siswaKelas = DB::table('siswa')
                ->where('kelas', $jadwal->nama_kelas)
                ->select('nis', 'nama_lengkap', 'kelas')
                ->get();

        foreach ($siswaKelas as $siswa) {
            $key = $siswa->nis . '|' . $jadwal->mapel;
            $status = 'Alfa';

            if ($absensiHariIni->has($key)) {
                $status = 'Hadir';
            } elseif (isset($izinHariIni[$siswa->nis])) {
                $izin = $izinHariIni[$siswa->nis];
                $status = $izin->status === 's' ? 'Sakit' : 'Izin';
            }

            $siswa->status = $status;
            $siswa->mapel = $jadwal->mapel;
            $daftarSiswaKelasHariIni->push($siswa);
        }

        }

        return view('dashboard.qr', compact('qrCode', 'guru', 'siswaAbsenHariIni', 'siswaIzin',
            'siswaSakit',
            'jadwalMengajarMinggu',
            'daftarSiswaKelasHariIni'));
    }

    // public function getSiswaAbsenHariIni(Request $request)
    // {
    //     $query = QRAbsen::where('nip', Auth::user()->guru->nip ?? null);

    //     if ($request->filled('tanggal')) {
    //         $query->whereDate('waktu', $request->tanggal);
    //     } else {
    //         $query->whereDate('waktu', Carbon::today());
    //     }

    //     if ($request->filled('kelas')) {
    //         if (in_array($request->kelas, ['7', '8', '9'])) {
    //             $query->where('kelas', 'like', $request->kelas . '%');
    //         } else {
    //             $query->where('kelas', $request->kelas);
    //         }
    //     }

    //     return response()->json($query->select('nis', 'nama', 'kelas', 'waktu')->get());
    // }

    // public function exportExcel(Request $request)
    // {

    //     $tanggal = $request->input('tanggal') ?? Carbon::today()->toDateString();
    //     $kelas = $request->input('kelas');

    //     $user = Auth::user();
    //     $guru = Guru::where('user_id', $user->id)->first();

    //     if (!$guru) {
    //         abort(404, 'Data guru tidak ditemukan.');
    //     }

    //     $namaFile = 'absensi_' . $tanggal;


    //     if (!$kelas) {
    //         $namaFile .= '_Semua_Kelas';
    //     } elseif (in_array($kelas, ['7', '8', '9'])) {
    //         $namaFile .= '_Semua_Kelas_' . $kelas;
    //     } else {
    //         $namaFile .= '_' . str_replace(' ', '', strtoupper($kelas));
    //     }

    //     $namaFile .= '.xlsx';


    //     return Excel::download(
    //         new AbsensiExport($tanggal, $kelas, $guru->nip),
    //         // 'absensi_' . $tanggal . ($kelas ? '_' . $kelas : '') . '.xlsx'
    //         $namaFile
    //     );

    // }


    public function exportExcel(Request $request)
    {
        $request->validate([
            'semester' => 'required|in:1,2',
            'tahun' => 'required|integer|min:2020|max:2100',
        ]);

        $tahun = $request->tahun;

        if ($request->semester == 1) {
            $awal = \Carbon\Carbon::createFromDate($tahun, 1, 1);
            $akhir = \Carbon\Carbon::createFromDate($tahun, 6, 30)->endOfDay();
        } else {
            $awal = \Carbon\Carbon::createFromDate($tahun, 7, 1);
            $akhir = \Carbon\Carbon::createFromDate($tahun, 12, 31)->endOfDay();
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\AbsensiExport($awal, $akhir),
            'rekap-semester-' . $request->semester . '-' . $tahun . '.xlsx'
        );
    }

    public function exportMingguanManual(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        $awal = Carbon::parse($request->tanggal_awal)->startOfDay();
        $akhir = Carbon::parse($request->tanggal_akhir)->endOfDay();

        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        $kelasDiajar = DB::connection('mysql_spensa')
            ->table('jadwal')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->where('jadwal.guru_id', $guru->id)
            ->pluck('kelas.nama')
            ->unique()
            ->implode('-');

        return Excel::download(
            new AbsensiMingguanExport($awal, $akhir, $user->name),
            'absensi-manual-' . $awal->format('Ymd') . '-' . $akhir->format('Ymd') . '-' . $kelasDiajar . '-' . $user->name . '.xlsx'
        );
    }

    public function downloadQr()
    {
        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->first();

        if (!$guru) {
            abort(404, 'Data guru tidak ditemukan.');
        }

        $timestamp = Carbon::now()->timestamp;
        $intervalKey = floor($timestamp / (30 * 60));

        $data = json_encode([
            'nama' => $user->name,
            'nip' => $guru->nip,
            'mapel' => $guru->mapel,
            'interval_key' => $intervalKey,
        ]);

        $qrCode = QrCode::format('svg')->size(300)->generate($data);

        return response($qrCode)
        ->header('Content-Type', 'image/svg+xml')
        ->header('Content-Disposition', 'attachment; filename="qrcode_' . $guru->mapel . '.svg"');

    }

    //benar fiks
    public function qrindex()
    {
        $hariini = Carbon::today()->toDateString();

        $user = Auth::user();

        if ($user->role !== 'guru') {
            abort(403, 'Akses ditolak.');
        }

        $guru = Guru::where('user_id', $user->id)->first();

        if (!$guru) {
            abort(404, 'Data guru tidak ditemukan.');
        }

        $jmlhadir = DB::table('qr_absens')
            ->whereDate('waktu', $hariini)
            ->where('nip', $guru->nip)
            ->count();

        $jumlahizin = DB::table('pengajuan_izin')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->where('status', 'i')
            ->count();

        $jumlahsakit = DB::table('pengajuan_izin')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->where('status', 's')
            ->count();

        $jumlahterlambat = DB::table('absensi')
            ->where('tgl_absen', $hariini)
            ->where('jam_masuk', '>', '07:45')
            ->count();

        $siswaIzin = DB::table('pengajuan_izin')
            ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
            ->select('siswa.nis', 'siswa.nama_lengkap', 'siswa.kelas', 'pengajuan_izin.tanggal_izin', 'pengajuan_izin.tanggal_izin_akhir')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->where('pengajuan_izin.status', 'i')
            ->get();

        $siswaSakit = DB::table('pengajuan_izin')
            ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
            ->select('siswa.nis', 'siswa.nama_lengkap', 'siswa.kelas', 'pengajuan_izin.tanggal_izin', 'pengajuan_izin.tanggal_izin_akhir')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->where('pengajuan_izin.status', 's')
            ->get();

        $hariMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $hariIndo = $hariMap[date('l')];

        $jadwalMengajarMinggu = DB::connection('mysql_spensa')
            ->table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->where('jadwal.guru_id', $guru->id)
            ->whereIn(DB::raw('LOWER(waktu.hari)'), ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'])
            ->select('mapel.mapel', 'kelas.nama as nama_kelas', 'waktu.jam_mulai', 'waktu.jam_selesai', 'waktu.hari')
            ->orderByRaw("FIELD(LOWER(waktu.hari), 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu')")
            ->orderBy('waktu.jam_mulai')
            ->get()
            ->groupBy('hari');

        $jadwalHariIni = DB::connection('mysql_spensa')
            ->table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->where('jadwal.guru_id', $guru->id)
            ->whereRaw('LOWER(waktu.hari) = ?', [strtolower($hariIndo)])
            ->select('kelas.nama as nama_kelas', 'kelas.id as kelas_id')
            ->get();
        $daftarSiswaKelasHariIni = collect();

        foreach ($jadwalHariIni as $jadwal) {
            $siswaKelas = DB::table('siswa')
                ->where('kelas', $jadwal->nama_kelas) // atau sesuaikan jika pakai `kelas_id`
                ->select('nis', 'nama_lengkap', 'kelas')
                ->get();

            $daftarSiswaKelasHariIni = $daftarSiswaKelasHariIni->merge($siswaKelas);
        }
        $jadwalHariIni = DB::connection('mysql_spensa')
            ->table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->where('jadwal.guru_id', $guru->id)
            ->whereRaw('LOWER(waktu.hari) = ?', [strtolower($hariIndo)])
            ->select('kelas.nama as nama_kelas')
            ->get();

        $absensiHariIni = DB::table('qr_absens')
            ->whereDate('waktu', $hariini)
            ->pluck('nis');

        $izinHariIni = DB::table('pengajuan_izin')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->get()
            ->keyBy('nis');

        $daftarSiswaKelasHariIni = collect();

            foreach ($jadwalHariIni as $jadwal) {
                $siswaKelas = DB::table('siswa')
                    ->where('kelas', $jadwal->nama_kelas)
                    ->select('nis', 'nama_lengkap', 'kelas')
                    ->get();

                foreach ($siswaKelas as $siswa) {
                    $status = 'Alfa';
                    if ($absensiHariIni->contains($siswa->nis)) {
                        $status = 'Hadir';
                    } elseif (isset($izinHariIni[$siswa->nis])) {
                        $izin = $izinHariIni[$siswa->nis];
                        $status = $izin->status === 's' ? 'Sakit' : 'Izin';
                    }
                    $siswa->status = $status;
                    $daftarSiswaKelasHariIni->push($siswa);
                }
            }


        return view('dashboard.dashboardguru', compact(
            'jmlhadir',
            'jumlahizin',
            'jumlahsakit',
            'jumlahterlambat',
            'siswaIzin',
            'siswaSakit',
            'jadwalMengajarMinggu',
            'daftarSiswaKelasHariIni'
        ));
    }

    public function ubahKeAlfa(Request $request)
    {
        DB::table('qr_absens')
            ->where('nis', $request->nis)
            ->where('mapel', $request->mapel)
            ->whereDate('waktu', $request->tanggal)
            ->delete();

        return back()->with('success', 'Status diubah menjadi Alfa.');
    }

    public function ubahKeHadir(Request $request)
    {
        $tanggal = $request->tanggal ?? now()->toDateString();
        $nis = $request->nis;

        // Ambil data siswa
        $siswa = DB::table('siswa')->where('nis', $nis)->first();
        if (!$siswa) {
            return back()->with('error', 'Siswa tidak ditemukan.');
        }

        // Ambil data guru & mapel yang diajar hari ini
        $user = Auth::user();
        $guru = Guru::where('user_id', $user->id)->first();
        if (!$guru) {
            return back()->with('error', 'Guru tidak ditemukan.');
        }

        $hari = strtolower(Carbon::parse($tanggal)->format('l'));
        $hariMap = [
            'sunday' => 'minggu', 'monday' => 'senin', 'tuesday' => 'selasa',
            'wednesday' => 'rabu', 'thursday' => 'kamis',
            'friday' => 'jumat', 'saturday' => 'sabtu',
        ];
        $hariIndo = $hariMap[$hari];

        $jadwal = DB::connection('mysql_spensa')
            ->table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->where('jadwal.guru_id', $guru->id)
            ->whereRaw('LOWER(waktu.hari) = ?', [$hariIndo])
            ->where('kelas.nama', $siswa->kelas)
            ->select('mapel.mapel')
            ->first();

        $mapel = $jadwal->mapel ?? 'Tidak diketahui';

        // Simpan ke qr_absens
        DB::table('qr_absens')->insert([
            'nis' => $nis,
            'nama' => $siswa->nama_lengkap,
            'kelas' => $siswa->kelas,
            'nip' => $guru->nip,
            // 'mapel' => $mapel,
            'mapel' => $request->mapel,
            'waktu' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Status siswa berhasil diubah menjadi Hadir.');
    }

}
