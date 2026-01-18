@extends('layouts/layoutMaster')

@section('title', 'Detail Pegawai')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Data Master / Pegawai /</span> Detail
         </h4>
      </div>

      <div class="row">
         <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card mb-4">
               <div class="card-body text-center">
                  <div class="avatar avatar-xl mx-auto mb-3">
                     <img src="{{ $data->foto_url }}" alt="{{ $data->nama_lengkap }}" class="rounded-circle"
                        style="width: 100px; height: 100px; object-fit: cover;">
                  </div>
                  <h4 class="mb-1">{{ $data->nama_lengkap }}</h4>
                  <p class="text-muted mb-0">{{ $data->jabatan ?? 'Staff' }}</p>

                  @if ($data->status_aktif)
                     <span class="badge bg-success mt-2">Aktif</span>
                  @else
                     <span class="badge bg-secondary mt-2">Non-Aktif</span>
                  @endif
               </div>
            </div>

            <!-- Info Card -->
            <div class="card mb-4">
               <div class="card-header">
                  <h6 class="mb-0"><i class="ri-information-line me-2"></i>Informasi</h6>
               </div>
               <div class="card-body">
                  <ul class="list-unstyled mb-0">
                     <li class="mb-3">
                        <span class="text-muted d-block mb-1">NIP</span>
                        <strong>{{ $data->nip ?? '-' }}</strong>
                     </li>
                     <li class="mb-3">
                        <span class="text-muted d-block mb-1">Email</span>
                        <strong>{{ $data->user->email ?? '-' }}</strong>
                     </li>
                     <li class="mb-3">
                        <span class="text-muted d-block mb-1">No. Telepon</span>
                        <strong>{{ $data->no_telp ?? '-' }}</strong>
                     </li>
                     <li class="mb-3">
                        <span class="text-muted d-block mb-1">Jenis Kelamin</span>
                        <strong>{{ $data->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</strong>
                     </li>
                     <li class="mb-3">
                        <span class="text-muted d-block mb-1">Tanggal Masuk</span>
                        <strong>{{ $data->tgl_masuk ? $data->tgl_masuk->format('d F Y') : '-' }}</strong>
                     </li>
                     <li class="mb-0">
                        <span class="text-muted d-block mb-1">Alamat</span>
                        <strong>{{ $data->alamat ?? '-' }}</strong>
                     </li>
                  </ul>
               </div>
            </div>
         </div>

         <div class="col-md-8">
            <!-- Kerja Card -->
            <div class="card mb-4">
               <div class="card-header">
                  <h6 class="mb-0"><i class="ri-building-line me-2"></i>Informasi Kerja</h6>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <span class="text-muted d-block mb-1">Divisi</span>
                        <strong>{{ $data->divisi->nama ?? '-' }}</strong>
                        @if ($data->divisi)
                           <br><small class="text-muted">
                              Jam: {{ $data->divisi->jam_masuk ? $data->divisi->jam_masuk->format('H:i') : '-' }} -
                              {{ $data->divisi->jam_pulang ? $data->divisi->jam_pulang->format('H:i') : '-' }}
                           </small>
                        @endif
                     </div>
                     <div class="col-md-6 mb-3">
                        <span class="text-muted d-block mb-1">Kantor Utama</span>
                        <strong>{{ $data->kantor->nama ?? '-' }}</strong>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Lokasi Absen Card -->
            <div class="card mb-4">
               <div class="card-header">
                  <h6 class="mb-0"><i class="ri-map-pin-line me-2"></i>Lokasi Absensi yang Diizinkan</h6>
               </div>
               <div class="card-body">
                  @if ($data->lokasiAbsen->count() > 0)
                     <div class="row">
                        @foreach ($data->lokasiAbsen as $lokasi)
                           <div class="col-md-6 mb-3">
                              <div class="d-flex align-items-center">
                                 <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                       <i class="ri-building-2-line"></i>
                                    </span>
                                 </div>
                                 <div>
                                    <h6 class="mb-0">{{ $lokasi->nama }}</h6>
                                    <small class="text-muted">Radius: {{ $lokasi->radius_meter }}m</small>
                                 </div>
                              </div>
                           </div>
                        @endforeach
                     </div>
                  @else
                     <p class="text-muted mb-0">Belum ada lokasi absensi yang diatur</p>
                  @endif
               </div>
            </div>

            <!-- Statistik Absensi -->
            <div class="card">
               <div class="card-header d-flex justify-content-between align-items-center">
                  <h6 class="mb-0"><i class="ri-bar-chart-line me-2"></i>Statistik Absensi Bulan Ini</h6>
               </div>
               <div class="card-body">
                  @php
                     $absensi = $data
                         ->absensis()
                         ->whereMonth('tanggal', now()->month)
                         ->whereYear('tanggal', now()->year)
                         ->get();
                     $hadir = $absensi->where('status', 'Hadir')->count();
                     $terlambat = $absensi->where('status', 'Terlambat')->count();
                     $izin = $absensi->whereIn('status', ['Izin', 'Cuti', 'Sakit'])->count();
                  @endphp
                  <div class="row text-center">
                     <div class="col-4">
                        <div class="p-3 bg-success bg-opacity-10 rounded">
                           <h3 class="text-success mb-0">{{ $hadir }}</h3>
                           <small>Hadir</small>
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="p-3 bg-warning bg-opacity-10 rounded">
                           <h3 class="text-warning mb-0">{{ $terlambat }}</h3>
                           <small>Terlambat</small>
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="p-3 bg-info bg-opacity-10 rounded">
                           <h3 class="text-info mb-0">{{ $izin }}</h3>
                           <small>Izin/Cuti</small>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="mt-4">
         <a href="{{ route('pegawai.edit', $data->id) }}" class="btn btn-primary">
            <i class="ri-pencil-line me-1"></i>Edit Pegawai
         </a>
         <a href="{{ route('pegawai.index') }}" class="btn btn-outline-secondary">Kembali</a>
      </div>
   </div>
@endsection
