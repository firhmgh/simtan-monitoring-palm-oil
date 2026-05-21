<?php

namespace App\Imports;

use App\Models\DetailRekap;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Facades\Log;

class DetailRekapImport implements ToCollection, WithCalculatedFormulas
{
    protected $simtanFormId, $kodeUpload, $labelPeriode;
    private $currentDistrik = null;
    private $currentKebun = null;

    public function __construct($simtanFormId, $kodeUpload, $labelPeriode)
    {
        $this->simtanFormId = $simtanFormId;
        $this->kodeUpload = $kodeUpload;
        $this->labelPeriode = $labelPeriode;
    }

    public function collection(Collection $rows)
    {
        Log::info("⚙️ START INGESTI: [{$this->labelPeriode}]");

        // Identifikasi Periode 1 (JANFEBMAR...) untuk pemetaan kolom spesifik
        $isP1 = str_contains(strtoupper($this->labelPeriode), 'JANFEBMAR');

        $success = 0;
        $skipped = 0;

        foreach ($rows as $index => $row) {
            // Data asli dimulai dari baris ke-5 (Index 4)
            if ($index < 3) continue;

            $c0 = trim($row[0] ?? ''); // Distrik
            $c1 = trim($row[1] ?? ''); // Kebun
            $c2 = trim($row[2] ?? ''); // Afdeling

            // 🧬 LOGIKA MERGED CELLS: Selalu tangkap identitas wilayah
            if (!empty($c0) && !is_numeric($c0) && strtoupper($c0) !== 'TOTAL') $this->currentDistrik = strtoupper($c0);
            if (!empty($c1) && !is_numeric($c1) && strtoupper($c1) !== 'TOTAL') $this->currentKebun = strtoupper($c1);

            // 🛡️ FILTER 1: Buang baris header (Jika baris mengandung teks judul)
            if ($this->isHeaderRow($c0, $c1, $c2)) {
                $skipped++;
                continue;
            }

            if (!$this->currentDistrik || !$this->currentKebun) continue;

            try {
                $afdeling = $this->sanitizeAfdeling($c2);
                $isTotal = ($afdeling == 'TOTAL') ? 1 : 0;

                $luasVal = $this->sanitizeDesimal($row[4]);
                $pkkAwal = $this->sanitizeRibuan($row[5]);

                // 🛡️ FILTER 2: Buang baris kosong atau baris total hantu (angka nol semua)
                if ($isTotal && $luasVal == 0 && $pkkAwal == 0) {
                    $skipped++;
                    continue;
                }

                // 🎯 PEMETAAN KOLOM MANUAL BERDASARKAN PERIODE
                if ($isP1) {
                    // MAPPING PERIODE 1 (JANFEBMAR...)
                    $mapping = [
                        'pkk_ha_kond_normal'             => $this->limitValue($row[9]),  // Kolom J (Index 9)
                        'persen_pkk_normal'              => $this->strictRound($row[10], 2),
                        'persen_pkk_non_valuer'          => $this->strictRound($row[11], 2),
                        'persen_pkk_mati'                => $this->strictRound($row[12], 2),
                        'persen_tutupan_kacangan'        => $this->strictRound($row[13], 2),
                        'persen_pir_pkk_kurang_baik'     => $this->strictRound($row[14], 2), // Gabungan PIR & Pasar Pikul
                        'persen_pasar_pikul_kurang_baik' => 0, // Kosong di P1
                        'persen_area_tergenang'          => $this->strictRound($row[15], 2),
                        'kondisi_anak_kayu'              => $this->strictRound($row[16], 3),
                        'gangguan_ternak'                => $this->sanitizeTeks($row[17]),
                        'pkk_mati_mati_kembali'          => 0,
                        'pkk_kerdil_mati_kembali'        => 0,
                        'persen_pkk_mati_mati_kembali'   => 0,
                        'persen_pkk_kerdil_mati_kembali' => 0,
                    ];
                } else {
                    // MAPPING PERIODE 2 & 3 (MEIJUL..., SEPOKT...)
                    $mapping = [
                        'pkk_ha_kond_normal'             => $this->limitValue($row[10]), // Kolom K (Index 10)
                        'persen_pkk_normal'              => $this->strictRound($row[11], 2),
                        'persen_pkk_non_valuer'          => $this->strictRound($row[12], 2),
                        'persen_pkk_mati'                => $this->strictRound($row[13], 2),
                        'persen_tutupan_kacangan'        => $this->strictRound($row[14], 2),
                        'persen_pasar_pikul_kurang_baik' => $this->strictRound($row[15], 2), // Kolom P (Index 15)
                        'persen_pir_pkk_kurang_baik'     => $this->strictRound($row[16], 2), // Kolom Q (Index 16)
                        'persen_area_tergenang'          => $this->strictRound($row[17], 2),
                        'kondisi_anak_kayu'              => $this->strictRound($row[18], 3),
                        'gangguan_ternak'                => $this->sanitizeTeks($row[19]),
                        'pkk_mati_mati_kembali'          => $this->limitValue($row[20] ?? 0),
                        'persen_pkk_mati_mati_kembali'   => $this->strictRound($row[21] ?? 0, 2),
                        'pkk_kerdil_mati_kembali'        => $this->limitValue($row[22] ?? 0),
                        'persen_pkk_kerdil_mati_kembali' => $this->strictRound($row[23] ?? 0, 2),
                    ];
                }

                DetailRekap::create(array_merge([
                    'simtan_form_id' => $this->simtanFormId,
                    'kode_upload'    => $this->kodeUpload,
                    'periode'        => $this->labelPeriode,
                    'distrik'        => $this->currentDistrik,
                    'kebun'          => $this->currentKebun,
                    'afdeling'       => $afdeling,
                    'tahun_tanam'    => is_numeric($row[3]) ? (int)$row[3] : null,
                    'luas_ha'        => $this->strictRound($row[4], 2),
                    'pkk_awal'       => $this->limitValue($row[5]),
                    'pkk_normal'     => $this->limitValue($row[6]),
                    'pkk_non_valuer' => $this->limitValue($row[7]),
                    'pkk_mati'       => $this->limitValue($row[8]),
                    'is_total'       => $isTotal,
                ], $mapping));

                $success++;
            } catch (\Exception $e) {
                Log::error("❌ Gagal [{$this->labelPeriode}] Baris " . ($index + 1) . ": " . $e->getMessage());
            }
        }
        Log::info("✅ [{$this->labelPeriode}] Ingesti Selesai. Total Sukses: $success");
    }

