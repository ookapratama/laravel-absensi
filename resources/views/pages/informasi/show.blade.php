@extends('layouts/layoutMaster')

@section('title', $informasi->judul)

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Informasi /</span> Detail
         </h4>
         <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i> Kembali
         </a>
      </div>

      <div class="row justify-content-center">
         <div class="col-lg-8">
            <div class="card overflow-hidden">
               <img class="img-fluid" src="{{ $informasi->gambar_url }}" alt="Card image cap"
                  style="width: 100%; max-height: 400px; object-fit: cover;">
               <div class="card-body p-sm-7 p-4">
                  <div class="d-flex justify-content-between mb-4">
                     <div class="d-flex align-items-center">
                        <i class="ri-user-line me-1 ri-20px"></i>
                        <span class="fw-medium">{{ $informasi->user->name ?? 'System' }}</span>
                     </div>
                     <div class="d-flex align-items-center">
                        <i class="ri-calendar-line me-1 ri-20px"></i>
                        <span class="text-muted">{{ $informasi->created_at->format('d M Y, H:i') }}</span>
                     </div>
                  </div>
                  <h2 class="mb-4">{{ $informasi->judul }}</h2>
                  <div class="text-body mt-4 informasi-content" style="line-height: 1.8;">
                     {!! $informasi->isi !!}
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <style>
      .informasi-content img {
         max-width: 100%;
         height: auto;
         border-radius: 8px;
      }
   </style>
@endsection
