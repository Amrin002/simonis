@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Buat User untuk Guru (Bulk)</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User Management</a></li>
                        <li class="breadcrumb-item active">Bulk Create Guru</li>
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

        <form action="{{ route('admin.users.bulk-guru.store') }}" method="POST">
            @csrf

            <div class="card card-custom mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Bulk Create User untuk Guru</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Cara Kerja:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Pilih guru yang ingin dibuatkan akun user</li>
                            <li>Email akan dibuat otomatis: <code>guru_[NIP]@sekolah.com</code></li>
                            <li>Nama user diambil dari nama guru</li>
                            <li>Password sama untuk semua akun yang dibuat</li>
                            <li>Hanya guru yang belum memiliki akun yang ditampilkan</li>
                        </ul>
                    </div>

                    @if($guruTersedia->count() > 0)
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">
                                        Pilih Guru <span class="text-danger">*</span>
                                    </label>

                                    <div class="mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                            <i class="fas fa-check-square me-1"></i> Pilih Semua
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                                            <i class="fas fa-square me-1"></i> Batal Pilih Semua
                                        </button>
                                    </div>

                                    <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                        @foreach($guruTersedia as $guru)
                                            <div class="form-check mb-3 p-3 border-bottom">
                                                <input class="form-check-input guru-checkbox" type="checkbox" name="guru_ids[]"
                                                    value="{{ $guru->id }}" id="guru_{{ $guru->id }}">
                                                <label class="form-check-label w-100" for="guru_{{ $guru->id }}">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <strong class="text-primary">{{ $guru->nama_guru }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fas fa-id-badge me-1"></i>NIP: {{ $guru->nip }}
                                                            </small>
                                                            <br>
                                                            <small class="text-info">
                                                                <i class="fas fa-arrow-right me-1"></i>
                                                                Email: <code>guru_{{ $guru->nip }}@sekolah.com</code>
                                                            </small>
                                                            <br>
                                                            <small>
                                                                @if($guru->is_wali_kelas)
                                                                    <span class="badge bg-info me-1">Wali Kelas</span>
                                                                @endif
                                                                @if($guru->is_guru_mapel)
                                                                    <span class="badge bg-warning text-dark">Guru Mapel</span>
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    @error('guru_ids')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror

                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Dipilih: <strong id="selected-count">0</strong> dari {{ $guruTersedia->count() }} guru
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-lock me-2"></i>Password Default
                                        </h6>

                                        <div class="mb-3">
                                            <label for="default_password" class="form-label">
                                                Password <span class="text-danger">*</span>
                                            </label>
                                            <input type="password"
                                                class="form-control @error('default_password') is-invalid @enderror"
                                                id="default_password" name="default_password" placeholder="Minimal 8 karakter"
                                                required>
                                            @error('default_password')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Password yang sama untuk semua akun</small>
                                        </div>

                                        <div class="alert alert-warning mb-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Penting!</strong> Catat password ini dan berikan ke guru yang bersangkutan.
                                        </div>
                                    </div>
                                </div>

                                <div class="card bg-info text-white mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle me-2"></i>Info
                                        </h6>
                                        <ul class="mb-0 small">
                                            <li>Total guru tersedia: <strong>{{ $guruTersedia->count() }}</strong></li>
                                            <li>Format email: <code>guru_[NIP]@sekolah.com</code></li>
                                            <li>Nama: Dari data guru</li>
                                            <li>Role: <strong>Guru</strong></li>
                                            <li>Auto-link ke data guru</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Tidak ada guru yang tersedia!</strong>
                            <br>
                            Semua guru sudah memiliki akun user atau belum ada data guru.
                            <br>
                            <a href="{{ route('admin.guru.create') }}" class="alert-link">Tambah guru baru</a>
                        </div>
                    @endif
                </div>
            </div>

            @if($guruTersedia->count() > 0)
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-users-cog me-1"></i> Buat User
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="card card-custom">
                    <div class="card-body">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            @endif
        </form>
    </div>

    @push('scripts')
        <script>
            const checkboxes = document.querySelectorAll('.guru-checkbox');
            const selectedCount = document.getElementById('selected-count');

            function updateCount() {
                const checked = document.querySelectorAll('.guru-checkbox:checked').length;
                selectedCount.textContent = checked;
            }

            function selectAll() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                updateCount();
            }

            function deselectAll() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateCount();
            }

            // Update count on change
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateCount);
            });

            // Initial count
            updateCount();
        </script>
    @endpush
@endsection
