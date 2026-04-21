<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RoleMiddleware
 * 
 * Implementasi Role-Based Access Control (RBAC) untuk membatasi akses 
 * 
 * @package App\Http\Middleware
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles  Parameter dinamis untuk nama-nama role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan pengguna sudah terautentikasi
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        $user = Auth::user();

        /** 
         * 2. Pengecekan Relasi Role
         * Mengambil nama role melalui relasi Eloquent (User -> Role)
         * Pastikan model User sudah memiliki method role()
         */
        if ($user->role && in_array($user->role->name, $roles)) {
            return $next($request);
        }

        /** 
         * 3. Response Unauthorized
         * Standar keamanan internasional menggunakan HTTP 403 Forbidden
         * jika pengguna terautentikasi namun tidak memiliki otoritas (Abstraksi Otorisasi).
         */
        return response()->view('pages.error403', [
            'message' => 'Akses Ditolak: Peran ' . ($user->role->name ?? 'Guest') . ' tidak memiliki izin untuk modul ini.'
        ], 403);
    }
}