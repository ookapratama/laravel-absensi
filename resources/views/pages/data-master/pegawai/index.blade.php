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

      <div class="card mb-4">
         <div class="card-body">
            <form action="{{ route('pegawai.index') }}" method="GET" class="row g-3">
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
                     <a href="{{ route('pegawai.index') }}" class="btn btn-label-secondary">
                        <i class="ri-refresh-line me-1"></i>Reset Filter
                     </a>
                  @endif
               </div>
            </form>
         </div>
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
                     <th>Shift</th>
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
                        <td>
                           @if ($item->shift)
                              <strong>{{ $item->shift->nama }}</strong>
                              <br><small class="text-muted">{{ $item->shift->jam_masuk->format('H:i') }} -
                                 {{ $item->shift->jam_pulang->format('H:i') }}</small>
                           @else
                              -
                           @endif
                        </td>
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
