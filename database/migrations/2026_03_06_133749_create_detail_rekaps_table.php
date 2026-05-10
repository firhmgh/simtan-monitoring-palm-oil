<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_rekap', function (Blueprint $table) {
            $table->id();
            $table->string('kode_upload')->index();
            $table->foreign('kode_upload')->references('kode_upload')->on('simtan_form')->onDelete('cascade');

            $table->string('distrik', 100);
            $table->string('kebun', 100);
            $table->string('afdeling', 50);
            $table->integer('tahun_tanam');

            // Metrik Agronomi (Precision Agriculture Standards)
            $table->double('luas_ha'); // Penggunaan double untuk akurasi spasial
            $table->integer('pkk_awal');
            $table->integer('pkk_normal');
            $table->integer('pkk_non_valuer');
            $table->integer('pkk_mati');

            // Kalkulasi Persentase (Untuk Dashboard Visualization)
            $table->double('persen_pkk_normal')->nullable();
            $table->double('persen_pkk_non_valuer')->nullable();
            $table->double('persen_pkk_mati')->nullable();

            $table->tinyInteger('is_total')->default(0); // Penanda baris total (1=Ya, 0=Tidak)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_rekap');
    }
};
