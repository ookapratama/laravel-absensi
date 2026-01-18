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
        // Skip jika tabel sudah ada
        if (Schema::hasTable('pegawais')) {
            return;
        }

        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('divisi_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nama_lengkap');
            $table->string('nik')->nullable();
            $table->string('foto')->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->string('no_telp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
