<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SpatialDataService;

/**
 * SpatialController
 * 
 * Mengelola permintaan API untuk penyajian data Geospasial (GeoJSON & XYZ Tiles).
 * Menghubungkan Frontend Peta (Leaflet.js) dengan dataset lapangan dan database produksi.
 * Sesuai Perancangan Bab 3.6.1.4 (Alur Kerja Spasial & Analisis).
 */
class SpatialController extends Controller
{
    protected $spatialService;

    /**
     * Dependency Injection SpatialDataService.
     * Menggunakan middleware 'auth' untuk menjamin keamanan akses aset spasial perusahaan.
     */
    public function __construct(SpatialDataService $spatialService)
    {
        $this->spatialService = $spatialService;
        $this->middleware('auth');
    }

    /**
     * API: Mengambil Konfigurasi Awal Peta (Center & Zoom).
     * Mengintegrasikan koordinat lokasi kebun dari tabel 'lokasi_kebun' ke UI.
     */
    public function getConfig($kode_kebun)
    {
        $config = $this->spatialService->getOrthophotoConfig($kode_kebun);

        if (!$config) {
            return response()->json([
                'status' => 'error',
                'message' => "Konfigurasi spasial untuk unit {$kode_kebun} tidak ditemukan."
            ], 404);
        }

        return response()->json($config);
    }

    /**
     * API: Mengambil Batas Administrasi (Afdeling/Blok) & Data Kesehatan.
     * Mengembalikan fusi data antara properti GeoJSON (kanas_batas.geojson) 
     * dengan metadata tooltip (Unit, Tahun Tanam, Luas Adm, Luas SHP).
     */
    public function getBlocks($kode_kebun)
    {
        $data = $this->spatialService->getBlockGeoJSON($kode_kebun);

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => "Berkas GeoJSON Batas untuk unit {$kode_kebun} belum diintegrasikan."
            ], 404);
        }

        return response()->json($data);
    }

    /**
     * API: Mengambil Layer Tanaman Penutup Tanah (Kacangan / LCC).
     * Menyajikan data spasial (kanas_kacangan.geojson) dengan metadata: 
     * ID Blok, Luas LCC, dan Status Aktif.
     */
    public function getLCC($kode_kebun)
    {
        $data = $this->spatialService->getExtraLayer($kode_kebun, 'kacangan');

        if (!$data) {
            return response()->json([
                'status' => 'warning',
                'message' => "Data spasial LCC (Kacangan) untuk unit {$kode_kebun} tidak tersedia."
            ], 404);
        }

        return response()->json($data);
    }

    /**
     * API: Mengambil Data Layer Pemeliharaan (Anomali Lapangan).
     * Menyajikan temuan spasial (kanas_pemeliharaan.geojson) dengan metadata: 
     * ID Blok, Jenis Temuan (Keterangan), dan Luas Terdampak.
     */
    public function getMaintenance($kode_kebun)
    {
        $data = $this->spatialService->getExtraLayer($kode_kebun, 'pemeliharaan');

        if (!$data) {
            return response()->json([
                'status' => 'warning',
                'message' => "Data temuan pemeliharaan (anomali) untuk unit {$kode_kebun} tidak ditemukan."
            ], 404);
        }

        return response()->json($data);
    }

    /**
     * API: Mengambil Titik Koordinat Pohon (Point).
     * (Roadmap: Mendukung sinkronisasi data sensus pohon biometrik ke dalam peta Live GIS).
     */
    public function getTrees($kode_kebun)
    {
        return response()->json([
            'status' => 'syncing',
            'message' => 'Modul koordinat individu pohon sedang dalam tahap sinkronisasi biometrik.'
        ]);
    }
}
