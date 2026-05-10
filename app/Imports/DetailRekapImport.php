<?php

namespace App\Imports;

use App\Models\DetailRekap;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

/**
 * DetailRekapImport
 * Mengintegrasikan logika cerdas dari simtanfix untuk menangani merged cells,
 * normalisasi header, dan pencegahan error numeric out of range.
 */
class DetailRekapImport implements ToCollection, WithHeadingRow
{
    protected $simtanFormId;
    protected $kodeUpload;

    /**
     * Constructor menerima ID (Integer) untuk relasi database
     * dan Kode (String) untuk identitas dokumen (Audit Trail).
     */
    public function __construct($simtanFormId, $kodeUpload)
    {
        $this->simtanFormId = $simtanFormId;
        $this->kodeUpload = $kodeUpload;
    }

    public function collection(Collection $rows)
    {
        Log::info('🧠 Inisialisasi Import Detail Rekap. Header terdeteksi:', $rows->first()->keys()->toArray());

        $currentDistrik = null;
        $currentKebun = null;
        $success = 0;
        $failed = 0;

        foreach ($rows as $index => $row) {
            // 1. Logika Mapping Header Khusus (Menangani simbol % dan variasi penulisan)
            $specialMapped = $this->mapSpecialHeaderValues($row);
            $row = collect($row)->merge($specialMapped);

            // 2. Normalisasi Keys (Mengubah header Excel menjadi format snake_case database)
            $row = $row->mapWithKeys(function ($val, $key) {
                $key = strtolower(trim($key));
                if (str_starts_with($key, '%')) $key = 'persen_' . substr($key, 1);
                $key = preg_replace('/[\s\/\(\)%]+/', '_', $key);
                $key = preg_replace('/_{2,}/', '_', $key); // Hilangkan double underscore
                $key = trim($key, '_');
                return [$key => $val];
            });

            // Skip jika baris kosong
            if ($row->filter()->isEmpty()) continue;

            // --- LOGIKA SKIP HEADER & TOTAL (Mencegah Error Data Truncated) ---
            $valDistrik = strtoupper(trim($row['distrik'] ?? ''));
            if ($valDistrik === 'DISTRIK' || $valDistrik === 'KEBUN' || $valDistrik === 'AFDELING') {
                continue;
            }

            // 3. Logika Merged Cells (Mengambil wilayah dari baris sebelumnya jika kosong)
            if (!empty($row['distrik']) && strtoupper(trim($row['distrik'])) !== 'TOTAL') {
                $currentDistrik = strtoupper(trim($row['distrik']));
            }
            if (!empty($row['kebun']) && strtoupper(trim($row['kebun'])) !== 'TOTAL') {
                $currentKebun = strtoupper(trim($row['kebun']));
            }

            // Jika identitas wilayah dasar belum ditemukan, lewati baris ini
            if (!$currentDistrik || !$currentKebun) continue;

            try {
                $afdeling = $this->sanitizeAfdeling($row['afdeling'] ?? null);
                // Jika afdeling kosong atau bertuliskan 'TOTAL', tandai sebagai baris total agregat
                $isTotal = (empty($afdeling) || strtoupper($afdeling) == 'TOTAL') ? 1 : 0;

                // 4. Integrasi ke Database (Menggunakan Hybrid Key)
                DetailRekap::create([
                    'simtan_form_id'              => $this->simtanFormId, // Integer
                    'kode_upload'                 => $this->kodeUpload,    // String (Audit Trail)
                    'distrik'                     => $currentDistrik,
                    'kebun'                       => $currentKebun,
                    'afdeling'                    => $afdeling,
                    'tahun_tanam'                 => !empty($row['tahun_tanam']) ? (int) $row['tahun_tanam'] : null,
                    'luas_ha'                     => $this->sanitizeDesimal($row['luas_ha'] ?? null),
                    'pkk_awal'                    => $this->sanitizeRibuan($row['pkk_awal'] ?? null),
                    'pkk_normal'                  => $this->sanitizeRibuan($row['pkk_normal'] ?? null),
                    'pkk_non_valuer'              => $this->sanitizeRibuan($row['pkk_non_valuer_kerdil'] ?? $row['pkk_non_valuer'] ?? null),
                    'pkk_mati'                    => $this->sanitizeRibuan($row['pkk_mati'] ?? null),

                    // PENANGANAN ERROR TRILIUNAN (Numeric value out of range)
                    'pkk_ha_kond_normal'          => $this->limitValue($this->sanitizeDesimal($row['pkkha_kond_normal'] ?? $row['pkk_ha_kond_normal'] ?? null)),

                    'persen_pkk_normal'           => $this->sanitizeDesimal($row['persen_pkk_normal'] ?? null),
                    'persen_pkk_non_valuer'       => $this->sanitizeDesimal($row['persen_pkk_non_valuer'] ?? $row['persen_pkk_non_valuer_kerdil'] ?? null),
                    'persen_pkk_mati'             => $this->sanitizeDesimal($row['persen_pkk_mati'] ?? null),
                    'persen_tutupan_kacangan'     => $this->sanitizeDesimal($row['persen_tutupan_kacangan'] ?? $row['tutupan_kacangan'] ?? null),
                    'persen_pir_pkk_kurang_baik'  => $this->sanitizeDesimal($row['persen_pir_pkk_dan_pasar_pikul_kurang_baik'] ?? $row['pir_pkk_dan_pasar_pikul_kurang_baik'] ?? null),
                    'persen_area_tergenang'       => $this->sanitizeDesimal($row['persen_area_tergenang'] ?? $row['area_tergenang'] ?? null),
                    'kondisi_anak_kayu'           => $this->sanitizeDesimal($row['kondisi_anak_kayu'] ?? null),
                    'gangguan_ternak'             => $row['gangguan_ternak'] ?? null,
                    'is_total'                    => $isTotal,
                ]);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("❌ Gagal simpan Detail Rekap baris {$index}: " . $e->getMessage());
            }
        }

        Log::info("[IMPORT SELESAI] ✅ Sukses: {$success} | ❌ Gagal: {$failed}");
    }

    /**
     * Helper untuk memetakan kolom persentase yang sering menggunakan simbol % di Excel
     */
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

    /**
     * Mencegah nilai di luar batas jangkauan kolom database (Standard fault tolerance)
     */
    private function limitValue($value)
    {
        // Limit untuk tipe data INT (2 Miliar). Jika lebih, kemungkinan itu error format Excel.
        if ($value > 2147483647) return null;
        return $value;
    }

    private function sanitizeRibuan($value)
    {
        if ($value === null || $value === '') return null;
        $clean = str_replace([',', '.'], '', $value);
        return is_numeric($clean) ? (int)$clean : null;
    }

    private function sanitizeDesimal($value)
    {
        if ($value === null || $value === '') return null;
        $clean = str_replace(',', '.', $value);
        return is_numeric($clean) ? (float)$clean : null;
    }

    private function sanitizeAfdeling($value)
    {
        if (!$value || trim($value) === '-' || strtoupper(trim($value)) === 'TOTAL') return null;
        return strtoupper(str_replace(' ', '', trim($value)));
    }
}
