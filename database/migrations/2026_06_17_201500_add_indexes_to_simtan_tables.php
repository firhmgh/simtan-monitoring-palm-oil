<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detail_rekap', function (Blueprint $table) {
            $table->index('kebun');
            $table->index('distrik');
            $table->index('periode');
        });

        Schema::table('korelasi_vegetatif', function (Blueprint $table) {
            $table->index('kebun');
            $table->index('blok');
            $table->index('periode');
        });

        Schema::table('lokasi_kebun', function (Blueprint $table) {
            $table->index('kebun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_rekap', function (Blueprint $table) {
            $table->dropIndex(['kebun']);
            $table->dropIndex(['distrik']);
            $table->dropIndex(['periode']);
        });

        Schema::table('korelasi_vegetatif', function (Blueprint $table) {
            $table->dropIndex(['kebun']);
            $table->dropIndex(['blok']);
            $table->dropIndex(['periode']);
        });

        Schema::table('lokasi_kebun', function (Blueprint $table) {
            $table->dropIndex(['kebun']);
        });
    }
};
