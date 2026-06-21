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
use Illuminate\Support\Facades\Storage;
use App\Models\SimtanForm;

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

    Route::get('/leave-impersonation', [\App\Http\Controllers\ImpersonateController::class, 'leaveImpersonation'])->name('impersonate.leave');
    Route::get('/superadmin/impersonate/{id}', [\App\Http\Controllers\ImpersonateController::class, 'impersonate'])->name('impersonate');

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
            // -- Proses Unggah Data (Upload Excel) --
            Route::get('/upload-data', [MonitoringController::class, 'importView'])->name('import');
            Route::post('/upload-data/store', [MonitoringController::class, 'importStore'])->name('import.store');

            // -- CRUD Metadata & Audit Trail --
            Route::get('/import/download/{id}', [MonitoringController::class, 'downloadFile'])->name('import.download');
            Route::put('/import/{id}', [MonitoringController::class, 'importUpdate'])->name('import.update');
            Route::delete('/import/{id}', [MonitoringController::class, 'importDestroy'])->name('import.destroy');
            Route::get('/riwayat-data', [MonitoringController::class, 'riwayatData'])->name('riwayat-data');

            // -- Ekspor Laporan Audit Trail (Riwayat Data) --
            Route::get('/audit-csv', [MonitoringController::class, 'exportAuditCsv'])->name('audit.csv');
            Route::get('/audit-pdf', [MonitoringController::class, 'printAuditPdf'])->name('audit.pdf');
        });

        /**
         * MODULE: LOGIKA PEMROSESAN AI
         * Prefix 'api/ai' agar fetch asinkron di Dashboard & Detail Kebun tidak terblokir.
         */
        Route::prefix('api/ai')->name('ai.')->controller(AI_Controller::class)->group(function () {
            // Analisis tren dashboard global
            Route::get('/dashboard-insight', 'getDashboardInsight')->name('analyze.dashboard');
            // Diagnosa spesifik per blok di peta
            Route::post('/block-insight', 'getBlockInsight')->name('analyze.block');
            // Update API Key & Threshold dari halaman Settings
            Route::post('/config/update', 'updateConfig')->name('config.update');
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
        Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/update/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/delete/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        /**
         * UTILITY: STORAGE CLEANER
         * Menghapus file .xlsx yang sudah tidak memiliki record di database (orphan files).
         */
        Route::get('/clean-storage', function () {
            // Ambil semua file di folder uploads/simtan
            $allFiles = Storage::disk('public')->files('uploads/simtan');
            $deletedCount = 0;

            foreach ($allFiles as $file) {
                // Cek apakah file ini ada di database simtan_form
                $existsInDb = SimtanForm::where('file_path', 'LIKE', "%$file%")->exists();

                if (!$existsInDb) {
                    Storage::disk('public')->delete($file);
                    $deletedCount++;
                }
            }

            return "Pembersihan selesai! $deletedCount file sampah berhasil dihapus.";
        })->name('clean-storage');
    });
});

/*
|--------------------------------------------------------------------------
| 5. FALLBACK
|--------------------------------------------------------------------------
|
*/
Route::fallback(fn() => response()->view('errors.404', [], 404));
