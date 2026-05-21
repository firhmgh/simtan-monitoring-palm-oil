<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('detail_rekap', function (Blueprint $table) {
            // Hapus manual dulu di phpmyadmin kalau pkk_mati_kembali dkk sudah ada
            $table->integer('pkk_mati_mati_kembali')->nullable()->after('pkk_mati');
            $table->double('persen_pkk_mati_mati_kembali', 8, 2)->nullable()->after('pkk_mati_mati_kembali');
            $table->integer('pkk_kerdil_mati_kembali')->nullable()->after('persen_pkk_mati_mati_kembali');
            $table->double('persen_pkk_kerdil_mati_kembali', 8, 2)->nullable()->after('pkk_kerdil_mati_kembali');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_rekap', function (Blueprint $table) {
            //
        });
    }
};
