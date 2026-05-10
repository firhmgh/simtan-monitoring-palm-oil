<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model UploadLog - Berfungsi sebagai audit trail untuk transparansi data.
 * Sesuai prinsip Keterlacakan (Traceability).
 */
class UploadLog extends Model
{
    protected $table = 'upload_log';

    // Karena tabel ini hanya mencatat histori (log), biasanya tidak butuh kolom updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'simtan_form_id',
        'user_id',
        'nama_file',
        'jenis_dataset',
        'rows_imported',
        'status',
        'message'
    ];

    /**
     * RELASI: Log ini milik Form yang mana?
     * Menghubungkan simtan_form_id ke id di tabel simtan_form
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(SimtanForm::class, 'simtan_form_id', 'id');
    }

    /**
     * RELASI: Siapa yang melakukan upload ini?
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
