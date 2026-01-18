<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsensiMenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Core Header / Parents
        $menus = [
            // Dashboard
            [
                'slug' => 'dashboard',
                'name' => 'Dashboard',
                'icon' => 'ri-home-smile-line',
                'path' => '/',
                'parent_slug' => null,
                'order_no' => 1
            ],
            
            // User Management (Existing or updated)
            [
                'slug' => 'user-management',
                'name' => 'User Management',
                'icon' => 'ri-user-settings-line',
                'path' => null,
                'parent_slug' => null,
                'order_no' => 50
            ],
            [
                'slug' => 'user.index',
                'name' => 'Users',
                'icon' => 'ri-user-line',
                'path' => '/user',
                'parent_slug' => 'user-management',
                'order_no' => 1
            ],
            [
                'slug' => 'role.index',
                'name' => 'Roles',
                'icon' => 'ri-shield-user-line',
                'path' => '/role',
                'parent_slug' => 'user-management',
                'order_no' => 2
            ],
            
            // Data Master (Group)
            [
                'slug' => 'data-master',
                'name' => 'Data Master',
                'icon' => 'ri-database-2-line',
                'path' => null,
                'parent_slug' => null,
                'order_no' => 10
            ],
            [
                'slug' => 'divisi.index',
                'name' => 'Divisi',
                'icon' => 'ri-community-line',
                'path' => '/divisi',
                'parent_slug' => 'data-master',
                'order_no' => 1
            ],
            [
                'slug' => 'kantor.index',
                'name' => 'Kantor',
                'icon' => 'ri-building-line',
                'path' => '/kantor',
                'parent_slug' => 'data-master',
                'order_no' => 2
            ],
            [
                'slug' => 'pegawai.index',
                'name' => 'Pegawai',
                'icon' => 'ri-user-star-line',
                'path' => '/pegawai',
                'parent_slug' => 'data-master',
                'order_no' => 3
            ],
            [
                'slug' => 'jenis-izin.index',
                'name' => 'Jenis Izin',
                'icon' => 'ri-file-settings-line',
                'path' => '/jenis-izin',
                'parent_slug' => 'data-master',
                'order_no' => 4
            ],
            
            // Absensi (Group)
            [
                'slug' => 'absensi-menu',
                'name' => 'Absensi',
                'icon' => 'ri-fingerprint-line',
                'path' => null,
                'parent_slug' => null,
                'order_no' => 20
            ],
            [
                'slug' => 'absensi.index',
                'name' => 'Absensi Saya',
                'icon' => 'ri-camera-line',
                'path' => '/absensi',
                'parent_slug' => 'absensi-menu',
                'order_no' => 1
            ],
            [
                'slug' => 'absensi.history',
                'name' => 'Riwayat Saya',
                'icon' => 'ri-history-line',
                'path' => '/absensi/history',
                'parent_slug' => 'absensi-menu',
                'order_no' => 2
            ],
            [
                'slug' => 'absensi.dashboard',
                'name' => 'Dashboard Admin',
                'icon' => 'ri-dashboard-line',
                'path' => '/absensi/dashboard',
                'parent_slug' => 'absensi-menu',
                'order_no' => 3
            ],
            [
                'slug' => 'absensi.rekap',
                'name' => 'Rekap Bulanan',
                'icon' => 'ri-file-chart-line',
                'path' => '/absensi/rekap',
                'parent_slug' => 'absensi-menu',
                'order_no' => 4
            ],
            
            // Izin & Cuti (Group)
            [
                'slug' => 'izin-menu',
                'name' => 'Izin & Cuti',
                'icon' => 'ri-calendar-check-line',
                'path' => null,
                'parent_slug' => null,
                'order_no' => 30
            ],
            [
                'slug' => 'izin.create',
                'name' => 'Ajukan Izin',
                'icon' => 'ri-add-circle-line',
                'path' => '/izin/create',
                'parent_slug' => 'izin-menu',
                'order_no' => 1
            ],
            [
                'slug' => 'izin.index',
                'name' => 'Izin Saya',
                'icon' => 'ri-file-list-3-line',
                'path' => '/izin',
                'parent_slug' => 'izin-menu',
                'order_no' => 2
            ],
            [
                'slug' => 'izin.admin',
                'name' => 'Kelola Izin (Admin)',
                'icon' => 'ri-checkbox-circle-line',
                'path' => '/izin/admin',
                'parent_slug' => 'izin-menu',
                'order_no' => 3
            ],
        ];

        // First pass: Create or update all menus without parent_id (or with parent_id = null first)
        foreach ($menus as $m) {
            Menu::updateOrCreate(
                ['slug' => $m['slug']],
                [
                    'name' => $m['name'],
                    'icon' => $m['icon'],
                    'path' => $m['path'],
                    'order_no' => $m['order_no'],
                    'is_active' => true,
                ]
            );
        }

        // Second pass: Link parents
        foreach ($menus as $m) {
            if ($m['parent_slug']) {
                $parent = Menu::where('slug', $m['parent_slug'])->first();
                if ($parent) {
                    Menu::where('slug', $m['slug'])->update(['parent_id' => $parent->id]);
                }
            } else {
                Menu::where('slug', $m['slug'])->update(['parent_id' => null]);
            }
        }

        // Clean up redundant slugs that might exist from previous sessions
        Menu::whereNotIn('slug', array_column($menus, 'slug'))
            ->whereIn('slug', ['master-menu', 'user-mgmt']) // targeted cleanup
            ->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('✅ Struktur Menu Absensi & Core berhasil diperbaiki dan disinkronkan!');
    }
}
