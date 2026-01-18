@extends('layouts/layoutMaster')

@section('title', 'Tambah Kantor')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Data Master / Kantor /</span> Tambah
         </h4>
      </div>

      <div class="row">
         <div class="col-md-8 mx-auto">
            <div class="card">
               <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">Form Tambah Kantor</h5>
                  <a href="{{ route('kantor.index') }}" class="btn btn-sm btn-outline-secondary">
                     <i class="ri-arrow-left-line me-1"></i>Kembali
                  </a>
               </div>
               <div class="card-body">
                  <form action="{{ route('kantor.store') }}" method="POST">
                     @csrf
                     <div class="row">
                        <div class="col-md-6 mb-3">
                           <label class="form-label" for="kode">Kode Kantor</label>
                           <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode"
                              name="kode" value="{{ old('kode') }}" placeholder="HQ">
                           @error('kode')
                              <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                           <label class="form-label" for="nama">Nama Kantor <span class="text-danger">*</span></label>
                           <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                              name="nama" value="{{ old('nama') }}" placeholder="Kantor Pusat" required>
                           @error('nama')
                              <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                     </div>

                     <div class="mb-3">
                        <label class="form-label" for="alamat">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2"
                           placeholder="Jl. Sudirman No. 123, Jakarta">{{ old('alamat') }}</textarea>
                        @error('alamat')
                           <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                     </div>

                     <div class="row">
                        <div class="col-md-4 mb-3">
                           <label class="form-label" for="latitude">Latitude <span class="text-danger">*</span></label>
                           <input type="number" step="any"
                              class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude"
                              value="{{ old('latitude') }}" placeholder="-6.2088" required>
                           @error('latitude')
                              <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label" for="longitude">Longitude <span class="text-danger">*</span></label>
                           <input type="number" step="any"
                              class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude"
                              value="{{ old('longitude') }}" placeholder="106.8456" required>
                           @error('longitude')
                              <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label" for="radius_meter">Radius (meter)</label>
                           <input type="number" class="form-control @error('radius_meter') is-invalid @enderror"
                              id="radius_meter" name="radius_meter" value="{{ old('radius_meter', 100) }}" min="10"
                              max="5000">
                           @error('radius_meter')
                              <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                           <small class="text-muted">Radius untuk validasi absensi (10 - 5000 meter)</small>
                        </div>
                     </div>

                     <div class="mb-3">
                        <button type="button" class="btn btn-outline-info btn-sm" id="btn-get-location">
                           <i class="ri-map-pin-line me-1"></i>Ambil Lokasi Saya
                        </button>
                     </div>

                     <div class="mb-3">
                        <div class="form-check form-switch">
                           <input class="form-check-input" type="checkbox" id="is_aktif" name="is_aktif" value="1"
                              {{ old('is_aktif', 1) ? 'checked' : '' }}>
                           <label class="form-check-label" for="is_aktif">Aktifkan Kantor</label>
                        </div>
                     </div>

                     <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">Simpan</button>
                        <button type="reset" class="btn btn-label-secondary">Reset</button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
@endsection

@section('page-script')
   <script>
      document.getElementById('btn-get-location').addEventListener('click', function() {
         if (navigator.geolocation) {
            this.innerHTML = '<i class="ri-loader-4-line ri-spin me-1"></i>Mengambil...';
            this.disabled = true;

            navigator.geolocation.getCurrentPosition(
               (position) => {
                  document.getElementById('latitude').value = position.coords.latitude.toFixed(8);
                  document.getElementById('longitude').value = position.coords.longitude.toFixed(8);
                  this.innerHTML = '<i class="ri-check-line me-1"></i>Lokasi Diambil';
                  setTimeout(() => {
                     this.innerHTML = '<i class="ri-map-pin-line me-1"></i>Ambil Lokasi Saya';
                     this.disabled = false;
                  }, 2000);
               },
               (error) => {
                  alert('Gagal mengambil lokasi: ' + error.message);
                  this.innerHTML = '<i class="ri-map-pin-line me-1"></i>Ambil Lokasi Saya';
                  this.disabled = false;
               }, {
                  enableHighAccuracy: true
               }
            );
         } else {
            alert('Geolocation tidak didukung browser ini');
         }
      });
   </script>
@endsection
