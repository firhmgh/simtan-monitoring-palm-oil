<?php

/**
 * SIMTAN - Sistem Informasi Monitoring Areal Tanaman (PTPN IV Regional I)
 * --------------------------------------------------------------------------
 * routes/web.php - Jalur Navigasi, Aksi Form, & API Internal
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\AI_Controller;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SpatialController;

/*
|--------------------------------------------------------------------------
| 1. GUEST ROUTES (Login & Auth Entry)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| 2. AUTHENTICATED ROUTES (Global Access)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', fn() => redirect()->route('index'));
    Route::get('/dashboard', [MonitoringController::class, 'index'])->name('index');

    /**
     * MODULE: SECURE SPATIAL DATA (Gatekeeper)
     * Mengamankan GeoJSON agar hanya bisa diakses via aplikasi.
     */
    Route::get('/spatial-data/{kebun}/{layer}', [SpatialController::class, 'serve'])
        ->name('spatial.serve');

    /**
     * MODULE: SETTINGS (Profile, Security, & AI Config)
     * Menggunakan name 'settings.' agar cocok dengan route('settings.update-profile') di Blade.
     */
    Route::prefix('monitoring/settings')->name('settings.')->group(function () {
        Route::get('/', [MonitoringController::class, 'settings'])->name('index');
        Route::post('/profile', [UserController::class, 'updateProfile'])->name('update-profile');
        Route::post('/security', [UserController::class, 'updatePassword'])->name('update-password');
    });

    /**
     * MODULE: MONITORING VIEWS
     * Navigasi utama Sidebar.
     */
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/data-kebun', [MonitoringController::class, 'dataKebun'])->name('data-kebun');
        Route::get('/detail-areal/{id?}', [MonitoringController::class, 'detailAreal'])->name('detail');
        Route::get('/laporan', [MonitoringController::class, 'laporan'])->name('laporan');

        // API Preview (Gunakan awalan /api agar jelas ini jalur data)
        Route::get('/laporan/preview-html', [MonitoringController::class, 'previewHTML'])->name('laporan.preview');

        // Download PDF
        Route::get('/laporan/export-pdf', [MonitoringController::class, 'exportPDF'])->name('laporan.pdf');

        // Alias untuk menjaga kecocokan dengan navigasi sidebar lama
        Route::get('/settings-main', [MonitoringController::class, 'settings'])->name('settings');
    });

    /*
    |--------------------------------------------------------------------------
    | 3. DATA MANAGER ROUTES (Admin & Superadmin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:superadmin,admin'])->group(function () {

        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            // -- Ingesti Data (Upload Excel) --
            Route::get('/upload-data', [MonitoringController::class, 'importView'])->name('import');
            Route::post('/upload-data/store', [MonitoringController::class, 'importStore'])->name('import.store');

            // -- CRUD Metadata & Audit Trail --
            Route::get('/import/download/{id}', [MonitoringController::class, 'downloadFile'])->name('import.download');
            Route::put('/import/{id}', [MonitoringController::class, 'importUpdate'])->name('import.update');
            Route::delete('/import/{id}', [MonitoringController::class, 'importDestroy'])->name('import.destroy');
            Route::get('/riwayat-data', [MonitoringController::class, 'riwayatData'])->name('riwayat-data');
        });

        /**
         * MODULE: AI NEURAL ENGINE
         * Prefix 'api/ai' agar fetch asinkron di Dashboard & Detail Kebun tidak terblokir.
         */
        Route::prefix('api/ai')->name('ai.')->controller(AI_Controller::class)->group(function () {
            // Analisis tren dashboard global
            Route::get('/dashboard-insight', 'getDashboardInsight')->name('analyze.dashboard');
            // Diagnosa spesifik per blok di peta
            Route::get('/block-insight', 'getBlockInsight')->name('analyze.block');
            // Update API Key & Threshold dari halaman Settings
            Route::post('/config/update', 'updateConfig')->name('config.update');
        });

        /**
         * MODULE: REPORTS (Export Engine)
         */
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::post('/preview', [ReportController::class, 'preview'])->name('preview');
            Route::post('/export/pdf', [ReportController::class, 'downloadPDF'])->name('pdf');
            Route::post('/export/excel', [ReportController::class, 'downloadExcel'])->name('excel');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | 4. SYSTEM ADMINISTRATOR (Hanya Superadmin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:superadmin'])->prefix('superadmin')->group(function () {

        // Menu Kelola Akun (User Management)
        Route::get('/kelola-akun', [UserController::class, 'index'])->name('monitoring.kelola-akun');

        Route::name('admin.')->group(function () {
            Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
            Route::put('/users/update/{id}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');
        });
    });
});

/*
|--------------------------------------------------------------------------
| 5. FALLBACK
|--------------------------------------------------------------------------
*/
Route::fallback(fn() => view('pages.error404'));
