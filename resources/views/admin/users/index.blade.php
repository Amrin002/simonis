@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Manajemen User</h1>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Tambah User Manual
                    </a>
                    <a href="{{ route('admin.users.bulk-guru') }}" class="btn btn-success">
                        <i class="fas fa-users me-1"></i> Buat User Guru (Bulk)
                    </a>
                    <a href="{{ route('admin.users.bulk-orangtua') }}" class="btn btn-info">
                        <i class="fas fa-user-friends me-1"></i> Buat User Orang Tua (Bulk)
                    </a>
                </div>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-users-cog me-2"></i>Daftar User</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Nama</th>
                                <th width="15%">Email</th>
                                <th width="10%" class="text-center">Role</th>
                                <th width="20%">Linked To</th>
                                <th width="12%" class="text-center">Status</th>
                                <th width="13%" class="text-center">Dibuat</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr>
                                    <td class="text-center">{{ $users->firstItem() + $index }}</td>
                                    <td>
                                        <i class="fas fa-user text-primary me-2"></i>
                                        <strong>{{ $user->name }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if($user->role === 'admin')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-user-shield me-1"></i>Admin
                                            </span>
                                        @elseif($user->role === 'guru')
                                            <span class="badge bg-success">
                                                <i class="fas fa-chalkboard-teacher me-1"></i>Guru
                                            </span>
                                        @elseif($user->role === 'orangtua')
                                            <span class="badge bg-info">
                                                <i class="fas fa-user-friends me-1"></i>Orang Tua
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->role === 'guru' && $user->guru)
                                            <div>
                                                <strong>{{ $user->guru->nama_guru }}</strong>
                                                <br>
                                                <small class="text-muted">NIP: {{ $user->guru->nip }}</small>
                                            </div>
                                        @elseif($user->role === 'orangtua' && $user->orangTua)
                                            <div>
                                                <strong>{{ $user->orangTua->nama_orang_tua }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $user->orangTua->nomor_tlp }}</small>
                                            </div>
                                        @elseif($user->role === 'admin')
                                            <span class="text-muted">-</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Belum Linked</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($user->role === 'admin')
                                            <span class="badge bg-secondary">System Admin</span>
                                        @elseif(($user->role === 'guru' && $user->guru) || ($user->role === 'orangtua' && $user->orangTua))
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Incomplete
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <small>{{ $user->created_at->format('d M Y') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info"
                                                title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" title="Hapus"
                                                onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Form Delete (Hidden) -->
                                        <form id="delete-form-{{ $user->id }}"
                                            action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada user</p>
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i> Tambah User Pertama
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section - Style DataTables -->
                @if($users->hasPages())
                    <div class="row mt-3">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info" role="status" aria-live="polite">
                                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers float-end">
                                {{ $users->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* DataTables Style Pagination */
            .dataTables_info {
                padding-top: 8px;
                color: #6c757d;
                font-size: 14px;
            }

            .dataTables_paginate {
                padding-top: 0;
            }

            .pagination {
                margin-bottom: 0;
            }

            .page-link {
                color: #495057;
                background-color: #fff;
                border: 1px solid #dee2e6;
                padding: 6px 12px;
                font-size: 14px;
                line-height: 1.5;
                border-radius: 3px;
                margin: 0 2px;
            }

            .page-link:hover {
                color: #0056b3;
                background-color: #e9ecef;
                border-color: #dee2e6;
            }

            .page-item.active .page-link {
                z-index: 3;
                color: #fff;
                background-color: #007bff;
                border-color: #007bff;
            }

            .page-item.disabled .page-link {
                color: #6c757d;
                pointer-events: none;
                background-color: #fff;
                border-color: #dee2e6;
            }

            .page-item:first-child .page-link {
                border-top-left-radius: 3px;
                border-bottom-left-radius: 3px;
            }

            .page-item:last-child .page-link {
                border-top-right-radius: 3px;
                border-bottom-right-radius: 3px;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function confirmDelete(id, name) {
                if (confirm('Apakah Anda yakin ingin menghapus user "' + name + '"?\n\nRelasi dengan Guru/Orang Tua akan dilepas.')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        </script>
    @endpush
@endsection
