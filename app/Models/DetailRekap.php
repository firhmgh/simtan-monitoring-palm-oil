<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model DetailRekap - Menyimpan hasil ekstraksi rekapitulasi populasi sawit.
 */
class DetailRekap extends Model
{
    use HasFactory;

    protected $table = 'detail_rekap';

    /**
     * Mass Assignment
     * 'simtan_form_id' menggantikan 'kode_upload' sebagai penghubung (Foreign Key)
     */
    protected $fillable = [
        'simtan_form_id',
        'kode_upload',    // Tetap dipertahankan sebagai referensi kode unik (Human-readable)
        'distrik',
        'kebun',
        'afdeling',
        'tahun_tanam',
        'luas_ha',
        'pkk_awal',
        'pkk_normal',
        'pkk_non_valuer',
        'pkk_mati',
        'pkk_ha_kond_normal',
        'persen_pkk_normal',
        'persen_pkk_non_valuer',
        'persen_pkk_mati',
        'persen_tutupan_kacangan',
        'persen_pir_pkk_kurang_baik',
        'persen_area_tergenang',
        'kondisi_anak_kayu',
        'gangguan_ternak',
        'is_total',
    ];

    /**
     * Casting tipe data agar presisi saat perhitungan matematik
     * Diambil dari logika project 'simtanfix' yang sudah berhasil
     */
    protected $casts = [
        'simtan_form_id' => 'integer',
        'luas_ha' => 'float',
        'pkk_awal' => 'integer',
        'pkk_normal' => 'integer',
        'pkk_non_valuer' => 'integer',
        'pkk_mati' => 'integer',
        'pkk_ha_kond_normal' => 'integer',
        'persen_pkk_normal' => 'float',
        'persen_pkk_non_valuer' => 'float',
        'persen_pkk_mati' => 'float',
        'persen_tutupan_kacangan' => 'float',
        'persen_pir_pkk_kurang_baik' => 'float',
        'persen_area_tergenang' => 'float',
        'kondisi_anak_kayu' => 'float',
        'is_total' => 'boolean',
    ];

    /**
     * RELASI: Berbasis Integer ID (Professional Standard)
     * Menghubungkan ke tabel SimtanForm menggunakan simtan_form_id
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(SimtanForm::class, 'simtan_form_id');
    }
}
