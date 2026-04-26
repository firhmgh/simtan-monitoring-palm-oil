<?php

namespace App\Services;

use App\Models\DetailRekaps;
use App\Models\LokasiKebuns;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * SpatialDataService
 * Layanan khusus untuk mengolah data Geospasial (GeoJSON & XYZ Tiles).
 * Mendukung fusi data antara atribut file GIS (.geojson) dengan database operasional.
 */
class SpatialDataService
{
    /**
     * 1. Ambil GeoJSON Batas (kanas_batas.geojson)
     * Mengintegrasikan data: AFDELING, TAHUNTANAM, LUAS_ADM, LUAS_SHP
     */
    public function getBlockGeoJSON($kodeKebun)
    {
        $path = public_path("maps/geojson/{$kodeKebun}/kanas_batas.geojson");

        if (!File::exists($path)) {
            Log::error("GeoJSON Batas untuk kebun {$kodeKebun} tidak ditemukan di path: {$path}");
            return null;
        }

        $geojsonData = json_decode(File::get($path), true);

        // Ambil data statistik dari DB untuk fusi data kesehatan
        $afdelingStats = DetailRekaps::where('kebun', strtoupper($kodeKebun))
            ->get()
            ->groupBy('afdeling');

        foreach ($geojsonData['features'] as &$feature) {
            $props = &$feature['properties'];
            $afdName = $props['AFDELING'] ?? "N/A";

            $props['display_name'] = $afdName;
            $props['type_layer'] = 'Afdeling Boundary';

            // INTEGRASI TOOLTIP: Menyusun data sesuai permintaan skripsi
            $props['tooltip_meta'] = [
                'Unit'         => $afdName,
                'Tahun Tanam'  => $props['TAHUNTANAM'] ?? '-',
                'Luas (Adm)'   => ($props['LUAS_ADM'] ?? 0) . ' Ha',
                'Luas (SHP)'   => round(($props['LUAS_SHP'] ?? 0), 2) . ' Ha',
                'Layer'        => 'Batas Administrasi'
            ];

            // Fusi Data: Jika ada data di database untuk afdeling ini
            if (isset($afdelingStats[$afdName])) {
                $avgHealth = $afdelingStats[$afdName]->avg('persen_pkk_normal');
                $props['persen_sehat'] = round($avgHealth, 2);
                $props['fill_color'] = $this->getColorByHealth($avgHealth);
                $props['status_label'] = $this->getStatusLabel($avgHealth);
            } else {
                $props['fill_color'] = '#cbd5e1'; // Grey jika data kosong
                $props['status_label'] = 'Data Tidak Tersedia';
            }
        }

        return $geojsonData;
    }

    /**
     * 2. Mengambil Layer tambahan (Kacangan / Pemeliharaan).
     * Mengintegrasikan data BLOK, LUAS, KETERANGAN, dan S
     * tatus.
     */
    public function getExtraLayer($kodeKebun, $layerName)
    {
        $path = public_path("maps/geojson/{$kodeKebun}/kanas_{$layerName}.geojson");

        if (!File::exists($path)) {
            Log::warning("Layer Spasial {$layerName} untuk kebun {$kodeKebun} tidak ditemukan.");
            return null;
        }

        $geojsonData = json_decode(File::get($path), true);

        foreach ($geojsonData['features'] as &$feature) {
            $props = &$feature['properties'];
            $blockName = $props['BLOK'] ?? "N/A";
            $props['display_name'] = $blockName;

            // INTEGRASI TOOLTIP: Logika pemisahan atribut berdasarkan file
            if ($layerName === 'kacangan') {
                $props['tooltip_meta'] = [
                    'ID Blok'       => $blockName,
                    'Luas Kacangan' => ($props['LUAS'] ?? 0) . ' Ha',
                    'Status'        => 'Area LCC Aktif',
                    'Layer'         => 'Legume Cover Crop'
                ];
                $props['type_layer'] = 'LCC Layer';
            } elseif ($layerName === 'pemeliharaan') {
                $props['tooltip_meta'] = [
                    'ID Blok'        => $blockName,
                    'Jenis Temuan'   => $props['KETERANGAN'] ?? 'N/A',
                    'Luas Terdampak' => ($props['LUAS'] ?? 0) . ' Ha',
                    'Status'         => 'Perlu Intervensi',
                    'Layer'          => 'Anomali Lapangan'
                ];
                $props['type_layer'] = 'Maintenance Anomaly';
            }
        }

        return $geojsonData;
    }

    /**
     * 3. Logika Pewarnaan Tematik Berdasarkan Persentase Kesehatan
     */
    private function getColorByHealth($percentage)
    {
        if ($percentage >= 90) {
            return '#10b981'; // Hijau (Optimal)
        } elseif ($percentage >= 70) {
            return '#f59e0b'; // Kuning (Warning)
        } else {
            return '#ef4444'; // Merah (Kritis)
        }
    }

    /**
     * Helper untuk label status kesehatan
     */
    private function getStatusLabel($percentage)
    {
        if ($percentage >= 90) return 'Sehat / Optimal';
        if ($percentage >= 70) return 'Perlu Perhatian';
        return 'Kritis';
    }

    /**
     * 4. Menyediakan Konfigurasi XYZ Tiles (Orthophoto)
     */
    public function getOrthophotoConfig($kodeKebun)
    {
        $kebun = LokasiKebuns::where('kebun', strtoupper($kodeKebun))->first();

        $lat = $kebun->latitude ?? 2.03394;
        $lng = $kebun->longitude ?? 99.9952;

        return [
            'url' => asset("tiles/{z}/{x}/{y}.jpg"),
            'latitude' => (float) $lat,
            'longitude' => (float) $lng,
            'minZoom' => 12,
            'maxZoom' => 22,
            'maxNativeZoom' => 18,
            'tile_exists' => File::isDirectory(public_path("tiles"))
        ];
    }
}
