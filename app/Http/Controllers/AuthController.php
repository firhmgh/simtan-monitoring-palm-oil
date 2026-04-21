<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * Class AuthController
 * 
 * Mengelola siklus hidup autentikasi pengguna (Login & Logout) 
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Memproses permintaan login dengan validasi kredensial.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // 1. Validasi Input sesuai standar keamanan
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // 2. Percobaan Autentikasi (Attempt Login)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            /**
             * 3. Role-Based Redirect (Otorisasi Pasca Login)
             * Mengarahkan user ke index sesuai otoritasnya
             */
            $user = Auth::user();

            // Logika pengalihan berdasarkan nama role
            if ($user->role->name === 'superadmin' || $user->role->name === 'admin') {
                return redirect()->intended('/index')
                    ->with('success', 'Selamat datang, ' . $user->name);
            }

            return redirect()->intended('/index')
                ->with('success', 'Login Berhasil.');
        }

        // 4. Response Gagal (Exception Handling)
        return back()->withErrors([
            'username' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('username');
    }

    /**
     * Mengakhiri sesi pengguna (Logout).
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
