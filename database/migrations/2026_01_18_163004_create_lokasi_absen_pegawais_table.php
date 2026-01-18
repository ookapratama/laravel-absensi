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
        if (Schema::hasTable('lokasi_absen_pegawais')) {
            return;
        }

        Schema::create('lokasi_absen_pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->foreignId('kantor_id')->constrained('kantors')->cascadeOnDelete();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            // Pegawai hanya bisa di-assign ke kantor yang sama sekali
            $table->unique(['pegawai_id', 'kantor_id'], 'unique_pegawai_kantor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lokasi_absen_pegawais');
    }
};
