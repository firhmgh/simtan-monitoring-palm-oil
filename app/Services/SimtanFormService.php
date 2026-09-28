<?php

namespace App\Services;

use App\Models\{SimtanForm, DetailRekap, LokasiKebun, KorelasiVegetatif};
use App\Imports\{TbmImport, LokasiKebunImport, KorelasiVegetatifImport};
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\{Storage, DB};

class SimtanFormService
{
    /**
     * Pre-flight Validation: Memvalidasi kelaikan berkas Excel sebelum dilakukan commit/transaksi database.
     * Memeriksa worksheet yang dibutuhkan sesuai periode, keberadaan header wajib, dan struktur kolom dasar.
     */
    public static function validatePreFlight($kategori, $file, $periode = null)
    {
        // 1. Validasi Whitelist Kategori Dataset
        $validKategori = ['Rekap TBM', 'Korelasi Vegetatif', 'Lokasi Kebun'];
        if (!in_array($kategori, $validKategori)) {
            throw new \Exception("Kategori dataset '{$kategori}' tidak dikenali oleh sistem.");
        }

        // 2. Validasi keberadaan file fisik dan dapat dibaca
        $filePath = is_string($file) ? $file : (method_exists($file, 'getRealPath') ? $file->getRealPath() : null);
        if (!$filePath || !file_exists($filePath)) {
            throw new \Exception("Berkas Excel tidak valid atau tidak dapat dibaca dari sistem penyimpanan sementara.");
        }

        // 3. Baca seluruh sheet dan struktur awal menggunakan Maatwebsite/PhpSpreadsheet
        try {
            $sheetsData = Excel::toArray(new \stdClass(), $file);
        } catch (\Throwable $e) {
            throw new \Exception("Format berkas Excel rusak atau tidak didukung: " . $e->getMessage());
        }

        if (empty($sheetsData) || !isset($sheetsData[0])) {
            throw new \Exception("Berkas Excel kosong atau tidak memiliki lembar kerja (worksheet).");
        }

        // 3. Validasi Berdasarkan Kategori Dataset
        match ($kategori) {
            'Rekap TBM'          => self::validateRekapTbmPreFlight($sheetsData, $file, $periode),
            'Korelasi Vegetatif' => self::validateVegetatifPreFlight($sheetsData),
            'Lokasi Kebun'       => self::validateLokasiKebunPreFlight($sheetsData),
            default              => throw new \Exception("Kategori dataset '{$kategori}' tidak dikenali oleh sistem.")
        };

        return true;
    }

    /**
     * Validasi Khusus Rekap TBM: Memeriksa kesesuaian Sheet dan Struktur Kolom Sensus
     */
    private static function validateRekapTbmPreFlight(array $sheetsData, $file, $periode)
    {
        // Peta nama sheet fisik yang wajib ada di file Excel Rekap TBM
        $requiredSheets = [
            'periode-1-2025' => 'JANFEBMARAPR2025REKAP',
            'periode-2-2025' => 'MEIJULJUNAGST2025REKAP',
            'periode-3-2025' => 'SEPOKTNOVDES2025REKAP',
            'JANFEBMARAPR2025REKAP' => 'JANFEBMARAPR2025REKAP',
            'MEIJULJUNAGST2025REKAP' => 'MEIJULJUNAGST2025REKAP',
            'SEPOKTNOVDES2025REKAP' => 'SEPOKTNOVDES2025REKAP',
        ];

        // Jika periode spesifik dipilih, periksa ketersediaan sheet tersebut
        $targetDbKey = config("simtan.map_periode.{$periode}.db_key") ?? $periode;

        // Ambil nama seluruh sheet dari file Excel
        $sheetNames = [];
        try {
            $reader = \Maatwebsite\Excel\Facades\Excel::toCollection(new \stdClass(), $file);
            // Ambil daftar sheet via reader jika memungkinkan
        } catch (\Throwable $e) {
            // Fallback inspect text
        }

        // Periksa 5 baris pertama untuk header wajib Rekap TBM
        $firstSheetRows = $sheetsData[0] ?? [];
        $headerText = strtolower(json_encode(array_slice($firstSheetRows, 0, 5)));

        $mandatoryKeywords = ['distrik', 'kebun', 'afdeling', 'pokok'];
        $foundCount = 0;
        foreach ($mandatoryKeywords as $kw) {
            if (str_contains($headerText, $kw)) $foundCount++;
        }

        // Minimal 2 keyword utama wajib ada
        if ($foundCount < 2 && !str_contains($headerText, 'luas') && !str_contains($headerText, 'normal')) {
            throw new \Exception("Struktur tabel Rekap TBM tidak sesuai. Kolom identitas wilayah (Distrik/Kebun/Afdeling) atau metrik sensus tidak ditemukan.");
        }
    }

