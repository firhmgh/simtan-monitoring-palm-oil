<?php

namespace App\Services;

// Import Model Utama
use App\Models\SimtanForms; // Pastikan nama model sesuai (biasanya SimtanForm tanpa 's')
use App\Models\DetailRekaps;
use App\Models\LokasiKebuns;
use App\Models\KorelasiVegetatif;

// Import Excel Logic
use App\Imports\DetailRekapImport;
use App\Imports\LokasiKebunImport;
use App\Imports\KorelasiVegetatifImport; 
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SimtanFormService
{
    public static function handleUpload(array $validated, $file)
    {
        return DB::transaction(function () use ($validated, $file) {
            
            // 1. CEK DATA GANDA (Duplicate Check - Standar BUMN/Scopus)
            // Mencari apakah sudah ada file dengan Kategori & Periode yang sama
            $existingForm = SimtanForms::where('kategori_file', $validated['kategori_file'])
                ->where('periode_data', $validated['periode_data'])
                ->first();

            if ($existingForm) {
                // Hapus data detail lama agar tidak menumpuk (Clean Overwrite)
                self::deleteExistingData($existingForm);
                
                // Hapus file fisik lama di storage
                if (Storage::disk('public')->exists($existingForm->file_path)) {
                    Storage::disk('public')->delete($existingForm->file_path);
                }

                // Update metadata file yang sudah ada
                $form = $existingForm;
                $form->update([
                    'kode_upload'    => $validated['kode_upload'],
                    'uploaded_by'    => $validated['uploaded_by'],
                    'judul_file'     => $validated['judul_file'],
                    'tanggal_upload' => now(),
                    'file_path'      => $file->store('uploads/simtan', 'public'),
                    'notes'          => $validated['notes'] ?? 'Data diperbarui (Overwrite)',
                ]);
            } else {
                // Jika belum ada, buat record baru
                $path = $file->store('uploads/simtan', 'public');
                $form = SimtanForms::create([
                    'kode_upload'    => $validated['kode_upload'],
                    'uploaded_by'    => $validated['uploaded_by'],
                    'judul_file'     => $validated['judul_file'],
                    'tanggal_upload' => now(),
                    'kategori_file'  => $validated['kategori_file'],
                    'periode_data'   => $validated['periode_data'],
                    'notes'          => $validated['notes'] ?? null,
                    'file_path'      => $path,
                ]);
            }

            // 2. PROSES IMPORT EXCEL SESUAI KATEGORI
            if ($validated['kategori_file'] === 'Lokasi Kebun') {
                Excel::import(new LokasiKebunImport($form->kode_upload), $file);
            } elseif ($validated['kategori_file'] === 'Rekap TBM') {
                Excel::import(new DetailRekapImport($form->kode_upload), $file);
            } elseif ($validated['kategori_file'] === 'Korelasi Vegetatif') { 
                Excel::import(new KorelasiVegetatifImport($form->kode_upload), $file);
            }

            return $form;
        });
    }

    /**
     * Helper: Menghapus data detail berdasarkan kode_upload lama
     * Mencegah redundansi data di tabel detail.
     */
    private static function deleteExistingData($form)
    {
        $oldCode = $form->kode_upload;

        if ($form->kategori_file === 'Lokasi Kebun') {
            LokasiKebuns::where('kode_upload', $oldCode)->delete();
        } elseif ($form->kategori_file === 'Rekap TBM') {
            DetailRekaps::where('kode_upload', $oldCode)->delete();
        } elseif ($form->kategori_file === 'Korelasi Vegetatif') {
            KorelasiVegetatif::where('kode_upload', $oldCode)->delete();
        }
    }
}