<?php

namespace App\Http\Controllers;

use App\Services\SpatialDataService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

/**
 * SpatialController
 * 
 * Mengelola permintaan API untuk penyajian data Geospasial (GeoJSON & XYZ Tiles).
 * Menghubungkan Frontend Peta (Leaflet.js) dengan dataset lapangan dan database produksi.
 */
class SpatialController extends Controller
{
    /**
     * Property $spatialService dengan Type Information.
     */
    protected SpatialDataService $spatialService;

    /**
     * Dependency Injection SpatialDataService.
     */
    public function __construct(SpatialDataService $spatialService)
    {
        $this->spatialService = $spatialService;
        $this->middleware('auth');
    }

    /**
     * API Secure Gatekeeper: Menyajikan file GeoJSON dari Private Storage.
     * 
     * @param string $kebun
     * @param string $layer
     * @return JsonResponse
     */
    public function serve(string $kebun, string $layer): JsonResponse
    {
        // Memanggil getGeoJSON dari Service agar data Sensus/Rekap ikut terbawa
        $data = $this->spatialService->getGeoJSON($kebun, $layer);

        if (!$data || (isset($data['features']) && count($data['features']) === 0)) {
            return response()->json([
                'status' => 'error',
                'message' => "Dataset {$layer} untuk unit {$kebun} tidak ditemukan atau kosong."
            ], 404);
        }

        return response()->json($data);
    }

    /**
     * API: Mengambil Konfigurasi Awal Peta (Center & Zoom).
     * 
     * @param string $kode_kebun
     * @return JsonResponse
     */
    public function getConfig(string $kode_kebun): JsonResponse
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
     * Menggunakan method universal getGeoJSON.
     * 
     * @param string $kode_kebun
     * @return JsonResponse
     */
    public function getBlocks(string $kode_kebun): JsonResponse
    {
        // PERBAIKAN: Mengganti getBlockGeoJSON menjadi getGeoJSON
        $data = $this->spatialService->getGeoJSON($kode_kebun, 'batas');

        if (!$data || (isset($data['features']) && count($data['features']) === 0)) {
            return response()->json([
                'status' => 'error',
                'message' => "Berkas GeoJSON Batas untuk unit {$kode_kebun} belum tersedia."
            ], 404);
        }

        return response()->json($data);
    }

    /**
     * API: Mengambil Layer Tanaman Penutup Tanah (Kacangan / LCC).
     * 
     * @param string $kode_kebun
     * @return JsonResponse
     */
    public function getLCC(string $kode_kebun): JsonResponse
    {
        // PERBAIKAN: Mengganti getExtraLayer menjadi getGeoJSON
        $data = $this->spatialService->getGeoJSON($kode_kebun, 'kacangan');

        if (!$data || (isset($data['features']) && count($data['features']) === 0)) {
            return response()->json([
                'status' => 'warning',
                'message' => "Data spasial LCC untuk unit {$kode_kebun} tidak tersedia."
            ], 404);
        }

        return response()->json($data);
    }

    /**
     * API: Mengambil Data Layer Pemeliharaan (Anomali Lapangan).
     * 
     * @param string $kode_kebun
     * @return JsonResponse
     */
    public function getMaintenance(string $kode_kebun): JsonResponse
    {
        // PERBAIKAN: Mengganti getExtraLayer menjadi getGeoJSON
        $data = $this->spatialService->getGeoJSON($kode_kebun, 'pemeliharaan');

        if (!$data || (isset($data['features']) && count($data['features']) === 0)) {
            return response()->json([
                'status' => 'warning',
                'message' => "Data temuan pemeliharaan untuk unit {$kode_kebun} tidak ditemukan."
            ], 404);
        }

        return response()->json($data);
    }

    /**
     * API: Mengambil Titik Koordinat Pohon.
     */
    public function getTrees(string $kode_kebun): JsonResponse
    {
        return response()->json([
            'status' => 'syncing',
            'message' => 'Modul koordinat individu pohon sedang dalam tahap sinkronisasi biometrik.'
        ]);
    }
}
