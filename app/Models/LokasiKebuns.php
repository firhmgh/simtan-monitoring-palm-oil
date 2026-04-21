<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model LokasiKebuns - Menyimpan koordinat geospasial untuk visualisasi peta.
 * Mendukung optimasi XYZ Tiles melalui atribut tile_url.
 */
class LokasiKebuns extends Model
{
    protected $table = 'lokasi_kebuns';

    protected $fillable = [
        'kode_upload',
        'distrik',
        'kebun',
        'jenis_lokasi',
        'nama_lokasi',
        'latitude',
        'longitude',
        'tile_url'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];
}
