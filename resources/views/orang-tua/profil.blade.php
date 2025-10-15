@extends('template-orangtua.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-user-circle me-2"></i>Profil Saya
                        </h1>
                        <p class="text-muted mb-0">
                            Kelola informasi profil dan data anak
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('orangtua.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Profile Card --}}
            <div class="col-lg-4 mb-4">
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>Informasi Akun
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="avatar-circle-large mb-3">
                            <i class="fas fa-user fa-4x text-white"></i>
                        </div>
                        <h4 class="mb-1">{{ $orangTua->nama_orang_tua }}</h4>
                        <p class="text-muted mb-3">
                            <i class="fas fa-envelope me-1"></i>{{ Auth::user()->email }}
                        </p>

                        <hr>

                        <div class="info-item text-start">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <strong>No. Telepon:</strong>
                            <span class="float-end">{{ $orangTua->nomor_tlp }}</span>
                        </div>

                        <div class="info-item text-start">
                            <i class="fas fa-users text-primary me-2"></i>
                            <strong>Jumlah Anak:</strong>
                            <span class="float-end badge bg-success">{{ $siswas->count() }} Anak</span>
                        </div>

                        <div class="info-item text-start">
                            <i class="fas fa-calendar text-primary me-2"></i>
                            <strong>Bergabung:</strong>
                            <span class="float-end">{{ Auth::user()->created_at->format('d/m/Y') }}</span>
                        </div>

                        <hr>

                        <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"
                            data-bs-target="#editProfilModal">
                            <i class="fas fa-edit me-2"></i>Edit Profil
                        </button>
                    </div>
                </div>

                {{-- Change Password Card --}}
                <div class="card card-custom mt-3">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-lock me-2"></i>Keamanan Akun
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Username:</strong> {{ Auth::user()->name }}
                        </div>

                        <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal"
                            data-bs-target="#changePasswordModal">
                            <i class="fas fa-key me-2"></i>Ubah Password
                        </button>
                    </div>
                </div>
            </div>

            {{-- Data Anak --}}
            <div class="col-lg-8 mb-4">
                <div class="card card-custom">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-user-graduate me-2"></i>Daftar Anak
                            </h5>
                            <span class="badge bg-light text-dark">{{ $siswas->count() }} Siswa</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($siswas->count() > 0)
                            <div class="row">
                                @foreach($siswas as $siswa)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border-0 shadow-sm hover-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-start mb-3">
                                                    <div class="avatar-siswa me-3"
                                                        style="background: {{ $siswa->jenis_kelamin == 'L' ? '#3498db' : '#e74c3c' }}">
                                                        <i class="fas fa-user-graduate fa-2x text-white"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1">{{ $siswa->nama }}</h5>
                                                        <p class="text-muted mb-0 small">
                                                            <i class="fas fa-id-card me-1"></i>{{ $siswa->nis }}
                                                        </p>
                                                    </div>
                                                    @if($siswa->jenis_kelamin)
                                                        <span
                                                            class="badge bg-{{ $siswa->jenis_kelamin == 'L' ? 'primary' : 'danger' }}">
                                                            <i class="fas fa-{{ $siswa->jenis_kelamin == 'L' ? 'mars' : 'venus' }}"></i>
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="info-box mb-2">
                                                    <small class="text-muted d-block">Kelas</small>
                                                    <strong>{{ $siswa->kelas->nama ?? '-' }}</strong>
                                                </div>

                                                <div class="info-box mb-3">
                                                    <small class="text-muted d-block">Wali Kelas</small>
                                                    <strong>{{ $siswa->kelas->waliKelas->nama_guru ?? '-' }}</strong>
                                                </div>

                                                <div class="d-grid">
                                                    <a href="{{ route('orangtua.detail-anak', $siswa->id) }}"
                                                        class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye me-1"></i>Lihat Detail
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum Ada Data Anak</h5>
                                <p class="text-muted mb-0">Silakan hubungi admin untuk menambahkan data anak.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="card card-custom mt-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Alamat Lengkap
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="address-box">
                            <i class="fas fa-home text-info me-2"></i>
                            <p class="mb-0">{{ $orangTua->alamat }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Profil --}}
    <div class="modal fade" id="editProfilModal" tabindex="-1" aria-labelledby="editProfilModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('orangtua.profil.update') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editProfilModalLabel">
                            <i class="fas fa-edit me-2"></i>Edit Profil
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Nama --}}
                        <div class="mb-3">
                            <label for="nama_orang_tua" class="form-label">
                                <i class="fas fa-user me-1"></i>Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('nama_orang_tua') is-invalid @enderror"
                                id="nama_orang_tua" name="nama_orang_tua"
                                value="{{ old('nama_orang_tua', $orangTua->nama_orang_tua) }}" required>
                            @error('nama_orang_tua')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nomor Telepon --}}
                        <div class="mb-3">
                            <label for="nomor_tlp" class="form-label">
                                <i class="fas fa-phone me-1"></i>Nomor Telepon <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('nomor_tlp') is-invalid @enderror" id="nomor_tlp"
                                name="nomor_tlp" value="{{ old('nomor_tlp', $orangTua->nomor_tlp) }}"
                                placeholder="08xxxxxxxxxx" required>
                            @error('nomor_tlp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: 08xxxxxxxxxx (10-13 digit)</small>
                        </div>

                        {{-- Alamat --}}
                        <div class="mb-3">
                            <label for="alamat" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Alamat Lengkap <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat"
                                rows="3" required>{{ old('alamat', $orangTua->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Pastikan nomor telepon aktif untuk menerima notifikasi WhatsApp.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Ubah Password --}}
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('orangtua.profil.password') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="changePasswordModalLabel">
                            <i class="fas fa-key me-2"></i>Ubah Password
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Password Lama --}}
                        <div class="mb-3">
                            <label for="password_lama" class="form-label">
                                <i class="fas fa-lock me-1"></i>Password Lama <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password_lama') is-invalid @enderror"
                                    id="password_lama" name="password_lama" required>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePassword('password_lama')">
                                    <i class="fas fa-eye" id="toggleIcon_password_lama"></i>
                                </button>
                                @error('password_lama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Password Baru --}}
                        <div class="mb-3">
                            <label for="password_baru" class="form-label">
                                <i class="fas fa-key me-1"></i>Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password_baru') is-invalid @enderror"
                                    id="password_baru" name="password_baru" required>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePassword('password_baru')">
                                    <i class="fas fa-eye" id="toggleIcon_password_baru"></i>
                                </button>
                                @error('password_baru')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Minimal 8 karakter</small>
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div class="mb-3">
                            <label for="password_baru_confirmation" class="form-label">
                                <i class="fas fa-check-circle me-1"></i>Konfirmasi Password Baru <span
                                    class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_baru_confirmation"
                                    name="password_baru_confirmation" required>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePassword('password_baru_confirmation')">
                                    <i class="fas fa-eye" id="toggleIcon_password_baru_confirmation"></i>
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <small><strong>Perhatian:</strong> Setelah mengubah password, Anda harus login ulang
                                menggunakan password baru.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-1"></i>Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .avatar-circle-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .avatar-siswa {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-item {
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-box {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .hover-card {
            transition: all 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        }

        .address-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #17a2b8;
            line-height: 1.8;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById('toggleIcon_' + fieldId);

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Show success message
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Show error message
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
            });
        @endif

            // Auto open edit profil modal if there are validation errors for profil
            @if ($errors->has('nama_orang_tua') || $errors->has('nomor_tlp') || $errors->has('alamat'))
                var editModal = new bootstrap.Modal(document.getElementById('editProfilModal'));
                editModal.show();
            @endif

            // Auto open change password modal if there are validation errors for password
            @if ($errors->has('password_lama') || $errors->has('password_baru'))
                var passwordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
                passwordModal.show();
            @endif
    </script>
@endpush
