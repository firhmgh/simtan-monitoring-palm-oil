<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model DetailRekap - Menyimpan hasil ekstraksi rekapitulasi populasi sawit.
 * Dilengkapi dengan tracking mortalitas (Pohon Mati Kembali & Kerdil Jadi Mati).
 */
class DetailRekap extends Model
{
    use HasFactory;

    protected $table = 'detail_rekap';

    protected $fillable = [
        'simtan_form_id',
        'kode_upload',
        'periode',
        'distrik',
        'kebun',
        'afdeling',
        'tahun_tanam',
        'luas_ha',
        'pkk_awal',
        'pkk_normal',
        'pkk_non_valuer',
        'pkk_mati',

        // Tracking Mortalitas
        'pkk_mati_mati_kembali',
        'persen_pkk_mati_mati_kembali',
        'pkk_kerdil_mati_kembali',
        'persen_pkk_kerdil_mati_kembali',

        'pkk_ha_kond_normal',
        'persen_pkk_normal',
        'persen_pkk_non_valuer',
        'persen_pkk_mati',
        'persen_tutupan_kacangan',
        'persen_pasar_pikul_kurang_baik',
        'persen_pir_pkk_kurang_baik',
        'persen_area_tergenang',
        'kondisi_anak_kayu',
        'gangguan_ternak',
        'is_total',
    ];

    protected $casts = [
        'simtan_form_id' => 'integer',
        'luas_ha' => 'float',
        'pkk_awal' => 'integer',
        'pkk_normal' => 'integer',
        'pkk_non_valuer' => 'integer',
        'pkk_mati' => 'integer',

        'pkk_mati_mati_kembali' => 'integer',
        'persen_pkk_mati_mati_kembali' => 'float',
        'pkk_kerdil_mati_kembali' => 'integer',
        'persen_pkk_kerdil_mati_kembali' => 'float',

        'pkk_ha_kond_normal' => 'integer',
        'persen_pkk_normal' => 'float',
        'persen_pkk_non_valuer' => 'float',
        'persen_pkk_mati' => 'float',
        'persen_tutupan_kacangan' => 'float',
        'persen_pasar_pikul_kurang_baik' => 'float',
        'persen_pir_pkk_kurang_baik' => 'float',
        'persen_area_tergenang' => 'float',
        'kondisi_anak_kayu' => 'float',
        'is_total' => 'boolean',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(SimtanForm::class, 'simtan_form_id');
    }
}
