<?php

namespace App\Services;

use App\Helpers\LocationHelper;
use App\Models\Absensi;
use App\Models\Pegawai;
use App\Repositories\AbsensiRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsensiService extends BaseService
{
    protected FileUploadService $fileUploadService;

    public function __construct(
        AbsensiRepository $repository,
        FileUploadService $fileUploadService
    ) {
        parent::__construct($repository);
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Get absensi by pegawai dan tanggal
     */
    public function getByPegawaiTanggal($pegawaiId, string $tanggal)
    {
        return $this->repository->getByPegawaiTanggal($pegawaiId, $tanggal);
    }

    /**
     * Get absensi by pegawai dan bulan
     */
    public function getByPegawaiBulan($pegawaiId, $bulan, $tahun)
    {
        return $this->repository->getByPegawaiBulan($pegawaiId, $bulan, $tahun);
    }

    /**
     * Get list pegawai yang belum absen hari ini
     */
    public function getBelumAbsenHariIni()
    {
        return $this->repository->getBelumAbsenHariIni();
    }

    /**
     * Get absensi hari ini
     */
    public function getAbsensiHariIni()
    {
        return $this->repository->getAbsensiHariIni();
    }

    /**
     * Proses absen masuk
     */
    public function absenMasuk(Pegawai $pegawai, array $data)
    {
        // Check apakah sudah absen masuk hari ini
        $existing = $this->getByPegawaiTanggal($pegawai->id, today()->toDateString());
        if ($existing && $existing->jam_masuk) {
            throw new \Exception('Anda sudah absen masuk hari ini.');
        }

        // Validasi lokasi
        $validasiLokasi = $this->validateLocation(
            $pegawai,
            $data['latitude'],
            $data['longitude']
        );

        if (!$validasiLokasi['valid']) {
            throw new \Exception($validasiLokasi['message']);
        }

        // Validasi Waktu Shift
        $shift = $pegawai->shift;
        
        if (!$shift) {
            throw new \Exception("Anda belum ditempatkan pada shift kerja. Silakan hubungi admin.");
        }

        if (!$shift->is_aktif) {
            throw new \Exception("Shift kerja Anda sedang dinonaktifkan. Silakan hubungi admin.");
        }

        $now = now();
        $currentTime = Carbon::parse($now->format('H:i:s'));
        
        $jamMasuk = Carbon::parse($shift->jam_masuk->format('H:i:s'));
        $jamPulang = Carbon::parse($shift->jam_pulang->format('H:i:s'));
        
        // Batas awal absen masuk: 2 jam sebelum jam_masuk
        $batasAwal = $jamMasuk->copy()->subHours(2);
        // Batas akhir absen masuk: Jam Pulang shift
        $batasAkhir = $jamPulang->copy();
        
        // Handle shift cross-day (misal masuk 22:00 pulang 06:00)
        $isCrossDay = $jamPulang->lt($jamMasuk);
        
        $isValid = false;
        if (!$isCrossDay) {
            $isValid = $currentTime->between($batasAwal, $batasAkhir);
        } else {
            // Jika shift malam, misal 22:00 - 06:00
            // Batas awal 20:00. 
            // Antara 20:00 - 23:59 ATAU 00:00 - 06:00
            $isValid = $currentTime->gte($batasAwal) || $currentTime->lte($batasAkhir);
        }

        if (!$isValid) {
            $msg = "Bukan waktu absen untuk shift Anda ({$shift->nama}: {$shift->jam_masuk->format('H:i')} - {$shift->jam_pulang->format('H:i')}).";
            if ($currentTime->lt($batasAwal)) {
                $msg .= " Absen masuk dibuka mulai pukul " . $batasAwal->format('H:i') . ".";
            }
            throw new \Exception($msg);
        }

        // Determine status (Hadir/Terlambat)
        $status = $this->determineStatus($pegawai, now());

        // Upload foto
        $fotoPath = null;
        if (isset($data['foto'])) {
            $media = $this->fileUploadService->upload($data['foto'], 'absensi/masuk', 'public', [
                'width' => 640,
                'height' => 480,
                'quality' => 70,
            ]);
            $fotoPath = $media->path;
        }

        // Create or update absensi
        $absensiData = [
            'pegawai_id' => $pegawai->id,
            'tanggal' => today(),
            'jam_masuk' => now()->format('H:i:s'),
            'foto_masuk' => $fotoPath,
            'latitude_masuk' => $data['latitude'],
            'longitude_masuk' => $data['longitude'],
            'lokasi_masuk' => $validasiLokasi['kantor_nama'] ?? null,
            'device_masuk' => $data['device'] ?? request()->header('User-Agent'),
            'status' => $status,
        ];

        if ($existing) {
            $existing->update($absensiData);
            return $existing->fresh();
        }

        return $this->create($absensiData);
    }

    /**
     * Proses absen pulang
     */
    public function absenPulang(Pegawai $pegawai, array $data)
    {
        // Check apakah sudah absen masuk hari ini
        $existing = $this->getByPegawaiTanggal($pegawai->id, today()->toDateString());
        if (!$existing || !$existing->jam_masuk) {
            throw new \Exception('Anda belum absen masuk hari ini.');
        }

        if ($existing->jam_pulang) {
            throw new \Exception('Anda sudah absen pulang hari ini.');
        }

        // Validasi lokasi
        $validasiLokasi = $this->validateLocation(
            $pegawai,
            $data['latitude'],
            $data['longitude']
        );

        if (!$validasiLokasi['valid']) {
            throw new \Exception($validasiLokasi['message']);
        }

        // Upload foto
        $fotoPath = null;
        if (isset($data['foto'])) {
            $media = $this->fileUploadService->upload($data['foto'], 'absensi/pulang', 'public', [
                'width' => 640,
                'height' => 480,
                'quality' => 70,
            ]);
            $fotoPath = $media->path;
        }

        // Update absensi
        $existing->update([
            'jam_pulang' => now()->format('H:i:s'),
            'foto_pulang' => $fotoPath,
            'latitude_pulang' => $data['latitude'],
            'longitude_pulang' => $data['longitude'],
            'lokasi_pulang' => $validasiLokasi['kantor_nama'] ?? null,
            'device_pulang' => $data['device'] ?? request()->header('User-Agent'),
        ]);

        return $existing->fresh();
    }

    /**
     * Validasi lokasi pegawai
     */
    public function validateLocation(Pegawai $pegawai, $latitude, $longitude): array
    {
        // Get lokasi yang diizinkan untuk pegawai
        $lokasiDiizinkan = $pegawai->lokasiAbsenAktif()->get();

        // Jika tidak ada lokasi yang di-assign, gunakan kantor utama
        if ($lokasiDiizinkan->isEmpty() && $pegawai->kantor) {
            $lokasiDiizinkan = collect([$pegawai->kantor]);
        }

        if ($lokasiDiizinkan->isEmpty()) {
            return [
                'valid' => false,
                'message' => 'Tidak ada lokasi absensi yang ditentukan untuk Anda.',
            ];
        }

        // Check apakah dalam radius salah satu lokasi
        $locations = $lokasiDiizinkan->map(fn($k) => [
            'id' => $k->id,
            'nama' => $k->nama,
            'latitude' => $k->latitude,
            'longitude' => $k->longitude,
            'radius_meter' => $k->radius_meter,
        ])->toArray();

        $nearest = LocationHelper::getNearestLocation($latitude, $longitude, $locations);

        if (!$nearest) {
            return [
                'valid' => false,
                'message' => 'Gagal memvalidasi lokasi.',
            ];
        }

        if (!$nearest['is_within_radius']) {
            $distance = LocationHelper::formatDistance($nearest['distance']);
            return [
                'valid' => false,
                'message' => "Anda berada di luar radius absensi. Jarak ke {$nearest['location']['nama']}: {$distance} (Radius: {$nearest['location']['radius_meter']}m)",
                'distance' => $nearest['distance'],
                'kantor_nama' => $nearest['location']['nama'],
            ];
        }

        return [
            'valid' => true,
            'message' => 'Lokasi valid.',
            'distance' => $nearest['distance'],
            'kantor_id' => $nearest['location']['id'],
            'kantor_nama' => $nearest['location']['nama'],
        ];
    }

    /**
     * Determine status kehadiran (Hadir/Terlambat)
     */
    protected function determineStatus(Pegawai $pegawai, Carbon $waktuAbsen): string
    {
        $shift = $pegawai->shift;
        $divisi = $pegawai->divisi;

        // Jika tidak punya shift, atau shift tidak punya jam masuk, anggap Hadir
        if (!$shift || !$shift->jam_masuk) {
            return 'Hadir';
        }

        // Ambil jam masuk dari shift
        $jamMasuk = Carbon::parse($shift->jam_masuk->format('H:i:s'));
        
        // Toleransi tetap dari divisi
        $toleransi = $divisi->toleransi_terlambat ?? 0;
        $jamMasukDenganToleransi = $jamMasuk->copy()->addMinutes($toleransi);

        $waktuAbsenTime = Carbon::parse($waktuAbsen->format('H:i:s'));

        if ($waktuAbsenTime->gt($jamMasukDenganToleransi)) {
            return 'Terlambat';
        }

        return 'Hadir';
    }

    /**
     * Get rekap absensi per divisi
     */
    public function getRekapPerDivisi(string $tanggal = null)
    {
        $tanggal = $tanggal ?? today()->toDateString();

        return DB::table('absensis')
            ->join('pegawais', 'absensis.pegawai_id', '=', 'pegawais.id')
            ->join('divisis', 'pegawais.divisi_id', '=', 'divisis.id')
            ->whereDate('absensis.tanggal', $tanggal)
            ->select(
                'divisis.id',
                'divisis.nama as divisi',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN absensis.status = 'Hadir' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN absensis.status = 'Terlambat' THEN 1 ELSE 0 END) as terlambat"),
                DB::raw("SUM(CASE WHEN absensis.status IN ('Izin', 'Cuti', 'Sakit') THEN 1 ELSE 0 END) as izin")
            )
            ->groupBy('divisis.id', 'divisis.nama')
            ->get();
    }

    /**
     * Get statistik absensi
     */
    public function getStatistik(string $tanggal = null)
    {
        $tanggal = $tanggal ?? today()->toDateString();
        $totalPegawai = Pegawai::aktif()->count();

        $absensis = Absensi::whereDate('tanggal', $tanggal)->get();

        return [
            'total_pegawai' => $totalPegawai,
            'sudah_absen' => $absensis->whereNotNull('jam_masuk')->count(),
            'belum_absen' => $totalPegawai - $absensis->whereNotNull('jam_masuk')->count(),
            'hadir' => $absensis->where('status', 'Hadir')->count(),
            'terlambat' => $absensis->where('status', 'Terlambat')->count(),
            'izin' => $absensis->whereIn('status', ['Izin', 'Cuti', 'Sakit'])->count(),
        ];
    }
    /**
     * Get statistik absensi untuk satu pegawai dalam satu bulan
     */
    public function getStatistikPegawai($pegawaiId, $bulan, $tahun)
    {
        $absensis = $this->repository->getByPegawaiBulan($pegawaiId, $bulan, $tahun);

        return [
            'hadir' => $absensis->where('status', 'Hadir')->count(),
            'terlambat' => $absensis->where('status', 'Terlambat')->count(),
            'izin' => $absensis->whereIn('status', ['Izin', 'Cuti', 'Sakit'])->count(),
            'alfa' => 0, // Bisa dikembangkan dengan membandingkan hari kerja vs jumlah absen
            'total_hari_kerja' => $absensis->count(),
        ];
    }
}
