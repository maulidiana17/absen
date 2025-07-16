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

            return redirect('/')->with('status', 'Anda telah logout.');
        }
    }


}


