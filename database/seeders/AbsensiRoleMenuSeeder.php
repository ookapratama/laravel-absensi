<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsensiRoleMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Get primary roles
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $adminRole = Role::where('slug', 'admin')->first();
        $pegawaiRole = Role::where('slug', 'pegawai')->first();

        if (!$superAdmin) {
            $superAdmin = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        }
        if (!$adminRole) {
            $adminRole = Role::create(['name' => 'Admin System', 'slug' => 'admin']);
        }
        if (!$pegawaiRole) {
            $pegawaiRole = Role::create(['name' => 'Pegawai', 'slug' => 'pegawai']);
        }

        // Slugs for each role
        $adminSlugs = [
            'data-master', 'divisi.index', 'kantor.index', 'pegawai.index', 'jenis-izin.index',
            'absensi-menu', 'absensi.index', 'absensi.history', 'absensi.dashboard', 'absensi.rekap',
            'izin-menu', 'izin.create', 'izin.index', 'izin.admin'
        ];

        $pegawaiSlugs = [
            'absensi-menu', 'absensi.index', 'absensi.history',
            'izin-menu', 'izin.create', 'izin.index'
        ];

        // 1. Super Admin gets EVERYTHING
        $allMenus = Menu::all();
        foreach ($allMenus as $menu) {
            DB::table('role_menu')->updateOrInsert(
                ['role_id' => $superAdmin->id, 'menu_id' => $menu->id],
                ['can_create' => true, 'can_read' => true, 'can_update' => true, 'can_delete' => true]
            );
        }

        // 2. Admin access
        foreach ($adminSlugs as $slug) {
            $menu = Menu::where('slug', $slug)->first();
            if ($menu) {
                DB::table('role_menu')->updateOrInsert(
                    ['role_id' => $adminRole->id, 'menu_id' => $menu->id],
                    ['can_create' => true, 'can_read' => true, 'can_update' => true, 'can_delete' => true]
                );
            }
        }

        // 3. Pegawai access
        foreach ($pegawaiSlugs as $slug) {
            $menu = Menu::where('slug', $slug)->first();
            if ($menu) {
                DB::table('role_menu')->updateOrInsert(
                    ['role_id' => $pegawaiRole->id, 'menu_id' => $menu->id],
                    ['can_create' => true, 'can_read' => true, 'can_update' => true, 'can_delete' => false]
                );
            }
        }

        $this->command->info('✅ Role permissions for Absensi successfully updated!');
    }
}
