@extends('layouts/layoutMaster')

@section('title', 'Rekap Absensi')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Absensi /</span> Rekap Bulanan
         </h4>
         <a href="{{ route('absensi.dashboard') }}" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i>Kembali ke Dashboard
         </a>
      </div>

      <!-- Filter -->
      <div class="card mb-4">
         <div class="card-body">
            <form method="GET" action="{{ route('absensi.rekap') }}" class="row g-3">
               <div class="col-md-4">
                  <label class="form-label">Bulan</label>
                  <select name="bulan" class="form-select">
                     @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                           {{ \Carbon\Carbon::create()->month($i)->locale('id')->isoFormat('MMMM') }}
                        </option>
                     @endfor
                  </select>
               </div>
               <div class="col-md-4">
                  <label class="form-label">Tahun</label>
                  <select name="tahun" class="form-select">
                     @for ($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}
                        </option>
                     @endfor
                  </select>
               </div>
               <div class="col-md-4 d-flex align-items-end gap-2">
                  <button type="submit" class="btn btn-primary">
                     <i class="ri-filter-line me-1"></i>Filter
                  </button>
                  <button type="button" class="btn btn-success" onclick="exportExcel()">
                     <i class="ri-file-excel-line me-1"></i>Export Excel
                  </button>
               </div>
            </form>
         </div>
      </div>

      <!-- Rekap Table -->
      <div class="card">
         <div class="card-header">
            <h5 class="mb-0">
               Rekap {{ \Carbon\Carbon::create()->month($bulan)->locale('id')->isoFormat('MMMM') }} {{ $tahun }}
            </h5>
         </div>
         <div class="table-responsive">
            <table class="table table-bordered table-hover" id="rekap-table">
               <thead class="table-light">
                  <tr>
                     <th rowspan="2" class="align-middle text-center">#</th>
                     <th rowspan="2" class="align-middle">Pegawai</th>
                     <th rowspan="2" class="align-middle">Divisi</th>
                     <th colspan="4" class="text-center">Rekap</th>
                     <th rowspan="2" class="align-middle text-center">%</th>
                  </tr>
                  <tr>
                     <th class="text-center bg-success text-white">Hadir</th>
                     <th class="text-center bg-warning text-dark">Terlambat</th>
                     <th class="text-center bg-info text-white">Izin</th>
                     <th class="text-center bg-danger text-white">Alpha</th>
                  </tr>
               </thead>
               <tbody>
                  @php
                     $hariKerja = \Carbon\Carbon::create($tahun, $bulan, 1)->daysInMonth;
                     // Simple calculation - you may want to exclude weekends
                  @endphp
                  @forelse(\App\Models\Pegawai::aktif()->with(['divisi', 'absensis' => function($q) use ($bulan, $tahun) {
                           $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
                       }])->get() as $index => $pegawai)
                     @php
                        $absensis = $pegawai->absensis;
                        $hadir = $absensis->where('status', 'Hadir')->count();
                        $terlambat = $absensis->where('status', 'Terlambat')->count();
                        $izin = $absensis->whereIn('status', ['Izin', 'Cuti', 'Sakit'])->count();
                        $totalAbsen = $hadir + $terlambat + $izin;
                        $alpha = max(0, $hariKerja - $totalAbsen);
                        $persentase = $hariKerja > 0 ? round((($hadir + $terlambat) / $hariKerja) * 100, 1) : 0;
                     @endphp
                     <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                           <div class="d-flex align-items-center">
                              <div class="avatar avatar-sm me-2">
                                 <img src="{{ $pegawai->foto_url }}" alt="" class="rounded-circle">
                              </div>
                              <div>
                                 <strong>{{ $pegawai->nama_lengkap }}</strong>
                                 <br><small class="text-muted">{{ $pegawai->nip ?? '-' }}</small>
                              </div>
                           </div>
                        </td>
                        <td>{{ $pegawai->divisi->nama ?? '-' }}</td>
                        <td class="text-center"><span class="badge bg-success">{{ $hadir }}</span></td>
                        <td class="text-center"><span class="badge bg-warning">{{ $terlambat }}</span></td>
                        <td class="text-center"><span class="badge bg-info">{{ $izin }}</span></td>
                        <td class="text-center"><span class="badge bg-danger">{{ $alpha }}</span></td>
                        <td class="text-center">
                           <div class="progress" style="height: 20px;">
                              <div class="progress-bar bg-success" style="width: {{ $persentase }}%">
                                 {{ $persentase }}%
                              </div>
                           </div>
                        </td>
                     </tr>
                  @empty
                     <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                           <i class="ri-file-list-line ri-3x mb-2"></i>
                           <p class="mb-0">Tidak ada data pegawai</p>
                        </td>
                     </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </div>
   </div>
@endsection

@section('page-script')
   <script>
      function exportExcel() {
         const bulan = document.querySelector('select[name="bulan"]').value;
         const tahun = document.querySelector('select[name="tahun"]').value;

         // For now, show alert - you can implement actual export later
         window.AlertHandler.info('Fitur export Excel akan segera tersedia');
      }
   </script>
@endsection
