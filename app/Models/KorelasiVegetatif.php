<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model KorelasiVegetatif - Parameter morfologis untuk analisis AI.
 */
class KorelasiVegetatif extends Model
{
    use HasFactory;

    protected $table = 'korelasi_vegetatif';

    /**
     * Mass Assignment
     * 'simtan_form_id' sebagai penghubung utama ke tabel induk.
     */
    protected $fillable = [
        'simtan_form_id', // Relasi Integer
        'kode_upload',    // Kode unik (Human-readable)
        'tahun',
        'kebun',
        'topografi',
        'blok',
        'keliling_crown',
        'lingkar_batang',
        'jumlah_pelepah',
        'panjang_pelepah',
    ];

    /**
     * Casting tipe data menggunakan 'float' sesuai project sebelumnya yang berhasil.
     */
    protected $casts = [
        'simtan_form_id' => 'integer',
        'keliling_crown' => 'float',
        'lingkar_batang' => 'float',
        'jumlah_pelepah' => 'float',
        'panjang_pelepah' => 'float',
    ];

    /**
     * RELASI: Menghubungkan ke tabel SimtanForm (Induk)
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(SimtanForm::class, 'simtan_form_id');
    }
}
