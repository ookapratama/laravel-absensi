@extends('layouts/layoutMaster')

@section('title', 'Detail Pegawai')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Data Master / Pegawai /</span> Detail Profil
         </h4>
         <div class="d-flex gap-2">
            <a href="{{ route('pegawai.index') }}" class="btn btn-outline-secondary">
               <i class="ri-arrow-left-line me-1"></i>Kembali
            </a>
            <a href="{{ route('pegawai.edit', $data->id) }}" class="btn btn-primary">
               <i class="ri-pencil-line me-1"></i>Edit Data
            </a>
         </div>
      </div>

      <div class="row">
         <!-- Sisi Kiri: Profil & Kontak -->
         <div class="col-md-5 col-lg-4">
            <div class="card mb-4 mt-3">
               <div class="card-body">
                  <div class="text-center mb-4">
                     <div class="avatar avatar-xl mx-auto mb-3" style="width: 120px; height: 120px;">
                        <img src="{{ $data->foto_url }}" alt="{{ $data->nama_lengkap }}" class="rounded-circle shadow-sm"
                           style="object-fit: cover; border: 3px solid #eee;">
                     </div>
                     <h4 class="mb-1 fw-bold">{{ $data->nama_lengkap }}</h4>
                     <p class="text-muted small mb-0">{{ $data->jabatan ?? 'Pegawai' }}</p>
                     @if ($data->status_aktif)
                        <span class="badge bg-label-success mt-2">Aktif</span>
                     @else
                        <span class="badge bg-label-secondary mt-2">Non-Aktif</span>
                     @endif
                  </div>

                  <div class="pt-2 border-top">
                     <h6 class="text-uppercase small fw-bold text-muted mb-3">Informasi Kontak</h6>
                     <div class="mb-3">
                        <small class="text-muted d-block">NIP</small>
                        <span class="fw-semibold">{{ $data->nip ?? '-' }}</span>
                     </div>
                     <div class="mb-3">
                        <small class="text-muted d-block">Email</small>
                        <span class="fw-semibold">{{ $data->user->email ?? '-' }}</span>
                     </div>
                     <div class="mb-3">
                        <small class="text-muted d-block">No. Telepon</small>
                        <span class="fw-semibold">{{ $data->no_telp ?? '-' }}</span>
                     </div>
                     <div class="mb-0">
                        <small class="text-muted d-block">Alamat</small>
                        <span class="fw-semibold small">{{ $data->alamat ?? '-' }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Sisi Kanan: Penempatan & Statistik -->
         <div class="col-md-7 col-lg-8">
            <!-- Penempatan -->
            <div class="card mb-4 mt-3">
               <div class="card-header d-flex justify-content-between align-items-center py-3">
                  <h6 class="mb-0 fw-bold"><i class="ri-briefcase-line me-2"></i>Penempatan & Jadwal</h6>
                  <span class="badge bg-info small">Hadir Sejak
                     {{ $data->tgl_masuk ? $data->tgl_masuk->format('d/m/Y') : '-' }}</span>
               </div>
               <div class="card-body">
                  <div class="col-sm-4 mb-3">
                     <div class="p-3 bg-light border-start border-primary border-4 rounded shadow-sm">
                        <small class="text-muted d-block mb-1">Kantor Utama</small>
                        <h6 class="mb-0 fw-bold">{{ $data->kantor->nama ?? '-' }}</h6>
                     </div>
                  </div>
                  <div class="col-sm-4 mb-3">
                     <div class="p-3 bg-light border-start border-success border-4 rounded shadow-sm">
                        <small class="text-muted d-block mb-1">Divisi Pekerjaan</small>
                        <h6 class="mb-0 fw-bold">{{ $data->divisi->nama ?? '-' }}</h6>
                     </div>
                  </div>
                  <div class="col-sm-4 mb-3">
                     <div class="p-3 bg-light border-start border-info border-4 rounded shadow-sm">
                        <small class="text-muted d-block mb-1">Informasi Shift</small>
                        <h6 class="mb-0 fw-bold">Jadwal Fleksibel</h6>
                     </div>
                  </div>
                  <div class="col-12 mt-2">
                     <div class="alert alert-primary mb-0 py-3">
                        <h6 class="alert-heading fw-bold mb-2"><i class="ri-time-line me-1"></i> Daftar Shift di Divisi
                           {{ $data->divisi->nama }}</h6>
                        <div class="row g-2">
                           @foreach ($data->divisi->shifts->where('is_aktif', true) as $s)
                              <div class="col-md-6 col-lg-4">
                                 <div class="bg-white p-2 rounded shadow-sm border">
                                    <div class="fw-bold small">{{ $s->nama }}</div>
                                    <div class="text-muted" style="font-size: 11px;">{{ $s->jam_masuk->format('H:i') }} -
                                       {{ $s->jam_pulang->format('H:i') }}</div>
                                 </div>
                              </div>
                           @endforeach
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Lokasi Absen -->
         <div class="card mb-4">
            <div class="card-header py-3">
               <h6 class="mb-0 fw-bold"><i class="ri-map-pin-line me-2"></i>Akses Lokasi Absensi</h6>
            </div>
            <div class="table-responsive">
               <table class="table table-sm table-hover mb-0">
                  <thead class="table-light">
                     <tr>
                        <th>Nama Area</th>
                        <th class="text-center">Radius</th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse($data->lokasiAbsen as $lokasi)
                        <tr>
                           <td>
                              <div class="fw-bold">{{ $lokasi->nama }}</div>
                              <small class="text-muted">{{ $lokasi->latitude }}, {{ $lokasi->longitude }}</small>
                           </td>
                           <td class="text-center align-middle">
                              <span class="badge bg-label-info">{{ $lokasi->radius_meter }}m</span>
                           </td>
                        </tr>
                     @empty
                        <tr>
                           <td colspan="2" class="text-center py-3 text-muted">Belum ada lokasi khusus yg diatur</td>
                        </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>

         <!-- Statistik -->
         <div class="card border-0 shadow-none bg-primary bg-opacity-10">
            <div class="card-body">
               @php
                  $absensi = $data
                      ->absensis()
                      ->whereMonth('tanggal', date('m'))
                      ->whereYear('tanggal', date('Y'))
                      ->get();
                  $hadir = $absensi->where('status', 'Hadir')->count();
                  $terlambat = $absensi->where('status', 'Terlambat')->count();
                  $izin = $absensi->whereIn('status', ['Izin', 'Cuti', 'Sakit'])->count();
               @endphp
               <h6 class="fw-bold text-primary mb-3">Statistik Kehadiran Bulan Ini</h6>
               <div class="row g-3">
                  <div class="col-4">
                     <div class="bg-white p-3 rounded text-center shadow-sm">
                        <h3 class="fw-bold text-success mb-0">{{ $hadir }}</h3>
                        <small class="text-muted">Hadir</small>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="bg-white p-3 rounded text-center shadow-sm">
                        <h3 class="fw-bold text-warning mb-0">{{ $terlambat }}</h3>
                        <small class="text-muted">Telat</small>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="bg-white p-3 rounded text-center shadow-sm">
                        <h3 class="fw-bold text-info mb-0">{{ $izin }}</h3>
                        <small class="text-muted">Izin/Cuti</small>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   </div>
@endsection
