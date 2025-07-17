<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    // public function proseslogin(Request $request)
    // {
    //     $request->validate([
    //         'login_sebagai' => 'required',
    //         'password' => 'required'
    //     ]);

    //     if ($request->login_sebagai == 'admin_guru') {
    //         $request->validate([
    //             'email' => 'required|email',
    //         ]);

    //         if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
    //             $user = Auth::user();

    //             if ($user->role === 'admin') {
    //                 Log::info('Session ID setelah login (admin): ' . session()->getId());

    //                 return redirect('/dashboardadmin');
    //             } elseif ($user->role === 'guru') {
    //                 Log::info('Session ID setelah login (guru): ' . session()->getId());

    //                 return redirect('/dashboardguru');
    //             } else {
    //                 Auth::logout();
    //                 return redirect('/')->with('warning', 'Role tidak diizinkan mengakses sistem.');
    //             }
    //         } else {
    //             return redirect('/')->with('warning', 'Email atau password yang Anda masukkan salah.');
    //         }
    //     } elseif ($request->login_sebagai == 'siswa') {
    //         $request->validate([
    //             'nis' => 'required',
    //         ]);

    //         if (Auth::guard('siswa')->attempt(['nis' => $request->nis, 'password' => $request->password])) {
    //             $siswa = Auth::guard('siswa')->user();
    //             session(['siswa_nama' => $siswa->nama_lengkap]);

    //             return redirect('/dashboard');
    //         } else {
    //             return redirect('/')->with('warning', 'NIS atau password yang Anda masukkan salah.');
    //         }
    //     } else {
    //         return redirect('/')->with('warning', 'Silakan pilih login sebagai siapa.');
    //     }
    // }
    public function proseslogin(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'login_sebagai' => 'required', // admin atau guru atau siswa
    ]);

    if (in_array($request->login_sebagai, ['admin', 'guru'])) {
        // Login admin/guru pakai default guard
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // Validasi role harus sesuai yang dipilih
            if ($user->role === $request->login_sebagai) {
                Log::info('Session ID setelah login (' . $user->role . '): ' . session()->getId());

                if ($user->role === 'admin') {
                    return redirect('/dashboardadmin');
                } elseif ($user->role === 'guru') {
                    return redirect('/dashboardguru');
                }
            } else {
                Auth::logout();
                return redirect('/')->with('warning', 'Anda tidak memiliki akses sebagai ' . $request->login_sebagai);
            }
        } else {
            return redirect('/')->with('warning', 'Email atau password salah.');
        }
    } elseif ($request->login_sebagai === 'siswa') {
        // Login siswa pakai guard siswa
        $request->validate([
            'nis' => 'required',
        ]);

        if (Auth::guard('siswa')->attempt(['nis' => $request->nis, 'password' => $request->password])) {
            $siswa = Auth::guard('siswa')->user();
            session(['siswa_nama' => $siswa->nama_lengkap]);

            return redirect('/dashboard');
        } else {
            return redirect('/')->with('warning', 'NIS atau password salah.');
        }
    } else {
        return redirect('/')->with('warning', 'Pilih login sebagai siapa.');
    }
}


    public function prosesLogout(Request $request) {
        if(Auth::guard('siswa')->check()) {
            Auth::guard('siswa')->logout();

            // Hapus session dan regenerasi token CSRF untuk keamanan
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('status', 'Anda telah logout.');
        }
    }


}


