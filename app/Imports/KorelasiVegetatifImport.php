<?php

namespace App\Imports;

use App\Models\KorelasiVegetatif;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas; // Diperlukan untuk mengambil hasil akhir rumus
use Illuminate\Support\Facades\Log;

/**
 * KorelasiVegetatifImport
 * Logika parsing Excel khusus untuk data Biometrik Vegetatif (Input AI).
 * Mendukung pengambilan hasil kalkulasi rumus, handling merged cells, 
 * dan proteksi terhadap baris ringkasan (summary rows).
 */
class KorelasiVegetatifImport implements ToCollection, WithStartRow, WithMultipleSheets, WithCalculatedFormulas
{
    protected $simtanFormId, $kodeUpload, $labelPeriode;

    /**
     * Constructor menerima metadata dari Service
     */
    public function __construct($simtanFormId, $kodeUpload, $labelPeriode)
    {
        $this->simtanFormId = $simtanFormId;
        $this->kodeUpload = $kodeUpload;
        $this->labelPeriode = $labelPeriode;
    }

    /**
     * Pilih hanya sheet pertama (index 0)
     */
    public function sheets(): array
    {
        return [
            0 => $this
        ];
    }

    /**
     * Baris awal pembacaan data (Melewati header judul)
     */
    public function startRow(): int
    {
        return 3;
    }

    public function collection(Collection $rows)
    {
        $success = 0;
        $failed = 0;

        // Variabel penampung nilai terakhir (State Maintenance untuk Merged Cells)
        $lastTahun     = null;
        $lastKebun     = null;
        $lastTopografi = null;

        foreach ($rows as $index => $row) {
            // 1. Tangkap data mentah dari kolom A sampai D
            $rawTahun     = $this->keepString($row[0] ?? null);
            $rawKebun     = $this->keepString($row[1] ?? null);
            $rawTopografi = $this->keepString($row[2] ?? null);
            $blok         = $this->keepString($row[3] ?? null);

            // 🧬 LOGIKA MERGED CELLS: Update 'last values' jika kolom tidak kosong
            if (!empty($rawTahun))     $lastTahun     = $rawTahun;
            if (!empty($rawKebun))     $lastKebun     = $rawKebun;
            if (!empty($rawTopografi)) $lastTopografi = $rawTopografi;

            // Variabel yang akan disimpan (menggunakan carry-over data jika baris kosong)
            $tahun     = $lastTahun;
            $kebun     = $lastKebun;
            $topografi = $lastTopografi;

            // 🛡️ FILTER 1: Lewati jika baris adalah baris judul kolom yang berulang
            if (str_contains(strtoupper((string)$tahun), 'TAHUN')) continue;

            // 🛡️ FILTER 2: Skip baris yang benar-benar kosong (tidak ada blok dan tidak ada angka)
            if (empty($row[4]) && empty($blok)) continue;

            // 🎯 LOGIKA IDENTIFIKASI BARIS RATA-RATA: 
            // Hanya anggap baris summary jika teksnya persis "RATA-RATA".
            // Teks "RATA S.D BERGELOMBANG" tetap dianggap data normal.
            $isSummaryRow = (trim(strtoupper((string)$topografi)) === 'RATA-RATA' || trim(strtoupper((string)$blok)) === 'RATA-RATA');

            // 2. Ambil data hasil kalkulasi rumus (Kolom E sampai H / Index 4-7)
            $rawCrown   = $this->sanitizeDesimal($row[4] ?? null);
            $rawBatang  = $this->sanitizeDesimal($row[5] ?? null);
            $rawPelepah = $this->sanitizeDesimal($row[6] ?? null);
            $rawPanjang = $this->sanitizeDesimal($row[7] ?? null);

            try {
                // 3. Eksekusi simpan ke Database
                KorelasiVegetatif::create([
                    'simtan_form_id'  => $this->simtanFormId,
                    'kode_upload'     => $this->kodeUpload,
                    'periode'         => $this->labelPeriode,
                    'tahun'           => $tahun,
                    'kebun'           => $kebun,
                    'topografi'       => $topografi,
                    'blok'            => $isSummaryRow ? null : $blok, // Blok di-NULL-kan jika baris rata-rata
                    'keliling_crown'  => $rawCrown !== null ? round($rawCrown, 3) : null,
                    'lingkar_batang'  => $rawBatang !== null ? round($rawBatang, 3) : null,
                    'jumlah_pelepah'  => $rawPelepah !== null ? round($rawPelepah, 3) : null,
                    'panjang_pelepah' => $rawPanjang !== null ? round($rawPanjang, 3) : null,
                ]);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("❌ Gagal simpan Korelasi Vegetatif baris " . ($index + 3) . ": " . $e->getMessage());
            }
        }
        Log::info("[IMPORT VEGETATIF] Selesai. ✅ Sukses: {$success}, ❌ Gagal: {$failed}");
    }

    /**
     * Membersihkan input desimal, menangani format koma, dan error rumus Excel (#DIV/0!, dsb)
     */
    private function sanitizeDesimal($value)
    {
        if ($value === null || $value === '' || $value === '-') return null;

        // Deteksi dan tangani error internal Excel (Contoh: #DIV/0!)
        if (is_string($value) && str_contains($value, '#')) return null;

        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? (float) $value : null;
    }

    /**
     * Membersihkan input teks (trim spasi)
     */
    private function keepString($value)
    {
        if ($value === null || $value === '' || $value === '-') return null;
        return trim((string) $value);
    }
}
