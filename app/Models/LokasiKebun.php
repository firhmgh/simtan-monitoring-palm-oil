<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model LokasiKebun - Menyimpan koordinat geospasial untuk visualisasi peta.
 * Dioptimalkan menggunakan integer relationship (simtan_form_id).
 */
class LokasiKebun extends Model
{
    use HasFactory;

    protected $table = 'lokasi_kebun';

    /**
     * Mass Assignment
     * 'simtan_form_id' menjadi penghubung utama ke tabel induk SimtanForm.
     */
    protected $fillable = [
        'simtan_form_id',
        'kode_upload',    // Kode unik (Human-readable)
        'distrik',
        'kebun',
        'jenis_lokasi',
        'nama_lokasi',
        'latitude',
        'longitude',
        'tile_url'
    ];

    /**
     * Casting tipe data.
     * Menggunakan float untuk latitude/longitude agar Leaflet.js mudah membacanya.
     */
    protected $casts = [
        'simtan_form_id' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * RELASI: Menghubungkan ke tabel SimtanForm (Induk)
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(SimtanForm::class, 'simtan_form_id');
    }
}
