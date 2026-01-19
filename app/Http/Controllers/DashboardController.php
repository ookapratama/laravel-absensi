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

        // User biasa (pegawai) langsung ke halaman absensi
        $pegawai = $this->pegawaiService->getByUserId($user->id);
        
        if (!$pegawai) {
            return view('pages.dashboard.dashboard')
                ->with('error', 'Anda belum terdaftar sebagai pegawai.');
        }

        $absensiHariIni = $pegawai->absensiHariIni();
        $historyAbsensi = $this->absensiService->getByPegawaiBulan(
            $pegawai->id,
            now()->month,
            now()->year
        );

        return view('pages.absensi.index', compact('pegawai', 'absensiHariIni', 'historyAbsensi'));
    }
}
