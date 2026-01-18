<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsensiMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seeder Divisi
        $divisis = [
            [
                'kode' => 'IT',
                'nama' => 'Information Technology',
                'jam_masuk' => '08:00',
                'jam_pulang' => '17:00',
                'toleransi_terlambat' => 15,
                'is_aktif' => true,
            ],
            [
                'kode' => 'HR',
                'nama' => 'Human Resources',
                'jam_masuk' => '08:00',
                'jam_pulang' => '17:00',
                'toleransi_terlambat' => 10,
                'is_aktif' => true,
            ],
            [
                'kode' => 'FIN',
                'nama' => 'Finance & Accounting',
                'jam_masuk' => '08:30',
                'jam_pulang' => '17:30',
                'toleransi_terlambat' => 10,
                'is_aktif' => true,
            ],
            [
                'kode' => 'OPS',
                'nama' => 'Operations',
                'jam_masuk' => '07:00',
                'jam_pulang' => '16:00',
                'toleransi_terlambat' => 15,
                'is_aktif' => true,
            ],
            [
                'kode' => 'MKT',
                'nama' => 'Marketing',
                'jam_masuk' => '09:00',
                'jam_pulang' => '18:00',
                'toleransi_terlambat' => 15,
                'is_aktif' => true,
            ],
        ];

        foreach ($divisis as $divisi) {
            DB::table('divisis')->updateOrInsert(
                ['kode' => $divisi['kode']],
                array_merge($divisi, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // 2. Seeder Kantor
        $kantors = [
            [
                'kode' => 'HQ',
                'nama' => 'Kantor Pusat',
                'alamat' => 'Jl. Sudirman No. 123, Jakarta Selatan',
                'titik_lokasi' => '-6.2088, 106.8456',
                'radius_meter' => 100,
                'is_aktif' => true,
            ],
            [
                'kode' => 'CBG',
                'nama' => 'Cabang Bandung',
                'alamat' => 'Jl. Asia Afrika No. 45, Bandung',
                'titik_lokasi' => '-6.9175, 107.6191',
                'radius_meter' => 100,
                'is_aktif' => true,
            ],
            [
                'kode' => 'SBY',
                'nama' => 'Cabang Surabaya',
                'alamat' => 'Jl. Pemuda No. 78, Surabaya',
                'titik_lokasi' => '-7.2504, 112.7688',
                'radius_meter' => 150,
                'is_aktif' => true,
            ],
            [
                'kode' => 'WH',
                'nama' => 'Gudang Utama',
                'alamat' => 'Jl. Industri Raya No. 99, Bekasi',
                'titik_lokasi' => '-6.2383, 107.0031',
                'radius_meter' => 200,
                'is_aktif' => true,
            ],
        ];

        foreach ($kantors as $kantor) {
            DB::table('kantors')->updateOrInsert(
                ['kode' => $kantor['kode']],
                array_merge($kantor, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // 3. Seeder Jenis Izin
        $jenisIzins = [
            [
                'kode' => 'sakit',
                'nama' => 'Sakit',
                'butuh_surat' => true,
                'max_hari' => null,
                'keterangan' => 'Izin karena sakit, wajib melampirkan surat dokter',
                'is_aktif' => true,
            ],
            [
                'kode' => 'cuti',
                'nama' => 'Cuti Tahunan',
                'butuh_surat' => false,
                'max_hari' => 12,
                'keterangan' => 'Cuti tahunan pegawai, maksimal 12 hari per tahun',
                'is_aktif' => true,
            ],
            [
                'kode' => 'izin',
                'nama' => 'Izin Pribadi',
                'butuh_surat' => false,
                'max_hari' => 3,
                'keterangan' => 'Izin keperluan pribadi, maksimal 3 hari per pengajuan',
                'is_aktif' => true,
            ],
            [
                'kode' => 'melahirkan',
                'nama' => 'Cuti Melahirkan',
                'butuh_surat' => true,
                'max_hari' => 90,
                'keterangan' => 'Cuti melahirkan untuk pegawai wanita',
                'is_aktif' => true,
            ],
            [
                'kode' => 'menikah',
                'nama' => 'Cuti Menikah',
                'butuh_surat' => true,
                'max_hari' => 3,
                'keterangan' => 'Cuti pernikahan pegawai',
                'is_aktif' => true,
            ],
            [
                'kode' => 'duka',
                'nama' => 'Cuti Duka',
                'butuh_surat' => false,
                'max_hari' => 3,
                'keterangan' => 'Cuti karena keluarga inti meninggal dunia',
                'is_aktif' => true,
            ],
            [
                'kode' => 'dinas',
                'nama' => 'Dinas Luar Kota',
                'butuh_surat' => true,
                'max_hari' => null,
                'keterangan' => 'Perjalanan dinas ke luar kota',
                'is_aktif' => true,
            ],
        ];

        foreach ($jenisIzins as $jenisIzin) {
            DB::table('jenis_izins')->updateOrInsert(
                ['kode' => $jenisIzin['kode']],
                array_merge($jenisIzin, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ Master data absensi berhasil di-seed!');
        $this->command->info('   - ' . count($divisis) . ' Divisi');
        $this->command->info('   - ' . count($kantors) . ' Kantor');
        $this->command->info('   - ' . count($jenisIzins) . ' Jenis Izin');
    }
}
