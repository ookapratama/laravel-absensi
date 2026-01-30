@extends('layouts/layoutMaster')

@section('vendor-style')
   @vite(['resources/assets/vendor/libs/fullcalendar/fullcalendar.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('page-style')
   @vite(['resources/assets/vendor/scss/pages/app-calendar.scss'])
@endsection

@section('vendor-script')
   @vite(['resources/assets/vendor/libs/fullcalendar/fullcalendar.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/moment/moment.js'])
@endsection

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Master Data /</span> Hari Libur
         </h4>
      </div>

      <div class="card mb-4">
         <div class="card-body">
            <form action="{{ route('hari-libur.index') }}" method="GET" class="row g-3 align-items-end">
               <div class="col-md-3">
                  <label class="form-label">Tahun</label>
                  <select name="year" class="form-select" onchange="this.form.submit()">
                     @for ($y = now()->year - 1; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}
                        </option>
                     @endfor
                  </select>
               </div>
               <div class="col-md-9 text-end">
                  <form action="{{ route('hari-libur.sync') }}" method="POST" class="d-inline">
                     @csrf
                     <input type="hidden" name="year" value="{{ $year }}">
                     <button type="submit" class="btn btn-primary me-2">
                        <i class="ri-refresh-line me-1"></i> Sinkronisasi API
                     </button>
                  </form>
                  <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAdd">
                     <i class="ri-add-line me-1"></i> Tambah Manual
                  </button>
               </div>
            </form>
         </div>
      </div>

      <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
         <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-table-tab" data-bs-toggle="pill" data-bs-target="#pills-table"
               type="button" role="tab">
               <i class="ri-table-line me-1"></i> Tabel
            </button>
         </li>
         <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-calendar-tab" data-bs-toggle="pill" data-bs-target="#pills-calendar"
               type="button" role="tab">
               <i class="ri-calendar-line me-1"></i> Kalender
            </button>
         </li>
      </ul>

      <div class="tab-content p-0" id="pills-tabContent">
         <div class="tab-pane fade show active" id="pills-table" role="tabpanel">
            <div class="card">
               <div class="table-responsive">
                  <table class="table table-hover">
                     <thead class="table-light">
                        <tr>
                           <th>Tanggal</th>
                           <th>Keterangan Libur</th>
                           <th class="text-center">Tipe</th>
                           <th class="text-center">Cuti Bersama</th>
                           <th class="text-center">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        @forelse($data as $item)
                           <tr>
                              <td style="width: 15%;">
                                 <span class="fw-bold">{{ $item->tanggal->format('d/m/Y') }}</span>
                                 <br>
                                 <small class="text-muted">{{ $item->tanggal->locale('id')->isoFormat('dddd') }}</small>
                              </td>
                              <td>
                                 <span class="fw-bold text-dark">{{ $item->nama }}</span>
                                 @if ($item->deskripsi)
                                    <br><small class="text-muted">{{ Str::limit($item->deskripsi, 50) }}</small>
                                 @endif
                              </td>
                              <td class="text-center">
                                 @if ($item->is_nasional)
                                    <span class="badge bg-label-danger">Nasional</span>
                                 @else
                                    <span class="badge bg-label-secondary">Lokal</span>
                                 @endif
                              </td>
                              <td class="text-center">
                                 @if ($item->is_cuti_bersama)
                                    <span class="badge bg-label-warning">Ya</span>
                                 @else
                                    <span class="badge bg-label-success">Tidak</span>
                                 @endif
                              </td>
                              <td class="text-center">
                                 <form action="{{ route('hari-libur.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <a href="{{ route('hari-libur.edit', $item->id) }}"
                                       class="btn btn-sm btn-icon btn-text-primary rounded-pill me-2">
                                       <i class="ri-pencil-line"></i>
                                    </a>
                                    <button type="submit" class="btn btn-sm btn-icon btn-text-danger rounded-pill">
                                       <i class="ri-delete-bin-line"></i>
                                    </button>
                                 </form>
                              </td>
                           </tr>
                        @empty
                           <tr>
                              <td colspan="5" class="text-center py-5 text-muted">Belum ada data hari libur untuk tahun
                                 {{ $year }}.</td>
                           </tr>
                        @endforelse
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
         <div class="tab-pane fade" id="pills-calendar" role="tabpanel">
            <div class="card p-4">
               <div id="calendar"></div>
            </div>
         </div>
      </div>
   </div>

   <!-- Modal Add -->
   <div class="modal fade" id="modalAdd" tabindex="-1">
      <div class="modal-dialog">
         <form action="{{ route('hari-libur.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
               <h5 class="modal-title">Tambah Hari Libur</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
               <div class="mb-3">
                  <label class="form-label">Tanggal</label>
                  <input type="date" name="tanggal" class="form-control" required value="{{ date('Y-m-d') }}">
               </div>
               <div class="mb-3">
                  <label class="form-label">Nama Libur</label>
                  <input type="text" name="nama" class="form-control" required
                     placeholder="Contoh: Gathering Kantor">
               </div>
               <div class="row">
                  <div class="col-6 mb-3">
                     <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="is_nasional" value="1"
                           id="is_nasional">
                        <label class="form-check-label" for="is_nasional">Libur Nasional</label>
                     </div>
                  </div>
                  <div class="col-6 mb-3">
                     <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="is_cuti_bersama" value="1"
                           id="is_cuti_bersama">
                        <label class="form-check-label" for="is_cuti_bersama">Cuti Bersama</label>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
         </form>
      </div>
   </div>
@endsection

@section('page-script')
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         const calendarEl = document.getElementById('calendar');
         const calendarTab = document.getElementById('pills-calendar-tab');

         let calendar;

         function initCalendar() {
            if (!calendar) {
               calendar = new FullCalendar.Calendar(calendarEl, {
                  initialView: 'dayGridMonth',
                  locale: 'id',
                  events: "{{ route('api.hari-libur.events') }}",
                  plugins: [FullCalendar.dayGridPlugin, FullCalendar.interactionPlugin, FullCalendar
                     .listPlugin, FullCalendar.timeGridPlugin
                  ],
                  headerToolbar: {
                     start: 'prev,next, title',
                     end: 'dayGridMonth,listMonth'
                  },
                  direction: 'ltr',
                  eventClassNames: function({
                     event: calendarEvent
                  }) {
                     const colorName = calendarEvent.extendedProps.calendar || 'primary';
                     return ['fc-event-' + colorName];
                  }
               });
               calendar.render();
            }
         }

         // Initialize calendar when tab is shown
         if (calendarTab) {
            calendarTab.addEventListener('shown.bs.tab', function() {
               initCalendar();
            });
         }

         // Also if URL has #pills-calendar
         if (window.location.hash === '#pills-calendar') {
            const tab = new bootstrap.Tab(calendarTab);
            tab.show();
         }
      });
   </script>
@endsection
