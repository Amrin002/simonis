@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Kelola Data Orang Tua</h1>
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
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Daftar Orang Tua Siswa</h5>
                <a href="{{ route('admin.orangtua.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Orang Tua
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="18%">Nama Orang Tua</th>
                                <th width="13%">Nomor Telepon</th>
                                <th width="25%">Alamat</th>
                                <th width="12%" class="text-center">Status Akun</th>
                                <th width="12%" class="text-center">Jumlah Anak</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orangtuas as $index => $orangtua)
                                <tr>
                                    <td class="text-center">{{ $orangtuas->firstItem() + $index }}</td>
                                    <td><strong>{{ $orangtua->nama_orang_tua }}</strong></td>
                                    <td>
                                        <i class="fas fa-phone text-success me-1"></i>
                                        {{ $orangtua->nomor_tlp }}
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($orangtua->alamat, 50) }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if($orangtua->user)
                                            <span class="badge bg-success" title="Username: {{ $orangtua->user->username }}">
                                                <i class="fas fa-check-circle me-1"></i>Ada Akun
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-circle me-1"></i>Belum Ada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($orangtua->siswas->count() > 0)
                                            <span class="badge bg-info">
                                                <i class="fas fa-child me-1"></i>{{ $orangtua->siswas->count() }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.orangtua.show', $orangtua->id) }}" class="btn btn-info"
                                                title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.orangtua.edit', $orangtua->id) }}" class="btn btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" title="Hapus"
                                                onclick="confirmDelete({{ $orangtua->id }}, '{{ $orangtua->nama_orang_tua }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Form Delete (Hidden) -->
                                        <form id="delete-form-{{ $orangtua->id }}"
                                            action="{{ route('admin.orangtua.destroy', $orangtua->id) }}" method="POST"
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
                                        <p class="text-muted">Belum ada data orang tua</p>
                                        <a href="{{ route('admin.orangtua.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i> Tambah Orang Tua Pertama
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section - Style DataTables -->
                @if($orangtuas->hasPages())
                    <div class="row mt-3">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info" role="status" aria-live="polite">
                                Showing {{ $orangtuas->firstItem() }} to {{ $orangtuas->lastItem() }} of
                                {{ $orangtuas->total() }} entries
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers float-end">
                                {{ $orangtuas->links('pagination::bootstrap-4') }}
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
            function confirmDelete(id, namaOrangtua) {
                if (confirm('Apakah Anda yakin ingin menghapus orang tua "' + namaOrangtua + '"?\n\nPastikan tidak ada siswa yang terdaftar dengan orang tua ini.')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        </script>
    @endpush
@endsection
