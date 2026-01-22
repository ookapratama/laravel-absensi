<?php

namespace App\Http\Controllers;

use App\Services\AbsensiService;
use App\Services\PegawaiService;

class DashboardController extends Controller
{
    public function __construct(
        protected AbsensiService $absensiService,
        protected PegawaiService $pegawaiService
    ) {}

    /**
     * Display the dashboard - different view based on role
     */
    public function index()
    {
        $user = auth()->user();
        $roleSlug = $user->role?->slug;

        // Admin dan Superadmin melihat dashboard admin
        if (in_array($roleSlug, ['super-admin', 'admin'])) {
            return view('pages.dashboard.dashboard');
        }

        // User biasa (pegawai) melihat dashboard khusus user
        $pegawai = $this->pegawaiService->getByUserId($user->id);
        
        if (!$pegawai) {
            return view('pages.dashboard.dashboard')
                ->with('error', 'Anda belum terdaftar sebagai pegawai.');
        }

        $absensiHariIni = $pegawai->absensiHariIni();
        $bulan = now()->month;
        $tahun = now()->year;

        $historyAbsensi = $this->absensiService->getByPegawaiBulan($pegawai->id, $bulan, $tahun);
        $statistik = $this->absensiService->getStatistikPegawai($pegawai->id, $bulan, $tahun);

        return view('pages.dashboard.user', compact(
            'pegawai', 
            'absensiHariIni', 
            'historyAbsensi', 
            'statistik'
        ));
    }
}
