@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Kelola Data Guru</h1>
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

        <div class="card card-custom">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Daftar Guru</h5>
                <a href="{{ route('admin.guru.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Guru
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="12%">NIP</th>
                                <th width="18%">Nama Guru</th>
                                <th width="10%" class="text-center">Status Akun</th>
                                <th width="15%">Role</th>
                                <th width="12%" class="text-center">Kelas Wali</th>
                                <th width="18%">Mapel Diampu</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gurus as $index => $guru)
                                <tr>
                                    <td class="text-center">{{ $gurus->firstItem() + $index }}</td>
                                    <td><strong>{{ $guru->nip }}</strong></td>
                                    <td>{{ $guru->nama_guru }}</td>
                                    <td class="text-center">
                                        @if($guru->user)
                                            <span class="badge bg-success" title="Username: {{ $guru->user->username }}">
                                                <i class="fas fa-check-circle me-1"></i>Ada Akun
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-circle me-1"></i>Belum Ada
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($guru->is_wali_kelas && $guru->is_guru_mapel)
                                            <span class="badge bg-success"><i class="fas fa-user-tie me-1"></i>Wali Kelas</span>
                                            <span class="badge bg-info"><i class="fas fa-book me-1"></i>Guru Mapel</span>
                                        @elseif($guru->is_wali_kelas)
                                            <span class="badge bg-success"><i class="fas fa-user-tie me-1"></i>Wali Kelas</span>
                                        @elseif($guru->is_guru_mapel)
                                            <span class="badge bg-info"><i class="fas fa-book me-1"></i>Guru Mapel</span>
                                        @else
                                            <span class="badge bg-secondary">Guru</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($guru->kelasWali)
                                            <span class="badge bg-primary">{{ $guru->kelasWali->nama }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($guru->mapels->count() > 0)
                                            @foreach($guru->mapels->take(2) as $mapel)
                                                <span class="badge bg-warning text-dark">{{ $mapel->nama_matapelajaran }}</span>
                                            @endforeach
                                            @if($guru->mapels->count() > 2)
                                                <small class="text-muted">+{{ $guru->mapels->count() - 2 }} lainnya</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.guru.show', $guru->id) }}" class="btn btn-info"
                                                title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.guru.edit', $guru->id) }}" class="btn btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" title="Hapus"
                                                onclick="confirmDelete({{ $guru->id }}, '{{ $guru->nama_guru }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Form Delete (Hidden) -->
                                        <form id="delete-form-{{ $guru->id }}"
                                            action="{{ route('admin.guru.destroy', $guru->id) }}" method="POST"
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
                                        <p class="text-muted">Belum ada data guru</p>
                                        <a href="{{ route('admin.guru.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i> Tambah Guru Pertama
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section - Style DataTables -->
                @if($gurus->hasPages())
                    <div class="row mt-3">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info" role="status" aria-live="polite">
                                Showing {{ $gurus->firstItem() }} to {{ $gurus->lastItem() }} of {{ $gurus->total() }} entries
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers float-end">
                                {{ $gurus->links('pagination::bootstrap-4') }}
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
            function confirmDelete(id, namaGuru) {
                if (confirm('Apakah Anda yakin ingin menghapus guru "' + namaGuru + '"?\n\nData yang terkait akan dilepas dari guru ini.')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        </script>
    @endpush
@endsection
