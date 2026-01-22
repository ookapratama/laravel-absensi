@extends('layouts/layoutMaster')

@section('title', 'Manajemen Shift')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Data Master /</span> Shift
         </h4>
         <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalShift" onclick="resetForm()">
            <i class="ri-add-line me-1"></i>Tambah Shift
         </button>
      </div>

      <div class="card mb-4">
         <div class="card-body">
            <form action="{{ route('shift.index') }}" method="GET" class="row g-3">
               <div class="col-md-4">
                  <label class="form-label">Filter Divisi</label>
                  <select name="divisi_id" class="form-select" onchange="this.form.submit()">
                     <option value="">Semua Divisi</option>
                     @foreach ($divisis as $divisi)
                        <option value="{{ $divisi->id }}" {{ request('divisi_id') == $divisi->id ? 'selected' : '' }}>
                           {{ $divisi->nama }}
                        </option>
                     @endforeach
                  </select>
               </div>
               <div class="col-md-4 d-flex align-items-end">
                  @if (request('divisi_id'))
                     <a href="{{ route('shift.index') }}" class="btn btn-label-secondary">
                        <i class="ri-refresh-line me-1"></i>Reset Filter
                     </a>
                  @endif
               </div>
            </form>
         </div>
      </div>

      <div class="card">
         <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Shift</h5>
         </div>
         <div class="table-responsive">
            <table class="table table-hover">
               <thead>
                  <tr>
                     <th>#</th>
                     <th>Divisi</th>
                     <th>Nama Shift</th>
                     <th>Jam Masuk</th>
                     <th>Jam Pulang</th>
                     <th>Status</th>
                     <th class="text-center">Aksi</th>
                  </tr>
               </thead>
               <tbody class="table-border-bottom-0">
                  @forelse($data as $index => $item)
                     <tr>
                        <td>{{ $data->firstItem() + $index }}</td>
                        <td>{{ $item->divisi->nama ?? '-' }}</td>
                        <td><strong>{{ $item->nama }}</strong></td>
                        <td><span class="badge bg-label-primary">{{ $item->jam_masuk->format('H:i') }}</span></td>
                        <td><span class="badge bg-label-secondary">{{ $item->jam_pulang->format('H:i') }}</span></td>
                        <td>
                           @if ($item->is_aktif)
                              <span class="badge bg-success">Aktif</span>
                           @else
                              <span class="badge bg-secondary">Non-Aktif</span>
                           @endif
                        </td>
                        <td class="text-center">
                           <div class="d-flex justify-content-center gap-2">
                              <button class="btn btn-sm btn-icon btn-label-primary"
                                 onclick="editShift({{ json_encode($item) }})">
                                 <i class="ri-pencil-line"></i>
                              </button>
                              <button type="button" class="btn btn-sm btn-icon btn-label-danger delete-record"
                                 data-id="{{ $item->id }}" data-name="{{ $item->nama }}">
                                 <i class="ri-delete-bin-line"></i>
                              </button>
                           </div>
                        </td>
                     </tr>
                  @empty
                     <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                           <i class="ri-time-line ri-3x mb-2"></i>
                           <p class="mb-0">Belum ada data shift</p>
                        </td>
                     </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
         <div class="card-footer border-top d-flex justify-content-between align-items-center py-3">
            <div class="text-muted small">
               Showing {{ $data->firstItem() ?? 0 }} to {{ $data->lastItem() ?? 0 }} of {{ $data->total() }} entries
            </div>
            <div class="pagination-container">
               {{ $data->appends(request()->query())->links() }}
            </div>
         </div>
      </div>
   </div>

   <!-- Modal Shift -->
   <div class="modal fade" id="modalShift" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modalTitle">Tambah Shift</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formShift" onsubmit="saveShift(event)">
               @csrf
               <input type="hidden" id="shift_id" name="id">
               <div class="modal-body">
                  <div class="mb-3">
                     <label class="form-label">Divisi <span class="text-danger">*</span></label>
                     <select name="divisi_id" id="divisi_id" class="form-select" required>
                        <option value="">Pilih Divisi</option>
                        @foreach ($divisis as $divisi)
                           <option value="{{ $divisi->id }}">{{ $divisi->nama }}</option>
                        @endforeach
                     </select>
                  </div>
                  <div class="mb-3">
                     <label class="form-label">Nama Shift <span class="text-danger">*</span></label>
                     <input type="text" name="nama" id="nama" class="form-control" placeholder="Pagi" required>
                  </div>
                  <div class="row">
                     <div class="col-md-6 mb-3">
                        <label class="form-label">Jam Masuk <span class="text-danger">*</span></label>
                        <input type="time" name="jam_masuk" id="jam_masuk" class="form-control" required>
                     </div>
                     <div class="col-md-6 mb-3">
                        <label class="form-label">Jam Pulang <span class="text-danger">*</span></label>
                        <input type="time" name="jam_pulang" id="jam_pulang" class="form-control" required>
                     </div>
                  </div>
                  <div class="mb-0">
                     <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_aktif" name="is_aktif" value="1"
                           checked>
                        <label class="form-check-label" for="is_aktif">Aktif</label>
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
   </div>
