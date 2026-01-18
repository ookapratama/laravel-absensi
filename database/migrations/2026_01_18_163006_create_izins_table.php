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
        if (Schema::hasTable('izins')) {
            return;
        }

        Schema::create('izins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->foreignId('jenis_izin_id')->constrained('jenis_izins');

            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->integer('jumlah_hari')->nullable();
            $table->text('alasan');
            $table->string('file_surat', 255)->nullable();

            // Approval
            $table->enum('status_approval', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan_admin')->nullable();

            $table->timestamps();

            $table->index('status_approval', 'idx_status');
            $table->index(['tgl_mulai', 'tgl_selesai'], 'idx_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izins');
    }
};
