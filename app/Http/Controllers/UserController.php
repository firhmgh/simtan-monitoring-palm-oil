<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

/**
 * Controller untuk Manajemen Pengguna dan Pengaturan Profil.
 * Sesuai Perancangan Bab 3.6.1.2 (Activity Diagram Kelola Akun)
 */
class UserController extends Controller
{
    /* 
    |--------------------------------------------------------------------------
    | BAGIAN 1: USER MANAGEMENT (Khusus Superadmin)
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan daftar pengguna (Read).
     */
    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::all();
        return view('apps.monitoring.kelola-akun', compact('users', 'roles'));
    }

    /**
     * Menyimpan pengguna baru (Create).
     * Sesuai alur Prosedur Penambahan Akun Baru di Bab 3.6.1.2.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|unique:users,username|max:100',
            'email' => 'required|email|unique:users,email|max:150',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id'
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi hash sesuai Bab 3.6.1.2 No 7
            'role_id' => $request->role_id
        ]);

        return back()->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * Memperbarui data pengguna lain (Update oleh Admin).
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'role_id' => $request->role_id
        ]);

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Data akun diperbarui.');
    }

    /**
     * Menghapus akun (Delete).
     */
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'Akun berhasil dihapus.');
    }


    /* 
    |--------------------------------------------------------------------------
    | BAGIAN 2: SELF SETTINGS (Berlaku untuk semua Role)
    | Terhubung dengan halaman apps.monitoring.settings
    |--------------------------------------------------------------------------
    */

    /**
     * Memperbarui profil diri sendiri (Update Profile).
     * Digunakan pada Tab Profil Pengguna di halaman Settings.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user = \App\Models\User::find(Auth::id());

        return back()->with('success', 'Profil personal Anda berhasil diperbarui.');
    }

    /**
     * Memperbarui kata sandi sendiri (Update Password).
     * Digunakan pada Tab Keamanan di halaman Settings.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed', // 'confirmed' mencari input 'password_confirmation'
        ]);

        // Verifikasi apakah kata sandi saat ini cocok dengan database
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Konfirmasi kata sandi saat ini tidak cocok dengan data kami.']);
        }

        // Update password baru dengan Hash
        $user = \App\Models\User::find(Auth::id());
        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Keamanan akun berhasil diperbarui. Silakan gunakan password baru pada login berikutnya.');
    }
}
