<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('korelasi_vegetatif', function (Blueprint $table) {
            $table->string('periode')->nullable()->after('kode_upload');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('korelasi_vegetatif', function (Blueprint $table) {
            //
        });
    }
};
