<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * Class ImpersonateController
 *
 * Mengelola fitur User Impersonation / Mode Penyamaran untuk pengujian peran (HCI & Dev Efficiency).
 */
class ImpersonateController extends Controller
{
    /**
     * Memulai impersonasi sebagai user lain.
     * Dapat diakses oleh Superadmin asli atau Superadmin yang sedang menyamar.
     */
    public function impersonate($userId)
    {
        $currentUser = Auth::user();

        // Keamanan: Hanya Superadmin atau user yang sedang impersonate (Superadmin menyamar) yang boleh beralih peran
        if ($currentUser->role->name !== 'superadmin' && !session()->has('original_user_id')) {
            abort(403, 'Aksi tidak diizinkan. Hanya Superadmin yang dapat melakukan penyamaran.');
        }

        $targetUser = User::findOrFail($userId);

        // Jangan menyamar sebagai diri sendiri
        if ($currentUser->id === $targetUser->id) {
            return redirect()->back()->with('error', 'Anda tidak bisa menyamar sebagai diri sendiri.');
        }

        // Simpan ID Superadmin asli ke dalam session (jangan overwrite jika sudah ada)
        if (!session()->has('original_user_id')) {
            // Jika user asli adalah superadmin, simpan ID-nya
            if ($currentUser->role->name === 'superadmin') {
                session(['original_user_id' => $currentUser->id]);
            } else {
                abort(403, 'Gagal mendeteksi identitas Superadmin asli.');
            }
        }

        // Autentikasi sebagai user target menggunakan ID
        Auth::loginUsingId($targetUser->id);

        return redirect()->route('index')->with('success', 'Berhasil menyamar sebagai ' . $targetUser->name);
    }

    /**
     * Mengakhiri impersonasi dan kembali ke akun Superadmin asli.
     */
    public function leaveImpersonation()
    {
        if (!session()->has('original_user_id')) {
            return redirect()->route('index')->with('error', 'Sesi penyamaran tidak ditemukan.');
        }

        $originalUserId = session('original_user_id');

        // Kembalikan login ke ID asli Superadmin
        Auth::loginUsingId($originalUserId);

        // Hapus session penyamaran
        session()->forget('original_user_id');

        return redirect()->route('index')->with('success', 'Kembali ke akun Superadmin asli.');
    }
}
