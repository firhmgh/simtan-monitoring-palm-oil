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
    /**
     * Chart: Peringkat Kondisi Pohon
     * Logic: Menangani kondisi data kosong agar chart tidak error saat render.
     */
    public function getPeringkatKondisiPohonData()
    {
        $data = DetailRekap::where('is_total', true)
            ->orderByDesc('persen_pkk_normal')
            ->get(['kebun', 'persen_pkk_normal', 'persen_pkk_non_valuer', 'persen_pkk_mati']);

        if ($data->isEmpty()) {
            return ['peringkatKondisiPohonChartData' => []];
        }

        return ExcelDataHelper::formatKondisiPohonData($data);
    }

    /**
     * Chart: Peringkat Pemeliharaan
     */
    public function getPeringkatPemeliharaanData()
    {
        $data = DetailRekap::where('is_total', true)
            ->orderByDesc('persen_tutupan_kacangan')
            ->get(['kebun', 'persen_tutupan_kacangan', 'persen_pir_pkk_kurang_baik', 'persen_area_tergenang', 'kondisi_anak_kayu']);

        if ($data->isEmpty()) {
            return ['peringkatPemeliharaanChartData' => []];
        }

        return ExcelDataHelper::formatPemeliharaanData($data);
    }

    /**
     * Chart: Korelasi Vegetatif
     */
    public function getKorelasiVegetatifChartData()
    {
        $data = KorelasiVegetatif::get();

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
     * Chart: Luas Areal Tahun Tanam
     */
    public function getLuasArealTahunTanamData()
    {
        $data = DetailRekap::where('is_total', false)
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
     * Analisis Perbandingan Target vs Realisasi Populasi (Scopus Level)
     * Target Standard: 143 Pokok/Ha
     * Ditambahkan untuk meningkatkan nilai riset pada tren data.
     */
    public function getPopulasiPerformanceData()
    {
        // Ambil data detail (per blok/afdeling) untuk akurasi SUM
        $data = DetailRekap::where('is_total', 0)
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
            // Hitung Target berdasarkan standar agronomi perkebunan sawit (143 pkk/ha)
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
     * Chart: Luas Areal Tahun Tanam per Kebun
     * Note: Fungsi ini sangat kompleks, dipastikan fallback array tetap konsisten.
     */
    public function getLuasArealTahunTanamPerKebunData()
    {
        $data = DetailRekap::where(function ($query) {
            $query->whereNotNull('tahun_tanam')->orWhere('is_total', 1);
        })
            ->whereNotNull('luas_ha')
            ->get();

        if ($data->isEmpty()) {
            return [
                'namaKebunTerluas' => [],
                'series' => []
            ];
        }

        $kebunTotals = $data->where('is_total', 1)
            ->filter(fn($item) => !empty($item->kebun))
            ->groupBy(fn($item) => strtoupper(trim($item->kebun)))
            ->map(fn($items) => $items->sum('luas_ha'));

        $namaKebun = $kebunTotals->sortDesc()->keys()->values();
        $seriesMap = [];

        foreach ($data as $row) {
            $isTotal = (int) $row->is_total === 1;
            $kebun = strtoupper(trim($row->kebun ?? ''));
            if (empty($kebun)) continue;

            $label = $isTotal ? 'TOTAL' : "{$row->afdeling} - {$row->tahun_tanam}";

            if (!isset($seriesMap[$label])) {
                $seriesMap[$label] = array_fill_keys($namaKebun->toArray(), 0);
            }

            if (!array_key_exists($kebun, $seriesMap[$label])) {
                $seriesMap[$label][$kebun] = 0;
                foreach ($seriesMap as &$otherLabel) {
                    if (!array_key_exists($kebun, $otherLabel)) $otherLabel[$kebun] = 0;
                }
                if (!$namaKebun->contains($kebun)) $namaKebun->push($kebun);
            }
            $seriesMap[$label][$kebun] += $row->luas_ha;
        }

        $totalSeries = [];
        if (isset($seriesMap['TOTAL'])) {
            $totalSeries['TOTAL'] = $seriesMap['TOTAL'];
            unset($seriesMap['TOTAL']);
        }

        $orderedSeriesMap = collect($seriesMap)->sortKeysUsing(function ($a, $b) {
            preg_match('/AFD(\d+)\s*-\s*(\d+)/', $a, $matchA);
            preg_match('/AFD(\d+)\s*-\s*(\d+)/', $b, $matchB);
            $afdA = isset($matchA[1]) ? (int)$matchA[1] : 999;
            $afdB = isset($matchB[1]) ? (int)$matchB[1] : 999;
            if ($afdA === $afdB) {
                $yearA = isset($matchA[2]) ? (int)$matchA[2] : 0;
                $yearB = isset($matchB[2]) ? (int)$matchB[2] : 0;
                return $yearA <=> $yearB;
            }
            return $afdA <=> $afdB;
        })->toArray();

        $orderedSeriesMap = $orderedSeriesMap + $totalSeries;
        $series = [];
        foreach ($orderedSeriesMap as $label => $values) {
            $sortedValues = [];
            foreach ($namaKebun as $kebun) {
                $sortedValues[] = $values[$kebun] ?? 0;
            }
            $series[] = ['name' => $label, 'data' => $sortedValues];
        }

        return [
            'namaKebunTerluas' => $namaKebun->toArray(),
            'series' => $series
        ];
    }

    /**
     * Info Kebun Data
     */
    public function getInfoKebunData($kodeKebun)
    {
        $row = DetailRekap::where('kebun', strtoupper($kodeKebun))
            ->where('is_total', true)
            ->first();

        if (!$row) {
            return [
                'distrik' => '-',
                'nama' => '-',
                'luas' => 0,
                'kode_kebun' => strtoupper($kodeKebun),
            ];
        }

        return \App\Helpers\ExcelDataHelper::getInfoKebun(
            $row->kebun,
            $row->kode_distrik ?? $row->distrik ?? '-',
            $row->luas_ha ?? 0
        );
    }

    /**
     * Chart: Kondisi Pohon (Detail per Kebun)
     */
    public function getKondisiPohonData($kodeKebun)
    {
        $data = DetailRekap::where('kebun', strtoupper($kodeKebun))
            ->where('is_total', true)
            ->get();

        if ($data->isEmpty()) return [];

        return ExcelDataHelper::getKondisiPohonData($data);
    }

    /**
     * Chart: Areal Tanaman (Detail per Kebun)
     */
    public function getArealTanamanData($kodeKebun)
    {
        $data = DetailRekap::where('kebun', strtoupper($kodeKebun))
            ->where('is_total', true)
            ->get();

        if ($data->isEmpty()) return [];

        return ExcelDataHelper::getArealTanamanData($data);
    }

    /**
     * Lokasi Kebun
     */
    public function getLokasiKebunData($kodeKebun)
    {
        $data = LokasiKebun::where('kebun', strtoupper($kodeKebun))->get();

        if ($data->isEmpty()) return [];

        return ExcelDataHelper::getLokasiKebun($data);
    }
}