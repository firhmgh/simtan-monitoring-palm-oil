<?php

namespace App\Services;

use App\Models\DetailRekaps;
use App\Models\LokasiKebuns;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SpatialDataService
{
    /**
     * 1. Fungsi untuk mengambil file GeoJSON Batas Blok dan mengintegrasikan data kesehatan.
     * Alur: Ambil file fisik -> Ambil data DB -> Gabungkan (Fusion) -> Kirim ke Frontend.
     */
    public function getBlockGeoJSON($kodeKebun)
    {
        $path = public_path("maps/geojson/blocks/{$kodeKebun}.json");

        if (!File::exists($path)) {
            Log::error("GeoJSON untuk kebun {$kodeKebun} tidak ditemukan di path: {$path}");
            return null;
        }

        // Ambil data mentah GeoJSON
        $geojsonData = json_decode(File::get($path), true);

        // Ambil data statistik terbaru dari database (per blok)
        $blockData = DetailRekaps::where('kebun', strtoupper($kodeKebun))
            ->where('is_total', 0)
            ->get()
            ->keyBy('blok'); // Mudahkan pencarian berdasarkan nama blok

        // Iterasi setiap fitur (kotak blok) di GeoJSON untuk diisi data DB
        foreach ($geojsonData['features'] as &$feature) {
            $namaBlok = $feature['properties']['block_id'] ?? null;

            if ($namaBlok && isset($blockData[$namaBlok])) {
                $data = $blockData[$namaBlok];
                
                // Tambahkan atribut database ke dalam properti GeoJSON
                $feature['properties']['luas_ha'] = $data->luas_ha;
                $feature['properties']['pkk_normal'] = $data->pkk_normal;
                $feature['properties']['persen_sehat'] = $data->persen_pkk_normal;
                
                // Tentukan warna blok berdasarkan persentase kesehatan (Thematic Mapping)
                $feature['properties']['fill_color'] = $this->getColorByHealth($data->persen_pkk_normal);
                $feature['properties']['status_label'] = $this->getStatusLabel($data->persen_pkk_normal);
            } else {
                // Warna default jika data tidak ditemukan
                $feature['properties']['fill_color'] = '#cbd5e1'; 
                $feature['properties']['status_label'] = 'Data Tidak Tersedia';
            }
        }

        return $geojsonData;
    }

    /**
     * 2. Logika Pewarnaan Tematik (Thematic Mapping)
     * Sesuai standar Agronomi Precision Agriculture.
     */
    private function getColorByHealth($percentage)
    {
        if ($percentage >= 90) {
            return '#10b981'; // Emerald/Hijau (Optimal)
        } elseif ($percentage >= 70) {
            return '#f59e0b'; // Amber/Kuning (Warning/Perhatian)
        } else {
            return '#ef4444'; // Rose/Merah (Kritis)
        }
    }

    /**
     * Helper untuk label status
     */
    private function getStatusLabel($percentage)
    {
        if ($percentage >= 90) return 'Sehat / Optimal';
        if ($percentage >= 70) return 'Perlu Perhatian';
        return 'Kritis';
    }

    /**
     * 3. Menyediakan Konfigurasi XYZ Tiles (Orthophoto)
     * Mengarahkan Leaflet ke folder hasil proses QGIS.
     */
    public function getOrthophotoConfig($kodeKebun)
    {
        $kebun = LokasiKebuns::where('kebun', strtoupper($kodeKebun))->first();

        if (!$kebun) return null;

        // Path folder XYZ Tiles yang digenerate dari QGIS 
        return [
            'url' => asset("maps/tiles/{$kodeKebun}/{z}/{x}/{y}.png"),
            'minZoom' => 12,
            'maxZoom' => 18,
            'latitude' => $kebun->latitude,
            'longitude' => $kebun->longitude,
            'tile_exists' => File::isDirectory(public_path("maps/tiles/{$kodeKebun}"))
        ];
    }
}