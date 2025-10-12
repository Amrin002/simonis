@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Edit Data Guru</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.guru.index') }}">Data Guru</a></li>
                        <li class="breadcrumb-item active">Edit Guru</li>
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

        <form action="{{ route('admin.guru.update', $guru->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card card-custom mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Data Guru</h5>
                </div>
                <div class="card-body">
                    @if($guru->user)
                    <div class="alert alert-success mb-4">
                        <i class="fas fa-check-circle me-2"></i>
                        Guru ini sudah memiliki akun: <strong>{{ $guru->user->username }}</strong>
                    </div>
                    @else
                    <div class="alert alert-warning mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Guru ini belum memiliki akun login. Akun dapat dibuat melalui menu Kelola User.
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_guru" class="form-label">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text"
                                           class="form-control @error('nama_guru') is-invalid @enderror"
                                           id="nama_guru"
                                           name="nama_guru"
                                           value="{{ old('nama_guru', $guru->nama_guru) }}"
                                           placeholder="Masukkan nama lengkap guru"
                                           required>
                                </div>
                                @error('nama_guru')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nip" class="form-label">
                                    NIP (Nomor Induk Pegawai) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                    <input type="text"
                                           class="form-control @error('nip') is-invalid @enderror"
                                           id="nip"
                                           name="nip"
                                           value="{{ old('nip', $guru->nip) }}"
                                           placeholder="Contoh: 198501012010011001"
                                           required>
                                </div>
                                @error('nip')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom mb-3">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Peran & Penugasan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-success mb-3">
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="is_wali_kelas"
                                               name="is_wali_kelas"
                                               value="1"
                                               {{ old('is_wali_kelas', $guru->is_wali_kelas) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="is_wali_kelas">
                                            <i class="fas fa-user-tie text-success me-1"></i> Jadikan Wali Kelas
                                        </label>
                                    </div>

                                    <div id="kelas_section" style="display: none;">
                                        <label for="kelas_id" class="form-label">
                                            Pilih Kelas
                                        </label>
                                        <select class="form-select @error('kelas_id') is-invalid @enderror"
                                                id="kelas_id"
                                                name="kelas_id">
                                            <option value="">-- Pilih Kelas --</option>
                                            @foreach($kelasTersedia as $kelas)
                                            <option value="{{ $kelas->id }}"
                                                    {{ old('kelas_id', $guru->kelasWali?->id) == $kelas->id ? 'selected' : '' }}>
                                                {{ $kelas->nama }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('kelas_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        @if($guru->kelasWali)
                                        <small class="text-info">
                                            <i class="fas fa-info-circle"></i> Saat ini: <strong>{{ $guru->kelasWali->nama }}</strong>
                                        </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-info mb-3">
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="is_guru_mapel"
                                               name="is_guru_mapel"
                                               value="1"
                                               {{ old('is_guru_mapel', $guru->is_guru_mapel) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="is_guru_mapel">
                                            <i class="fas fa-book text-info me-1"></i> Jadikan Guru Mata Pelajaran
                                        </label>
                                    </div>

                                    <div id="mapel_section" style="display: none;">
                                        <label class="form-label">
                                            Pilih Mata Pelajaran
                                        </label>
                                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                            @foreach($mapels as $mapel)
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       name="mapel_ids[]"
                                                       value="{{ $mapel->id }}"
                                                       id="mapel_{{ $mapel->id }}"
                                                       {{ in_array($mapel->id, old('mapel_ids', $guru->mapels->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="mapel_{{ $mapel->id }}">
                                                    {{ $mapel->nama_matapelajaran }}
                                                    <small class="text-muted">({{ $mapel->kode_mapel }})</small>
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                        @error('mapel_ids')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        @if($guru->mapels->count() > 0)
                                        <small class="text-info">
                                            <i class="fas fa-info-circle"></i> Saat ini mengajar:
                                            <strong>{{ $guru->mapels->pluck('nama_matapelajaran')->join(', ') }}</strong>
                                        </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Catatan:</strong> Guru dapat menjadi Wali Kelas, Guru Mapel, atau keduanya sekaligus.
                    </div>
                </div>
            </div>

            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Data
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // Toggle kelas section
            document.getElementById('is_wali_kelas').addEventListener('change', function() {
                const kelasSection = document.getElementById('kelas_section');
                kelasSection.style.display = this.checked ? 'block' : 'none';
            });

            // Toggle mapel section
            document.getElementById('is_guru_mapel').addEventListener('change', function() {
                const mapelSection = document.getElementById('mapel_section');
                mapelSection.style.display = this.checked ? 'block' : 'none';
            });
            // Show sections on page load if checked
            window.addEventListener('DOMContentLoaded', function () {
                if (document.getElementById('is_wali_kelas').checked) {
                    document.getElementById('kelas_section').style.display = 'block';
                }
                if (document.getElementById('is_guru_mapel').checked) {
                    document.getElementById('mapel_section').style.display = 'block';
                }
            });
        </script>
    @endpush
@endsection
