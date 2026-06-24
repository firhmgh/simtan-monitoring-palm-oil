<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * Class AuthController
 *
 * Mengelola otentikasi pengguna termasuk fungsi login dan logout.
 */
class AuthController extends Controller
{
    /**
     * Menampilkan halaman formulir login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Memproses verifikasi kredensial login pengguna.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role->name === 'superadmin' || $user->role->name === 'admin') {
                return redirect()->intended('/dashboard')
                    ->with('success', 'Selamat datang, ' . $user->name);
            }

            return redirect()->intended('/dashboard')
                ->with('success', 'Login Berhasil.');
        }

        return back()->withErrors([
            'email' => 'Kredensial yang Anda masukkan tidak terdaftar di sistem kami.',
        ])->onlyInput('email');
    }

    /**
     * Mengakhiri sesi aktif pengguna dan melakukan logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
