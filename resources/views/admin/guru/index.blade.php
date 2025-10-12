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
                                <th width="5%">No</th>
                                <th width="12%">NIP</th>
                                <th width="18%">Nama Guru</th>
                                <th width="10%">Status Akun</th>
                                <th width="15%">Role</th>
                                <th width="12%">Kelas Wali</th>
                                <th width="18%">Mapel Diampu</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gurus as $index => $guru)
                                <tr>
                                    <td>{{ $gurus->firstItem() + $index }}</td>
                                    <td><strong>{{ $guru->nip }}</strong></td>
                                    <td>{{ $guru->nama_guru }}</td>
                                    <td>
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
                                    <td>
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
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.guru.show', $guru->id) }}" class="btn btn-info btn-sm"
                                                title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.guru.edit', $guru->id) }}" class="btn btn-warning btn-sm"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" title="Hapus"
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

                @if($gurus->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $gurus->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

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
