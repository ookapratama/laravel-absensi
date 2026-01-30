@extends('layouts/layoutMaster')

@section('title', 'Kalender Absensi')

@section('vendor-style')
   @vite(['resources/assets/vendor/libs/fullcalendar/fullcalendar.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('page-style')
   @vite(['resources/assets/vendor/scss/pages/app-calendar.scss'])
   <style>
      .fc-event {
         cursor: pointer;
      }
   </style>
@endsection

@section('vendor-script')
   @vite(['resources/assets/vendor/libs/fullcalendar/fullcalendar.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/moment/moment.js'])
@endsection

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Absensi /</span> Kalender
         </h4>
         <a href="{{ route('absensi.index') }}" class="btn btn-primary">
            <i class="ri-camera-lens-line me-1"></i>Halaman Absensi
         </a>
      </div>

      <div class="card app-calendar-wrapper">
         <div class="row g-0">
            <!-- Calendar Sidebar (Optional/Legends) -->
            <div class="col app-calendar-sidebar border-end d-none d-lg-block" id="app-calendar-sidebar"
               style="flex: 0 0 250px;">
               <div class="p-4">
                  <h5 class="mb-4">Keterangan</h5>
                  <div class="d-flex flex-column gap-3">
                     <div class="d-flex align-items-center">
                        <span class="badge badge-dot bg-success me-2"></span>
                        <span>Hadir Tepat Waktu</span>
                     </div>
                     <div class="d-flex align-items-center">
                        <span class="badge badge-dot bg-warning me-2"></span>
                        <span>Terlambat</span>
                     </div>
                     <div class="d-flex align-items-center">
                        <span class="badge badge-dot bg-danger me-2"></span>
                        <span>Libur / Libur Nasional</span>
                     </div>
                     <div class="d-flex align-items-center">
                        <span class="badge badge-dot bg-info me-2"></span>
                        <span>Izin / Sakit / Cuti</span>
                     </div>
                  </div>

                  <hr class="my-4">

                  <div class="text-muted small">
                     <p><i class="ri-information-line me-1"></i> Klik pada event untuk melihat detail keterangan.</p>
                  </div>
               </div>
            </div>

            <!-- FullCalendar -->
            <div class="col app-calendar-content p-4">
               <div id="calendar"></div>
            </div>
         </div>
      </div>
   </div>
@endsection

@section('page-script')
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         const calendarEl = document.getElementById('calendar');
         if (calendarEl) {
            const calendar = new FullCalendar.Calendar(calendarEl, {
               initialView: 'dayGridMonth',
               locale: 'id',
               events: "{{ route('absensi.calendar-events') }}",
               plugins: [FullCalendar.dayGridPlugin, FullCalendar.interactionPlugin, FullCalendar.listPlugin,
                  FullCalendar.timeGridPlugin
               ],
               headerToolbar: {
                  start: 'prev,next, title',
                  end: 'dayGridMonth,listMonth'
               },
               buttonText: {
                  today: 'Hari Ini',
                  month: 'Bulan',
                  list: 'List'
               },
               eventClassNames: function({
                  event: calendarEvent
               }) {
                  const colorName = calendarEvent.extendedProps.calendar || 'primary';
                  return ['fc-event-' + colorName];
               },
               eventDidMount: function(info) {
                  if (info.event.extendedProps.description) {
                     new bootstrap.Tooltip(info.el, {
                        title: info.event.extendedProps.description,
                        placement: 'top',
                        trigger: 'hover',
                        container: 'body'
                     });
                  }
               },
               eventClick: function(info) {
                  // Show simple alert or modal for detail
                  Swal.fire({
                     title: info.event.title,
                     text: info.event.extendedProps.description || 'Tidak ada keterangan tambahan.',
                     icon: 'info',
                     confirmButtonText: 'Tutup',
                     customClass: {
                        confirmButton: 'btn btn-primary'
                     },
                     buttonsStyling: false
                  });
               }
            });
            calendar.render();
         }
      });
   </script>
@endsection