@endsection

@section('page-script')
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         const modalElement = document.getElementById('modalShift');
         const formShift = document.getElementById('formShift');
         let modalShiftInstance = null;

         // Initialize modal only when needed
         function getModalInstance() {
            if (!modalShiftInstance && window.bootstrap) {
               modalShiftInstance = new bootstrap.Modal(modalElement);
            }
            return modalShiftInstance;
         }

         window.resetForm = function() {
            formShift.reset();
            document.getElementById('shift_id').value = '';
            document.getElementById('modalTitle').textContent = 'Tambah Shift';
         };

         window.editShift = function(data) {
            window.resetForm();
            document.getElementById('modalTitle').textContent = 'Edit Shift';
            document.getElementById('shift_id').value = data.id;
            document.getElementById('divisi_id').value = data.divisi_id;
            document.getElementById('nama').value = data.nama;

            // Handle time format H:i:s -> H:i
            if (data.jam_masuk) document.getElementById('jam_masuk').value = data.jam_masuk.substring(0, 5);
            if (data.jam_pulang) document.getElementById('jam_pulang').value = data.jam_pulang.substring(0, 5);

            document.getElementById('is_aktif').checked = !!data.is_aktif;
            const modal = getModalInstance();
            if (modal) modal.show();
         };

         window.saveShift = function(e) {
            e.preventDefault();
            const id = document.getElementById('shift_id').value;
            const url = id ? `{{ url('shift') }}/${id}` : `{{ url('shift') }}`;

            const formData = new FormData(formShift);
            if (id) formData.append('_method', id ? 'PUT' : 'POST');

            fetch(url, {
                  method: 'POST',
                  body: formData,
                  headers: {
                     'X-CSRF-TOKEN': '{{ csrf_token() }}',
                     'Accept': 'application/json'
                  }
               })
               .then(async response => {
                  const data = await response.json();
                  if (!response.ok) {
                     // Handle validation errors or other server errors
                     if (window.AlertHandler) {
                        window.AlertHandler.showError(data.message || 'Terjadi kesalahan', data.errors);
                     } else {
                        alert(data.message || 'Terjadi kesalahan');
                     }
                     return {
                        success: false
                     };
                  }
                  return data;
               })
               .then(data => {
                  if (data && data.success) {
                     if (window.AlertHandler) {
                        window.AlertHandler.showSuccess(data.message);
                     }

                     const modal = getModalInstance();
                     if (modal) modal.hide();
                     setTimeout(() => window.location.reload(), 1000);
                  }
               })
               .catch(error => {
                  console.error('Error:', error);
                  if (window.AlertHandler) {
                     window.AlertHandler.showError('Terjadi kesalahan sistem');
                  }
               });
         };

         document.querySelectorAll('.delete-record').forEach(btn => {
            btn.addEventListener('click', function() {
               const id = this.dataset.id;
               const name = this.dataset.name;

               if (window.AlertHandler) {
                  window.AlertHandler.confirm(
                     'Hapus Shift?',
                     `Apakah Anda yakin ingin menghapus shift "${name}"?`,
                     'Ya, Hapus!',
                     function() {
                        fetch(`{{ url('shift') }}/${id}`, {
                              method: 'DELETE',
                              headers: {
                                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                 'Accept': 'application/json'
                              }
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
               } else {
                  if (confirm(`Apakah Anda yakin ingin menghapus shift "${name}"?`)) {
                     // simplified delete for fallback
                  }
               }
            });
         });
      });
   </script>
@endsection
