@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Detail Data Orang Tua</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.orangtua.index') }}">Data Orang Tua</a></li>
                        <li class="breadcrumb-item active">Detail Orang Tua</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Profile Card -->
            <div class="col-lg-4">
                <div class="card card-custom mb-3">
                    <div class="card-body text-center">
                        <div class="user-avatar mx-auto mb-3"
                            style="width: 100px; height: 100px; font-size: 2.5rem; background: #17a2b8;">
                            {{ strtoupper(substr($orangtua->nama_orang_tua, 0, 1)) }}
                        </div>
                        <h4 class="mb-1">{{ $orangtua->nama_orang_tua }}</h4>
                        <p class="text-muted mb-3">
                            <i class="fas fa-phone me-1"></i>{{ $orangtua->nomor_tlp }}
                        </p>

                        <div class="mb-3">
                            <span class="badge bg-info">
                                <i class="fas fa-child me-1"></i>
                                {{ $orangtua->siswas->count() }} Anak Terdaftar
                            </span>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.orangtua.edit', $orangtua->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit Data
                            </a>
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash me-1"></i> Hapus Orang Tua
                            </button>
                            <a href="{{ route('admin.orangtua.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>

                        <!-- Form Delete (Hidden) -->
                        <form id="delete-form" action="{{ route('admin.orangtua.destroy', $orangtua->id) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>

                <!-- Info Akun -->
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-user-circle me-2"></i>Informasi Akun</h6>
                    </div>
                    <div class="card-body">
                        @if($orangtua->user)
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Sudah Memiliki Akun</strong>
                            </div>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="40%"><i class="fas fa-user text-muted me-2"></i>Username</td>
                                    <td><strong>{{ $orangtua->user->username }}</strong></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-shield-alt text-muted me-2"></i>Role</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($orangtua->user->role) }}</span></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-plus text-muted me-2"></i>Dibuat</td>
                                    <td><small>{{ $orangtua->user->created_at->format('d M Y') }}</small></td>
                                </tr>
                            </table>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Belum Memiliki Akun</strong>
                                <p class="mb-0 small mt-2">Akun login dapat dibuat melalui menu Kelola User</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detail Information -->
            <div class="col-lg-8">
                <!-- Info Kontak -->
                <div class="card card-custom mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-address-card me-2"></i>Informasi Kontak</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">Nama Lengkap</label>
                                    <h6>{{ $orangtua->nama_orang_tua }}</h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">Nomor Telepon</label>
                                    <h6>
                                        <i class="fas fa-phone text-success me-2"></i>
                                        <a href="tel:{{ $orangtua->nomor_tlp }}" class="text-decoration-none">
                                            {{ $orangtua->nomor_tlp }}
                                        </a>
                                    </h6>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-0">
                                    <label class="text-muted small">Alamat Lengkap</label>
                                    <p class="mb-0">
                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                        {{ $orangtua->alamat }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Anak/Siswa -->
                <div class="card card-custom">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Daftar Anak (Siswa)</h5>
                    </div>
                    <div class="card-body">
                        @if($orangtua->siswas->count() > 0)
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Total {{ $orangtua->siswas->count() }} siswa</strong> terdaftar sebagai anak dari orang
                                tua ini
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>NIS</th>
                                            <th>Nama Siswa</th>
                                            <th>Kelas</th>
                                            <th width="10%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orangtua->siswas as $index => $siswa)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $siswa->nis }}</strong></td>
                                                <td>
                                                    <i class="fas fa-user-graduate text-primary me-2"></i>
                                                    {{ $siswa->nama }}
                                                </td>
                                                <td>
                                                    @if($siswa->kelas)
                                                        <span class="badge bg-primary">{{ $siswa->kelas->nama }}</span>
                                                    @else
                                                        <span class="text-muted">Belum ada kelas</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.siswa.show', $siswa->id) }}"
                                                        class="btn btn-info btn-sm" title="Lihat Detail Siswa">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Belum ada siswa</strong> yang terdaftar dengan orang tua ini
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete() {
                @if($orangtua->siswas->count() > 0)
                    alert('Tidak dapat menghapus orang tua yang masih memiliki siswa terdaftar!\n\nSilakan hapus atau pindahkan siswa terlebih dahulu.');
                @else
                    if (confirm('Apakah Anda yakin ingin menghapus orang tua "{{ $orangtua->nama_orang_tua }}"?')) {
                        document.getElementById('delete-form').submit();
                    }
                @endif
            }
        </script>
    @endpush
@endsection
