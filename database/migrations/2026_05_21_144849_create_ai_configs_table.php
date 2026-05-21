<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_configs', function (Blueprint $table) {
            $table->id();
            $table->string('provider_primary')->default('gemini'); // gemini / groq
            $table->text('key_primary')->nullable();
            $table->string('provider_backup')->default('groq');
            $table->text('key_backup')->nullable();
            $table->integer('threshold_yellow')->default(85);
            $table->integer('threshold_red')->default(75);
            $table->timestamps();
        });

        // Insert default data
        DB::table('ai_configs')->insert(['created_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_configs');
    }
};
