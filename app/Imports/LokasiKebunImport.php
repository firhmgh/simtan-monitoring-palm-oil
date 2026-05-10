<?php

namespace App\Imports;

use App\Models\LokasiKebun;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

/**
 * LokasiKebunImport
 * Logika parsing Excel khusus untuk data koordinat (GIS).
 * Mendukung pengisian simtan_form_id (Integer) dan handling merged cells.
 */
class LokasiKebunImport implements ToCollection
{
    private $simtanFormId;
    private $kodeUpload;

    /**
     * Constructor menerima ID (Integer) dan Kode (String) dari Service.
     */
    public function __construct($simtanFormId, $kodeUpload)
    {
        $this->simtanFormId = $simtanFormId;
        $this->kodeUpload = $kodeUpload;
    }

    public function collection(Collection $rows)
    {
        $success = 0;
        $failed = 0;

        // State untuk menyimpan nilai terakhir (Handle Merged Cells)
        $currentDistrik = null;
        $currentKebun = null;
        $lastJenisLokasi = null;

        // Skip header (slice 1) untuk mulai membaca dari data baris ke-2
        foreach ($rows->slice(1) as $index => $row) {
            $rowArray = $row->toArray();

            // Skip jika baris benar-benar kosong total
            if (collect($rowArray)->filter()->isEmpty()) {
                continue;
            }

            // Pemetaan index kolom Excel (0=A, 1=B, 2=C, dst)
            $distrik      = $rowArray[0] ?? null;
            $kebun        = $rowArray[1] ?? null;
            $jenisLokasi  = $rowArray[2] ?? null;
            $namaLokasi   = $rowArray[3] ?? null;
            $latitude     = $rowArray[4] ?? null;
            $longitude    = $rowArray[5] ?? null;

            // --- LOGIKA MERGED CELLS ---
            if (!empty(trim((string)$distrik))) {
                $currentDistrik = trim($distrik);
            }
            if (!empty(trim((string)$kebun))) {
                $currentKebun = trim($kebun);
            }
            if (!empty(trim((string)$jenisLokasi))) {
                $lastJenisLokasi = trim($jenisLokasi);
            } else {
                $jenisLokasi = $lastJenisLokasi;
            }

            // Validasi baris: jika data krusial kosong, lewati
            if (!$currentDistrik || !$currentKebun || !$namaLokasi) {
                Log::warning("⚠️ Baris " . ($index + 2) . " dilewati: Data identitas lokasi tidak lengkap.");
                continue;
            }

            try {
                // Integrasi simpan ke Database
                LokasiKebun::create([
                    'simtan_form_id' => $this->simtanFormId, // Integer 
                    'kode_upload'    => $this->kodeUpload,    // String (Audit Trail)
                    'distrik'        => $currentDistrik,
                    'kebun'          => $currentKebun,
                    'jenis_lokasi'   => $jenisLokasi ?? '-',
                    'nama_lokasi'    => trim($namaLokasi),
                    'latitude'       => $this->toFloat($latitude),
                    'longitude'      => $this->toFloat($longitude),
                ]);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("❌ Gagal simpan Lokasi Kebun baris " . ($index + 2) . ": " . $e->getMessage());
            }
        }

        Log::info("[IMPORT LOKASI KEBUN SELESAI] ✅ Sukses: {$success} | ❌ Gagal: {$failed}");
    }

    /**
     * Membersihkan koordinat latitude/longitude agar menjadi float yang valid
     */
    private function toFloat($value)
    {
        if ($value === null || trim((string)$value) === '' || $value === '-') {
            return null;
        }
        // Ganti koma dengan titik dan hapus karakter non-numeric kecuali titik dan minus
        $cleaned = preg_replace('/[^\d\.\-]/', '', str_replace(',', '.', (string) $value));
        return is_numeric($cleaned) ? (float) $cleaned : null;
    }
}