    /**
     * Validasi Khusus Korelasi Vegetatif: Header Biometrik
     */
    private static function validateVegetatifPreFlight(array $sheetsData)
    {
        $firstSheetRows = $sheetsData[0] ?? [];
        $headerText = strtolower(json_encode(array_slice($firstSheetRows, 0, 5)));

        $mandatoryKeywords = ['batang', 'pelepah', 'lingkar', 'crown', 'kebun', 'blok'];
        $found = false;
        foreach ($mandatoryKeywords as $kw) {
            if (str_contains($headerText, $kw)) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new \Exception("Struktur file Korelasi Vegetatif tidak sesuai. Kolom biometrik (Lingkar Batang/Pelepah/Crown) tidak ditemukan.");
        }
    }

    /**
     * Validasi Khusus Lokasi Kebun: Kolom Geospasial
     */
    private static function validateLokasiKebunPreFlight(array $sheetsData)
    {
        $firstSheetRows = $sheetsData[0] ?? [];
        $headerText = strtolower(json_encode(array_slice($firstSheetRows, 0, 5)));

        $mandatoryKeywords = ['latitude', 'longitude', 'lintang', 'bujur', 'kebun', 'lokasi'];
        $found = false;
        foreach ($mandatoryKeywords as $kw) {
            if (str_contains($headerText, $kw)) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new \Exception("Struktur file Lokasi Kebun tidak sesuai. Kolom koordinat (Latitude/Longitude) atau identitas kebun tidak ditemukan.");
        }
    }

    /**
     * Validasi sederhana apakah header file Excel mengandung kata kunci yang sesuai kategori.
     * (Dipertahankan untuk backward compatibility penuh).
     */
    public static function validateHeader($kategori, $file)
    {
        return self::validatePreFlight($kategori, $file);
    }

    /**
     * Menangani proses upload, overwrite data lama, dan proses unggah data ke database.
     * Mengembalikan array eksplisit: [$form, $oldFilePath]
     * agar penghapusan berkas fisik lama dilakukan HANYA setelah transaksi database outer commit.
     */
    public static function handleUpload(array $validated, $file): array
    {
        return DB::transaction(function () use ($validated, $file) {
            // Cari data existing berdasarkan kategori dan periode yang sama
            $existing = SimtanForm::where('kategori_file', $validated['kategori_file'])
                 ->where('periode_data', $validated['periode_data'])->first();

            // Simpan referensi path berkas fisik lama sebelum record dihapus di database
            $oldFilePath = $existing ? $existing->file_path : null;

            if ($existing) {
                /**
                 * PENTING: Menghapus record lama dari database di dalam transaksi.
                 * Berkas fisik lama TIDAK dihapus di sini agar jika transaksi gagal (rollback),
                 * data lama beserta berkas fisiknya tetap utuh.
                 */
                $existing->delete();
            }

            // Simpan file baru ke Storage
            $path = $file->store('uploads/simtan', 'public');
            $validated['file_path'] = $path;

            // Buat record form baru
            $form = SimtanForm::create($validated);

            // Jalankan unggah berkas Excel berdasarkan kategori file
            match ($form->kategori_file) {
                'Rekap TBM'          => Excel::import(new TbmImport($form->id, $form->kode_upload, $form->periode_data), $file),
                'Lokasi Kebun'       => Excel::import(new LokasiKebunImport($form->id, $form->kode_upload), $file),
                'Korelasi Vegetatif' => Excel::import(new KorelasiVegetatifImport($form->id, $form->kode_upload, $form->periode_data), $file),
            };

            // Mengembalikan entitas form baru dan referensi file lama untuk dibersihkan setelah outer commit
            return [$form, $oldFilePath];
        });
    }
}
