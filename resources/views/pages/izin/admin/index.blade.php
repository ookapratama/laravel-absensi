@extends('layouts/layoutMaster')

@section('title', 'Manajemen Izin')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Admin /</span> Manajemen Izin
         </h4>
         <div class="d-flex gap-2">
            <a href="{{ route('izin.admin.pending') }}" class="btn btn-warning">
               <i class="ri-time-line me-1"></i>Pending ({{ $statistik['pending'] }})
            </a>
         </div>
      </div>

      <!-- Stats -->
      <div class="row mb-4">
         <div class="col-md-4">
            <div class="card bg-warning text-white">
               <div class="card-body">
                  <h3 class="mb-0 text-white">{{ $statistik['pending'] }}</h3>
                  <span>Menunggu Persetujuan</span>
               </div>
            </div>
         </div>
         <div class="col-md-4">
            <div class="card bg-success text-white">
               <div class="card-body">
                  <h3 class="mb-0 text-white">{{ $statistik['approved'] }}</h3>
                  <span>Disetujui</span>
               </div>
            </div>
         </div>
         <div class="col-md-4">
            <div class="card bg-danger text-white">
               <div class="card-body">
                  <h3 class="mb-0 text-white">{{ $statistik['rejected'] }}</h3>
                  <span>Ditolak</span>
               </div>
            </div>
         </div>
      </div>

      <!-- Table -->
      <div class="card">
         <div class="card-header">
            <h5 class="mb-0">Semua Pengajuan Izin</h5>
         </div>
         <div class="table-responsive">
            <table class="table table-hover">
               <thead>
                  <tr>
                     <th>#</th>
                     <th>Pegawai</th>
                     <th>Jenis Izin</th>
                     <th>Tanggal</th>
                     <th>Jumlah</th>
                     <th>Status</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  @forelse($data as $index => $izin)
                     <tr>
                        <td>{{ $data->firstItem() + $index }}</td>
                        <td>
                           <div class="d-flex align-items-center">
                              <div class="avatar avatar-sm me-2">
                                 <img src="{{ $izin->pegawai->foto_url }}" alt="" class="rounded-circle">
                              </div>
                              <div>
                                 <strong>{{ $izin->pegawai->nama_lengkap }}</strong>
                                 <br><small class="text-muted">{{ $izin->pegawai->divisi->nama ?? '-' }}</small>
                              </div>
                           </div>
                        </td>
                        <td>{{ $izin->jenisIzin->nama }}</td>
                        <td>
                           {{ $izin->tgl_mulai->format('d/m/Y') }}
                           @if ($izin->tgl_mulai != $izin->tgl_selesai)
                              - {{ $izin->tgl_selesai->format('d/m/Y') }}
                           @endif
                        </td>
                        <td>{{ $izin->jumlah_hari }} hari</td>
                        <td>
                           @if ($izin->status_approval === 'Pending')
                              <span class="badge bg-warning">Menunggu</span>
                           @elseif($izin->status_approval === 'Approved')
                              <span class="badge bg-success">Disetujui</span>
                           @else
                              <span class="badge bg-danger">Ditolak</span>
                           @endif
                        </td>
                        <td>
                           <div class="d-flex gap-1">
                              <a href="{{ route('izin.show', $izin->id) }}" class="btn btn-sm btn-outline-info">
                                 <i class="ri-eye-line"></i>
                              </a>
                              @if ($izin->status_approval === 'Pending')
                                 <button type="button" class="btn btn-sm btn-success btn-approve"
                                    data-id="{{ $izin->id }}" data-name="{{ $izin->pegawai->nama_lengkap }}">
                                    <i class="ri-check-line"></i>
                                 </button>
                                 <button type="button" class="btn btn-sm btn-danger btn-reject"
                                    data-id="{{ $izin->id }}" data-name="{{ $izin->pegawai->nama_lengkap }}">
                                    <i class="ri-close-line"></i>
                                 </button>
                              @endif
                           </div>
                        </td>
                     </tr>
                  @empty
                     <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                           <i class="ri-file-list-3-line ri-3x mb-2"></i>
                           <p class="mb-0">Belum ada pengajuan izin</p>
                        </td>
                     </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
         @if ($data->hasPages())
            <div class="card-footer border-top py-3">
               {{ $data->links() }}
            </div>
         @endif
      </div>
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
