<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lokasi_kebuns', function (Blueprint $table) {
            $table->id();
            $table->string('kode_upload');
            $table->foreign('kode_upload')->references('kode_upload')->on('simtan_forms')->onDelete('cascade');
            
            $table->string('distrik', 100);
            $table->string('kebun', 100);
            $table->string('jenis_lokasi', 50); // Blok/Kantor/Pos
            $table->string('nama_lokasi', 150);
            
            // Koordinat Geospasial (Standard EPSG:4326)
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            
            $table->string('tile_url', 255)->nullable(); // Jalur direktori XYZ Tiles
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('lokasi_kebuns');
    }
};