<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
//Middleware siswa login biasa
// Route::middleware(['guest:siswa'])->group(function () {
//     Route::get('/login', function () {
//         return view('auth.login');
//     })->name('login');
//     Route::post('/prosesLogin', [AuthController::class, 'prosesLogin']);
// });


// Middleware siswa login animasi
// Route::middleware(['guest:siswa'])->group(function () {
//     Route::get('/loginn', function () {
//         return view('auth.loginn');
//     })->name('login');
//     Route::post('/prosesLoginn', [AuthController::class, 'prosesLoginn']);
// });


//Middleware untuk login admin atau guru
// Route::middleware(['guest'])->group(function () {
//     Route::get('/panel', function () {
//         return view('auth.loginadmin');
//     })->name('loginadmin');

//     Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin']);
// });

//Middleware untuk login admin, guru dan siswa
    Route::middleware(['guest'])->group(function () {
        Route::get('/', function () {
            return view('auth.loginadmin');
        })->name('loginadmin');

        Route::post('/proseslogin', [AuthController::class, 'proseslogin']);
    });

    //Middleware absen siswa
    Route::middleware(['auth:siswa'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::post('/prosesLogout', [AuthController::class, 'prosesLogout']);

        //Absen
        Route::get('/absensi/create', [AbsensiController::class, 'create']);
        Route::post('/absensi/store', [AbsensiController::class, 'store']);

        //Edit Profile
        Route::get('/editprofile', [AbsensiController::class, 'editprofile']);
        Route::post('/absensi/{nis}/updateprofile', [AbsensiController::class, 'updateprofile']);

        //Histori
        Route::get('/absensi/histori', [AbsensiController::class, 'histori']);
        Route::post('/gethistori', [AbsensiController::class, 'gethistori']);

        //Izin
        Route::get('/absensi/izin', [AbsensiController::class, 'izin']);
        Route::get('/absensi/buatizin', [AbsensiController::class, 'buatizin']);
        Route::post('/absensi/storeizin', [AbsensiController::class, 'storeizin']);

        //QR
        Route::get('/absensi/scan', [AbsensiController::class, 'scan']);
        Route::post('/absensi/simpanScanQR', [AbsensiController::class, 'simpanScanQR']);
        Route::get('/absensi/scan', [AbsensiController::class, 'scan'])->name('absensi.scan');
        Route::get('/absensi/success', [AbsensiController::class, 'success'])->name('absensi.success');
        Route::get('/absensi/status', [AbsensiController::class, 'getStatusHadir'])->middleware('auth:siswa');
        Route::get('/absensi/editizin/{id}', [AbsensiController::class, 'editizin'])->middleware('auth:siswa');
        Route::post('/absensi/updateizin', [AbsensiController::class, 'updateizin'])->middleware('auth:siswa');

    });

    //Middleware login admin dan guru
    Route::middleware(['auth', 'checkRole:admin'])->group(function () {
        Route::get('/dashboardadmin', [DashboardController::class, 'dashboardadmin']);
    });

    Route::middleware(['auth', 'checkRole:guru'])->group(function () {
        Route::get('/dashboardguru', [DashboardController::class, 'dashboardguru']);
        Route::get('/qr', [GuruController::class, 'qr']);
        Route::get('/dashboardguru', [GuruController::class, 'qrIndex']);
        Route::get('/download-qr', [GuruController::class, 'downloadQr'])->name('guru.qr.download');
        //Route::get('/absensi/export-excel', [GuruController::class, 'exportExcel'])->name('absensi.exportExcel');
        Route::get('/qr/export', [GuruController::class, 'exportExcel'])->name('qr.export');
        Route::get('/qr/export-mingguan', [GuruController::class, 'exportMingguan'])->name('qr.export.mingguan');
        Route::get('/qr/export-mingguan-manual', [GuruController::class, 'exportMingguanManual'])->name('qr.export.mingguan.manual');
        //Route::get('/absensi/hari-ini', [GuruController::class, 'getSiswaAbsenHariIni'])->name('absensi.hariini');
    });

    //Admin
    Route::get('/absensi/kelas', [AdminController::class, 'kelas'])->middleware('auth');
    Route::get('/admin/index', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/store', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/admin/{admin}/edit', [AdminController::class, 'edit'])->name('admin.edit');
    Route::put('/admin/{admin}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/admin/{admin}', [AdminController::class, 'destroy'])->name('admin.destroy');

    // Guru
    Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
    Route::post('/guru', [GuruController::class, 'store'])->name('guru.store');
    Route::get('/guru/{guru}', [GuruController::class, 'edit'])->name('guru.edit');
    Route::put('/guru/{guru}', [GuruController::class, 'update'])->name('guru.update');
    Route::delete('/guru/{guru}', [GuruController::class, 'destroy'])->name('guru.destroy');
      Route::post('/ubah-absen-alfa', [GuruController::class, 'ubahKeAlfa'])->name('ubah.absen.alfa');
    Route::post('/ubah-absen-hadir', [GuruController::class, 'ubahKeHadir'])->name('ubah.absen.hadir');

    //Siswa
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::post('/siswa/store', [SiswaController::class, 'store'])->name('siswa.store');
    Route::post('/siswa/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::post('/siswa/{nis}/update', [SiswaController::class, 'update'])->name('siswa.update');
    Route::post('/siswa/{nis}/delete', [SiswaController::class, 'delete'])->name('siswa.delete');
    Route::get('/siswa/import', [SiswaController::class, 'importForm'])->name('siswa.importForm');
    Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::post('/siswa/aksi-massal', [SiswaController::class, 'aksiMassal'])->name('siswa.aksiMassal');
    Route::get('/alumni', [SiswaController::class, 'alumni'])->name('siswa.alumni');

   // Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.index'); // ← ini lebih tepat


    //Presensi Monitoring Admin
    Route::get('/absensi/monitoring', [AbsensiController::class, 'monitoring'])->name('absensi.monitoring');
    Route::post('/getabsensi', [AbsensiController::class, 'getabsensi'])->name('absensi.getabsensi');
    Route::get('/get-notifikasi', [AbsensiController::class, 'getNotifikasi']);
    Route::post('/showmap', [AbsensiController::class, 'showmap'])->name('absensi.showmap');
    Route::get('/absensi/laporan', [AbsensiController::class, 'laporan'])->name('absensi.laporan');
    Route::get('/siswa/bykelas/{kelas}', [AbsensiController::class, 'getSiswaByKelas'])->name('siswa.bykelas');
    Route::post('/absensi/cetaklaporan', [AbsensiController::class, 'cetaklaporan'])->name('absensi.cetaklaporan');
    Route::get('/absensi/rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap');
    Route::post('/absensi/cetakrekap', [AbsensiController::class, 'cetakrekap'])->name('absensi.cetakrekap');
    Route::get('/absensi/izinsakit', [AbsensiController::class, 'izinsakit'])->name('absensi.izinsakit');
    Route::post('/absensi/approvedizinsakit', [AbsensiController::class, 'approvedizinsakit'])->name('absensi.approvedizinsakit');
    Route::get('/absensi/{id}/batalkanizinsakit', [AbsensiController::class, 'batalkanizinsakit'])->name('absensi.batalkanizinsakit');
    Route::delete('/absensi/{id}/hapusizinsakit', [AbsensiController::class, 'hapusIzinSakit']);
    Route::get('/absensi/qr-admin', [AbsensiController::class, 'showQrPresensi'])->middleware('auth');
   // Route::get('/absensi/kode-qr', [AbsensiController::class, 'getKodeQr']);
    Route::get('/absensi/qr-terbaru', [AbsensiController::class, 'getQrTerbaru']);
    Route::get('/absensi/maps', [AbsensiController::class, 'maps'])->name('absensi.maps');


    // Route::get('/absensi/qr-display', [AbsensiController::class, 'displayQr']);
    Route::get('/absensi/qr-display/{token}', [AbsensiController::class, 'displayQr']);


    //Konfigurasi
    Route::get('/konfigurasi/lokasisekolah', [KonfigurasiController::class, 'lokasisekolah']);
    Route::post('/konfigurasi/updatelokasisekolah', [KonfigurasiController::class, 'updatelokasisekolah']);

    Route::post('/logout', function () {
        Auth::guard('web')->logout();
        return redirect('/');
    })->name('logout');


Route::get('/debug-session', function () {
    return [
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token(),
        'session_data' => session()->all(),
    ];
});

// di routes/web.php
Route::get('/test-session', function () {
    session(['test_key' => 'ini session test']);
    return 'Session disimpan';
});

Route::get('/check-session', function () {
    return session('test_key', 'Session tidak ditemukann');
});
