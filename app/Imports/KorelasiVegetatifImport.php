<?php

namespace App\Imports;

use App\Models\KorelasiVegetatif;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Log;

/**
 * KorelasiVegetatifImport
 * Logika parsing Excel khusus untuk data Biometrik Vegetatif (Input AI).
 * Mengintegrasikan logika carry-over (merged cells) dan pembulatan angka presisi.
 */
class KorelasiVegetatifImport implements ToCollection, WithStartRow, WithMultipleSheets
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

    /**
     * Pilih hanya sheet pertama (index 0) agar proses efisien.
     */
    public function sheets(): array
    {
        return [
            0 => $this
        ];
    }

    /**
     * Baris awal pembacaan Excel.
     */
    public function startRow(): int
    {
        return 3;
    }

    public function collection(Collection $rows)
    {
        $success = 0;
        $failed = 0;

        // Variabel penampung nilai terakhir untuk baris yang kosong (Handle Merged Cells)
        $lastTahun     = null;
        $lastKebun     = null;
        $lastTopografi = null;
        $lastBlok      = null;

        foreach ($rows as $index => $row) {
            // 1. Ambil nilai berdasarkan index kolom (0=A, 1=B, dst)
            // Jika kolom kosong (akibat merged cells), ambil data dari baris sebelumnya (Carry-over logic)
            $tahun     = $this->keepString($row[0] ?? null) ?: $lastTahun;
            $kebun     = $this->keepString($row[1] ?? null) ?: $lastKebun;
            $topografi = $this->keepString($row[2] ?? null) ?: $lastTopografi;
            $blok      = $this->keepString($row[3] ?? null) ?: $lastBlok;

            // Logika Khusus: Jika baris adalah baris 'RATA-RATA', kolom blok dipaksa NULL
            if ($topografi && strtoupper((string)$topografi) === 'RATA-RATA') {
                $blok = null;
            }

            // 2. Ambil data mentah angka (kolom index 4 sampai 7)
            $rawCrown   = $this->sanitizeDesimal($row[4] ?? null);
            $rawBatang  = $this->sanitizeDesimal($row[5] ?? null);
            $rawPelepah = $this->sanitizeDesimal($row[6] ?? null);
            $rawPanjang = $this->sanitizeDesimal($row[7] ?? null);

            // 3. Update 'last values' untuk digunakan baris selanjutnya (State maintenance)
            if (!empty($tahun))     $lastTahun     = $tahun;
            if (!empty($kebun))     $lastKebun     = $kebun;
            if (!empty($topografi)) $lastTopografi = $topografi;
            if (!empty($blok))      $lastBlok      = $blok;

            // Cek baris kosong atau baris header duplikat untuk di-skip
            if (empty($tahun) && empty($kebun) && $rawCrown === null && $rawBatang === null) continue;
            if (strtoupper(trim((string)$tahun)) === 'TAHUN') continue;

            try {
                // 4. Simpan ke Database menggunakan Hybrid Key dan Rounding (Pembulatan)
                KorelasiVegetatif::create([
                    'simtan_form_id'  => $this->simtanFormId, // Integer
                    'kode_upload'     => $this->kodeUpload,    // String (Audit Trail)
                    'tahun'           => $tahun,
                    'kebun'           => $kebun,
                    'topografi'       => $topografi,
                    'blok'            => $blok,
                    'keliling_crown'  => $rawCrown !== null ? round($rawCrown, 0) : null,
                    'lingkar_batang'  => $rawBatang !== null ? round($rawBatang, 3) : null,
                    'jumlah_pelepah'  => $rawPelepah !== null ? round($rawPelepah, 3) : null,
                    'panjang_pelepah' => $rawPanjang !== null ? round($rawPanjang, 3) : null,
                ]);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                Log::error("❌ Gagal simpan Korelasi Vegetatif baris {$index}: " . $e->getMessage());
            }
        }
        Log::info("[IMPORT VEGETATIF] Selesai. ✅ Sukses: {$success}, ❌ Gagal: {$failed}");
    }

    /**
     * Membersihkan input desimal (mengubah koma menjadi titik)
     */
    private function sanitizeDesimal($value)
    {
        if ($value === null || $value === '') return null;
        if (is_string($value)) $value = str_replace(',', '.', $value);
        return is_numeric($value) ? (float) $value : null;
    }

    /**
     * Membersihkan input teks (trim spasi)
     */
    private function keepString($value)
    {
        if ($value === null) return null;
        return trim((string) $value);
    }
}
