<?php

namespace App\Services;

use App\Models\{SimtanForm, DetailRekap, LokasiKebun, KorelasiVegetatif};
use App\Imports\{TbmImport, LokasiKebunImport, KorelasiVegetatifImport};
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\{Storage, DB};

class SimtanFormService
{
    public static function validateHeader($kategori, $file)
    {
        $rawArray = Excel::toArray(new \stdClass(), $file);
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

    public static function handleUpload(array $validated, $file)
    {
        return DB::transaction(function () use ($validated, $file) {
            // Overwrite Logic berdasarkan kategori dan periode_data
            $existing = SimtanForm::where('kategori_file', $validated['kategori_file'])
                ->where('periode_data', $validated['periode_data'])->first();

            if ($existing) {
                self::deleteExistingData($existing);
                if ($existing->file_path) Storage::disk('public')->delete($existing->file_path);
                $form = $existing;
                $form->update(array_merge($validated, ['file_path' => $file->store('uploads/simtan', 'public')]));
            } else {
                $validated['file_path'] = $file->store('uploads/simtan', 'public');
                $form = SimtanForm::create($validated);
            }

            // Ingesti berdasarkan kategori
            match ($form->kategori_file) {
                'Rekap TBM' => Excel::import(new TbmImport($form->id, $form->kode_upload, $form->periode_data), $file),
                'Lokasi Kebun' => Excel::import(new LokasiKebunImport($form->id, $form->kode_upload), $file),
                'Korelasi Vegetatif' => Excel::import(new KorelasiVegetatifImport($form->id, $form->kode_upload, $form->periode_data), $file),
            };

            return $form;
        });
    }

    private static function deleteExistingData($form)
    {
        match ($form->kategori_file) {
            'Rekap TBM' => DetailRekap::where('simtan_form_id', $form->id)->delete(),
            'Lokasi Kebun' => LokasiKebun::where('simtan_form_id', $form->id)->delete(),
            'Korelasi Vegetatif' => KorelasiVegetatif::where('simtan_form_id', $form->id)->delete(),
            default => null
        };
    }
}