    private function isHeaderRow($c0, $c1, $c2)
    {
        $h = strtoupper($c0 . $c1 . $c2);
        return (str_contains($h, 'DISTRIK') || str_contains($h, 'KEBUN') || str_contains($h, 'AFDELING'));
    }

    private function strictRound($value, $decimal = 2)
    {
        $val = trim($value ?? '');
        if ($val === '' || str_contains($val, '#') || !is_numeric(str_replace(',', '.', $val))) return 0;
        return round((float)str_replace(',', '.', $val), $decimal);
    }

    private function limitValue($value)
    {
        $val = trim($value ?? '');
        if ($val === '' || str_contains($val, '#') || $val === '-') return 0;

        // Bersihkan karakter non-numerik kecuali titik/koma desimal
        $clean = str_replace(',', '.', $val);

        if (!is_numeric($clean)) return 0;

        // Konversi ke float dulu baru integer
        $num = (int)round((float)$clean);

        // Validasi range agar tidak merusak database (BigInt/Int)
        return ($num > 2000000 || $num < 0) ? 0 : $num;
    }

    private function sanitizeDesimal($value)
    {
        return $this->strictRound($value, 2);
    }

    private function sanitizeRibuan($value)
    {
        return $this->limitValue($value);
    }

    private function sanitizeTeks($value)
    {
        $val = trim($value ?? '');
        // JANGAN SAMPAI ADA ANGKA NYASAR (Akibat pergeseran kolom)
        if (is_numeric($val) || str_contains($val, '.')) return '-';
        return ($val === '' || $val === '0') ? '-' : $val;
    }

    private function sanitizeAfdeling($value)
    {
        $val = strtoupper(trim($value ?? ''));
        if (!$val || $val === '-' || $val === 'TOTAL') return 'TOTAL';
        return str_replace(' ', '', $val);
    }
}
