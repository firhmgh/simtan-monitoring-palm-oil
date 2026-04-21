<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model UploadLogs - Berfungsi sebagai audit trail untuk transparansi data.
 * Sesuai prinsip Keterlacakan (Traceability).
 */
class UploadLogs extends Model
{
    protected $table = 'upload_logs';

    protected $fillable = [
        'simtan_form_id',
        'user_id',
        'nama_file',
        'jenis_dataset',
        'rows_imported',
        'status',
        'message'
    ];
}
