@php
   $configData = Helper::appClasses();
@endphp
@extends('layouts/layoutMaster')

@section('title', 'Dashboard - Logistics')

@section('vendor-style')
   @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss', 'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss'])
@endsection

@section('page-style')
   @vite('resources/assets/vendor/scss/pages/app-logistics-dashboard.scss')
@endsection

@section('vendor-script')
   @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('page-script')
   @vite('resources/assets/js/app-logistics-dashboard.js')
@endsection

@section('content')
   <!-- Card Border Shadow -->
   <div class="row g-6">
      <div class="col-sm-6 col-lg-3">
         <div class="card card-border-shadow-primary h-100">
            <div class="card-body">
               <div class="d-flex align-items-center mb-2">
                  <div class="avatar me-4">
                     <span class="avatar-initial rounded-3 bg-label-primary"><i class="ri-car-line ri-24px"></i></span>
                  </div>
                  <h4 class="mb-0">42</h4>
               </div>
               <h6 class="mb-0 fw-normal">On route vehicles</h6>
               <p class="mb-0">
                  <span class="me-1 fw-medium">+18.2%</span>
                  <small class="text-muted">than last week</small>
               </p>
            </div>
         </div>
      </div>
      <div class="col-sm-6 col-lg-3">
         <div class="card card-border-shadow-warning h-100">
            <div class="card-body">
               <div class="d-flex align-items-center mb-2">
                  <div class="avatar me-4">
                     <span class="avatar-initial rounded-3 bg-label-warning"><i class='ri-alert-line ri-24px'></i></span>
                  </div>
                  <h4 class="mb-0">8</h4>
               </div>
               <h6 class="mb-0 fw-normal">Vehicles with errors</h6>
               <p class="mb-0">
                  <span class="me-1 fw-medium">-8.7%</span>
                  <small class="text-muted">than last week</small>
               </p>
            </div>
         </div>
      </div>
      <div class="col-sm-6 col-lg-3">
         <div class="card card-border-shadow-danger h-100">
            <div class="card-body">
               <div class="d-flex align-items-center mb-2">
                  <div class="avatar me-4">
                     <span class="avatar-initial rounded-3 bg-label-danger"><i class='ri-route-line ri-24px'></i></span>
                  </div>
                  <h4 class="mb-0">27</h4>
               </div>
               <h6 class="mb-0 fw-normal">Deviated from route</h6>
               <p class="mb-0">
                  <span class="me-1 fw-medium">+4.3%</span>
                  <small class="text-muted">than last week</small>
               </p>
            </div>
         </div>
      </div>
      <div class="col-sm-6 col-lg-3">
         <div class="card card-border-shadow-info h-100">
            <div class="card-body">
               <div class="d-flex align-items-center mb-2">
                  <div class="avatar me-4">
                     <span class="avatar-initial rounded-3 bg-label-info"><i class='ri-time-line ri-24px'></i></span>
                  </div>
                  <h4 class="mb-0">13</h4>
               </div>
               <h6 class="mb-0 fw-normal">Late vehicles</h6>
               <p class="mb-0">
                  <span class="me-1 fw-medium">-2.5%</span>
                  <small class="text-muted">than last week</small>
               </p>
            </div>
         </div>
      </div>
      <!--/ Card Border Shadow -->

      <!-- Reasons for delivery exceptions -->
      {{-- <div class="col-md-6 col-xxl-12 order-1 order-xxl-3">
         <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
               <div class="card-title mb-0">
                  <h5 class="m-0 me-2">Reasons for delivery exceptions</h5>
               </div>
               <div class="dropdown">
                  <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-1" type="button"
                     id="deliveryExceptionsReasons" data-bs-toggle="dropdown" aria-haspopup="true"
                     aria-expanded="false">
                     <i class="ri-more-2-line ri-20px"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="deliveryExceptionsReasons">
                     <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                     <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                     <a class="dropdown-item" href="javascript:void(0);">Share</a>
                  </div>
               </div>
            </div>
            <div class="card-body">
               <div id="deliveryExceptionsChart"></div>
            </div>
         </div>
      </div> --}}
      <!--/ Reasons for delivery exceptions -->

      <!-- On route vehicles Table -->
      <div class="col-12 order-5">
         <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
               <div class="card-title mb-0">
                  <h5 class="m-0 me-2">Riwayat Absensi</h5>
               </div>
               <div class="dropdown">
                  <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-1" type="button"
                     id="routeVehicles" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <i class="ri-more-2-line ri-20px"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="routeVehicles">
                     <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                     <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                     <a class="dropdown-item" href="javascript:void(0);">Share</a>
                  </div>
               </div>
            </div>
            <div class="card-datatable table-responsive">
               <table class="dt-route-vehicles table">
                  <thead>
                     <tr>
                        <th></th>
                        <th></th>
                        <th>location</th>
                        <th>starting route</th>
                        <th>ending route</th>
                        <th>warnings</th>
                        <th class="w-20">progress</th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
      </div>

      <!-- On route vehicles Table -->
      <div class="col-12 order-5">
         <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
               <div class="card-title mb-0">
                  <h5 class="m-0 me-2">Riwayat Izin</h5>
               </div>
               <div class="dropdown">
                  <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-1" type="button"
                     id="routeVehicles" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <i class="ri-more-2-line ri-20px"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="routeVehicles">
                     <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                     <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                     <a class="dropdown-item" href="javascript:void(0);">Share</a>
                  </div>
               </div>
            </div>
            <div class="card-datatable table-responsive">
               <table class="dt-route-vehicles table">
                  <thead>
                     <tr>
                        <th></th>
                        <th></th>
                        <th>location</th>
                        <th>starting route</th>
                        <th>ending route</th>
                        <th>warnings</th>
                        <th class="w-20">progress</th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
      </div>
   </div>
   <!--/ On route vehicles Table -->
@endsection
