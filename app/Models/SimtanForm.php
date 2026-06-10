<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Model SimtanForm - Entitas Pusat Metadata Unggahan.
 * Menangani metadata file Excel dan otomatis membersihkan data terkait serta file fisik saat dihapus.
 */
class SimtanForm extends Model
{
    use HasFactory;

    protected $table = 'simtan_form';

    protected $fillable = [
        'kode_upload',
        'uploaded_by',
        'personel_pj',
        'judul_file',
        'tanggal_upload',
        'kategori_file',
        'periode_data',
        'notes',
        'file_path'
    ];

    /**
     * Relasi ke User (Aktor Pengunggah).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Relasi ke DetailRekap (Data Sensus/Rekap TBM).
     */
    public function detailRekap(): HasMany
    {
        return $this->hasMany(DetailRekap::class, 'simtan_form_id', 'id');
    }

    /**
     * Relasi ke KorelasiVegetatif (Data Biometrik).
     */
    public function korelasiVegetatif(): HasMany
    {
        return $this->hasMany(KorelasiVegetatif::class, 'simtan_form_id', 'id');
    }

    /**
     * Relasi ke LokasiKebun (Data Koordinat/GIS).
     */
    public function lokasiKebun(): HasMany
    {
        return $this->hasMany(LokasiKebun::class, 'simtan_form_id', 'id');
    }

    /**
     * Relasi ke Audit Trail (UploadLog).
     */
    public function uploadLog(): HasMany
    {
        return $this->hasMany(UploadLog::class, 'simtan_form_id', 'id');
    }

    /**
     * Model Boot Hook
     * Menangani logika otomatis saat terjadi event pada model.
     */
    protected static function boot()
    {
        parent::boot();

        // Event ini berjalan otomatis saat method $model->delete() dipanggil
        static::deleting(function ($form) {
            // 1. Hapus data di tabel-tabel detail terkait berdasarkan kategori file
            match ($form->kategori_file) {
                'Rekap TBM'          => $form->detailRekap()->delete(),
                'Lokasi Kebun'       => $form->lokasiKebun()->delete(),
                'Korelasi Vegetatif' => $form->korelasiVegetatif()->delete(),
                default              => null
            };

            // 2. Otomatis hapus Log Upload terkait
            $form->uploadLog()->delete();

            // 3. Hapus file fisik di storage
            if ($form->file_path) {
                // Bersihkan path: hapus prefix 'public/' jika tersimpan di database
                // agar sesuai dengan akses Storage::disk('public')
                $cleanPath = str_replace('public/', '', $form->file_path);

                try {
                    if (Storage::disk('public')->exists($cleanPath)) {
                        Storage::disk('public')->delete($cleanPath);
                        Log::info("Pembersihan Storage: File berhasil dihapus -> " . $cleanPath);
                    } else {
                        Log::warning("Pembersihan Storage: File tidak ditemukan saat penghapusan record -> " . $cleanPath);
                    }
                } catch (\Exception $e) {
                    Log::error("Pembersihan Storage: Gagal menghapus file " . $cleanPath . ". Error: " . $e->getMessage());
                }
            }
        });
    }
}
