@extends('layouts/layoutMaster')

@section('title', 'Manajemen Kantor')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Data Master /</span> Kantor
         </h4>
         <a href="{{ route('kantor.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i>Tambah Kantor
         </a>
      </div>

      <div class="card">
         <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Daftar Kantor / Lokasi Absensi</h5>
         </div>
         <div class="table-responsive">
            <table class="table table-hover">
               <thead>
                  <tr>
                     <th>#</th>
                     <th>Kode</th>
                     <th>Nama Kantor</th>
                     <th>Alamat</th>
                     <th>Koordinat</th>
                     <th>Radius</th>
                     <th>Status</th>
                     <th class="text-center">Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  @forelse($data as $index => $item)
                     <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $item->kode ?? '-' }}</code></td>
                        <td><strong>{{ $item->nama }}</strong></td>
                        <td>
                           <span class="d-inline-block text-truncate" style="max-width: 200px;">
                              {{ $item->alamat ?? '-' }}
                           </span>
                        </td>
                        <td>
                           <small class="text-muted">
                              {{ $item->latitude }}, {{ $item->longitude }}
                           </small>
                           <a href="https://maps.google.com/?q={{ $item->latitude }},{{ $item->longitude }}"
                              target="_blank" class="ms-1" title="Lihat di Maps">
                              <i class="ri-map-pin-line text-primary"></i>
                           </a>
                        </td>
                        <td><span class="badge bg-label-info">{{ $item->radius_meter }}m</span></td>
                        <td>
                           @if ($item->is_aktif)
                              <span class="badge bg-success">Aktif</span>
                           @else
                              <span class="badge bg-secondary">Non-Aktif</span>
                           @endif
                        </td>
                        <td class="text-center">
                           <div class="d-flex justify-content-center gap-2">
                              <a href="{{ route('kantor.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
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
                           <i class="ri-building-line ri-3x mb-2"></i>
                           <p class="mb-0">Belum ada data kantor</p>
                        </td>
                     </tr>
                  @endforelse
               </tbody>
            </table>
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
               'Hapus Kantor?',
               `Apakah Anda yakin ingin menghapus kantor "${name}"?`,
               'Ya, Hapus!',
               function() {
                  fetch(`{{ url('kantor') }}/${id}`, {
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
