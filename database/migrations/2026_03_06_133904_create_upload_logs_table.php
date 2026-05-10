<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('upload_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simtan_form_id')->constrained('simtan_form')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Siapa yang memproses
            $table->string('nama_file', 150);
            $table->string('jenis_dataset', 100);
            $table->integer('rows_imported'); // Jumlah baris berhasil
            $table->string('status', 20); // Success / Error
            $table->text('message')->nullable(); // Galat logika jika ada
            $table->timestamp('created_at')->useCurrent(); // Waktu pencatatan log
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_log');
    }
};
