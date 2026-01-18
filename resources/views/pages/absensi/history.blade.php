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
      <div class="row mb-4">
         @php
            $hadir = $data->where('status', 'Hadir')->count();
            $terlambat = $data->where('status', 'Terlambat')->count();
            $izin = $data->whereIn('status', ['Izin', 'Cuti', 'Sakit'])->count();
         @endphp
         <div class="col-md-4">
            <div class="card bg-success text-white">
               <div class="card-body">
                  <h3 class="mb-0">{{ $hadir }}</h3>
                  <span>Hadir Tepat Waktu</span>
               </div>
            </div>
         </div>
         <div class="col-md-4">
            <div class="card bg-warning text-white">
               <div class="card-body">
                  <h3 class="mb-0">{{ $terlambat }}</h3>
                  <span>Terlambat</span>
               </div>
            </div>
         </div>
         <div class="col-md-4">
            <div class="card bg-info text-white">
               <div class="card-body">
                  <h3 class="mb-0">{{ $izin }}</h3>
                  <span>Izin/Cuti/Sakit</span>
               </div>
            </div>
         </div>
      </div>

      <!-- Table -->
      <div class="card">
         <div class="card-header">
            <h5 class="mb-0">
               Riwayat {{ \Carbon\Carbon::create()->month($bulan)->locale('id')->isoFormat('MMMM') }} {{ $tahun }}
            </h5>
         </div>
         <div class="table-responsive">
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
      </div>
   </div>
@endsection
