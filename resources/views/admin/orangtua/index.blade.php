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
                                <th width="5%">No</th>
                                <th width="20%">Nama Orang Tua</th>
                                <th width="15%">Nomor Telepon</th>
                                <th width="25%">Alamat</th>
                                <th width="10%">Status Akun</th>
                                <th width="10%">Jumlah Anak</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orangtuas as $index => $orangtua)
                                <tr>
                                    <td>{{ $orangtuas->firstItem() + $index }}</td>
                                    <td><strong>{{ $orangtua->nama_orang_tua }}</strong></td>
                                    <td>
                                        <i class="fas fa-phone text-success me-1"></i>
                                        {{ $orangtua->nomor_tlp }}
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($orangtua->alamat, 50) }}</small>
                                    </td>
                                    <td>
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
                                                <i class="fas fa-child me-1"></i>{{ $orangtua->siswas->count() }} Anak
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.orangtua.show', $orangtua->id) }}"
                                                class="btn btn-info btn-sm" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.orangtua.edit', $orangtua->id) }}"
                                                class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" title="Hapus"
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

                @if($orangtuas->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $orangtuas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

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
