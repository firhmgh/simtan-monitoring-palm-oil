<?php

namespace App\Services;

use App\Models\DetailRekap;
use App\Models\LokasiKebun;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * SpatialDataService - High-Performance GIS & Decision Support Engine
 * 
 * Mengimplementasikan Data Fusion Multimodal 
 * (Tabular + Spasial + Biometrik) untuk kalkulasi Plantation Health Index.
 */
class SpatialDataService
{
    /**
     * Mengambil GeoJSON dengan Injeksi Analisis Performa Blok (IPHI)
     */
    public function getGeoJSON($kodeKebun, $layerType, $periode = null)
    {
        $kode = strtoupper($kodeKebun);
        $type = strtolower($layerType);
        $filePath = $this->resolvePath($kode, $type);

        if (!$filePath) return ['type' => 'FeatureCollection', 'features' => []];

        // 1. MAPPING PERIODE
        $mapPeriode = [
            'periode-1-2025' => 'JANFEBMARAPR2025REKAP',
            'periode-2-2025' => 'MEIJULJUNAGST2025REKAP',
            'periode-3-2025' => 'SEPOKTNOVDES2025REKAP',
            'tahunan-2025'   => 'Tahun 2025',
        ];

        $dbKey = $mapPeriode[$periode] ?? $periode;
        
        // OPTIMASI SPASIAL: Membaca & men-decode file GeoJSON dari cache jika sudah pernah diload (mencegah I/O bottleneck)
        $cacheKeyGeoJson = "geojson_raw_{$kode}_{$type}";
        $geojsonData = Cache::remember($cacheKeyGeoJson, 3600, function () use ($filePath) {
            return json_decode(Storage::disk('local')->get($filePath), true);
        });

        // 2. DATA FUSION: Ambil data rekapitulasi database
        $dbData = DetailRekap::where('kebun', $kode)
            ->where('periode', $dbKey)
            ->get();

        $lookupMap = $dbData->keyBy(function ($item) {
            return strtoupper(trim($item->blok ?? $item->afdeling));
        });

        // 3. TREE DATA FUSION (Khusus untuk Layer BLOK)
        // Ambil data pohon untuk menghitung Coefficient of Variation (CV) dan Survival
        $treeDataGrouped = [];
        if ($type === 'blok') {
            $treeDataGrouped = $this->getTreeDataGroupedByBlock($kode);
        }

        foreach ($geojsonData['features'] as &$feature) {
            $props = &$feature['properties'];
            $unitID = strtoupper(trim($props['BLOK'] ?? $props['AFDELING'] ?? ''));

            // Ambil data statistik dari DB
            $data = $lookupMap->get($unitID);

            if ($type === 'blok') {
                // Jalankan Logika "Integrated Plantation Health Index" (IPHI)
                $trees = $treeDataGrouped[$unitID] ?? [];
                $analysis = $this->calculateBlockHealth($trees);

                // Injeksi hasil analisis IPHI ke properties GeoJSON
                $props['analysis'] = $analysis;
                $props['fill_color'] = $this->getIPHIColor($analysis['status']);
                $props['survival_rate'] = $analysis['survival_rate'];
            } else if ($data) {
                $props['survival_rate'] = (float) $data->persen_pkk_normal;
                $props['fill_color'] = $this->calculateColor($data->persen_pkk_normal, $type);
                $props['LUAS_ADM'] = (float) $data->luas_ha;
            }

            $props['std_blok'] = $unitID;
            $props['layer_type'] = $type;
            $props['db_found'] = $data ? true : false;
        }

        return $geojsonData;
    }

