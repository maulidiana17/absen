<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function proseslogin(Request $request)
    {
        $request->validate([
            'login_sebagai' => 'required',
            'password' => 'required'
        ]);

        if ($request->login_sebagai == 'admin_guru') {
            $request->validate([
                'email' => 'required|email',
            ]);

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();

                if ($user->role === 'admin') {
                    return redirect('/dashboardadmin');
                } elseif ($user->role === 'guru') {
                    return redirect('/dashboardguru');
                } else {
                    Auth::logout();
                    return redirect('/')->with('warning', 'Role tidak diizinkan mengakses sistem.');
                }
            } else {
                return redirect('/')->with('warning', 'Email atau password yang Anda masukkan salah.');
            }
        } elseif ($request->login_sebagai == 'siswa') {
            $request->validate([
                'nis' => 'required',
            ]);

            if (Auth::guard('siswa')->attempt(['nis' => $request->nis, 'password' => $request->password])) {
                $siswa = Auth::guard('siswa')->user();
                session(['siswa_nama' => $siswa->nama_lengkap]);

                return redirect('/dashboard');
            } else {
                return redirect('/')->with('warning', 'NIS atau password yang Anda masukkan salah.');
            }
        } else {
            return redirect('/')->with('warning', 'Silakan pilih login sebagai siapa.');
        }
    }

    public function prosesLogout(Request $request) {
        if(Auth::guard('siswa')->check()) {
            Auth::guard('siswa')->logout();

            // Hapus session dan regenerasi token CSRF untuk keamanan
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/panel')->with('status', 'Anda telah logout.');
        }
    }

// public function prosesloginadmin(Request $request)
// {
//     $request->validate([
//         'email' => 'required|email',
//         'password' => 'required'
//     ]);

//     if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
//         $user = Auth::user();

//         if ($user->role === 'admin') {
//             return redirect('/dashboardadmin');
//         } elseif ($user->role === 'guru') {
//             return redirect('/dashboardguru');
//         } else {
//             // Jika role tidak dikenali, logout dan tolak akses
//             Auth::logout();
//             return redirect('/')->with('warning', 'Role tidak diizinkan mengakses sistem.');
//         }
//     }

//     return redirect('/')->with('warning', 'Email atau password yang Anda masukkan salah');
// }

// public function prosesLoginn(Request $request)
// {
//     if(Auth::guard('siswa')->attempt(['nis' => $request->nis, 'password' => $request->password])) {
//         // Ambil data siswa yang berhasil login
//         $siswa = Auth::guard('siswa')->user();

//         // Simpan nama lengkap siswa di session
//         session(['siswa_nama' => $siswa->nama_lengkap]);

//         return redirect('/dashboard');
//     } else {
//         return redirect('/')->with(['warning' => 'NIS atau password yang anda masukkan salah']);
//     }
// }



}


