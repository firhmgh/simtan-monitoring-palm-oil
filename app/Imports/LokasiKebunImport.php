<?php

namespace App\Imports;

use App\Models\LokasiKebun;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class LokasiKebunImport implements ToCollection
{
    private $simtanFormId;
    private $kodeUpload;

    // State untuk menangani Merged Cells
    private $currentDistrik = null;
    private $currentKebun = null;
    private $lastJenisLokasi = null;

    public function __construct($simtanFormId, $kodeUpload)
    {
        $this->simtanFormId = $simtanFormId;
        $this->kodeUpload = $kodeUpload;
    }

    public function collection(Collection $rows)
    {
        // Mulai membaca dari baris ke-2 (Skip Header)
        foreach ($rows->slice(1) as $index => $row) {
            $cells = $row->toArray();

            // 1. Update State Merged Cells (Kolom A, B, C)
            if (!empty(trim((string)($cells[0] ?? '')))) {
                $this->currentDistrik = trim($cells[0]);
            }
            if (!empty(trim((string)($cells[1] ?? '')))) {
                $this->currentKebun = trim($cells[1]);
            }
            if (!empty(trim((string)($cells[2] ?? '')))) {
                $this->lastJenisLokasi = trim($cells[2]);
            }

            $jenisLokasi = $this->lastJenisLokasi;
            $namaLokasi  = trim((string)($cells[3] ?? ''));
            $rawUrlExcel = trim((string)($cells[6] ?? ''));

            // 2. Identifikasi apakah ini baris MAP METADATA
            $isMetadata = (str_contains(strtoupper($jenisLokasi), 'MAP METADATA') || str_contains(strtoupper($jenisLokasi), 'MAP_METADATA'));

            // 3. Ambil & Bersihkan Koordinat (Kolom E dan F)
            $lat = $this->toFloat($cells[4] ?? null);
            $lng = $this->toFloat($cells[5] ?? null);

            // --- LOGIKA FILTER PENYIMPANAN PERSIS SQL ANDA ---

            $hasCoordinates = ($lat !== null && $lng !== null);
            $hasUrlInExcel  = (!empty($rawUrlExcel) && $rawUrlExcel !== '-');

            // Syarat Simpan: 
            // - Jika baris punya koordinat (seperti Kantor Kebun)
            // - ATAU Jika baris MAP METADATA yang punya link URL (seperti Orthophoto)
            if (!$hasCoordinates && !($isMetadata && $hasUrlInExcel)) {
                continue;
            }

            try {
                // 4. Penentuan Nilai TILE_URL
                // HANYA isi tile_url jika baris ini adalah MAP METADATA
                $finalTileUrl = null;
                if ($isMetadata && $hasUrlInExcel) {
                    $finalTileUrl = $this->formatToRawGithub($rawUrlExcel);
                }

                // 5. Eksekusi Simpan
                LokasiKebun::create([
                    'simtan_form_id' => $this->simtanFormId,
                    'kode_upload'    => $this->kodeUpload,
                    'distrik'        => $this->currentDistrik ?? '-',
                    'kebun'          => $this->currentKebun ?? '-',
                    'jenis_lokasi'   => $isMetadata ? 'MAP_METADATA' : ($jenisLokasi ?? '-'),
                    'nama_lokasi'    => $namaLokasi,
                    'latitude'       => $lat ?? 0, // Metadata tanpa koordinat jadi 0.00000000
                    'longitude'      => $lng ?? 0, // Metadata tanpa koordinat jadi 0.00000000
                    'tile_url'       => $finalTileUrl, // Baris biasa akan NULL, baris metadata akan berisi link
                ]);
            } catch (\Exception $e) {
                Log::error("Gagal simpan baris " . ($index + 2) . ": " . $e->getMessage());
            }
        }
    }

    /**
     * Konversi URL ke format RAW GitHub lengkap
     */
    private function formatToRawGithub($url)
    {
        if (str_contains($url, 'github.com')) {
            $raw = str_replace(
                ['github.com', '/tree/'],
                ['raw.githubusercontent.com', '/'],
                $url
            );
            return rtrim($raw, '/') . '/{z}/{x}/{y}.jpg';
        }
        return $url;
    }

    /**
     * Membersihkan koordinat
     */
    private function toFloat($value)
    {
        if ($value === null || trim((string)$value) === '' || trim((string)$value) === '-') {
            return null;
        }
        $cleaned = preg_replace('/[^\d\.\-]/', '', str_replace(',', '.', (string) $value));
        return is_numeric($cleaned) ? (float) $cleaned : null;
    }
}
