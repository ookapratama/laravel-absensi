@extends('layouts/layoutMaster')

@section('title', 'Dashboard Absensi')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Absensi /</span> Dashboard
         </h4>
         <div>
            <input type="date" class="form-control" id="filter-tanggal" value="{{ $tanggal }}">
         </div>
      </div>

      <!-- Statistics Cards -->
      <div class="row mb-4">
         <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card h-100">
               <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between">
                     <div>
                        <span class="text-muted">Total Pegawai</span>
                        <h3 class="mb-0">{{ $statistik['total_pegawai'] }}</h3>
                     </div>
                     <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded bg-label-primary">
                           <i class="ri-group-line"></i>
                        </span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card h-100">
               <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between">
                     <div>
                        <span class="text-muted">Sudah Absen</span>
                        <h3 class="mb-0 text-success">{{ $statistik['sudah_absen'] }}</h3>
                     </div>
                     <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded bg-label-success">
                           <i class="ri-check-line"></i>
                        </span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card h-100">
               <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between">
                     <div>
                        <span class="text-muted">Belum Absen</span>
                        <h3 class="mb-0 text-warning">{{ $statistik['belum_absen'] }}</h3>
                     </div>
                     <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded bg-label-warning">
                           <i class="ri-time-line"></i>
                        </span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card h-100">
               <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between">
                     <div>
                        <span class="text-muted">Terlambat</span>
                        <h3 class="mb-0 text-danger">{{ $statistik['terlambat'] }}</h3>
                     </div>
                     <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded bg-label-danger">
                           <i class="ri-alarm-warning-line"></i>
                        </span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="row">
         <!-- Rekap Per Divisi -->
         <div class="col-lg-6 mb-4">
            <div class="card h-100">
               <div class="card-header">
                  <h5 class="mb-0"><i class="ri-pie-chart-line me-2"></i>Rekap Per Divisi</h5>
               </div>
               <div class="table-responsive">
                  <table class="table">
                     <thead>
                        <tr>
                           <th>Divisi</th>
                           <th class="text-center">Hadir</th>
                           <th class="text-center">Terlambat</th>
                           <th class="text-center">Izin</th>
                        </tr>
                     </thead>
                     <tbody>
                        @forelse($rekapDivisi as $rekap)
                           <tr>
                              <td>{{ $rekap->divisi }}</td>
                              <td class="text-center"><span class="badge bg-success">{{ $rekap->hadir }}</span></td>
                              <td class="text-center"><span class="badge bg-warning">{{ $rekap->terlambat }}</span></td>
                              <td class="text-center"><span class="badge bg-info">{{ $rekap->izin }}</span></td>
                           </tr>
                        @empty
                           <tr>
                              <td colspan="4" class="text-center text-muted">Belum ada data</td>
                           </tr>
                        @endforelse
                     </tbody>
                  </table>
               </div>
            </div>
         </div>

         <!-- Belum Absen -->
         <div class="col-lg-6 mb-4">
            <div class="card h-100">
               <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0"><i class="ri-user-unfollow-line me-2 text-warning"></i>Belum Absen Hari Ini</h5>
                  <span class="badge bg-warning">{{ $belumAbsen->count() }}</span>
               </div>
               <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                  @forelse($belumAbsen as $pegawai)
                     <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-sm me-3">
                           <img src="{{ $pegawai->foto_url }}" alt="{{ $pegawai->nama_lengkap }}" class="rounded-circle">
                        </div>
                        <div class="flex-grow-1">
                           <h6 class="mb-0">{{ $pegawai->nama_lengkap }}</h6>
                           <small class="text-muted">{{ $pegawai->divisi->nama ?? '-' }}</small>
                        </div>
                        <span class="badge bg-label-warning">Belum Absen</span>
                     </div>
                  @empty
                     <div class="text-center text-muted py-4">
                        <i class="ri-check-double-line ri-3x text-success mb-2"></i>
                        <p class="mb-0">Semua pegawai sudah absen!</p>
                     </div>
                  @endforelse
               </div>
            </div>
         </div>
      </div>

      <!-- Data Absensi Hari Ini -->
      <div class="card">
         <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ri-list-check-2 me-2"></i>Data Absensi Hari Ini</h5>
            <a href="{{ route('absensi.rekap') }}" class="btn btn-sm btn-outline-primary">
               <i class="ri-file-excel-line me-1"></i>Rekap Bulanan
            </a>
         </div>
         <div class="table-responsive">
            <table class="table table-hover">
               <thead>
                  <tr>
                     <th>#</th>
                     <th>Pegawai</th>
                     <th>Divisi</th>
                     <th>Jam Masuk</th>
                     <th>Jam Pulang</th>
                     <th>Lokasi</th>
                     <th>Status</th>
                  </tr>
               </thead>
               <tbody>
                  @forelse($sudahAbsen as $index => $absen)
                     <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                           <div class="d-flex align-items-center">
                              <div class="avatar avatar-sm me-2">
                                 <img src="{{ $absen->pegawai->foto_url }}" alt="" class="rounded-circle">
                              </div>
                              <div>
                                 <strong>{{ $absen->pegawai->nama_lengkap }}</strong>
                                 <br><small class="text-muted">{{ $absen->pegawai->nip ?? '-' }}</small>
                              </div>
                           </div>
                        </td>
                        <td>{{ $absen->pegawai->divisi->nama ?? '-' }}</td>
                        <td>
                           @if ($absen->jam_masuk)
                              {{ $absen->jam_masuk->format('H:i:s') }}
                              @if ($absen->foto_masuk)
                                 <a href="{{ $absen->foto_masuk_url }}" target="_blank" class="ms-1">
                                    <i class="ri-image-line text-primary"></i>
                                 </a>
                              @endif
                           @else
                              <span class="text-muted">-</span>
                           @endif
                        </td>
                        <td>
                           @if ($absen->jam_pulang)
                              {{ $absen->jam_pulang->format('H:i:s') }}
                              @if ($absen->foto_pulang)
                                 <a href="{{ $absen->foto_pulang_url }}" target="_blank" class="ms-1">
                                    <i class="ri-image-line text-primary"></i>
                                 </a>
                              @endif
                           @else
                              <span class="text-muted">-</span>
                           @endif
                        </td>
                        <td>{{ $absen->lokasi_masuk ?? '-' }}</td>
                        <td>
                           <span
                              class="badge bg-{{ $absen->status === 'Hadir' ? 'success' : ($absen->status === 'Terlambat' ? 'warning' : 'info') }}">
                              {{ $absen->status }}
                           </span>
                        </td>
                     </tr>
                  @empty
                     <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                           <i class="ri-inbox-line ri-3x mb-2"></i>
                           <p class="mb-0">Belum ada data absensi hari ini</p>
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
      document.getElementById('filter-tanggal').addEventListener('change', function() {
         window.location.href = '{{ route('absensi.dashboard') }}?tanggal=' + this.value;
      });
   </script>
@endsection
