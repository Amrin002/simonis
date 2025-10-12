@extends('template.main')

@section('section')
<div class="content-wrapper">
    <div class="row mb-3">
        <div class="col-md-12">
            <h1 class="page-title">Kelola Jadwal Pelajaran</h1>
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
            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Daftar Jadwal</h5>
            <a href="{{ route('admin.jadwal.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Tambah Jadwal
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Hari</th>
                            <th width="12%">Waktu</th>
                            <th width="18%">Mata Pelajaran</th>
                            <th width="12%">Kelas</th>
                            <th width="20%">Guru Pengajar</th>
                            <th width="13%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwals as $index => $jadwal)
                        <tr>
                            <td>{{ $jadwals->firstItem() + $index }}</td>
                            <td>
                                <strong class="text-primary">{{ $jadwal->hari }}</strong>
                            </td>
                            <td>
                                <i class="fas fa-clock text-info me-1"></i>
                                <strong>{{ date('H:i', strtotime($jadwal->waktu_mulai)) }}</strong> -
                                <strong>{{ date('H:i', strtotime($jadwal->waktu_selesai)) }}</strong>
                            </td>
                            <td>
                                <i class="fas fa-book text-warning me-1"></i>
                                {{ $jadwal->mapel->nama_matapelajaran }}
                                <br>
                                <small class="text-muted">{{ $jadwal->mapel->kode_mapel }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $jadwal->kelas->nama }}</span>
                            </td>
                            <td>
                                <i class="fas fa-chalkboard-teacher text-success me-1"></i>
                                {{ $jadwal->guru->nama_guru }}
                                <br>
                                <small class="text-muted">NIP: {{ $jadwal->guru->nip }}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.jadwal.show', $jadwal->id) }}"
                                       class="btn btn-info btn-sm"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.jadwal.edit', $jadwal->id) }}"
                                       class="btn btn-warning btn-sm"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-danger btn-sm"
                                            title="Hapus"
                                            onclick="confirmDelete({{ $jadwal->id }}, '{{ $jadwal->mapel->nama_matapelajaran }}', '{{ $jadwal->kelas->nama }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Form Delete (Hidden) -->
                                <form id="delete-form-{{ $jadwal->id }}"
                                      action="{{ route('admin.jadwal.destroy', $jadwal->id) }}"
                                      method="POST"
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
                                <p class="text-muted">Belum ada jadwal pelajaran</p>
                                <a href="{{ route('admin.jadwal.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Tambah Jadwal Pertama
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($jadwals->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $jadwals->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(id, mapel, kelas) {
        if (confirm('Apakah Anda yakin ingin menghapus jadwal:\n\nMapel: ' + mapel + '\nKelas: ' + kelas + '?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
@endsection
