<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('korelasi_vegetatif', function (Blueprint $table) {
            $table->id();
            $table->string('kode_upload');
            $table->foreign('kode_upload')->references('kode_upload')->on('simtan_form')->onDelete('cascade');

            $table->string('tahun', 4);
            $table->string('kebun', 100);
            $table->string('topografi', 100);
            $table->string('blok', 50);

            // Parameter Biometrik
            $table->decimal('keliling_crown', 8, 2)->nullable(); // cm
            $table->decimal('lingkar_batang', 8, 2)->nullable(); // cm
            $table->decimal('jumlah_pelepah', 5, 2)->nullable(); // per pokok
            $table->decimal('panjang_pelepah', 8, 2)->nullable(); // meter
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('korelasi_vegetatif');
    }
};
