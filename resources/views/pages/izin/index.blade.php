@extends('layouts/layoutMaster')

@section('title', 'Pengajuan Izin')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Izin /</span> Daftar Izin Saya
         </h4>
         <a href="{{ route('izin.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i>Ajukan Izin
         </a>
      </div>

      @if (session('error'))
         <div class="alert alert-danger alert-dismissible mb-4">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
         </div>
      @endif

      <div class="card">
         <div class="card-header">
            <h5 class="mb-0">Riwayat Pengajuan Izin</h5>
         </div>
         <div class="table-responsive">
            <table class="table table-hover">
               <thead>
                  <tr>
                     <th>#</th>
                     <th>Jenis Izin</th>
                     <th>Tanggal</th>
                     <th>Jumlah Hari</th>
                     <th>Alasan</th>
                     <th>Status</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  @forelse($data as $index => $izin)
                     <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                           <span class="fw-bold">{{ $izin->jenisIzin->nama }}</span>
                           @if ($izin->jenisIzin->butuh_surat)
                              <i class="ri-file-text-line text-muted ms-1" title="Butuh Surat"></i>
                           @endif
                        </td>
                        <td>
                           {{ $izin->tgl_mulai->format('d/m/Y') }}
                           @if ($izin->tgl_mulai != $izin->tgl_selesai)
                              - {{ $izin->tgl_selesai->format('d/m/Y') }}
                           @endif
                        </td>
                        <td>{{ $izin->jumlah_hari }} hari</td>
                        <td>
                           <span class="d-inline-block text-truncate" style="max-width: 200px;">
                              {{ $izin->alasan }}
                           </span>
                        </td>
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
                                 <button type="button" class="btn btn-sm btn-outline-danger btn-cancel"
                                    data-id="{{ $izin->id }}" data-name="{{ $izin->jenisIzin->nama }}">
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
      </div>
   </div>
@endsection

@section('page-script')
   <script>
      document.querySelectorAll('.btn-cancel').forEach(btn => {
         btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            window.AlertHandler.confirm(
               'Batalkan Izin?',
               `Apakah Anda yakin ingin membatalkan pengajuan izin "${name}"?`,
               'Ya, Batalkan',
               function() {
                  fetch(`{{ url('izin') }}/${id}/cancel`, {
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
                     })
                     .catch(err => {
                        console.error(err);
                        window.AlertHandler.showError('Terjadi kesalahan');
                     });
               }
            );
         });
      });
   </script>
@endsection
