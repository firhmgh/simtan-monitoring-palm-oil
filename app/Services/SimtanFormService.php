<?php

namespace App\Services;

use App\Models\{SimtanForm, DetailRekap, LokasiKebun, KorelasiVegetatif};
use App\Imports\{TbmImport, LokasiKebunImport, KorelasiVegetatifImport};
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\{Storage, DB};

class SimtanFormService
{
    /**
     * Validasi sederhana apakah header file Excel mengandung kata kunci yang sesuai kategori.
     */
    public static function validateHeader($kategori, $file)
    {
        $rawArray = Excel::toArray(new \stdClass(), $file);
        // Ambil 5 baris pertama untuk pengecekan kata kunci
        $allText = strtolower(json_encode(array_slice($rawArray[0], 0, 5)));

        $keywords = match ($kategori) {
            'Rekap TBM'          => ['luas', 'ha', 'pokok', 'normal'],
            'Korelasi Vegetatif' => ['batang', 'pelepah', 'lingkar'],
            'Lokasi Kebun'       => ['latitude', 'longitude', 'lintang'],
            default              => []
        };

        foreach ($keywords as $kw) {
            if (str_contains($allText, $kw)) return true;
        }
        throw new \Exception("Mismatch: Struktur file tidak sesuai kategori '{$kategori}'.");
    }

    /**
     * Menangani proses upload, overwrite data lama, dan ingesti data ke database.
     */
    public static function handleUpload(array $validated, $file)
    {
        return DB::transaction(function () use ($validated, $file) {
            // Cari data existing berdasarkan kategori dan periode yang sama
            $existing = SimtanForm::where('kategori_file', $validated['kategori_file'])
                ->where('periode_data', $validated['periode_data'])->first();

            if ($existing) {
                /**
                 * PENTING: Memanggil delete() di sini akan memicu Model SimtanForm::boot() 
                 * yang secara otomatis menghapus file fisik di Storage dan 
                 * menghapus data di tabel terkait (Rekap/Lokasi/Vegetatif).
                 */
                $existing->delete();
            }

            // Simpan file baru ke Storage
            $path = $file->store('uploads/simtan', 'public');
            $validated['file_path'] = $path;

            // Buat record form baru
            $form = SimtanForm::create($validated);

            // Jalankan ingesti Excel berdasarkan kategori file
            match ($form->kategori_file) {
                'Rekap TBM'          => Excel::import(new TbmImport($form->id, $form->kode_upload, $form->periode_data), $file),
                'Lokasi Kebun'       => Excel::import(new LokasiKebunImport($form->id, $form->kode_upload), $file),
                'Korelasi Vegetatif' => Excel::import(new KorelasiVegetatifImport($form->id, $form->kode_upload, $form->periode_data), $file),
            };

            return $form;
        });
    }
}
