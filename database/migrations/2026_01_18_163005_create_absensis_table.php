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
        if (Schema::hasTable('absensis')) {
            return;
        }

        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->date('tanggal');

            // Data Absen Masuk
            $table->time('jam_masuk')->nullable();
            $table->string('foto_masuk', 255)->nullable();
            $table->decimal('latitude_masuk', 10, 8)->nullable();
            $table->decimal('longitude_masuk', 11, 8)->nullable();
            $table->string('lokasi_masuk', 255)->nullable()->comment('nama kantor/alamat');
            $table->string('device_masuk', 100)->nullable();

            // Data Absen Pulang
            $table->time('jam_pulang')->nullable();
            $table->string('foto_pulang', 255)->nullable();
            $table->decimal('latitude_pulang', 10, 8)->nullable();
            $table->decimal('longitude_pulang', 11, 8)->nullable();
            $table->string('lokasi_pulang', 255)->nullable();
            $table->string('device_pulang', 100)->nullable();

            // Status & Keterangan
            $table->enum('status', ['Hadir', 'Terlambat', 'Alpha', 'Izin', 'Cuti', 'Sakit'])->default('Alpha');
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Pegawai hanya bisa absen sekali per hari
            $table->unique(['pegawai_id', 'tanggal'], 'unique_pegawai_tanggal');
            $table->index('tanggal', 'idx_tanggal');
            $table->index('status', 'idx_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
