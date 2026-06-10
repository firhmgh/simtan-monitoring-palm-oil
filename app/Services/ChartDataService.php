<?php

namespace App\Services;

use App\Models\DetailRekap;
use App\Models\LokasiKebun;
use App\Models\KorelasiVegetatif;
use App\Helpers\ExcelDataHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ChartDataService

{

    // =========================================================================
    // THRESHOLD ILMIAH (Sinkron dengan Standar Agronomi PPKS)
    // Digunakan untuk Klasifikasi Kesehatan Tanaman pada Dashboard & Peta
    // =========================================================================
    const CRITICAL_THRESHOLD = 85.0; // Di bawah 85% = Kritis (Merah)
    const WARNING_THRESHOLD  = 95.0; // 85% - 95% = Warning (Kuning)
                                     // Di atas 95% = Optimal (Hijau)
    /**
     * Chart: Peringkat Kondisi Pohon Global
     * Menampilkan performa kesehatan pohon antar kebun.
     */
    public function getPeringkatKondisiPohonData($periode)
    {
        $data = DetailRekap::where('is_total', true)
            ->where('periode', $periode)
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
    public function getPeringkatPemeliharaanData($periode)
    {
        $data = DetailRekap::where('is_total', true)
            ->where('periode', $periode)
            ->orderByDesc('persen_tutupan_kacangan')
            ->get(['kebun', 'persen_tutupan_kacangan', 'persen_pir_pkk_kurang_baik', 'persen_area_tergenang', 'kondisi_anak_kayu']);

        if ($data->isEmpty()) {
            return ['peringkatPemeliharaanChartData' => []];
        }

        return ExcelDataHelper::formatPemeliharaanData($data);
    }

    /**
     * Chart: Korelasi Vegetatif (Biometrik)
     * Mengolah data Rasio Lingkar Batang (LB/KC), pelepah, dan Rasio Panjang Pelepah (PP/KC).
     */
    public function getKorelasiVegetatifChartData($periode)
    {
        $data = KorelasiVegetatif::where('periode', $periode)->get();

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
    public function getLuasArealTahunTanamData($periode)
    {
        $data = DetailRekap::where('is_total', false)
            ->where('periode', $periode)
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
    public function getPopulasiPerformanceData($periode)
    {
        $data = DetailRekap::where('is_total', 0)
            ->where('periode', $periode)
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
            // Target dihitung berdasarkan Standar Kerapatan 143 Pokok per Hektar
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
    public function getLuasArealTahunTanamPerKebunData($periode)
    {
        $data = DetailRekap::where('periode', $periode)
            ->where(function ($query) {
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
     * Info Kebun: Metadata (Detail Page) - Mendukung Filter Periode
     */
    public function getInfoKebunData($kodeKebun, $periode)
    {
        $row = DetailRekap::where('kebun', strtoupper($kodeKebun))
            ->where('periode', $periode)
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
     * Logika Spasial: Sinkronisasi Status Blok dengan Dashboard
     */
    public function getBlockAnalysisData($kodeKebun, $periode)
    {
        $blocks = DetailRekap::where('kebun', strtoupper($kodeKebun))
            ->where('periode', $periode)
            ->where('is_total', 0)
            ->get();

        $counts = ['healthy' => 0, 'moderate' => 0, 'critical' => 0];
        $mapping = [];

        foreach ($blocks as $b) {
            $val = (float)$b->persen_pkk_normal;

            if ($val >= self::WARNING_THRESHOLD) {
                $status = 'healthy';
                $color = '#10b981'; // Hijau
            } elseif ($val >= self::CRITICAL_THRESHOLD) {
                $status = 'moderate';
                $color = '#f59e0b'; // Kuning
            } else {
                $status = 'critical';
                $color = '#ef4444'; // Merah
            }

            $counts[$status]++;
            $mapping[$b->blok] = $color;
        }

        return [
            'statusCounts' => $counts,
            'blockStatuses' => $mapping
        ];
    }

    /**
     * Chart: Kondisi Pohon per Kebun (Detail Page)
     */
    public function getKondisiPohonData($kodeKebun, $periode)
    {
        $data = DetailRekap::where('kebun', strtoupper($kodeKebun))
            ->where('periode', $periode)
            ->where('is_total', true)
            ->get();

        return $data->isEmpty() ? [] : ExcelDataHelper::getKondisiPohonData($data);
    }

    /**
     * Chart: Areal Tanaman per Kebun (Detail Page)
     */
    public function getArealTanamanData($kodeKebun, $periode)
    {
        $data = DetailRekap::where('kebun', strtoupper($kodeKebun))
            ->where('periode', $periode)
            ->where('is_total', true)
            ->get();

        return $data->isEmpty() ? [] : ExcelDataHelper::getArealTanamanData($data);
    }

    /**
     * Chart: Korelasi Vegetatif Khusus per Kebun (Detail Page)
     */
    public function getKorelasiVegetatifPerKebun($kodeKebun, $periode)
    {
        $data = KorelasiVegetatif::where('kebun', strtoupper($kodeKebun))
            ->where('periode', $periode)
            ->get();

        if ($data->isEmpty()) {
            return [
                'vegLabels' => [],
                'vegLingkar' => [],
                'vegJumlah' => [],
                'vegPanjang' => [],
            ];
        }

        $formatted = ExcelDataHelper::formatKorelasiVegetatifData($data);

        return [
            'vegLabels' => $formatted['korelasiVegetatifLabels'],
            'vegLingkar' => $formatted['korelasiVegetatifLingkarBatang'],
            'vegJumlah' => $formatted['korelasiVegetatifJumlahPelepah'],
            'vegPanjang' => $formatted['korelasiVegetatifPanjangPelepah'],
        ];
    }

    /**
     * Geospasial: Data Lokasi Peta (Static - Koordinat tidak berubah tiap periode)
     */
    public function getLokasiKebunData($kodeKebun)
    {
        $data = LokasiKebun::where('kebun', strtoupper($kodeKebun))->get();
        return $data->isEmpty() ? [] : ExcelDataHelper::getLokasiKebun($data);
    }
}
