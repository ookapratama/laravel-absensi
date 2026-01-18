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
        Schema::table('pegawais', function (Blueprint $table) {
            // Add kantor_id sebagai kantor utama
            if (!Schema::hasColumn('pegawais', 'kantor_id')) {
                $table->unsignedBigInteger('kantor_id')->nullable()->after('divisi_id');
                $table->foreign('kantor_id')->references('id')->on('kantors')->nullOnDelete();
            }
            
            // Rename nik to nip jika nik masih ada
            if (Schema::hasColumn('pegawais', 'nik') && !Schema::hasColumn('pegawais', 'nip')) {
                $table->renameColumn('nik', 'nip');
            }
            
            // Tambah kolom tambahan jika belum ada
            if (!Schema::hasColumn('pegawais', 'jabatan')) {
                $table->string('jabatan', 100)->nullable()->after('nama_lengkap');
            }
            if (!Schema::hasColumn('pegawais', 'tgl_masuk')) {
                $table->date('tgl_masuk')->nullable()->after('jabatan');
            }
            if (!Schema::hasColumn('pegawais', 'alamat')) {
                $table->text('alamat')->nullable()->after('no_telp');
            }
            if (!Schema::hasColumn('pegawais', 'status_aktif')) {
                $table->boolean('status_aktif')->default(true)->after('alamat');
            }
        });

        // Modifikasi kolom existing agar nullable
        Schema::table('pegawais', function (Blueprint $table) {
            if (Schema::hasColumn('pegawais', 'nip')) {
                $table->string('nip', 50)->nullable()->change();
            }
            if (Schema::hasColumn('pegawais', 'foto')) {
                $table->string('foto')->nullable()->change();
            }
            if (Schema::hasColumn('pegawais', 'no_telp')) {
                $table->string('no_telp')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            if (Schema::hasColumn('pegawais', 'kantor_id')) {
                $table->dropForeign(['kantor_id']);
                $table->dropColumn('kantor_id');
            }
            
            $columns = ['jabatan', 'tgl_masuk', 'alamat', 'status_aktif'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('pegawais', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            if (Schema::hasColumn('pegawais', 'nip') && !Schema::hasColumn('pegawais', 'nik')) {
                $table->renameColumn('nip', 'nik');
            }
        });
    }
};
