@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Detail User</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User Management</a></li>
                        <li class="breadcrumb-item active">Detail User</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Profile Card -->
            <div class="col-lg-4">
                <div class="card card-custom mb-3">
                    <div class="card-body text-center">
                        <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;
                                    @if($user->role === 'admin') background: #dc3545;
                                    @elseif($user->role === 'guru') background: #28a745;
                                    @else background: #17a2b8; @endif">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-2">{{ $user->email }}</p>
                        <p class="mb-3">
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
                        </p>

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit User
                            </a>

                            <!-- Reset Password Button -->
                            <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                data-bs-target="#resetPasswordModal">
                                <i class="fas fa-key me-1"></i> Reset Password
                            </button>

                            @if($user->role !== 'admin' || \App\Models\User::where('role', 'admin')->count() > 1)
                                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                    <i class="fas fa-trash me-1"></i> Hapus User
                                </button>
                            @endif

                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>

                        <!-- Form Delete (Hidden) -->
                        <form id="delete-form" action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 pb-3 border-bottom">
                            <label class="text-muted small">Nama</label>
                            <h6 class="mb-0">
                                <i class="fas fa-user text-primary me-2"></i>
                                {{ $user->name }}
                            </h6>
                        </div>

                        <div class="mb-3 pb-3 border-bottom">
                            <label class="text-muted small">Email</label>
                            <h6 class="mb-0">
                                <i class="fas fa-envelope text-info me-2"></i>
                                {{ $user->email }}
                            </h6>
                        </div>

                        <div class="mb-3 pb-3 border-bottom">
                            <label class="text-muted small">Role</label>
                            <h6 class="mb-0">
                                <i class="fas fa-user-tag text-success me-2"></i>
                                {{ ucfirst($user->role) }}
                            </h6>
                        </div>

                        <div class="mb-3 pb-3 border-bottom">
                            <label class="text-muted small">Dibuat</label>
                            <h6 class="mb-0">
                                <i class="fas fa-calendar-plus text-info me-2"></i>
                                {{ $user->created_at->format('d M Y H:i') }}
                            </h6>
                        </div>

                        <div class="mb-0">
                            <label class="text-muted small">Terakhir Update</label>
                            <h6 class="mb-0">
                                <i class="fas fa-calendar-check text-warning me-2"></i>
                                {{ $user->updated_at->format('d M Y H:i') }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Information -->
            <div class="col-lg-8">
                @if($user->role === 'guru' && $user->guru)
                    <!-- Info Guru -->
                    <div class="card card-custom mb-3">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Informasi Guru</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small">Nama Guru</label>
                                        <h6>
                                            <i class="fas fa-user text-success me-2"></i>
                                            {{ $user->guru->nama_guru }}
                                        </h6>
                                    </div>

                                    <div class="mb-3">
                                        <label class="text-muted small">NIP</label>
                                        <h6>
                                            <i class="fas fa-id-badge text-primary me-2"></i>
                                            {{ $user->guru->nip }}
                                        </h6>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small">Role Guru</label>
                                        <h6>
                                            @if($user->guru->is_wali_kelas)
                                                <span class="badge bg-info me-1">Wali Kelas</span>
                                            @endif
                                            @if($user->guru->is_guru_mapel)
                                                <span class="badge bg-warning text-dark">Guru Mapel</span>
                                            @endif
                                        </h6>
                                    </div>

                                    @if($user->guru->is_wali_kelas && $user->guru->kelasWali)
                                        <div class="mb-3">
                                            <label class="text-muted small">Wali Kelas</label>
                                            <h6>
                                                <i class="fas fa-door-open text-primary me-2"></i>
                                                {{ $user->guru->kelasWali->nama }}
                                            </h6>
                                        </div>
                                    @endif
                                </div>

                                @if($user->guru->is_guru_mapel && $user->guru->mapels->count() > 0)
                                    <div class="col-md-12">
                                        <div class="mb-0">
                                            <label class="text-muted small">Mata Pelajaran yang Diampu</label>
                                            <div class="mt-2">
                                                @foreach($user->guru->mapels as $mapel)
                                                    <span class="badge bg-warning text-dark me-1 mb-1">
                                                        {{ $mapel->nama_matapelajaran }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('admin.guru.show', $user->guru->id) }}"
                                    class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-eye me-1"></i> Lihat Profil Guru Lengkap
                                </a>
                            </div>
                        </div>
                    </div>

                @elseif($user->role === 'orangtua' && $user->orangTua)
                    <!-- Info Orang Tua -->
                    <div class="card card-custom mb-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-user-friends me-2"></i>Informasi Orang Tua</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small">Nama Orang Tua</label>
                                        <h6>
                                            <i class="fas fa-user text-info me-2"></i>
                                            {{ $user->orangTua->nama_orang_tua }}
                                        </h6>
                                    </div>

                                    <div class="mb-3">
                                        <label class="text-muted small">Nomor Telepon</label>
                                        <h6>
                                            <i class="fas fa-phone text-success me-2"></i>
                                            <a href="tel:{{ $user->orangTua->nomor_tlp }}" class="text-decoration-none">
                                                {{ $user->orangTua->nomor_tlp }}
                                            </a>
                                        </h6>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small">Alamat</label>
                                        <p class="mb-0">
                                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                            {{ $user->orangTua->alamat }}
                                        </p>
                                    </div>
                                </div>

                                @if($user->orangTua->siswas->count() > 0)
                                    <div class="col-md-12">
                                        <div class="mb-0">
                                            <label class="text-muted small">Anak</label>
                                            <div class="table-responsive mt-2">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Nama Siswa</th>
                                                            <th>NIS</th>
                                                            <th>Kelas</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($user->orangTua->siswas as $siswa)
                                                            <tr>
                                                                <td>{{ $siswa->nama }}</td>
                                                                <td>{{ $siswa->nis }}</td>
                                                                <td>
                                                                    @if($siswa->kelas)
                                                                        <span class="badge bg-primary">{{ $siswa->kelas->nama }}</span>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('admin.orangtua.show', $user->orangTua->id) }}"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye me-1"></i> Lihat Profil Orang Tua Lengkap
                                </a>
                            </div>
                        </div>
                    </div>

                @elseif($user->role === 'admin')
                    <!-- Info Admin -->
                    <div class="card card-custom">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Informasi Admin</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Admin System</strong> memiliki akses penuh ke seluruh sistem.
                                <br>
                                Admin tidak terhubung dengan data Guru atau Orang Tua.
                            </div>
                        </div>
                    </div>

                @else
                    <!-- User Belum Linked -->
                    <div class="card card-custom">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Status User</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>User ini belum terhubung</strong> dengan data Guru atau Orang Tua.
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="alert-link">
                                    Edit user untuk menghubungkan
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="resetPasswordModalLabel">
                            <i class="fas fa-key me-2"></i>Reset Password
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Reset password untuk user: <strong>{{ $user->name }}</strong>
                            <br>
                            <small>Email: {{ $user->email }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="modal_password" class="form-label">
                                Password Baru <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="modal_password" name="password"
                                placeholder="Minimal 8 karakter" required>
                        </div>

                        <div class="mb-3">
                            <label for="modal_password_confirmation" class="form-label">
                                Konfirmasi Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="modal_password_confirmation"
                                name="password_confirmation" placeholder="Ulangi password baru" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-key me-1"></i> Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete() {
                if (confirm('Apakah Anda yakin ingin menghapus user "{{ $user->name }}"?\n\nEmail: {{ $user->email }}\n\nRelasi dengan Guru/Orang Tua akan dilepas.')) {
                    document.getElementById('delete-form').submit();
                }
            }
        </script>
    @endpush
@endsection
