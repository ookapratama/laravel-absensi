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
    public function getBelumAbsenHariIni($tanggal = null)
    {
        return $this->repository->getBelumAbsenHariIni($tanggal);
    }

    /**
     * Get absensi hari ini
     */
    public function getAbsensiHariIni($tanggal = null)
    {
        return $this->repository->getAbsensiHariIni($tanggal);
    }

    /**
     * Proses absen masuk
     */
    /**
     * Proses absen masuk
     */
    /**
     * Proses absen masuk
     */
    public function absenMasuk(Pegawai $pegawai, array $data)
    {
        $shiftId = $data['shift_id'] ?? null;
        if (!$shiftId) {
            throw new \Exception('Shift tidak valid.');
        }

        $shift = \App\Models\Shift::find($shiftId);
        if (!$shift || !$shift->is_aktif) {
            throw new \Exception('Shift tidak ditemukan atau tidak aktif.');
        }

        // Check apakah ada sesi yang masih "MENGGANTUNG" (sudah masuk tapi belum pulang) secara global
        $activeSession = Absensi::where('pegawai_id', $pegawai->id)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->first();

        // Check apakah masih ada sesi aktif di shift mana pun
        // REVISI USER: Perbolehkan masuk shift baru meskipun shift sebelumnya belum pulang (irisan)
        /*
        if ($activeSession) {
             $shiftName = $activeSession->shift ? $activeSession->shift->nama : 'sebelumnya';
             throw new \Exception("Anda masih memiliki sesi aktif di {$shiftName}. Silakan absen pulang terlebih dahulu.");
        }
        */

        // Check apakah sudah absen masuk untuk shift ini (untuk mencegah double entry di shift yang sama)
        // REVISI: Check sesi terbuka atau sesi yang baru saja selesai hari ini
        $existing = Absensi::where('pegawai_id', $pegawai->id)
            ->where('shift_id', $shiftId)
            ->where(function($q) {
                $q->whereNull('jam_pulang') // Sesi yang masih terbuka (bisa dari kemarin)
                  ->orWhereDate('tanggal', today()); // Atau sudah absen hari ini
            })
            ->first();

        if ($existing && $existing->jam_masuk) {
            if (!$existing->jam_pulang) {
                throw new \Exception('Anda masih memiliki sesi aktif untuk shift ini yang belum ditutup.');
            }
            throw new \Exception('Anda sudah absen masuk pada shift ini hari ini.');
        }

        // VALIDASI WAKTU MASUK
        $now = now();
        $jamMasuk = Carbon::parse($shift->jam_masuk->format('H:i:s'));
        $jamPulang = Carbon::parse($shift->jam_pulang->format('H:i:s'));

        // Handle Cross-Day Shift (Misal: 20:00 - 04:00)
        $isCrossDay = $jamPulang->lt($jamMasuk);
        if ($isCrossDay) {
             // Jika cross-day, dan jam sekarang < jam pulang, berarti masih sesi malam kemarin (seharusnya).
             // Tapi karena kita "one session per shift", kita fokus ke rentang HARI INI.
             // Namun untuk cross-day yang dimulai HARI INI (malam), jam pulang dianggap besok.
             $jamPulang->addDay();
        }

        // 1. Tidak boleh absen jika sudah lewat jam pulang (Waktu Shift Habis)
        // Kita bandingkan jika sekarang sudah jauh melewati jam pulang
        if ($now->gt($jamPulang)) {
             throw new \Exception('Waktu shift ini sudah berakhir.');
        }

        // 2. Tidak boleh absen terlalu awal (Misal: 2 Jam sebelum shift mulai)
        // Jika cross-day (Masuk 20:00), maka batas awal 18:00.
        // Jika sekarang jam 07:00 pagi, jelas belum bisa.
        $batasAwal = $jamMasuk->copy()->subHours(2);
        
        // Perlu logika khusus untuk membandingkan jam jika rentang hari
        // Kita sederhanakan dengan membandingkan diffInHours atau time string jika hari sama
        if (!$isCrossDay && $now->lt($batasAwal)) {
             throw new \Exception('Absen masuk belum dibuka untuk shift ini (Dibuka: ' . $batasAwal->format('H:i') . ').');
        } 
        // Logic Cross Day lebih kompleks: jika jam sekarang siang (misal 12.00) dan shift mulai 20.00
        // batas awal 18.00. 12.00 < 18.00 -> belum buka.
        // Tapi jika jam sekarang 01.00 (dini hari), itu masuk sesi kemarin atau besok?
        // Untuk "absen masuk", kita asumsikan pegawai mulai kerja. Jadi harus mendekati jam masuk.

        // Validasi lokasi
        $validasiLokasi = $this->validateLocation(
            $pegawai,
            $data['latitude'],
            $data['longitude']
        );

        if (!$validasiLokasi['valid']) {
            throw new \Exception($validasiLokasi['message']);
        }

        // Determine status (Hadir/Terlambat) based on selected shift
        $status = $this->determineStatusByShift($pegawai, $shift, now());

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

        // Create absensi
        $absensiData = [
            'pegawai_id' => $pegawai->id,
            'shift_id' => $shiftId,
            'tanggal' => today(),
            'jam_masuk' => now()->format('H:i:s'),
            'foto_masuk' => $fotoPath,
            'latitude_masuk' => $data['latitude'],
            'longitude_masuk' => $data['longitude'],
            'lokasi_masuk' => $validasiLokasi['kantor_nama'] ?? null,
            'device_masuk' => $data['device'] ?? request()->header('User-Agent'),
            'status' => $status,
        ];

        return $this->create($absensiData);
    }

    /**
     * Proses absen pulang
     */
    public function absenPulang(Pegawai $pegawai, array $data)
    {
        // Check apakah sudah absen masuk hari ini
        // PERBAIKAN: Harus mencari absensi yang OPEN (masuk tapi belum pulang)
        // Jika pegawai punya multiple shift, kita harus tahu mana yang mau dipulangkan.
        // Karena di UI tombol "Pulang" menempel di kartu shift, kita sebaiknya terima shift_id juga di sini.
        // Tapi untuk kompatibilitas, kita cari yang belum pulang.

        $shiftId = $data['shift_id'] ?? null;
        
        $query = Absensi::where('pegawai_id', $pegawai->id)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang');

        if ($shiftId) {
            $query->where('shift_id', $shiftId);
        }

        // Cari sesi terbaru yang belum pulang (bisa dari hari ini atau kemarin)
        $existing = $query->orderBy('tanggal', 'desc')->orderBy('jam_masuk', 'desc')->first();

        if (!$existing) {
             throw new \Exception('Tidak ditemukan data absen masuk yang aktif (belum pulang)' . ($shiftId ? ' untuk shift ini.' : '.'));
        }

        // VALIDASI WAKTU PULANG
        $shift = $existing->shift;
        if ($shift) {
            $now = now();
            $jamPulang = Carbon::parse($shift->jam_pulang->format('H:i:s'));
            
            // Handle Cross Day logic for check out
            // Jika jam pulang < jam masuk (misal masuk 20.00, pulang 04.00)
            if (Carbon::parse($shift->jam_masuk->format('H:i:s'))->gt($jamPulang)) {
                // Jika sekarang jam > jam masuk (misal 21.00), berarti jam pulang adalah besok
                if ($now->format('H:i:s') > $shift->jam_masuk->format('H:i:s')) {
                    $jamPulang->addDay();
                }
            }

            // Aturan Ketat: Tidak boleh pulang sebelum jam pulang
            if ($now->lt($jamPulang)) {
                $diff = $now->diffInMinutes($jamPulang);
                $hours = floor($diff / 60);
                $mins = $diff % 60;
                $waitTime = ($hours > 0 ? "{$hours} jam " : "") . "{$mins} menit";
                
                throw new \Exception("Belum waktunya jam pulang. Silakan tunggu {$waitTime} lagi (Jadwal: " . $jamPulang->format('H:i') . ").");
            }
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
     * Determine status kehadiran (Hadir/Terlambat) based on specific shift
     */
    protected function determineStatusByShift(Pegawai $pegawai, $shift, Carbon $waktuAbsen): string
    {
        // Jika shift tidak punya jam masuk, anggap Hadir
        if (!$shift || !$shift->jam_masuk) {
            return 'Hadir';
        }

        // Ambil jam masuk dari shift
        $jamMasuk = Carbon::parse($shift->jam_masuk->format('H:i:s'));
        
        // Toleransi tetap dari divisi pegawai (atau bisa di-override per shift nantinya)
        $divisi = $pegawai->divisi;
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

        $data = DB::table('absensis')
            ->join('pegawais', 'absensis.pegawai_id', '=', 'pegawais.id')
            ->join('divisis', 'pegawais.divisi_id', '=', 'divisis.id')
            ->leftJoin('shifts', 'absensis.shift_id', '=', 'shifts.id')
            ->whereDate('absensis.tanggal', $tanggal)
            ->select(
                'divisis.id',
                'divisis.nama as divisi',
                DB::raw('COUNT(absensis.id) as total_sesi'),
                DB::raw("COUNT(DISTINCT CASE WHEN absensis.status = 'Hadir' AND absensis.jam_pulang IS NOT NULL THEN absensis.pegawai_id END) as hadir"),
                DB::raw("COUNT(DISTINCT CASE WHEN absensis.status = 'Terlambat' AND absensis.jam_pulang IS NOT NULL THEN absensis.pegawai_id END) as terlambat"),
                DB::raw("COUNT(DISTINCT CASE WHEN absensis.status IN ('Izin', 'Cuti', 'Sakit') THEN absensis.pegawai_id END) as izin"),
                DB::raw("SUM(
                    CASE 
                        WHEN absensis.jam_masuk IS NOT NULL AND absensis.jam_pulang IS NOT NULL AND shifts.id IS NOT NULL THEN
                            CASE 
                                WHEN shifts.jam_pulang < shifts.jam_masuk THEN
                                    TIMESTAMPDIFF(MINUTE, shifts.jam_masuk, DATE_ADD(shifts.jam_pulang, INTERVAL 1 DAY))
                                ELSE
                                    TIMESTAMPDIFF(MINUTE, shifts.jam_masuk, shifts.jam_pulang)
                            END
                        ELSE 0 
                    END
                ) as total_menit")
            )
            ->groupBy('divisis.id', 'divisis.nama')
            ->get();

        return $data->map(function($item) {
            $jam = floor($item->total_menit / 60);
            $menit = $item->total_menit % 60;
            $item->total_jam_format = "{$jam}h" . ($menit > 0 ? " {$menit}m" : "");
            return $item;
        });
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
            'sudah_absen' => $absensis->whereNotNull('jam_masuk')->unique('pegawai_id')->count(),
            'belum_absen' => $totalPegawai - $absensis->whereNotNull('jam_masuk')->unique('pegawai_id')->count(),
            'hadir' => $absensis->where('status', 'Hadir')->whereNotNull('jam_pulang')->unique('pegawai_id')->count(),
            'terlambat' => $absensis->where('status', 'Terlambat')->whereNotNull('jam_pulang')->unique('pegawai_id')->count(),
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
            'hadir' => $absensis->where('status', 'Hadir')->whereNotNull('jam_pulang')->unique(fn($i) => $i->tanggal->format('Y-m-d'))->count(),
            'terlambat' => $absensis->where('status', 'Terlambat')->whereNotNull('jam_pulang')->unique(fn($i) => $i->tanggal->format('Y-m-d'))->count(),
            'izin' => $absensis->whereIn('status', ['Izin', 'Cuti', 'Sakit'])->count(),
            'alfa' => 0, // Bisa dikembangkan dengan membandingkan hari kerja vs jumlah absen
            'total_hari_kerja' => $absensis->unique(fn($i) => $i->tanggal->format('Y-m-d'))->count(),
        ];
    }
}
