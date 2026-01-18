<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\AbsensiRequest;
use App\Services\AbsensiService;
use App\Services\PegawaiService;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function __construct(
        protected AbsensiService $service,
        protected PegawaiService $pegawaiService
    ) {}

    /**
     * Halaman utama absensi (untuk pegawai)
     */
    public function index()
    {
        $pegawai = $this->pegawaiService->getByUserId(auth()->id());
        
        if (!$pegawai) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum terdaftar sebagai pegawai.');
        }

        $absensiHariIni = $pegawai->absensiHariIni();
        $historyAbsensi = $this->service->getByPegawaiBulan(
            $pegawai->id,
            now()->month,
            now()->year
        );

        return view('pages.absensi.index', compact('pegawai', 'absensiHariIni', 'historyAbsensi'));
    }

    /**
     * Proses absen masuk
     */
    public function absenMasuk(AbsensiRequest $request)
    {
        try {
            $pegawai = $this->pegawaiService->getByUserId(auth()->id());

            if (!$pegawai) {
                return ResponseHelper::error('Anda belum terdaftar sebagai pegawai.', 403);
            }

            $absensi = $this->service->absenMasuk($pegawai, [
                'foto' => $request->file('foto'),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'device' => $request->device,
            ]);

            return ResponseHelper::success([
                'absensi' => $absensi,
                'message' => "Absen masuk berhasil! Status: {$absensi->status}",
            ], 'Absen masuk berhasil!');

        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 400);
        }
    }

    /**
     * Proses absen pulang
     */
    public function absenPulang(AbsensiRequest $request)
    {
        try {
            $pegawai = $this->pegawaiService->getByUserId(auth()->id());

            if (!$pegawai) {
                return ResponseHelper::error('Anda belum terdaftar sebagai pegawai.', 403);
            }

            $absensi = $this->service->absenPulang($pegawai, [
                'foto' => $request->file('foto'),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'device' => $request->device,
            ]);

            return ResponseHelper::success([
                'absensi' => $absensi,
            ], 'Absen pulang berhasil!');

        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 400);
        }
    }

    /**
     * Validasi lokasi saja (untuk preview)
     */
    public function validateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        try {
            $pegawai = $this->pegawaiService->getByUserId(auth()->id());

            if (!$pegawai) {
                return ResponseHelper::error('Anda belum terdaftar sebagai pegawai.', 403);
            }

            $result = $this->service->validateLocation(
                $pegawai,
                $request->latitude,
                $request->longitude
            );

            return ResponseHelper::success($result);

        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 400);
        }
    }

    /**
     * History absensi pegawai
     */
    public function history(Request $request)
    {
        $pegawai = $this->pegawaiService->getByUserId(auth()->id());

        if (!$pegawai) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum terdaftar sebagai pegawai.');
        }

        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $data = $this->service->getByPegawaiBulan($pegawai->id, $bulan, $tahun);

        return view('pages.absensi.history', compact('data', 'pegawai', 'bulan', 'tahun'));
    }

    /**
     * Dashboard absensi untuk admin
     */
    public function dashboard(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->toDateString());

        $statistik = $this->service->getStatistik($tanggal);
        $rekapDivisi = $this->service->getRekapPerDivisi($tanggal);
        $belumAbsen = $this->service->getBelumAbsenHariIni();
        $sudahAbsen = $this->service->getAbsensiHariIni();

        return view('pages.absensi.dashboard', compact(
            'statistik',
            'rekapDivisi',
            'belumAbsen',
            'sudahAbsen',
            'tanggal'
        ));
    }

    /**
     * Rekap absensi (untuk admin)
     */
    public function rekap(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $data = $this->pegawaiService->rekapPaginate($bulan, $tahun);

        return view('pages.absensi.rekap', compact('data', 'bulan', 'tahun'));
    }
}
