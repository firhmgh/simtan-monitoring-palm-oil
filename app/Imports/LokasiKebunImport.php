<?php

namespace App\Imports;

use App\Models\LokasiKebuns;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class LokasiKebunImport implements ToCollection
{
    protected $kodeUpload;

    public function __construct($kodeUpload)
    {
        $this->kodeUpload = $kodeUpload;
    }

    public function collection(Collection $rows)
    {
        $success = 0;
        $failed = 0;

        $currentDistrik = null;
        $currentKebun = null;
        $lastJenisLokasi = null;

        // Skip header (slice 1)
        foreach ($rows->slice(1) as $index => $row) {
            $rowArray = $row->toArray();

            // Skip jika baris benar-benar kosong
            if (collect($rowArray)->filter()->isEmpty()) {
                continue;
            }

            $distrik      = $rowArray[0] ?? null;
            $kebun        = $rowArray[1] ?? null;
            $jenisLokasi  = $rowArray[2] ?? null;
            $namaLokasi   = $rowArray[3] ?? null;
            $latitude     = $rowArray[4] ?? null;
            $longitude    = $rowArray[5] ?? null;

            // Logika Merged Cells untuk Distrik dan Kebun
            if (!empty(trim((string)$distrik))) {
                $currentDistrik = trim($distrik);
            }
            if (!empty(trim((string)$kebun))) {
                $currentKebun = trim($kebun);
            }

            // Logika Merged Cells untuk Jenis Lokasi
            if (!empty(trim((string)$jenisLokasi))) {
                $lastJenisLokasi = trim($jenisLokasi);
            } else {
                $jenisLokasi = $lastJenisLokasi;
            }

            if (!$currentDistrik || !$currentKebun || !$namaLokasi) {
                continue;
            }

            try {
                LokasiKebuns::create([
                    'kode_upload'  => $this->kodeUpload,
                    'distrik'      => $currentDistrik,
                    'kebun'        => $currentKebun,
                    'jenis_lokasi' => $jenisLokasi ?? '-',
                    'nama_lokasi'  => trim($namaLokasi),
                    'latitude'     => $this->toFloat($latitude),
                    'longitude'    => $this->toFloat($longitude),
                ]);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("Gagal simpan Lokasi Kebun baris " . ($index + 2) . ": " . $e->getMessage());
            }
        }
        Log::info("[IMPORT LOKASI KEBUN] Sukses: {$success}, Gagal: {$failed}");
    }

    private function toFloat($value)
    {
        if ($value === null || trim((string)$value) === '' || $value === '-') {
            return null;
        }
        $cleaned = preg_replace('/[^\d\.\-]/', '', str_replace(',', '.', (string) $value));
        return is_numeric($cleaned) ? (float) $cleaned : null;
    }
}