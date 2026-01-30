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
    public function index(Request $request)
    {
        $pegawai = $this->pegawaiService->getByUserId(auth()->id());
        
        if (!$pegawai) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum terdaftar sebagai pegawai.');
        }

        $shiftId = $request->get('shift_id');
        $shift = null;
        $absensiHariIni = null;

        if ($shiftId) {
            $shift = \App\Models\Shift::find($shiftId);
            // Validasi shift milik divisi pegawai
            if ($shift && $shift->divisi_id !== $pegawai->divisi_id) {
                return redirect()->route('dashboard')->with('error', 'Shift tidak valid untuk divisi Anda.');
            }
            
            // Ambil absensi spesifik untuk shift ini
            // REVISI: Cari sesi yang masih terbuka (bisa dari kemarin) atau yang sudah selesai hari ini
            $absensiHariIni = \App\Models\Absensi::where('pegawai_id', $pegawai->id)
                ->where('shift_id', $shiftId)
                ->where(function($q) {
                    $q->whereNull('jam_pulang')
                      ->orWhereDate('tanggal', today());
                })
                ->orderBy('tanggal', 'desc')
                ->first();
        } else {
            // Fallback ke logic lama (ambil absensi pertama hari ini) atau redirect ke dashboard
            // Agar konsisten dengan fitur baru, sebaiknya pegawai harus pilih shift dari dashboard
            // Tapi jika akses langsung menu, kita bisa ambil shift yang "aktif" sekarang jika ada
            
            // Untuk sementara, jika tidak ada shift_id, redirect back ke dashboard agar user memilih
            return redirect()->route('dashboard')->with('info', 'Silakan pilih shift terlebih dahulu.');
        }

        $historyAbsensi = $this->service->getByPegawaiBulan(
            $pegawai->id,
            now()->month,
            now()->year
        );

        return view('pages.absensi.index', compact('pegawai', 'absensiHariIni', 'historyAbsensi', 'shift'));
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
                'shift_id' => $request->shift_id,
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
                'shift_id' => $request->shift_id,
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
        $belumAbsen = $this->service->getBelumAbsenHariIni($tanggal);
        $sudahAbsen = $this->service->getAbsensiHariIni($tanggal);

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
        $bulan = (int)$request->get('bulan', now()->month);
        $tahun = (int)$request->get('tahun', now()->year);

        $data = $this->pegawaiService->rekapPaginate($bulan, $tahun);
        
        // Hitung hari kerja efektif bulan ini
        $absensiService = app(\App\Services\AbsensiService::class);
        $hariEfektif = $absensiService->getHariKerjaEfektif($bulan, $tahun);

        return view('pages.absensi.rekap', compact('data', 'bulan', 'tahun', 'hariEfektif'));
    }

    /**
     * Halaman calendar absensi untuk pegawai
     */
    public function calendar()
    {
        return view('pages.absensi.calendar');
    }

    /**
     * API for calendar events
     */
    public function getCalendarEvents(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        $pegawai = auth()->user()->pegawai;

        if (!$pegawai) {
            return response()->json([]);
        }

        // Attendance Events
        $absensis = \App\Models\Absensi::where('pegawai_id', $pegawai->id)
            ->whereBetween('tanggal', [$start, $end])
            ->get();

        $events = [];

        foreach ($absensis as $absen) {
            $color = 'success';
            if ($absen->status === 'Terlambat') $color = 'warning';
            if ($absen->status === 'Alpha') $color = 'danger';
            if (in_array($absen->status, ['Izin', 'Cuti', 'Sakit'])) $color = 'info';

            $events[] = [
                'id' => 'absen-' . $absen->id,
                'title' => $absen->status . ($absen->jam_masuk ? ' (' . $absen->jam_masuk->format('H:i') . ')' : ''),
                'start' => $absen->tanggal->format('Y-m-d') . ($absen->jam_masuk ? 'T' . $absen->jam_masuk->format('H:i:s') : ''),
                'end' => $absen->tanggal->format('Y-m-d') . ($absen->jam_pulang ? 'T' . $absen->jam_pulang->format('H:i:s') : ''),
                'allDay' => $absen->jam_masuk ? false : true,
                'extendedProps' => [
                    'calendar' => $color,
                    'description' => $absen->keterangan ?? 'Absensi Shift: ' . ($absen->shift->nama ?? '-'),
                ]
            ];
        }

        // Holiday Events
        $holidays = \App\Models\HariLibur::whereBetween('tanggal', [$start, $end])->get();
        foreach ($holidays as $holiday) {
            $events[] = [
                'id' => 'holiday-' . $holiday->id,
                'title' => 'Libur: ' . $holiday->nama,
                'start' => $holiday->tanggal->format('Y-m-d'),
                'allDay' => true,
                'extendedProps' => [
                    'calendar' => 'danger',
                    'description' => $holiday->deskripsi ?? 'Libur Nasional',
                ]
            ];
        }

        return response()->json($events);
    }
}
