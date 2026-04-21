<?php

namespace App\Imports;

use App\Models\KorelasiVegetatif;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Log;

class KorelasiVegetatifImport implements ToCollection, WithStartRow, WithMultipleSheets
{
    protected $kodeUpload;

    public function __construct($kodeUpload)
    {
        $this->kodeUpload = $kodeUpload;
    }

    public function sheets(): array
    {
        return [0 => $this]; // Hanya baca sheet pertama
    }

    public function startRow(): int
    {
        return 3; // Mulai baca dari baris ke-3
    }

    public function collection(Collection $rows)
    {
        $success = 0;
        $failed = 0;

        $lastTahun     = null;
        $lastKebun     = null;
        $lastTopografi = null;
        $lastBlok      = null;

        foreach ($rows as $index => $row) {
            $tahun     = $this->keepString($row[0] ?? null) ?: $lastTahun;
            $kebun     = $this->keepString($row[1] ?? null) ?: $lastKebun;
            $topografi = $this->keepString($row[2] ?? null) ?: $lastTopografi;
            $blok      = $this->keepString($row[3] ?? null) ?: $lastBlok;

            if (strtoupper((string)$topografi) === 'RATA-RATA') {
                $blok = null;
            }

            $keliling_crown  = $this->sanitizeDesimal($row[4] ?? null);
            $lingkar_batang  = $this->sanitizeDesimal($row[5] ?? null);
            $jumlah_pelepah  = $this->sanitizeDesimal($row[6] ?? null);
            $panjang_pelepah = $this->sanitizeDesimal($row[7] ?? null);

            if (!empty($tahun))     $lastTahun     = $tahun;
            if (!empty($kebun))     $lastKebun     = $kebun;
            if (!empty($topografi)) $lastTopografi = $topografi;
            if (!empty($blok))      $lastBlok      = $blok;

            // Baris header duplikat skip
            if (strtoupper(trim((string)$tahun)) === 'TAHUN') continue;

            if (empty($tahun) && empty($kebun) && $keliling_crown === null) continue;

            try {
                KorelasiVegetatif::create([
                    'kode_upload'     => $this->kodeUpload,
                    'tahun'           => $tahun,
                    'kebun'           => $kebun,
                    'topografi'       => $topografi,
                    'blok'            => $blok,
                    'keliling_crown'  => $keliling_crown,
                    'lingkar_batang'  => $lingkar_batang,
                    'jumlah_pelepah'  => $jumlah_pelepah,
                    'panjang_pelepah' => $panjang_pelepah,
                ]);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("Gagal simpan Korelasi Vegetatif: " . $e->getMessage());
            }
        }
        Log::info("[IMPORT VEGETATIF] Sukses: {$success}, Gagal: {$failed}");
    }

    private function sanitizeDesimal($value)
    {
        if ($value === null || $value === '') return null;
        if (is_string($value)) $value = str_replace(',', '.', $value);
        return is_numeric($value) ? (float) $value : null;
    }

    private function keepString($value)
    {
        return $value === null ? null : trim((string) $value);
    }
}