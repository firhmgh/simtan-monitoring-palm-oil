<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('simtan_form', function (Blueprint $table) {
            $table->id();
            $table->string('kode_upload', 50)->unique(); // UK: Kode unik referensi
            $table->foreignId('uploaded_by')->constrained('users'); // FK: Aktor pengunggah
            $table->string('judul_file', 255);
            $table->date('tanggal_upload');
            $table->string('kategori_file', 100); // Vegetatif, Rekap, dll
            $table->string('periode_data', 50); // Rentang waktu cakupan
            $table->text('notes')->nullable();
            $table->string('file_path', 255); // Lokasi fisik berkas di server
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simtan_form');
    }
};
