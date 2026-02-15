<table>
   <thead>
      <tr>
         <th rowspan="2"
            style="background-color: #f2f2f2; border: 1px solid #000; text-align: center; vertical-align: middle;">No
         </th>
         <th rowspan="2"
            style="background-color: #f2f2f2; border: 1px solid #000; text-align: center; vertical-align: middle;">Nama
         </th>
         <th colspan="{{ 6 + $jenisIzins->count() }}"
            style="background-color: #d9ead3; border: 1px solid #000; text-align: center;">Kehadiran Hari</th>
         <th colspan="7" style="background-color: #fce5cd; border: 1px solid #000; text-align: center;">Waktu Kerja
         </th>
      </tr>
      <tr>
         <!-- Kehadiran Hari Sub-columns -->
         <th style="background-color: #d9ead3; border: 1px solid #000;">Hadir</th>
         <th style="background-color: #d9ead3; border: 1px solid #000;">Absen</th>
         <th style="background-color: #d9ead3; border: 1px solid #000;">Tepat Waktu</th>
         @foreach ($jenisIzins as $j)
            <th style="background-color: #d9ead3; border: 1px solid #000;">{{ $j->nama }}</th>
         @endforeach
         <th style="background-color: #d9ead3; border: 1px solid #000;">Libur</th>
         <th style="background-color: #d9ead3; border: 1px solid #000;">Persentase</th>
         <th style="background-color: #d9ead3; border: 1px solid #000;">Scoring</th>

         <!-- Waktu Kerja Sub-columns -->
         <th style="background-color: #fce5cd; border: 1px solid #000;">Denda Keterlambatan</th>
         <th style="background-color: #fce5cd; border: 1px solid #000;">Jam Masuk</th>
         <th style="background-color: #fce5cd; border: 1px solid #000;">Terlambat</th>
         <th style="background-color: #fce5cd; border: 1px solid #000;">Durasi Menit</th>
         <th style="background-color: #fce5cd; border: 1px solid #000;">Akumulasi Kehadiran</th>
         <th style="background-color: #fce5cd; border: 1px solid #000;">Akumulasi Jam Kerja</th>
      </tr>
   </thead>
   <tbody>
      @foreach ($data as $index => $pegawai)
         @php
            $absensis = $pegawai->absensis;

            // Hadir: Total unique days present (Tepat Waktu or Terlambat, must have checked out)
            $hadirCount = $absensis
                ->whereIn('status', ['Tepat Waktu', 'Terlambat'])
                ->whereNotNull('jam_pulang')
                ->unique(fn($i) => $i->tanggal->format('Y-m-d'))
                ->count();

            // Tepat Waktu: Total sessions with Tepat Waktu status
            $tepatWaktuCount = $absensis->where('status', 'Tepat Waktu')->whereNotNull('jam_pulang')->count();

            // Terlambat: Total sessions with Terlambat status
            $terlambatCount = $absensis->where('status', 'Terlambat')->whereNotNull('jam_pulang')->count();

            // Izin counts per type
            $izinCounts = [];
            foreach ($jenisIzins as $j) {
                $izinCounts[$j->nama] = $absensis
                    ->where('status', $j->nama)
                    ->unique(fn($i) => $i->tanggal->format('Y-m-d'))
                    ->count();
            }

            // Total unique days covered (Hadir + any Izin/Sakit/Cuti)
            $daysActive = $absensis
                ->filter(function ($item) {
                    if (in_array($item->status, ['Izin', 'Sakit', 'Cuti'])) {
                        return true;
                    }
                    // Check other leave types too
                    return in_array($item->status, $GLOBALS['leaveTypes'] ?? []);
                })
                ->merge($absensis->whereNotNull('jam_pulang'))
                ->unique(fn($i) => $i->tanggal->format('Y-m-d'))
                ->count();

            // Alpha (Absen)
            $alphaCount = max(0, $hariEfektif - $daysActive);

            // Percentage
            $persentase = $hariEfektif > 0 ? round(($daysActive / $hariEfektif) * 100, 2) : 0;

            // Duration work
            $totalMenit = $absensis->sum('durasi_kerja_menit');
            $totalJam = floor($totalMenit / 60);
            $sisaMenit = $totalMenit % 60;
            $durasiFormat = "{$totalJam} Jam " . ($sisaMenit > 0 ? "{$sisaMenit} Menit" : '');
         @endphp
         <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000;">{{ $pegawai->nama_lengkap }}</td>

            <!-- Kehadiran Hari Values -->
            <td style="border: 1px solid #000; text-align: center;">{{ $hadirCount }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $alphaCount }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $tepatWaktuCount }}</td>
            @foreach ($jenisIzins as $j)
               <td style="border: 1px solid #000; text-align: center;">{{ $izinCounts[$j->nama] ?? 0 }}</td>
            @endforeach
            <td style="border: 1px solid #000; text-align: center;">{{ $totalLibur }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $persentase }}%</td>
            <td style="border: 1px solid #000; text-align: center;">0,00</td>

            <!-- Waktu Kerja Values -->
            <td style="border: 1px solid #000; text-align: center;">0</td>
            <td style="border: 1px solid #000; text-align: center;">0</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $terlambatCount }}</td>
            <td style="border: 1px solid #000; text-align: center;">0</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $hadirCount }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $durasiFormat }}</td>
         </tr>
      @endforeach
   </tbody>
</table>
