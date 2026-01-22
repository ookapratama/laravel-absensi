@extends('layouts/layoutMaster')

@section('title', 'Manajemen Divisi')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Data Master /</span> Divisi
         </h4>
         <a href="{{ route('divisi.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i>Tambah Divisi
         </a>
      </div>

      <div class="card">
         <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Daftar Divisi</h5>
         </div>
         <div class="table-responsive">
            <table class="table table-hover">
               <thead>
                  <tr>
                     <th>#</th>
                     <th>Kode</th>
                     <th>Nama Divisi</th>
                     <th>Toleransi</th>
                     <th>Status</th>
                     <th class="text-center">Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  @forelse($data as $index => $item)
                     <tr>
                        <td>{{ $data->firstItem() + $index }}</td>
                        <td><code>{{ $item->kode ?? '-' }}</code></td>
                        <td><strong>{{ $item->nama }}</strong></td>
                        <td>{{ $item->toleransi_terlambat ?? 0 }} menit</td>
                        <td>
                           @if ($item->is_aktif)
                              <span class="badge bg-success">Aktif</span>
                           @else
                              <span class="badge bg-secondary">Non-Aktif</span>
                           @endif
                        </td>
                        <td class="text-center">
                           <div class="d-flex justify-content-center gap-2">
                              <a href="{{ route('divisi.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                 <i class="ri-pencil-line"></i>
                              </a>
                              <button type="button" class="btn btn-sm btn-outline-danger delete-record"
                                 data-id="{{ $item->id }}" data-name="{{ $item->nama }}">
                                 <i class="ri-delete-bin-line"></i>
                              </button>
                           </div>
                        </td>
                     </tr>
                  @empty
                     <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                           <i class="ri-inbox-line ri-3x mb-2"></i>
                           <p class="mb-0">Belum ada data divisi</p>
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
@endsection

@section('page-script')
   <script>
      document.querySelectorAll('.delete-record').forEach(btn => {
         btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            window.AlertHandler.confirm(
               'Hapus Divisi?',
               `Apakah Anda yakin ingin menghapus divisi "${name}"?`,
               'Ya, Hapus!',
               function() {
                  fetch(`{{ url('divisi') }}/${id}`, {
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
         });
      });
   </script>
@endsection
