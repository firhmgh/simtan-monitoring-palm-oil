<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model DetailRekaps - Menyimpan hasil ekstraksi rekapitulasi populasi sawit.
 * Menggunakan tipe data double untuk luasan.
 */
class DetailRekaps extends Model
{
    protected $table = 'detail_rekaps';

    protected $fillable = [
        'kode_upload', 'distrik', 'kebun', 'afdeling', 'tahun_tanam',
        'luas_ha', 'pkk_awal', 'pkk_normal', 'pkk_non_valuer', 'pkk_mati',
        'persen_pkk_normal', 'persen_pkk_non_valuer', 'persen_pkk_mati', 'is_total'
    ];

    protected $casts = [
        'luas_ha' => 'double',
        'persen_pkk_normal' => 'double',
        'is_total' => 'boolean'
    ];
}