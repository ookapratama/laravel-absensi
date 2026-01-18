@extends('layouts/layoutMaster')

@section('title', 'Izin Pending')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Admin / Izin /</span> Menunggu Persetujuan
         </h4>
         <a href="{{ route('izin.admin.index') }}" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i>Kembali
         </a>
      </div>

      @if ($data->isEmpty())
         <div class="card">
            <div class="card-body text-center py-5">
               <i class="ri-check-double-line ri-4x text-success mb-3"></i>
               <h5>Tidak Ada Izin Pending</h5>
               <p class="text-muted">Semua pengajuan izin sudah diproses</p>
            </div>
         </div>
      @else
         <div class="row">
            @foreach ($data as $izin)
               <div class="col-md-6 col-lg-4 mb-4">
                  <div class="card h-100">
                     <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                           <div class="avatar avatar-sm me-2">
                              <img src="{{ $izin->pegawai->foto_url }}" alt="" class="rounded-circle">
                           </div>
                           <div>
                              <h6 class="mb-0">{{ $izin->pegawai->nama_lengkap }}</h6>
                              <small class="text-muted">{{ $izin->pegawai->divisi->nama ?? '-' }}</small>
                           </div>
                        </div>
                        <span class="badge bg-warning">Pending</span>
                     </div>
                     <div class="card-body">
                        <h5 class="mb-2">{{ $izin->jenisIzin->nama }}</h5>
                        <p class="text-muted mb-3">
                           <i class="ri-calendar-line me-1"></i>
                           {{ $izin->tgl_mulai->format('d M Y') }}
                           @if ($izin->tgl_mulai != $izin->tgl_selesai)
                              - {{ $izin->tgl_selesai->format('d M Y') }}
                           @endif
                           <span class="badge bg-label-info ms-1">{{ $izin->jumlah_hari }} hari</span>
                        </p>
                        <p class="mb-0"><strong>Alasan:</strong></p>
                        <p class="text-muted mb-3">{{ Str::limit($izin->alasan, 100) }}</p>

                        @if ($izin->file_surat)
                           <a href="{{ $izin->file_surat_url }}" target="_blank" class="btn btn-sm btn-outline-info mb-3">
                              <i class="ri-attachment-line me-1"></i>Lihat Surat
                           </a>
                        @endif

                        <p class="text-muted small mb-0">
                           <i class="ri-time-line me-1"></i>Diajukan: {{ $izin->created_at->diffForHumans() }}
                        </p>
                     </div>
                     <div class="card-footer d-flex gap-2">
                        <button type="button" class="btn btn-success flex-grow-1 btn-approve"
                           data-id="{{ $izin->id }}" data-name="{{ $izin->pegawai->nama_lengkap }}">
                           <i class="ri-check-line me-1"></i>Setujui
                        </button>
                        <button type="button" class="btn btn-outline-danger flex-grow-1 btn-reject"
                           data-id="{{ $izin->id }}" data-name="{{ $izin->pegawai->nama_lengkap }}">
                           <i class="ri-close-line me-1"></i>Tolak
                        </button>
                     </div>
                  </div>
               </div>
            @endforeach
         </div>
      @endif
   </div>

   <!-- Modal Reject -->
   <div class="modal fade" id="rejectModal" tabindex="-1">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Tolak Izin</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reject-form">
               <div class="modal-body">
                  <p>Izin dari: <strong id="reject-name"></strong></p>
                  <div class="mb-3">
                     <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                     <textarea class="form-control" id="reject-catatan" name="catatan" rows="3" required
                        placeholder="Jelaskan alasan penolakan..."></textarea>
                     <small class="text-muted">Minimal 10 karakter</small>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-danger">Tolak Izin</button>
               </div>
            </form>
         </div>
      </div>
   </div>
@endsection

@section('page-script')
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         let currentRejectId = null;
         const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));

         // Approve
         document.querySelectorAll('.btn-approve').forEach(btn => {
            btn.addEventListener('click', function() {
               const id = this.dataset.id;
               const name = this.dataset.name;

               window.AlertHandler.confirm(
                  'Setujui Izin?',
                  `Setujui pengajuan izin dari "${name}"?`,
                  'Ya, Setujui',
                  function() {
                     fetch(`{{ url('izin/admin') }}/${id}/approve`, {
                           method: 'POST',
                           headers: {
                              'X-CSRF-TOKEN': '{{ csrf_token() }}',
                              'Accept': 'application/json',
                              'Content-Type': 'application/json'
                           },
                           body: JSON.stringify({})
                        })
                        .then(response => response.json())
                        .then(data => {
                           window.AlertHandler.handle(data);
                           if (data.success) {
                              setTimeout(() => window.location.reload(), 1500);
                           }
                        });
                  }
               );
            });
         });

         // Reject - Open Modal
         document.querySelectorAll('.btn-reject').forEach(btn => {
            btn.addEventListener('click', function() {
               currentRejectId = this.dataset.id;
               document.getElementById('reject-name').textContent = this.dataset.name;
               document.getElementById('reject-catatan').value = '';
               rejectModal.show();
            });
         });

         // Reject - Submit
         document.getElementById('reject-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const catatan = document.getElementById('reject-catatan').value;

            if (catatan.length < 10) {
               window.AlertHandler.error('Alasan penolakan minimal 10 karakter');
               return;
            }

            fetch(`{{ url('izin/admin') }}/${currentRejectId}/reject`, {
                  method: 'POST',
                  headers: {
                     'X-CSRF-TOKEN': '{{ csrf_token() }}',
                     'Accept': 'application/json',
                     'Content-Type': 'application/json'
                  },
                  body: JSON.stringify({
                     catatan: catatan
                  })
               })
               .then(response => response.json())
               .then(data => {
                  rejectModal.hide();
                  window.AlertHandler.handle(data);
                  if (data.success) {
                     setTimeout(() => window.location.reload(), 1500);
                  }
               });
         });
      });
   </script>
@endsection
