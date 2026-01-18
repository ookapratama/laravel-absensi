@extends('layouts/layoutMaster')

@section('title', 'Edit Kantor')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Data Master / Kantor /</span> Edit
         </h4>
      </div>

      <div class="row">
         <div class="col-md-8 mx-auto">
            <div class="card">
               <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">Edit Kantor: {{ $data->nama }}</h5>
                  <a href="{{ route('kantor.index') }}" class="btn btn-sm btn-outline-secondary">
                     <i class="ri-arrow-left-line me-1"></i>Kembali
                  </a>
               </div>
               <div class="card-body">
                  <form action="{{ route('kantor.update', $data->id) }}" method="POST">
                     @csrf
                     @method('PUT')
                     <div class="row">
                        <div class="col-md-6 mb-3">
                           <label class="form-label" for="kode">Kode Kantor</label>
                           <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode"
                              name="kode" value="{{ old('kode', $data->kode) }}">
                           @error('kode')
                              <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                           <label class="form-label" for="nama">Nama Kantor <span class="text-danger">*</span></label>
                           <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                              name="nama" value="{{ old('nama', $data->nama) }}" required>
                           @error('nama')
                              <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                     </div>

                     <div class="mb-3">
                        <label class="form-label" for="alamat">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2">{{ old('alamat', $data->alamat) }}</textarea>
                        @error('alamat')
                           <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                     </div>

                     <div class="row">
                        <div class="col-md-4 mb-3">
                           <label class="form-label" for="latitude">Latitude <span class="text-danger">*</span></label>
                           <input type="number" step="any"
                              class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude"
                              value="{{ old('latitude', $data->latitude) }}" required>
                           @error('latitude')
                              <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label" for="longitude">Longitude <span class="text-danger">*</span></label>
                           <input type="number" step="any"
                              class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude"
                              value="{{ old('longitude', $data->longitude) }}" required>
                           @error('longitude')
                              <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                           <label class="form-label" for="radius_meter">Radius (meter)</label>
                           <input type="number" class="form-control @error('radius_meter') is-invalid @enderror"
                              id="radius_meter" name="radius_meter" value="{{ old('radius_meter', $data->radius_meter) }}"
                              min="10" max="5000">
                           @error('radius_meter')
                              <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                     </div>

                     <div class="mb-3">
                        <a href="https://maps.google.com/?q={{ $data->latitude }},{{ $data->longitude }}" target="_blank"
                           class="btn btn-outline-info btn-sm">
                           <i class="ri-map-pin-line me-1"></i>Lihat di Google Maps
                        </a>
                     </div>

                     <div class="mb-3">
                        <div class="form-check form-switch">
                           <input class="form-check-input" type="checkbox" id="is_aktif" name="is_aktif" value="1"
                              {{ old('is_aktif', $data->is_aktif) ? 'checked' : '' }}>
                           <label class="form-check-label" for="is_aktif">Aktifkan Kantor</label>
                        </div>
                     </div>

                     <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">Update</button>
                        <a href="{{ route('kantor.index') }}" class="btn btn-label-secondary">Batal</a>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
@endsection
