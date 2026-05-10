<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model SimtanForm - Entitas Pusat Metadata Unggahan.
 * Merupakan wadah agregasi bagi data operasional lainnya.
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
     * Relasi Agregasi ke DetailRekap.
     * Menggunakan simtan_form_id sebagai foreign key (Integer)
     */
    public function DetailRekap(): HasMany
    {
        return $this->hasMany(DetailRekap::class, 'simtan_form_id', 'id');
    }

    /**
     * Relasi ke KorelasiVegetatif (Data Input AI).
     */
    public function korelasiVegetatif(): HasMany
    {
        return $this->hasMany(KorelasiVegetatif::class, 'simtan_form_id', 'id');
    }

    /**
     * Relasi ke LokasiKebun (Data Spasial/GIS).
     */
    public function LokasiKebun(): HasMany
    {
        return $this->hasMany(LokasiKebun::class, 'simtan_form_id', 'id');
    }

    /**
     * Relasi ke Audit Trail (UploadLog).
     */
    public function UploadLog(): HasMany
    {
        return $this->hasMany(UploadLog::class, 'simtan_form_id', 'id');
    }
}
