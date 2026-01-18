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
        if (Schema::hasTable('jenis_izins')) {
            return;
        }

        Schema::create('jenis_izins', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->string('kode', 20)->unique()->nullable();
            $table->boolean('butuh_surat')->default(false);
            $table->integer('max_hari')->nullable()->comment('maksimal hari izin, null = unlimited');
            $table->text('keterangan')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_izins');
    }
};
