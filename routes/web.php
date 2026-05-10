<?php

/**
 * SIMTAN - Sistem Informasi Monitoring Areal Tanaman (PTPN IV Regional I)
 * --------------------------------------------------------------------------
 * @package     SIMTAN
 * @author      Maghfirah <220203064>
 * 
 * routes/web.php - Khusus Halaman, Form Actions, & Download (Session-Based)
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\AI_Controller;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| 1. GUEST ROUTES (Login)
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
     * MODULE: SETTINGS (Profile & Password)
     * Diakses melalui dropdown profil kanan atas
     */
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [MonitoringController::class, 'settings'])->name('index');
        Route::post('/profile', [UserController::class, 'updateProfile'])->name('update-profile');
        Route::post('/security', [UserController::class, 'updatePassword'])->name('update-password');
    });

    /**
     * MODULE: MONITORING VIEWS
     * Navigasi utama Sidebar
     */
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/data-kebun', [MonitoringController::class, 'dataKebun'])->name('data-kebun');
        Route::get('/detail-kebun', [MonitoringController::class, 'detailKebun'])->name('detail-kebun');
        Route::get('/detail/{id}', [MonitoringController::class, 'detailAreal'])->name('detail');
        Route::get('/laporan', [MonitoringController::class, 'laporan'])->name('laporan');
        Route::get('/settings', [MonitoringController::class, 'settings'])->name('settings');
    });

    /*
    |--------------------------------------------------------------------------
    | 3. DATA MANAGER ROUTES (Admin & Superadmin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:superadmin,admin'])->group(function () {

        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            // -- Ingesti Data (Upload) --
            Route::get('/upload-data', [MonitoringController::class, 'importView'])->name('import');
            Route::post('/upload-data/store', [MonitoringController::class, 'importStore'])->name('import.store');

            // -- CRUD Metadata & Audit Trail Download --
            // Catatan: Wajib di web.php karena butuh Session Auth & CSRF Protection
            Route::get('/import/download/{id}', [MonitoringController::class, 'downloadFile'])->name('import.download');
            Route::put('/import/{id}', [MonitoringController::class, 'importUpdate'])->name('import.update');
            Route::delete('/import/{id}', [MonitoringController::class, 'importDestroy'])->name('import.destroy');

            Route::get('/riwayat-data', [MonitoringController::class, 'riwayatData'])->name('riwayat-data');
        });

        /**
         * MODULE: AI ENGINE
         */
        Route::controller(AI_Controller::class)->group(function () {
            Route::get('/ai/analyze-dashboard', 'analyzeDashboard')->name('ai.analyze.dashboard');
            Route::post('/ai/analyze/{blockId}', 'generateAnalysis')->name('ai.analyze');
            Route::get('/ai/recommendation/{blockId}', 'getPrescriptiveRecommendation')->name('ai.recommendation');
            Route::post('/ai/config/update', 'updateConfig')->name('ai.config.update');
            Route::post('/monitoring/analyze-block', 'analyzeBlock')->name('monitoring.analyze-block');
        });

        /**
         * MODULE: REPORTS (Export PDF & Excel)
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

        // Menu Kelola Akun
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
| 5. FALLBACK ROUTE
|--------------------------------------------------------------------------
*/
Route::fallback(fn() => view('pages.error404'));
