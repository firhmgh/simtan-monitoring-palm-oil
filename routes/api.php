<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpatialController;

/*
|--------------------------------------------------------------------------
| API Routes - SIMTAN (Sistem Informasi Monitoring Tanaman)
|--------------------------------------------------------------------------
|
| Rute API ini digunakan untuk komunikasi data antara server (Backend) 
| dengan modul interaksi peta (Frontend Leaflet.js).
|
*/

/**
 * 1. USER AUTHENTICATION API
 * Menggunakan Laravel Sanctum untuk manajemen token akses user.
 */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * 2. MODULE: SPATIAL INTELLIGENCE (GIS API)
 * Sesuai Perancangan Bab 3.6.1.4 dalam skripsi.
 * 
 * Prefix: /api/spatial/
 */
Route::prefix('spatial')->group(function () {

    /**
     * API: Konfigurasi Spasial Kebun
     * Mengambil koordinat pusat (Center) dan level zoom berdasarkan data tabel 'lokasi_kebun'.
     */
    Route::get('/config/{kode_kebun}', [SpatialController::class, 'getConfig'])->name('api.spatial.config');

    /**
     * API: Data Poligon Batas Blok
     * Mengembalikan fusi data antara file 'kanas_batas.geojson' dengan status kesehatan dari DB.
     * Metadata Tooltip: Unit (AFD), Tahun Tanam, Luas ADM, Luas SHP.
     */
    Route::get('/blocks/{kode_kebun}', [SpatialController::class, 'getBlocks'])->name('api.spatial.blocks');

    /**
     * API: Data Layer Kacangan (LCC)
     * Mengambil poligon dari 'kanas_kacangan.geojson'.
     * Metadata Tooltip: ID Blok, Luas Kacangan, Status Aktif.
     */
    Route::get('/lcc/{kode_kebun}', [SpatialController::class, 'getLCC'])->name('api.spatial.lcc');

    /**
     * API: Data Layer Pemeliharaan (Anomali)
     * Mengambil data temuan anomali lapangan dari 'kanas_pemeliharaan.geojson'.
     * Metadata Tooltip: ID Blok, Jenis Temuan (Keterangan), Luas Terdampak.
     */
    Route::get('/maintenance/{kode_kebun}', [SpatialController::class, 'getMaintenance'])->name('api.spatial.maintenance');

    /**
     * API: Data Titik Individu Pohon (Future Development)
     */
    Route::get('/trees/{kode_kebun}', [SpatialController::class, 'getTrees'])->name('api.spatial.trees');
});
