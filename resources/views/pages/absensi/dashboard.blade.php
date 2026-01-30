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
                           <th class="text-center">Telat</th>
                           <th class="text-center">Izin</th>
                           <th class="text-center">Jam Kerja</th>
                        </tr>
                     </thead>
                     <tbody>
                        @forelse($rekapDivisi as $rekap)
                           <tr>
                              <td>{{ $rekap->divisi }}</td>
                              <td class="text-center"><span class="badge bg-success">{{ $rekap->hadir }}</span></td>
                              <td class="text-center"><span class="badge bg-warning">{{ $rekap->terlambat }}</span></td>
                              <td class="text-center"><span class="badge bg-info">{{ $rekap->izin }}</span></td>
                              <td class="text-center text-primary fw-bold">{{ $rekap->total_jam_format }}</td>
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
                     <th>Shift</th>
                     <th class="text-center">Jam Masuk</th>
                     <th class="text-center">Jam Pulang</th>
                     <th class="text-center">Durasi</th>
                     <th>Lokasi</th>
                     <th class="text-center">Status</th>
                     <th>Keterangan</th>
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
                        <td>
                           @if ($absen->shift)
                              <span class="badge bg-label-secondary">{{ $absen->shift->nama }}</span>
                           @else
                              -
                           @endif
                        </td>
                        <td class="text-center">
                           @if ($absen->jam_masuk)
                              <span class="fw-medium">{{ $absen->jam_masuk->format('H:i') }}</span>
                              @if ($absen->foto_masuk)
                                 <a href="javascript:void(0);"
                                    onclick="previewFoto('{{ $absen->foto_masuk_url }}', 'Foto Masuk - {{ $absen->pegawai->nama_lengkap }}')"
                                    class="ms-1">
                                    <i class="ri-image-line text-primary"></i>
                                 </a>
                              @endif
                           @else
                              <span class="text-muted">-</span>
                           @endif
                        </td>
                        <td class="text-center">
                           @if ($absen->jam_pulang)
                              <span class="fw-medium">{{ $absen->jam_pulang->format('H:i') }}</span>
                              @if ($absen->foto_pulang)
                                 <a href="javascript:void(0);"
                                    onclick="previewFoto('{{ $absen->foto_pulang_url }}', 'Foto Pulang - {{ $absen->pegawai->nama_lengkap }}')"
                                    class="ms-1">
                                    <i class="ri-image-line text-primary"></i>
                                 </a>
                              @endif
                           @else
                              <span class="text-muted">-</span>
                           @endif
                        </td>
                        <td class="text-center">
                           <strong>{{ $absen->durasi_shift }}</strong>
                        </td>
                        <td>{{ $absen->lokasi_masuk ?? '-' }}</td>
                        <td class="text-center">
                           <span
                              class="badge bg-{{ $absen->status === 'Hadir' ? 'success' : ($absen->status === 'Terlambat' ? 'warning' : 'info') }}">
                              {{ $absen->status }}
                           </span>
                        </td>
                        <td>
                           <small class="text-muted">{{ $absen->keterangan ?? '-' }}</small>
                        </td>
                     </tr>
                  @empty
                     <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
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

   <!-- Modal Preview Foto (Premium Style) -->
   <div class="modal fade animate__animated animate__fadeIn" id="modalPreviewFoto" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content bg-transparent shadow-none border-0">
            <div class="modal-header border-0 p-0 mb-3 justify-content-end">
               <button type="button" class="btn btn-icon btn-light rounded-circle shadow-lg" data-bs-dismiss="modal"
                  aria-label="Close" style="width: 40px; height: 40px;">
                  <i class="ri-close-line ri-xl text-dark"></i>
               </button>
            </div>
            <div class="modal-body p-0 text-center">
               <div class="position-relative overflow-hidden rounded-4 shadow-2xl">
                  <!-- Header Label (Glassmorphism) -->
                  <div id="modal-photo-title"
                     class="position-absolute top-0 start-50 translate-middle-x mt-3 px-4 py-2 rounded-pill shadow-lg"
                     style="z-index: 10; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); color: white; font-weight: 600; letter-spacing: 0.5px; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                  </div>

                  <img src="" id="foto-preview" class="img-fluid w-100 shadow-lg"
                     style="max-height: 85vh; object-fit: contain; background: #000; border-radius: 12px;">

                  <!-- Quality Badge -->
                  <div
                     class="position-absolute bottom-0 end-0 mb-3 me-3 px-2 py-1 bg-dark bg-opacity-50 text-white rounded small"
                     style="font-size: 10px;">
                     <i class="ri-shield-check-line me-1"></i>Verified Attendance Photo
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <style>
      .modal-backdrop.show {
         backdrop-filter: blur(8px);
         -webkit-backdrop-filter: blur(8px);
         background-color: rgba(0, 0, 0, 0.6);
      }

      .shadow-2xl {
         box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      }
   </style>
@endsection

@section('page-script')
   <script>
      window.addEventListener('load', function() {
         document.getElementById('filter-tanggal').addEventListener('change', function() {
            window.location.href = '{{ route('absensi.dashboard') }}?tanggal=' + this.value;
         });
      });

      function previewFoto(url, title) {
         const modal = new bootstrap.Modal(document.getElementById('modalPreviewFoto'));
         document.getElementById('foto-preview').src = url;
         document.getElementById('modal-photo-title').textContent = title;
         modal.show();
      }
   </script>
@endsection
