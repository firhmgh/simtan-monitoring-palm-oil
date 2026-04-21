<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model KorelasiVegetatif - Parameter morfologis untuk analisis AI.
 * Sesuai Entitas Agronomi.
 */
class KorelasiVegetatif extends Model
{
    protected $table = 'korelasi_vegetatif';

    protected $fillable = [
        'kode_upload', 'tahun', 'kebun', 'topografi', 'blok',
        'keliling_crown', 'lingkar_batang', 'jumlah_pelepah', 'panjang_pelepah'
    ];

    protected $casts = [
        'keliling_crown' => 'decimal:2',
        'lingkar_batang' => 'decimal:2',
        'jumlah_pelepah' => 'decimal:2',
        'panjang_pelepah' => 'decimal:2',
    ];
}