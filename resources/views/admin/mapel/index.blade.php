@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Kelola Mata Pelajaran</h1>
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
                <h5 class="mb-0"><i class="fas fa-book me-2"></i>Daftar Mata Pelajaran</h5>
                <a href="{{ route('admin.mapel.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Mata Pelajaran
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Kode Mapel</th>
                                <th width="25%">Nama Mata Pelajaran</th>
                                <th width="15%">Jumlah Guru</th>
                                <th width="15%">Jumlah Jadwal</th>
                                <th width="13%">Status</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mapels as $index => $mapel)
                                <tr>
                                    <td>{{ $mapels->firstItem() + $index }}</td>
                                    <td>
                                        @if($mapel->kode_mapel)
                                            <span class="badge bg-primary">{{ $mapel->kode_mapel }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <i class="fas fa-book text-warning me-2"></i>
                                        <strong>{{ $mapel->nama_matapelajaran }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if($mapel->gurus_count > 0)
                                            <span class="badge bg-info">
                                                <i class="fas fa-chalkboard-teacher me-1"></i>{{ $mapel->gurus_count }} Guru
                                            </span>
                                        @else
                                            <span class="text-muted">Belum ada guru</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($mapel->jadwals_count > 0)
                                            <span class="badge bg-success">
                                                <i class="fas fa-calendar-alt me-1"></i>{{ $mapel->jadwals_count }} Jadwal
                                            </span>
                                        @else
                                            <span class="text-muted">Belum ada jadwal</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $mapel->status_badge_color }}">
                                            {{ $mapel->status_penggunaan }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.mapel.show', $mapel->id) }}" class="btn btn-info btn-sm"
                                                title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.mapel.edit', $mapel->id) }}" class="btn btn-warning btn-sm"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" title="Hapus"
                                                onclick="confirmDelete({{ $mapel->id }}, '{{ $mapel->nama_matapelajaran }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Form Delete (Hidden) -->
                                        <form id="delete-form-{{ $mapel->id }}"
                                            action="{{ route('admin.mapel.destroy', $mapel->id) }}" method="POST"
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
                                        <p class="text-muted">Belum ada mata pelajaran</p>
                                        <a href="{{ route('admin.mapel.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i> Tambah Mata Pelajaran Pertama
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($mapels->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $mapels->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete(id, namaMapel) {
                if (confirm('Apakah Anda yakin ingin menghapus mata pelajaran "' + namaMapel + '"?\n\nPastikan tidak ada jadwal yang menggunakan mata pelajaran ini.')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        </script>
    @endpush
@endsection
