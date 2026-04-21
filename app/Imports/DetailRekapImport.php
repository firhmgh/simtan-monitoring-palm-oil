<?php

namespace App\Imports;

use App\Models\DetailRekaps;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class DetailRekapImport implements ToCollection, WithHeadingRow
{
    protected $kodeUpload;

    public function __construct($kodeUpload)
    {
        $this->kodeUpload = $kodeUpload;
    }

    public function collection(Collection $rows)
    {
        $currentDistrik = null;
        $currentKebun = null;
        $success = 0;
        $failed = 0;

        foreach ($rows as $index => $row) {
            // Logika Mapping Header Khusus
            $specialMapped = $this->mapSpecialHeaderValues($row);
            $row = collect($row)->merge($specialMapped);

            // Normalisasi Keys (Menghilangkan Spasi, %, dll)
            $row = $row->mapWithKeys(function ($val, $key) {
                $key = strtolower(trim($key));
                if (str_starts_with($key, '%')) $key = 'persen_' . substr($key, 1);
                $key = preg_replace('/[\s\/\(\)%]+/', '_', $key);
                $key = trim($key, '_');
                return [$key => $val];
            });

            if ($row->filter()->isEmpty()) continue;

            // Simpan Distrik & Kebun Terakhir (Merged Cells Logic)
            if (!empty($row['distrik']) && strtoupper(trim($row['distrik'])) !== 'TOTAL') {
                $currentDistrik = strtoupper(trim($row['distrik']));
            }
            if (!empty($row['kebun']) && strtoupper(trim($row['kebun'])) !== 'TOTAL') {
                $currentKebun = strtoupper(trim($row['kebun']));
            }

            if (!$currentDistrik || !$currentKebun) continue;

            try {
                $afdeling = $this->sanitizeAfdeling($row['afdeling'] ?? null);
                $isTotal = empty($afdeling) ? 1 : 0;

                DetailRekaps::create([
                    'kode_upload'                 => $this->kodeUpload,
                    'distrik'                     => $currentDistrik,
                    'kebun'                       => $currentKebun,
                    'afdeling'                    => $afdeling,
                    'tahun_tanam'                 => !empty($row['tahun_tanam']) ? (int) $row['tahun_tanam'] : null,
                    'luas_ha'                     => $this->sanitizeDesimal($row['luas_ha'] ?? null),
                    'pkk_awal'                    => $this->sanitizeRibuan($row['pkk_awal'] ?? null),
                    'pkk_normal'                  => $this->sanitizeRibuan($row['pkk_normal'] ?? null),
                    'pkk_non_valuer'              => $this->sanitizeRibuan($row['pkk_non_valuer_kerdil'] ?? $row['pkk_non_valuer'] ?? null),
                    'pkk_mati'                    => $this->sanitizeRibuan($row['pkk_mati'] ?? null),
                    'persen_pkk_normal'           => $this->sanitizeDesimal($row['persen_pkk_normal'] ?? null),
                    'persen_pkk_non_valuer'       => $this->sanitizeDesimal($row['persen_pkk_non_valuer'] ?? null),
                    'persen_pkk_mati'             => $this->sanitizeDesimal($row['persen_pkk_mati'] ?? null),
                    'persen_tutupan_kacangan'     => $this->sanitizeDesimal($row['persen_tutupan_kacangan'] ?? $row['tutupan_kacangan'] ?? null),
                    'persen_pir_pkk_kurang_baik'  => $this->sanitizeDesimal($row['persen_pir_pkk_dan_pasar_pikul_kurang_baik'] ?? null),
                    'persen_area_tergenang'       => $this->sanitizeDesimal($row['persen_area_tergenang'] ?? null),
                    'kondisi_anak_kayu'           => $row['kondisi_anak_kayu'] ?? null,
                    'is_total'                    => $isTotal,
                ]);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("Gagal simpan Detail Rekap baris {$index}: " . $e->getMessage());
            }
        }
    }

    private function mapSpecialHeaderValues($row): array
    {
        $mapping = [];
        foreach ($row as $key => $value) {
            $n = strtolower(trim($key));
            if (str_contains($n, '%')) {
                if (str_contains($n, 'pkk normal')) $mapping['persen_pkk_normal'] = $value;
                elseif (str_contains($n, 'non valuer') || str_contains($n, 'kerdil')) $mapping['persen_pkk_non_valuer'] = $value;
                elseif (str_contains($n, 'pkk mati')) $mapping['persen_pkk_mati'] = $value;
            }
        }
        return $mapping;
    }

    private function sanitizeRibuan($value)
    {
        if ($value === null || $value === '') return null;
        return is_numeric(str_replace([',', '.'], '', $value)) ? (int)str_replace([',', '.'], '', $value) : null;
    }

    private function sanitizeDesimal($value)
    {
        if ($value === null || $value === '') return null;
        return is_numeric(str_replace(',', '.', $value)) ? (float)str_replace(',', '.', $value) : null;
    }

    private function sanitizeAfdeling($value)
    {
        if (!$value || trim($value) === '-' || strtoupper(trim($value)) === 'TOTAL') return null;
        return strtoupper(str_replace(' ', '', trim($value)));
    }
}
