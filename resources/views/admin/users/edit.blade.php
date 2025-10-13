@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Edit User</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User Management</a></li>
                        <li class="breadcrumb-item active">Edit User</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card card-custom mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Informasi User</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Nama: <strong>{{ $user->name }}</strong> |
                        Email: <strong>{{ $user->email }}</strong> |
                        Role: <strong>{{ ucfirst($user->role) }}</strong>
                        <br>
                        <small>Kosongkan password jika tidak ingin mengubah</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name', $user->name) }}"
                                        placeholder="Masukkan nama lengkap" required>
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                        name="email" value="{{ old('email', $user->email) }}" placeholder="contoh@email.com"
                                        required>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    Password Baru <span class="text-muted">(Opsional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Minimal 8 karakter">
                                </div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    Konfirmasi Password Baru
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Ulangi password baru">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">
                                    Role <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role"
                                    required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin
                                    </option>
                                    <option value="guru" {{ old('role', $user->role) == 'guru' ? 'selected' : '' }}>Guru
                                    </option>
                                    <option value="orangtua" {{ old('role', $user->role) == 'orangtua' ? 'selected' : '' }}>
                                        Orang Tua</option>
                                </select>
                                @error('role')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="guru-section" style="display: none;">
                                <label for="guru_id" class="form-label">
                                    Link ke Guru <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('guru_id') is-invalid @enderror" id="guru_id"
                                    name="guru_id">
                                    <option value="">-- Pilih Guru --</option>
                                    @foreach($guruTersedia as $guru)
                                        <option value="{{ $guru->id }}" {{ old('guru_id', $user->guru ? $user->guru->id : '') == $guru->id ? 'selected' : '' }}>
                                            {{ $guru->nama_guru }} - {{ $guru->nip }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('guru_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @if($user->guru)
                                    <small class="text-info">
                                        <i class="fas fa-info-circle"></i> Saat ini:
                                        <strong>{{ $user->guru->nama_guru }}</strong>
                                    </small>
                                @endif
                            </div>

                            <div class="mb-3" id="orangtua-section" style="display: none;">
                                <label for="orang_tua_id" class="form-label">
                                    Link ke Orang Tua <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('orang_tua_id') is-invalid @enderror" id="orang_tua_id"
                                    name="orang_tua_id">
                                    <option value="">-- Pilih Orang Tua --</option>
                                    @foreach($orangTuaTersedia as $orangTua)
                                        <option value="{{ $orangTua->id }}" {{ old('orang_tua_id', $user->orangTua ? $user->orangTua->id : '') == $orangTua->id ? 'selected' : '' }}>
                                            {{ $orangTua->nama_orang_tua }} - {{ $orangTua->nomor_tlp }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('orang_tua_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @if($user->orangTua)
                                    <small class="text-info">
                                        <i class="fas fa-info-circle"></i> Saat ini:
                                        <strong>{{ $user->orangTua->nama_orang_tua }}</strong>
                                    </small>
                                @endif
                            </div>

                            <div id="admin-info" style="display: none;">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Role Admin</strong> tidak perlu di-link ke Guru atau Orang Tua.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update User
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            const roleSelect = document.getElementById('role');
            const guruSection = document.getElementById('guru-section');
            const orangtuaSection = document.getElementById('orangtua-section');
            const adminInfo = document.getElementById('admin-info');
            const guruSelect = document.getElementById('guru_id');
            const orangtuaSelect = document.getElementById('orang_tua_id');

            function updateRoleSections() {
                const role = roleSelect.value;

                // Hide all sections first
                guruSection.style.display = 'none';
                orangtuaSection.style.display = 'none';
                adminInfo.style.display = 'none';

                // Remove required attribute
                guruSelect.removeAttribute('required');
                orangtuaSelect.removeAttribute('required');

                // Show relevant section
                if (role === 'guru') {
                    guruSection.style.display = 'block';
                    guruSelect.setAttribute('required', 'required');
                } else if (role === 'orangtua') {
                    orangtuaSection.style.display = 'block';
                    orangtuaSelect.setAttribute('required', 'required');
                } else if (role === 'admin') {
                    adminInfo.style.display = 'block';
                }
            }

            roleSelect.addEventListener('change', updateRoleSections);

            // Trigger on page load
            updateRoleSections();
        </script>
    @endpush
@endsection
