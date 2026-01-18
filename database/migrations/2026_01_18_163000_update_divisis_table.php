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
        Schema::table('divisis', function (Blueprint $table) {
            // Cek dan tambah kolom jika belum ada
            if (!Schema::hasColumn('divisis', 'kode')) {
                $table->string('kode', 20)->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('divisis', 'nama')) {
                $table->string('nama', 100)->after('id');
            }
            if (!Schema::hasColumn('divisis', 'jam_masuk')) {
                $table->time('jam_masuk')->nullable()->after('nama');
            }
            if (!Schema::hasColumn('divisis', 'jam_pulang')) {
                $table->time('jam_pulang')->nullable()->after('jam_masuk');
            }
            if (!Schema::hasColumn('divisis', 'toleransi_terlambat')) {
                $table->integer('toleransi_terlambat')->default(0)->comment('dalam menit')->after('jam_pulang');
            }
            if (!Schema::hasColumn('divisis', 'is_aktif')) {
                $table->boolean('is_aktif')->default(true)->after('toleransi_terlambat');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('divisis', function (Blueprint $table) {
            $columns = ['kode', 'nama', 'jam_masuk', 'jam_pulang', 'toleransi_terlambat', 'is_aktif'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('divisis', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
