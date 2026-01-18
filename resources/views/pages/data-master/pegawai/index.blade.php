@extends('layouts/layoutMaster')

@section('title', 'Manajemen Pegawai')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Data Master /</span> Pegawai
         </h4>
         <a href="{{ route('pegawai.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i>Tambah Pegawai
         </a>
      </div>

      <div class="card">
         <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Daftar Pegawai</h5>
         </div>
         <div class="table-responsive">
            <table class="table table-hover">
               <thead>
                  <tr>
                     <th>#</th>
                     <th>Foto</th>
                     <th>NIP</th>
                     <th>Nama Lengkap</th>
                     <th>Divisi</th>
                     <th>Kantor</th>
                     <th>Jabatan</th>
                     <th>Status</th>
                     <th class="text-center">Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  @forelse($data as $index => $item)
                     <tr>
                        <td>{{ $data->firstItem() + $index }}</td>
                        <td>
                           <div class="avatar avatar-sm">
                              <img src="{{ $item->foto_url }}" alt="{{ $item->nama_lengkap }}" class="rounded-circle">
                           </div>
                        </td>
                        <td><code>{{ $item->nip ?? '-' }}</code></td>
                        <td>
                           <strong>{{ $item->nama_lengkap }}</strong>
                           <br><small class="text-muted">{{ $item->user->email ?? '-' }}</small>
                        </td>
                        <td>{{ $item->divisi->nama ?? '-' }}</td>
                        <td>{{ $item->kantor->nama ?? '-' }}</td>
                        <td>{{ $item->jabatan ?? '-' }}</td>
                        <td>
                           @if ($item->status_aktif)
                              <span class="badge bg-success">Aktif</span>
                           @else
                              <span class="badge bg-secondary">Non-Aktif</span>
                           @endif
                        </td>
                        <td class="text-center">
                           <div class="d-flex justify-content-center gap-2">
                              <a href="{{ route('pegawai.show', $item->id) }}" class="btn btn-sm btn-outline-info">
                                 <i class="ri-eye-line"></i>
                              </a>
                              <a href="{{ route('pegawai.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                 <i class="ri-pencil-line"></i>
                              </a>
                              <button type="button" class="btn btn-sm btn-outline-danger delete-record"
                                 data-id="{{ $item->id }}" data-name="{{ $item->nama_lengkap }}">
                                 <i class="ri-delete-bin-line"></i>
                              </button>
                           </div>
                        </td>
                     </tr>
                  @empty
                     <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                           <i class="ri-user-line ri-3x mb-2"></i>
                           <p class="mb-0">Belum ada data pegawai</p>
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
@endsection

@section('page-script')
   <script>
      document.querySelectorAll('.delete-record').forEach(btn => {
         btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            window.AlertHandler.confirm(
               'Hapus Pegawai?',
               `Apakah Anda yakin ingin menghapus pegawai "${name}"?`,
               'Ya, Hapus!',
               function() {
                  fetch(`{{ url('pegawai') }}/${id}`, {
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
