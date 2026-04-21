<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade'); // Foreign Key ke roles
            $table->string('name', 100);
            $table->string('username', 100)->unique(); // Identitas login
            $table->string('email', 150)->unique();
            $table->string('password'); // Hash kata sandi
            $table->rememberToken();
            $table->timestamps(); // Termasuk created_at untuk waktu pendaftaran
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};