<?php

namespace App\Services;

use App\Models\SimtanForm;
use App\Models\DetailRekap;
use App\Models\LokasiKebun;
use App\Models\KorelasiVegetatif;

use App\Imports\DetailRekapImport;
use App\Imports\LokasiKebunImport;
use App\Imports\KorelasiVegetatifImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SimtanFormService
{
    /**
     * TAHAP 1: VALIDASI HEADER (Logika Pencarian Kata Kunci Tanpa Terpaku Baris)
     */
    public static function validateHeader($kategori, $file)
    {
        // Baca 5 baris pertama secara mentah (tanpa header logic)
        $rawArray = Excel::toArray(new \stdClass(), $file);
        $firstFiveRows = array_slice($rawArray[0], 0, 5);

        // Satukan semua teks dari 5 baris tersebut menjadi satu teks panjang (Case Insensitive)
        $allText = strtolower(json_encode($firstFiveRows));

        if ($kategori === 'Rekap TBM') {
            // Cari kata kunci: luas, ha, pokok, atau normal
            $keywords = ['luas', 'ha', 'pokok', 'normal', 'pkk'];
            if (!self::containsAny($allText, $keywords)) {
                throw new \Exception("Mismatch: Anda memilih 'Rekap TBM' tapi file ini tidak mengandung kolom Luas atau Populasi.");
            }
        } elseif ($kategori === 'Korelasi Vegetatif') {
            // Cari kata kunci: batang, pelepah, crown, atau biometrik
            $keywords = ['batang', 'pelepah', 'crown', 'keliling', 'lingkar'];
            if (!self::containsAny($allText, $keywords)) {
                throw new \Exception("Mismatch: Anda memilih 'Korelasi Vegetatif' tapi file ini tidak mengandung data biometrik (Lingkar Batang/Pelepah).");
            }
        } elseif ($kategori === 'Lokasi Kebun') {
            // Cari kata kunci: latitude, longitude, atau koordinat
            $keywords = ['latitude', 'longitude', 'lintang', 'bujur', 'gps'];
            if (!self::containsAny($allText, $keywords)) {
                throw new \Exception("Mismatch: Anda memilih 'Lokasi Kebun' tapi file ini tidak mengandung koordinat GPS.");
            }
        }
    }

    /**
     * Helper untuk mengecek apakah salah satu kata kunci ada dalam teks
     */
    private static function containsAny($text, array $keywords)
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }
        return false;
    }

    /**
     * TAHAP 2: HANDLE UPLOAD (Tetap Sama)
     */
    public static function handleUpload(array $validated, $file)
    {
        return DB::transaction(function () use ($validated, $file) {
            $existingForm = SimtanForm::where('kategori_file', $validated['kategori_file'])
                ->where('periode_data', $validated['periode_data'])
                ->first();

            if ($existingForm) {
                self::deleteExistingData($existingForm);
                if (Storage::disk('public')->exists($existingForm->file_path)) {
                    Storage::disk('public')->delete($existingForm->file_path);
                }
                $form = $existingForm;
                $form->update([
                    'kode_upload'    => $validated['kode_upload'],
                    'uploaded_by'    => $validated['uploaded_by'],
                    'personel_pj'    => $validated['personel_pj'],
                    'judul_file'     => $validated['judul_file'],
                    'tanggal_upload' => now(),
                    'file_path'      => $file->store('uploads/simtan', 'public'),
                    'notes'          => $validated['notes'] ?? 'Data diperbarui (Overwrite Mode)',
                ]);
            } else {
                $path = $file->store('uploads/simtan', 'public');
                $form = SimtanForm::create([
                    'kode_upload'    => $validated['kode_upload'],
                    'uploaded_by'    => $validated['uploaded_by'],
                    'personel_pj'    => $validated['personel_pj'],
                    'judul_file'     => $validated['judul_file'],
                    'tanggal_upload' => now(),
                    'kategori_file'  => $validated['kategori_file'],
                    'periode_data'   => $validated['periode_data'],
                    'notes'          => $validated['notes'] ?? null,
                    'file_path'      => $path,
                ]);
            }

            if ($validated['kategori_file'] === 'Lokasi Kebun') {
                Excel::import(new LokasiKebunImport($form->id, $form->kode_upload), $file);
            } elseif ($validated['kategori_file'] === 'Rekap TBM') {
                Excel::import(new DetailRekapImport($form->id, $form->kode_upload), $file);
            } elseif ($validated['kategori_file'] === 'Korelasi Vegetatif') {
                Excel::import(new KorelasiVegetatifImport($form->id, $form->kode_upload), $file);
            }

            return $form;
        });
    }

    private static function deleteExistingData($form)
    {
        if ($form->kategori_file === 'Lokasi Kebun') {
            LokasiKebun::where('simtan_form_id', $form->id)->delete();
        } elseif ($form->kategori_file === 'Rekap TBM') {
            DetailRekap::where('simtan_form_id', $form->id)->delete();
        } elseif ($form->kategori_file === 'Korelasi Vegetatif') {
            KorelasiVegetatif::where('simtan_form_id', $form->id)->delete();
        }
    }
}
