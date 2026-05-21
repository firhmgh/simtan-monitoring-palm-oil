<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UploadLog extends Model
{
    protected $table = 'upload_log';

    // Jika tabel hanya punya created_at tanpa updated_at
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
     * Casting data agar saat ditarik ke array/json formatnya konsisten
     */
    protected $casts = [
        'rows_imported' => 'integer',
        'created_at' => 'datetime',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(SimtanForm::class, 'simtan_form_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
