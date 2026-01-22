@extends('layouts/layoutMaster')

@section('title', 'Manajemen Role')

@section('content')
   <div class="container-xxl flex-grow-1 container-p-y">
      @if (session('success'))
         <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>
      @endif

      <div class="d-flex justify-content-between align-items-center mb-4">
         <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Manajemen /</span> Role
         </h4>
         <a href="{{ route('role.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i> Tambah Role
         </a>
      </div>

      <div class="card">
         <div class="table-responsive">
            <table class="table table-hover">
               <thead>
                  <tr>
                     <th>#</th>
                     <th>Nama Role</th>
                     <th>Slug</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  @forelse($roles as $index => $role)
                     <tr>
                        <td>{{ $roles->firstItem() + $index }}</td>
                        <td><strong>{{ $role->name }}</strong></td>
                        <td><code>{{ $role->slug }}</code></td>
                        <td>
                           <a href="{{ route('role.edit', $role->id) }}" class="btn btn-sm btn-outline-primary">
                              <i class="ri-pencil-line"></i>
                           </a>
                           <button type="button" class="btn btn-sm btn-outline-danger delete-record"
                              data-action="{{ route('role.destroy', $role->id) }}">
                              <i class="ri-delete-bin-line"></i>
                           </button>
                        </td>
                     </tr>
                  @empty
                     <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada role</td>
                     </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
         <div class="card-footer border-top d-flex justify-content-between align-items-center py-3">
            <div class="text-muted small">
               Showing {{ $roles->firstItem() ?? 0 }} to {{ $roles->lastItem() ?? 0 }} of {{ $roles->total() }} entries
            </div>
            <div class="pagination-container">
               {{ $roles->appends(request()->query())->links() }}
            </div>
         </div>
      </div>
   </div>
@endsection

@section('page-script')
   @vite(['resources/assets/js/app-role-index.js'])
@endsection
