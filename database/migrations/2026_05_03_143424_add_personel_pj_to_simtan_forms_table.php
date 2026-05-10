<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan perintah penambahan kolom.
     */
    public function up(): void
    {
        Schema::table('simtan_form', function (Blueprint $table) {
            // Menambahkan kolom personel_pj (Personel Penanggung Jawab)
            // Diletakkan setelah kolom uploaded_by untuk menjaga hierarki audit trail
            $table->string('personel_pj')->after('uploaded_by')->nullable();
        });
    }

    /**
     * Batalkan perintah (Rollback).
     */
    public function down(): void
    {
        Schema::table('simtan_form', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn('personel_pj');
        });
    }
};
