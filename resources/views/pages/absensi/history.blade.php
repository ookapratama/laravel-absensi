@extends('layouts/layoutMaster')

@section('title', 'Riwayat Absensi')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Absensi /</span> Riwayat
         </h4>
         <a href="{{ route('absensi.index') }}" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i>Kembali
         </a>
      </div>

      <!-- Filter -->
      <div class="card mb-4">
         <div class="card-body">
            <form method="GET" action="{{ route('absensi.history') }}" class="row g-3">
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
               <div class="col-md-4 d-flex align-items-end">
                  <button type="submit" class="btn btn-primary">
                     <i class="ri-filter-line me-1"></i>Filter
                  </button>
               </div>
            </form>
         </div>
      </div>

      <!-- Stats -->
      <div class="row mb-4 g-3">
         @php
            $hadir = $data->where('status', 'Hadir')->count();
            $terlambat = $data->where('status', 'Terlambat')->count();
            $izin = $data->whereIn('status', ['Izin', 'Cuti', 'Sakit'])->count();
         @endphp
         <div class="col-md-4 col-sm-6">
            <div class="card bg-label-success">
               <div class="card-body">
                  <h3 class="mb-0 text-success">{{ $hadir }}</h3>
                  <span class="text-success">Hadir Tepat Waktu</span>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-sm-6">
            <div class="card bg-label-warning">
               <div class="card-body">
                  <h3 class="mb-0 text-warning">{{ $terlambat }}</h3>
                  <span class="text-warning">Terlambat</span>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-sm-12">
            <div class="card bg-label-info">
               <div class="card-body">
                  <h3 class="mb-0 text-info">{{ $izin }}</h3>
                  <span class="text-info">Izin/Cuti/Sakit</span>
               </div>
            </div>
         </div>
      </div>

      <!-- Table -->
      <div class="card">
         <div class="card-header border-bottom">
            <h5 class="mb-0">
               Riwayat {{ \Carbon\Carbon::create()->month($bulan)->locale('id')->isoFormat('MMMM') }} {{ $tahun }}
            </h5>
         </div>

         <!-- Table View (Desktop & Large Tablets) -->
         <div class="table-responsive d-none d-lg-block">
            <table class="table table-hover">
               <thead>
                  <tr>
                     <th>Tanggal</th>
                     <th>Jam Masuk</th>
                     <th>Jam Pulang</th>
                     <th>Lokasi Masuk</th>
                     <th>Status</th>
                     <th>Keterangan</th>
                  </tr>
               </thead>
               <tbody>
                  @forelse($data as $absen)
                     <tr>
                        <td>
                           <strong>{{ $absen->tanggal->locale('id')->isoFormat('ddd, D MMM Y') }}</strong>
                        </td>
                        <td>
                           @if ($absen->jam_masuk)
                              {{ $absen->jam_masuk->format('H:i:s') }}
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
                        <td>
                           @if ($absen->jam_pulang)
                              {{ $absen->jam_pulang->format('H:i:s') }}
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
                        <td>{{ $absen->lokasi_masuk ?? '-' }}</td>
                        <td>
                           <span
                              class="badge bg-{{ $absen->status === 'Hadir' ? 'success' : ($absen->status === 'Terlambat' ? 'warning' : 'info') }}">
                              {{ $absen->status }}
                           </span>
                        </td>
                        <td>{{ $absen->keterangan ?? '-' }}</td>
                     </tr>
                  @empty
                     <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                           <i class="ri-calendar-line ri-3x mb-2"></i>
                           <p class="mb-0">Belum ada data absensi bulan ini</p>
                        </td>
                     </tr>
                  @endforelse
               </tbody>
            </table>
         </div>

         <!-- Card View (Mobile & Mini Tablets) -->
         <div class="d-block d-lg-none p-3">
            @forelse($data as $absen)
               <div class="card mb-3 shadow-sm">
                  <div class="card-body p-3">
                     <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                           <strong
                              class="text-primary d-block mb-1">{{ $absen->tanggal->locale('id')->isoFormat('ddd, D MMM Y') }}</strong>
                        </div>
                        <span
                           class="badge bg-{{ $absen->status === 'Hadir' ? 'success' : ($absen->status === 'Terlambat' ? 'warning' : 'info') }}">
                           {{ $absen->status }}
                        </span>
                     </div>

                     <div class="row g-3 mb-2">
                        <div class="col-6">
                           <small class="text-muted d-block mb-1"><i class="ri-login-circle-line me-1"></i>Jam
                              Masuk</small>
                           <div class="d-flex align-items-center">
                              @if ($absen->jam_masuk)
                                 <span class="me-2 fw-semibold">{{ $absen->jam_masuk->format('H:i:s') }}</span>
                                 @if ($absen->foto_masuk)
                                    <a href="javascript:void(0);"
                                       onclick="previewFoto('{{ $absen->foto_masuk_url }}', 'Foto Masuk')"
                                       class="text-primary">
                                       <i class="ri-image-line"></i>
                                    </a>
                                 @endif
                              @else
                                 <span class="text-muted">-</span>
                              @endif
                           </div>
                        </div>
                        <div class="col-6">
                           <small class="text-muted d-block mb-1"><i class="ri-logout-circle-line me-1"></i>Jam
                              Pulang</small>
                           <div class="d-flex align-items-center">
                              @if ($absen->jam_pulang)
                                 <span class="me-2 fw-semibold">{{ $absen->jam_pulang->format('H:i:s') }}</span>
                                 @if ($absen->foto_pulang)
                                    <a href="javascript:void(0);"
                                       onclick="previewFoto('{{ $absen->foto_pulang_url }}', 'Foto Pulang')"
                                       class="text-primary">
                                       <i class="ri-image-line"></i>
                                    </a>
                                 @endif
                              @else
                                 <span class="text-muted">-</span>
                              @endif
                           </div>
                        </div>
                     </div>

                     @if ($absen->lokasi_masuk)
                        <div class="mb-2 pb-2 border-bottom">
                           <small class="text-muted d-block mb-1"><i class="ri-map-pin-line me-1"></i>Lokasi</small>
                           <span class="d-block small text-truncate"
                              style="max-width: 100%;">{{ $absen->lokasi_masuk }}</span>
                        </div>
                     @endif

                     @if ($absen->keterangan)
                        <div class="mt-2">
                           <small class="text-muted d-block mb-1"><i class="ri-file-text-line me-1"></i>Keterangan</small>
                           <p class="mb-0 small">{{ $absen->keterangan }}</p>
                        </div>
                     @endif
                  </div>
               </div>
            @empty
               <div class="text-center py-5 text-muted">
                  <i class="ri-calendar-line ri-3x mb-3 d-block"></i>
                  <p class="mb-0">Belum ada data absensi bulan ini</p>
               </div>
            @endforelse
         </div>
      </div>
   </div>
   </div>

   <!-- Modal Preview Foto -->
   <div class="modal fade animate__animated animate__fadeIn" id="modalPreviewFoto" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content bg-transparent shadow-none border-0">
            <div class="modal-header border-0 p-0 mb-3 justify-content-end">
               <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                  aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center">
               <div class="position-relative">
                  <div id="modal-photo-title"
                     class="position-absolute top-0 start-50 translate-middle-x bg-dark bg-opacity-50 text-white px-3 py-1 rounded-bottom small"
                     style="z-index: 10;"></div>
                  <img src="" id="foto-preview"
                     class="img-fluid rounded-3 shadow-lg border border-3 border-white"
                     style="max-height: 85vh; border-radius: 15px !important;">
               </div>
            </div>
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
      function previewFoto(url, title) {
         const modal = new bootstrap.Modal(document.getElementById('modalPreviewFoto'));
         document.getElementById('foto-preview').src = url;
         document.getElementById('modal-photo-title').textContent = title;
         modal.show();
      }
   </script>
@endsection
