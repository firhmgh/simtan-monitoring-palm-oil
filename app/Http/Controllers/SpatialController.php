<?php

namespace App\Http\Controllers;

use App\Services\SpatialDataService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * SpatialController
 * 
 * Mengelola permintaan API untuk penyajian data Geospasial (GeoJSON & XYZ Tiles).
 * Menghubungkan Frontend Peta (Leaflet.js) dengan dataset lapangan dan database produksi.
 * Mendukung analisis performa otomatis (Scopus Q1 Logic) melalui SpatialDataService.
 */
class SpatialController extends Controller
{
    /**
     * Property $spatialService dengan Type Information.
     */
    protected SpatialDataService $spatialService;

    /**
     * MASTER PEMETAAN PERIODE
     * Sinkronisasi antara slug Frontend dengan Database Key.
     */
    protected array $mapPeriode = [
        'periode-1-2025' => 'JANFEBMARAPR2025REKAP',
        'periode-2-2025' => 'MEIJULJUNAGST2025REKAP',
        'periode-3-2025' => 'SEPOKTNOVDES2025REKAP',
        'tahunan-2025'   => 'Tahun 2025',
    ];

    /**
     * Dependency Injection SpatialDataService.
     */
    public function __construct(SpatialDataService $spatialService)
    {
        $this->spatialService = $spatialService;
        $this->middleware('auth');
    }

    /**
     * API Secure Gatekeeper: Menyajikan file GeoJSON dengan Injeksi Analisis IPHI.
     * 
     * Method ini akan otomatis menyisipkan data analisis biometrik dan skor kesehatan
     * ke dalam GeoJSON 'blok' melalui SpatialDataService.
     * 
     * @param string $kebun
     * @param string $layer
     * @param Request $request
     * @return JsonResponse
     */
    public function serve(string $kebun, string $layer, Request $request): JsonResponse
    {
        // 1. Resolusi DB Key berdasarkan slug periode dari request
        $dbKey = $this->resolveDbKey($request);

        // 2. Ambil data melalui Service. 
        // Logika IPHI (Integrated Plantation Health Index) sudah dijalankan di dalam Service 
        // jika $layer === 'blok', termasuk kalkulasi CV dan Survival Rate.
        $data = $this->spatialService->getGeoJSON($kebun, $layer, $dbKey);

        // Jangan return 404 jika hanya datanya kosong, agar JS tidak error
        if (!$data) {
            return response()->json(['type' => 'FeatureCollection', 'features' => []]);
        }

        return response()->json($data);
    }

    /**
     * API: Mengambil Konfigurasi Awal Peta (Center, Zoom, & Orthophoto URL).
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
     * API: Mengambil Batas Administrasi (Afdeling).
     */
    public function getBlocks(string $kode_kebun, Request $request): JsonResponse
    {
        $dbKey = $this->resolveDbKey($request);
        $data = $this->spatialService->getGeoJSON($kode_kebun, 'batas', $dbKey);

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
     */
    public function getLCC(string $kode_kebun, Request $request): JsonResponse
    {
        $dbKey = $this->resolveDbKey($request);
        $data = $this->spatialService->getGeoJSON($kode_kebun, 'kacangan', $dbKey);

        if (!$data || count($data['features']) === 0) {
            return response()->json([
                'status' => 'warning',
                'message' => "Data spasial LCC untuk unit {$kode_kebun} tidak tersedia."
            ], 404);
        }

        return response()->json($data);
    }

    /**
     * API: Mengambil Data Layer Pemeliharaan (Anomali Lapangan).
     */
    public function getMaintenance(string $kode_kebun, Request $request): JsonResponse
    {
        $dbKey = $this->resolveDbKey($request);
        $data = $this->spatialService->getGeoJSON($kode_kebun, 'pemeliharaan', $dbKey);

        if (!$data || count($data['features']) === 0) {
            return response()->json([
                'status' => 'warning',
                'message' => "Data temuan pemeliharaan untuk unit {$kode_kebun} tidak ditemukan."
            ], 404);
        }

        return response()->json($data);
    }

    /**
     * API: Mengambil Titik Koordinat Pohon (KONPOKOK).
     */
    public function getTrees(string $kode_kebun, Request $request): JsonResponse
    {
        $dbKey = $this->resolveDbKey($request);
        $data = $this->spatialService->getGeoJSON($kode_kebun, 'konpokok', $dbKey);

        if (!$data || count($data['features']) === 0) {
            return response()->json(['status' => 'error', 'message' => 'Data pohon tidak ditemukan'], 404);
        }

        return response()->json($data);
    }

    /**
     * HELPER: Resolusi Slug Periode ke Database Key.
     * 
     * @param Request $request
     * @return string
     */
    private function resolveDbKey(Request $request): string
    {
        $slug = $request->query('periode');

        // Jika slug terdaftar di map, kembalikan DB Key. 
        // Jika tidak, kembalikan slug asli (antisipasi jika input manual).
        return $this->mapPeriode[$slug] ?? $slug ?? '';
    }
}
