@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Kelola Data Siswa</h1>
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
                <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Daftar Siswa</h5>
                <a href="{{ route('admin.siswa.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Siswa
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="12%">NIS</th>
                                <th width="20%">Nama Siswa</th>
                                <th width="15%" class="text-center">Kelas</th>
                                <th width="23%">Orang Tua</th>
                                <th width="15%" class="text-center">Status</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswas as $index => $siswa)
                                <tr>
                                    <td class="text-center">{{ $siswas->firstItem() + $index }}</td>
                                    <td><strong>{{ $siswa->nis }}</strong></td>
                                    <td>
                                        <i class="fas fa-user-graduate text-primary me-2"></i>
                                        <strong>{{ $siswa->nama }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if($siswa->kelas)
                                            <span class="badge bg-primary">
                                                <i class="fas fa-door-open me-1"></i>{{ $siswa->kelas->nama }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-circle me-1"></i>Belum Ada
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($siswa->orangTua)
                                            <div>
                                                <i class="fas fa-users text-info me-1"></i>
                                                <strong>{{ $siswa->orangTua->nama_orang_tua }}</strong>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-phone"></i> {{ $siswa->orangTua->nomor_tlp }}
                                            </small>
                                        @else
                                            <span class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i> Tidak Ada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($siswa->kelas && $siswa->orangTua)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Lengkap
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Belum Lengkap
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.siswa.show', $siswa->id) }}" class="btn btn-info"
                                                title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.siswa.edit', $siswa->id) }}" class="btn btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" title="Hapus"
                                                onclick="confirmDelete({{ $siswa->id }}, '{{ $siswa->nama }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Form Delete (Hidden) -->
                                        <form id="delete-form-{{ $siswa->id }}"
                                            action="{{ route('admin.siswa.destroy', $siswa->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada data siswa</p>
                                        <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i> Tambah Siswa Pertama
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section - Style DataTables -->
                @if($siswas->hasPages())
                    <div class="row mt-3">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info" role="status" aria-live="polite">
                                Showing {{ $siswas->firstItem() }} to {{ $siswas->lastItem() }} of {{ $siswas->total() }}
                                entries
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers float-end">
                                {{ $siswas->links('pagination::bootstrap-4') }}
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
            function confirmDelete(id, namaSiswa) {
                if (confirm('Apakah Anda yakin ingin menghapus siswa "' + namaSiswa + '"?\n\nSemua data terkait siswa ini akan ikut terhapus.')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        </script>
    @endpush
@endsection
