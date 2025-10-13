@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Buat User untuk Orang Tua (Bulk)</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User Management</a></li>
                        <li class="breadcrumb-item active">Bulk Create Orang Tua</li>
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

        <form action="{{ route('admin.users.bulk-orangtua.store') }}" method="POST">
            @csrf

            <div class="card card-custom mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user-friends me-2"></i>Bulk Create User untuk Orang Tua</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Cara Kerja:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Pilih orang tua yang ingin dibuatkan akun user</li>
                            <li>Email akan dibuat otomatis: <code>ortu_[NoHP]@sekolah.com</code></li>
                            <li>Nama user diambil dari nama orang tua</li>
                            <li>Password sama untuk semua akun yang dibuat</li>
                            <li>Hanya orang tua yang belum memiliki akun yang ditampilkan</li>
                        </ul>
                    </div>

                    @if($orangTuaTersedia->count() > 0)
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">
                                        Pilih Orang Tua <span class="text-danger">*</span>
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
                                        @foreach($orangTuaTersedia as $orangTua)
                                            <div class="form-check mb-3 p-3 border-bottom">
                                                <input class="form-check-input orangtua-checkbox" type="checkbox"
                                                    name="orang_tua_ids[]" value="{{ $orangTua->id }}"
                                                    id="orangtua_{{ $orangTua->id }}">
                                                <label class="form-check-label w-100" for="orangtua_{{ $orangTua->id }}">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <strong class="text-primary">{{ $orangTua->nama_orang_tua }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fas fa-phone me-1"></i>{{ $orangTua->nomor_tlp }}
                                                            </small>
                                                            <br>
                                                            <small class="text-info">
                                                                <i class="fas fa-arrow-right me-1"></i>
                                                                Email:
                                                                <code>ortu_{{ preg_replace('/[^0-9]/', '', $orangTua->nomor_tlp) }}@sekolah.com</code>
                                                            </small>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i
                                                                    class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($orangTua->alamat, 50) }}
                                                            </small>
                                                            @if($orangTua->siswas->count() > 0)
                                                                <br>
                                                                <small>
                                                                    <span class="badge bg-success">
                                                                        <i class="fas fa-users me-1"></i>
                                                                        {{ $orangTua->siswas->count() }} Anak
                                                                    </span>
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    @error('orang_tua_ids')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror

                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Dipilih: <strong id="selected-count">0</strong> dari {{ $orangTuaTersedia->count() }}
                                        orang tua
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
                                            <strong>Penting!</strong> Catat password ini dan berikan ke orang tua yang
                                            bersangkutan.
                                        </div>
                                    </div>
                                </div>

                                <div class="card bg-info text-white mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle me-2"></i>Info
                                        </h6>
                                        <ul class="mb-0 small">
                                            <li>Total orang tua tersedia: <strong>{{ $orangTuaTersedia->count() }}</strong></li>
                                            <li>Format email: <code>ortu_[NoHP]@sekolah.com</code></li>
                                            <li>Nama: Dari data orang tua</li>
                                            <li>Role: <strong>Orang Tua</strong></li>
                                            <li>Auto-link ke data orang tua</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Tidak ada orang tua yang tersedia!</strong>
                            <br>
                            Semua orang tua sudah memiliki akun user atau belum ada data orang tua.
                            <br>
                            <a href="{{ route('admin.orangtua.create') }}" class="alert-link">Tambah orang tua baru</a>
                        </div>
                    @endif
                </div>
            </div>

            @if($orangTuaTersedia->count() > 0)
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-info">
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
            const checkboxes = document.querySelectorAll('.orangtua-checkbox');
            const selectedCount = document.getElementById('selected-count');

            function updateCount() {
                const checked = document.querySelectorAll('.orangtua-checkbox:checked').length;
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
