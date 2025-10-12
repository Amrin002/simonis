@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Kelola Data Kelas</h1>
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
                <h5 class="mb-0"><i class="fas fa-door-open me-2"></i>Daftar Kelas</h5>
                <a href="{{ route('admin.kelas.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Kelas
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Nama Kelas</th>
                                <th width="25%">Wali Kelas</th>
                                <th width="15%">Jumlah Siswa</th>
                                <th width="20%">Status</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kelas as $index => $item)
                                <tr>
                                    <td>{{ $kelas->firstItem() + $index }}</td>
                                    <td>
                                        <strong class="text-primary">
                                            <i class="fas fa-graduation-cap me-2"></i>{{ $item->nama }}
                                        </strong>
                                    </td>
                                    <td>
                                        @if($item->waliKelas)
                                            <i class="fas fa-user-tie text-success me-2"></i>
                                            <strong>{{ $item->waliKelas->nama_guru }}</strong>
                                            <br>
                                            <small class="text-muted">NIP: {{ $item->waliKelas->nip }}</small>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-circle me-1"></i>Belum Ada Wali Kelas
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->siswas->count() > 0)
                                            <span class="badge bg-info" style="font-size: 1rem;">
                                                <i class="fas fa-users me-1"></i>{{ $item->siswas->count() }} Siswa
                                            </span>
                                        @else
                                            <span class="text-muted">Belum ada siswa</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->waliKelas && $item->siswas->count() > 0)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Lengkap
                                            </span>
                                        @elseif($item->waliKelas || $item->siswas->count() > 0)
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Belum Lengkap
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-info-circle me-1"></i>Baru Dibuat
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.kelas.show', $item->id) }}" class="btn btn-info btn-sm"
                                                title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.kelas.edit', $item->id) }}" class="btn btn-warning btn-sm"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" title="Hapus"
                                                onclick="confirmDelete({{ $item->id }}, '{{ $item->nama }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Form Delete (Hidden) -->
                                        <form id="delete-form-{{ $item->id }}"
                                            action="{{ route('admin.kelas.destroy', $item->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada data kelas</p>
                                        <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i> Tambah Kelas Pertama
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($kelas->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $kelas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete(id, namaKelas) {
                if (confirm('Apakah Anda yakin ingin menghapus kelas "' + namaKelas + '"?\n\nPastikan tidak ada siswa di kelas ini.')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        </script>
    @endpush
@endsection
