<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RoleMiddleware
 *
 * Mengontrol otorisasi akses rute berdasarkan peran (role) pengguna.
 */
class RoleMiddleware
{
    /**
     * Memproses permintaan masuk dan memverifikasi izin peran pengguna.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        $user = Auth::user();

        if ($user->role && in_array($user->role->name, $roles)) {
            return $next($request);
        }

        return response()->view('pages.error403', [
            'message' => 'Akses Ditolak: Peran ' . ($user->role->name ?? 'Guest') . ' tidak memiliki izin untuk modul ini.'
        ], 403);
    }
}