    /**
     * Logic Integrated Plantation Health Index (IPHI)
     * Menggunakan MCDM (Multi-Criteria Decision Making)
     */
    public function calculateBlockHealth($trees)
    {
        if (empty($trees)) {
            return [
                'status' => 'healthy',
                'score' => 100,
                'cv' => 0,
                'survival_rate' => 100,
                'message' => 'No Tree Data Available'
            ];
        }

        $total = count($trees);
        $mati = 0;
        $kerdil = 0;
        $girths = [];

        foreach ($trees as $tree) {
            $status = strtoupper($tree['properties']['KONPOKOK'] ?? '');
            if ($status === 'MATI') $mati++;
            if ($status === 'KERDIL') $kerdil++;

            // Ambil data biometrik (Rasio Lingkar Batang (LB/KC))
            $girth = (float) ($tree['properties']['std_lingkar_batang'] ?? 0);
            if ($girth > 0) $girths[] = $girth;
        }

        // Kriteria 1: Survival Score (W1 = 40%)
        $survivalRate = (($total - $mati) / $total) * 100;
        $s_score = $survivalRate * 0.4;

        // Kriteria 2: Uniformity Score / CV (W2 = 30%)
        // Semakin rendah variasi pertumbuhan, semakin sehat bloknya
        $u_score = 30; // Default
        $cv = 0;
        if (count($girths) > 1) {
            $mean = array_sum($girths) / count($girths);
            $sumSq = 0;
            foreach ($girths as $g) $sumSq += pow($g - $mean, 2);
            $stdDev = sqrt($sumSq / count($girths));
            $cv = ($stdDev / $mean) * 100;

            if ($cv <= 15) $u_score = 30; // Sangat Seragam
            elseif ($cv > 30) $u_score = 10; // Heterogenitas Tinggi (Masalah)
            else $u_score = 20;
        }

        // Kriteria 3: Growth Performance (W3 = 30%)
        $normalRate = (($total - ($mati + $kerdil)) / $total) * 100;
        $g_score = $normalRate * 0.3;

        // Total Agregated Score
        $totalScore = $s_score + $u_score + $g_score;

        // Classification Logic
        $statusFinal = 'healthy';
        if ($totalScore < 65) $statusFinal = 'critical';
        elseif ($totalScore < 85) $statusFinal = 'moderate';

        return [
            'status' => $statusFinal,
            'score' => round($totalScore, 2),
            'cv' => round($cv, 2),
            'survival_rate' => round($survivalRate, 2),
            'counts' => ['total' => $total, 'dead' => $mati, 'stunted' => $kerdil]
        ];
    }

    /**
     * Memuat data KONPOKOK dan mengelompokkan per blok
     * Hasil grouping di-cache selama 1 jam (3600 detik) untuk menghindari I/O & CPU overhead dari data spasial raksasa.
     */
    private function getTreeDataGroupedByBlock($kode)
    {
        $cacheKey = "tree_grouped_{$kode}";

        return Cache::remember($cacheKey, 3600, function () use ($kode) {
            $path = $this->resolvePath($kode, 'konpokok');
            if (!$path) return [];

            $data = json_decode(Storage::disk('local')->get($path), true);
            $grouped = [];

            foreach ($data['features'] as $f) {
                $bid = strtoupper(trim($f['properties']['BLOK'] ?? ''));
                if ($bid) {
                    $grouped[$bid][] = $f;
                }
            }
            return $grouped;
        });
    }

    /**
     * Resolve path file GeoJSON dengan sistem fallback
     */
    private function resolvePath($kode, $type)
    {
        try {
            $config = DB::table('kebun_layers')
                ->where('kebun_code', $kode)
                ->where('layer_type', $type)
                ->where('is_active', 1)
                ->first();

            if ($config && Storage::disk('local')->exists($config->file_path)) {
                return $config->file_path;
            }
        } catch (\Exception $e) {
            Log::error("Database Kebun Layers Error: " . $e->getMessage());
        }

        $upper = strtoupper($type);
        $fallbacks = [
            "spatial/{$kode}/{$kode}_TBM2023_{$upper}.geojson",
            "spatial/{$kode}/{$kode}_{$upper}.geojson",
            "spatial/{$kode}/{$type}.geojson"
        ];

        foreach ($fallbacks as $path) {
            if (Storage::disk('local')->exists($path)) return $path;
        }

        return null;
    }

    /**
     * Warna IPHI (Integrated Plantation Health Index)
     */
    private function getIPHIColor($status)
    {
        return [
            'healthy'  => '#10b981', // Hijau
            'moderate' => '#f59e0b', // Kuning
            'critical' => '#ef4444', // Merah
        ][$status] ?? '#cbd5e1';
    }

    /**
     * Penentuan warna tematik standar
     */
    private function calculateColor($percentage, $type)
    {
        if ($percentage >= 95) return '#10b981';
        if ($percentage >= 90) return '#f59e0b';
        if ($percentage > 0) return '#ef4444';
        return ($type === 'batas') ? '#94a3b8' : '#cbd5e1';
    }

    /**
     * Konfigurasi Orthophoto UAV
     */
    public function getOrthophotoConfig($kodeKebun)
    {
        $kebun = LokasiKebun::where('kebun', strtoupper($kodeKebun))->first();
        return [
            'tile_url' => $kebun->tile_url ?? null,
            'latitude' => (float) ($kebun->latitude ?? 2.03394),
            'longitude' => (float) ($kebun->longitude ?? 99.9952),
            'minZoom' => 12,
            'maxZoom' => 22,
            'maxNativeZoom' => 18
        ];
    }
}
