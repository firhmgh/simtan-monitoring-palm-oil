<?php

namespace App\Services;

use App\Models\DetailRekap;
use App\Models\LokasiKebun;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * SpatialDataService
 * 
 * Layanan profesional untuk mengolah data Geospasial (GeoJSON & XYZ Tiles).
 * Mendukung fusi data antara atribut file GIS dengan database operasional (Sensus Pohon).
 * Menggunakan Private Storage untuk melindungi keamanan aset digital PTPN IV.
 */
class SpatialDataService
{
    /**
     * 1. Ambil GeoJSON secara Dinamis (Universal untuk 26 Kebun)
     * Mengambil path file dari database dan menggabungkan data statistik sensus.
     * 
     * @param string $kodeKebun Contoh: 1KAS, 1KDH
     * @param string $layerType Contoh: batas, kacangan, pemeliharaan
     */
    public function getGeoJSON($kodeKebun, $layerType)
    {
        $kode = strtoupper($kodeKebun);
        $filePath = "spatial/{$kode}/{$layerType}.geojson";

        try {
            $config = DB::table('kebun_layers')
                ->where('kebun_code', $kode)
                ->where('layer_type', $layerType)
                ->where('is_active', 1)
                ->first();
            if ($config) {
                $filePath = $config->file_path;
            }
        } catch (\Exception $e) {
            Log::warning("Database fallback for {$kode}");
        }

        if (!Storage::exists($filePath)) return ['type' => 'FeatureCollection', 'features' => []];

        $geojsonData = json_decode(Storage::get($filePath), true);

        // Ambil data statistik riil. 
        // Berdasarkan SQL Dump Anda, ID unit tersimpan di kolom 'afdeling'
        $blockStats = DetailRekap::where('kebun', $kode)
            ->where('is_total', 0)
            ->get()
            ->keyBy('afdeling'); // Sesuaikan dengan kolom identitas di DB anda

        foreach ($geojsonData['features'] as &$feature) {
            $props = &$feature['properties'];

            // 1. Ekstraksi Afdeling dari properti 'layer' (Sangat Penting untuk Kacangan)
            if (!isset($props['AFDELING']) && isset($props['layer'])) {
                preg_match('/AFD\d+/', $props['layer'], $matches);
                $props['afdeling_id'] = $matches[0] ?? 'N/A';
            } else {
                $props['afdeling_id'] = $props['AFDELING'] ?? 'N/A';
            }

            // 2. Mapping Key untuk pencarian database
            $blockKey = $props['BLOK'] ?? $props['AFDELING'] ?? $props['blok'] ?? null;

            if ($blockKey && isset($blockStats[$blockKey])) {
                $data = $blockStats[$blockKey];
                $props['survival_rate'] = (float) $data->persen_pkk_normal;
                $props['pkk_mati'] = (int) $data->pkk_mati;
                $props['pkk_kerdil'] = (int) $data->pkk_kerdil_mati_kembali;
                $props['fill_color'] = $this->getColorByHealth($data->persen_pkk_normal);
                $props['display_name'] = $blockKey;
            } else {
                $props['survival_rate'] = 0;
                $props['fill_color'] = '#cbd5e1';
                $props['display_name'] = $blockKey ?? 'N/A';
            }
        }

        return $geojsonData;
    }

    /**
     * 2. Logika Pewarnaan Tematik Berdasarkan Persentase Kesehatan (Standar PPKS)
     */
    private function getColorByHealth($percentage)
    {
        if ($percentage >= 95) return '#10b981'; // Hijau (Optimal)
        if ($percentage >= 90) return '#f59e0b'; // Kuning (Waspada)
        return '#ef4444'; // Merah (Kritis)
    }

    /**
     * 3. Helper untuk Label Status Kesehatan
     */
    private function getStatusLabel($percentage)
    {
        if ($percentage >= 95) return 'Optimal / Sehat';
        if ($percentage >= 90) return 'Waspada / Perlu Perhatian';
        return 'Kritis / Perlu Intervensi';
    }

    /**
     * 4. Menyediakan Konfigurasi XYZ Tiles (Orthophoto Drone)
     * Mengintegrasikan koordinat pusat dari database 'lokasi_kebun'.
     */
    public function getOrthophotoConfig($kodeKebun)
    {
        $kebun = LokasiKebun::where('kebun', strtoupper($kodeKebun))->first();

        $lat = $kebun->latitude ?? 2.03394;
        $lng = $kebun->longitude ?? 99.9952;

        return [
            'tile_url' => $kebun->tile_url ?? null, // Mengambil URL dari DB (bisa GitHub/Local)
            'latitude' => (float) $lat,
            'longitude' => (float) $lng,
            'minZoom' => 12,
            'maxZoom' => 22,
            'maxNativeZoom' => 18
        ];
    }
}
