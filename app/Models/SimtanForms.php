<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model SimtanForms - Entitas Pusat Metadata Unggahan.
 * Merupakan wadah agregasi bagi data operasional lainnya.
 */
class SimtanForms extends Model
{
    use HasFactory;

    protected $table = 'simtan_forms';

    protected $fillable = [
        'kode_upload',
        'uploaded_by',
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
     * Relasi Agregasi ke DetailRekaps.
     */
    public function detailRekaps(): HasMany
    {
        return $this->hasMany(DetailRekaps::class, 'kode_upload', 'kode_upload');
    }

    /**
     * Relasi ke KorelasiVegetatif (Data Input AI).
     */
    public function korelasiVegetatif(): HasMany
    {
        return $this->hasMany(KorelasiVegetatif::class, 'kode_upload', 'kode_upload');
    }

    /**
     * Relasi ke LokasiKebuns (Data Spasial/GIS).
     */
    public function lokasiKebuns(): HasMany
    {
        return $this->hasMany(LokasiKebuns::class, 'kode_upload', 'kode_upload');
    }

    /**
     * Relasi ke Audit Trail (UploadLogs).
     */
    public function uploadLogs(): HasMany
    {
        return $this->hasMany(UploadLogs::class, 'simtan_form_id');
    }
}