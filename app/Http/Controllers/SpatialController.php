<?php

namespace App\Http\Controllers;

use App\Services\SpatialDataService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class SpatialController
 *
 * Mengelola penyajian data geospasial (GeoJSON & konfigurasi peta orthophoto)
 * untuk visualisasi peta interaktif Leaflet.js.
 */
class SpatialController extends Controller
{
    /**
     * Service data spasial.
     */
    protected SpatialDataService $spatialService;



    /**
     * Inisialisasi controller dengan injeksi SpatialDataService.
     */
    public function __construct(SpatialDataService $spatialService)
    {
        $this->spatialService = $spatialService;
        $this->middleware('auth');
    }

    /**
     * Menyajikan data GeoJSON dengan integrasi analisis kesehatan tanaman (IPHI).
     */
    public function serve(string $kebun, string $layer, Request $request): JsonResponse
    {
        $dbKey = $this->resolveDbKey($request);
        $data = $this->spatialService->getGeoJSON($kebun, $layer, $dbKey);

        if (!$data) {
            return response()->json(['type' => 'FeatureCollection', 'features' => []]);
        }

        return response()->json($data);
    }

    /**
     * Mengambil konfigurasi awal peta (center, zoom, dan URL orthophoto).
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
     * Mengambil data GeoJSON batas afdeling kebun.
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
     * Mengambil data GeoJSON tanaman penutup tanah (LCC).
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
     * Mengambil data GeoJSON titik/area pemeliharaan dan anomali gulma.
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
     * Mengambil data GeoJSON koordinat titik individu pohon sawit.
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
     * Mengubah slug periode dari request ke database key yang sesuai.
     */
    private function resolveDbKey(Request $request): string
    {
        $slug = $request->query('periode');
        return config("simtan.map_periode.{$slug}.db_key") ?? $slug ?? '';
    }
}
