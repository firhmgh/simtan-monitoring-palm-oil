<?php

namespace App\Services;

use App\Models\DetailRekaps;
use App\Models\LokasiKebuns;
use App\Models\KorelasiVegetatif;
use App\Helpers\ExcelDataHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ChartDataService
{
    /**
     * Chart: Peringkat Kondisi Pohon Global
     * Menampilkan performa kesehatan pohon antar kebun.
     */
    public function getPeringkatKondisiPohonData()
    {
        $data = DetailRekaps::where('is_total', true)
            ->orderByDesc('persen_pkk_normal')
            ->get(['kebun', 'persen_pkk_normal', 'persen_pkk_non_valuer', 'persen_pkk_mati']);

        if ($data->isEmpty()) {
            return ['peringkatKondisiPohonChartData' => []];
        }

        return ExcelDataHelper::formatKondisiPohonData($data);
    }

    /**
     * Chart: Peringkat Pemeliharaan Global
     * Menampilkan kualitas teknis pemeliharaan (LCC, Piringan, Drainase).
     */
    public function getPeringkatPemeliharaanData()
    {
        $data = DetailRekaps::where('is_total', true)
            ->orderByDesc('persen_tutupan_kacangan')
            ->get(['kebun', 'persen_tutupan_kacangan', 'persen_pir_pkk_kurang_baik', 'persen_area_tergenang', 'kondisi_anak_kayu']);

        if ($data->isEmpty()) {
            return ['peringkatPemeliharaanChartData' => []];
        }

        return ExcelDataHelper::formatPemeliharaanData($data);
    }

    /**
     * Chart: Korelasi Vegetatif (Biometrik)
     * Mengolah data lingkar batang, pelepah, dan panjang pelepah.
     */
    public function getKorelasiVegetatifChartData()
    {
        $data = KorelasiVegetatif::all();

        if ($data->isEmpty()) {
            return [
                'korelasiVegetatifLabels' => [],
                'korelasiVegetatifLingkarBatang' => [],
                'korelasiVegetatifJumlahPelepah' => [],
                'korelasiVegetatifPanjangPelepah' => [],
            ];
        }

        return ExcelDataHelper::formatKorelasiVegetatifData($data);
    }

    /**
     * Chart: Luas Areal Berdasarkan Tahun Tanam
     */
    public function getLuasArealTahunTanamData()
    {
        $data = DetailRekaps::where('is_total', false)
            ->whereNotNull('tahun_tanam')
            ->where('tahun_tanam', '!=', 0)
            ->selectRaw('tahun_tanam as tahun, SUM(luas_ha) as total_luas')
            ->groupBy('tahun_tanam')
            ->orderBy('tahun_tanam')
            ->get();

        if ($data->isEmpty()) {
            return [
                'tahunTanam' => [],
                'totalLuas' => [],
            ];
        }

        return [
            'tahunTanam' => $data->pluck('tahun'),
            'totalLuas' => $data->pluck('total_luas')->map(fn($v) => round($v, 2)),
        ];
    }

    /**
     * Analisis Perbandingan Target vs Realisasi Populasi (Agronomic Standard)
     * Target: 143 Pokok/Ha
     */
    public function getPopulasiPerformanceData()
    {
        $data = DetailRekaps::where('is_total', 0)
            ->selectRaw('kebun, SUM(luas_ha) as total_luas, SUM(pkk_normal) as realisasi_pokok')
            ->groupBy('kebun')
            ->get();

        if ($data->isEmpty()) {
            return [
                'populasiLabels' => [],
                'populasiTarget' => [],
                'populasiActual' => []
            ];
        }

        $labels = [];
        $targets = [];
        $actuals = [];

        foreach ($data as $item) {
            $labels[] = $item->kebun;
            $targetPokok = round($item->total_luas * 143);
            $targets[] = $targetPokok;
            $actuals[] = (int) $item->realisasi_pokok;
        }

        return [
            'populasiLabels' => $labels,
            'populasiTarget' => $targets,
            'populasiActual' => $actuals
        ];
    }

    /**
     * Chart: Luas Areal per Kebun per Afdeling (Stacked Bar Logic)
     */
    public function getLuasArealTahunTanamPerKebunData()
    {
        $data = DetailRekaps::where(function ($query) {
            $query->whereNotNull('tahun_tanam')->orWhere('is_total', 1);
        })
            ->whereNotNull('luas_ha')
            ->get();

        if ($data->isEmpty()) {
            return ['namaKebunTerluas' => [], 'series' => []];
        }

        $kebunTotals = $data->where('is_total', 1)
            ->filter(fn($item) => !empty($item->kebun))
            ->groupBy(fn($item) => strtoupper(trim($item->kebun)))
            ->map(fn($items) => $items->sum('luas_ha'));

        $namaKebun = $kebunTotals->sortDesc()->keys()->values();
        $seriesMap = [];

        foreach ($data as $row) {
            if ((int) $row->is_total === 1) continue;

            $kebun = strtoupper(trim($row->kebun ?? ''));
            if (empty($kebun)) continue;

            $label = "{$row->afdeling} - {$row->tahun_tanam}";

            if (!isset($seriesMap[$label])) {
                $seriesMap[$label] = array_fill_keys($namaKebun->toArray(), 0);
            }

            if (array_key_exists($kebun, $seriesMap[$label])) {
                $seriesMap[$label][$kebun] += $row->luas_ha;
            }
        }

        $series = [];
        foreach ($seriesMap as $label => $values) {
            $sortedValues = [];
            foreach ($namaKebun as $kbn) {
                $sortedValues[] = round($values[$kbn] ?? 0, 2);
            }
            $series[] = ['name' => $label, 'data' => $sortedValues];
        }

        return [
            'namaKebunTerluas' => $namaKebun->toArray(),
            'series' => $series
        ];
    }

    /**
     * Info Kebun: Metadata untuk Detail Page
     */
    public function getInfoKebunData($kodeKebun)
    {
        $row = DetailRekaps::where('kebun', strtoupper($kodeKebun))
            ->where('is_total', true)
            ->first();

        if (!$row) {
            return [
                'distrik' => '-',
                'nama' => strtoupper($kodeKebun),
                'luas' => 0,
                'kode_kebun' => strtoupper($kodeKebun),
            ];
        }

        return ExcelDataHelper::getInfoKebun(
            $row->kebun,
            $row->distrik ?? '-',
            $row->luas_ha ?? 0
        );
    }

    /**
     * Logika Spasial: Menghitung jumlah status blok dan mapping warna SVG
     * Digunakan untuk Sidebar Legend dan Peta Interaktif
     */
    public function getBlockAnalysisData($kodeKebun)
    {
        $blocks = DetailRekaps::where('kebun', strtoupper($kodeKebun))
            ->where('is_total', 0)
            ->get();

        $counts = ['healthy' => 0, 'moderate' => 0, 'critical' => 0];
        $mapping = [];

        foreach ($blocks as $b) {
            $status = 'healthy';
            $color = '#10b981'; // Default: Hijau (Success)

            // Logika Penentuan Status Berdasarkan Standar Agronomi TBM
            if ($b->persen_pkk_normal < 90) {
                $status = 'moderate';
                $color = '#f59e0b'; // Oranye (Warning)
            }
            if ($b->persen_pkk_normal < 70 || $b->persen_pkk_mati > 5) {
                $status = 'critical';
                $color = '#ef4444'; // Merah (Danger)
            }

            $counts[$status]++;
            // Mapping ID Blok ke Warna untuk SVG
            $mapping[$b->blok] = $color;
        }

        return [
            'statusCounts' => $counts,
            'blockStatuses' => $mapping
        ];
    }

    /**
     * Chart: Kondisi Pohon (Detail per Satu Kebun)
     */
    public function getKondisiPohonData($kodeKebun)
    {
        $data = DetailRekaps::where('kebun', strtoupper($kodeKebun))
            ->where('is_total', true)
            ->get();

        return $data->isEmpty() ? [] : ExcelDataHelper::getKondisiPohonData($data);
    }

    /**
     * Chart: Areal Tanaman/Pemeliharaan (Detail per Satu Kebun)
     */
    public function getArealTanamanData($kodeKebun)
    {
        $data = DetailRekaps::where('kebun', strtoupper($kodeKebun))
            ->where('is_total', true)
            ->get();

        return $data->isEmpty() ? [] : ExcelDataHelper::getArealTanamanData($data);
    }

    /**
     * Geospasial: Data Lokasi Peta Kebun
     */
    public function getLokasiKebunData($kodeKebun)
    {
        $data = LokasiKebuns::where('kebun', strtoupper($kodeKebun))->get();

        return $data->isEmpty() ? [] : ExcelDataHelper::getLokasiKebun($data);
    }
}
