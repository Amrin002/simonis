@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-clipboard-check me-2"></i>Input Absensi Kelas {{ $kelas->nama }}
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i> Input absensi untuk siswa kelas {{ $kelas->nama }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.absensi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Warning if already absent today --}}
        @if($absenHariIni)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-warning border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                            <div>
                                <h5 class="mb-1">Perhatian!</h5>
                                <p class="mb-0">Sudah ada absensi untuk kelas ini pada tanggal hari ini. Silakan edit absensi yang sudah ada atau pilih tanggal lain.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Kelas Info Card --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-door-open me-2"></i>Informasi Kelas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <small class="text-muted d-block">Nama Kelas</small>
                                    <strong class="text-primary">{{ $kelas->nama }}</strong>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="info-box">
                                    <small class="text-muted d-block">Wali Kelas</small>
                                    <strong class="text-info">{{ $kelas->waliKelas->nama_guru ?? $guru->nama_guru }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <small class="text-muted d-block">Jumlah Siswa</small>
                                    <strong class="text-warning">{{ $siswas->count() }} Siswa</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Input Absensi --}}
        <form action="{{ route('guru.absensi.store-wali-kelas') }}" method="POST" id="formAbsensi">
            @csrf

            {{-- Form Header --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-edit me-2"></i>Data Absensi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="tanggal" class="form-label">
                                        <i class="fas fa-calendar me-1"></i>Tanggal <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           class="form-control @error('tanggal') is-invalid @enderror"
                                           id="tanggal"
                                           name="tanggal"
                                           value="{{ old('tanggal', date('Y-m-d')) }}"
                                           max="{{ date('Y-m-d') }}"
                                           required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Absensi untuk seluruh siswa kelas {{ $kelas->nama }}
                                    </small>
                                </div>
                            </div>

                            {{-- Quick Action Buttons --}}
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="alert alert-info mb-0">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                                            <span><i class="fas fa-magic me-2"></i><strong>Aksi Cepat:</strong> Set semua siswa dengan status yang sama</span>
                                            <div class="btn-group mt-2 mt-md-0" role="group">
                                                <button type="button" class="btn btn-sm btn-success" onclick="setAllStatus('Hadir')">
                                                    <i class="fas fa-check me-1"></i>Semua Hadir
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="setAllStatus('Sakit')">
                                                    <i class="fas fa-thermometer me-1"></i>Semua Sakit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-info" onclick="setAllStatus('Izin')">
                                                    <i class="fas fa-file-alt me-1"></i>Semua Izin
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="setAllStatus('Alpa')">
                                                    <i class="fas fa-times me-1"></i>Semua Alpa
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar Siswa --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-users me-2"></i>Daftar Siswa ({{ $siswas->count() }})
                                </h5>
                                <div>
                                    <span class="badge bg-light text-dark" id="countHadir">Hadir: 0</span>
                                    <span class="badge bg-warning text-dark" id="countSakit">Sakit: 0</span>
                                    <span class="badge bg-info text-dark" id="countIzin">Izin: 0</span>
                                    <span class="badge bg-danger" id="countAlpa">Alpa: 0</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="12%">NIS</th>
                                            <th width="25%">Nama Siswa</th>
                                            <th width="30%">Status Absensi</th>
                                            <th width="28%">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($siswas as $index => $siswa)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $siswa->nis }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar me-2">
                                                            <span class="avatar-initial">{{ substr($siswa->nama, 0, 1) }}</span>
                                                        </div>
                                                        <strong>{{ $siswa->nama }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group w-100" role="group">
                                                        <input type="radio"
                                                               class="btn-check status-radio"
                                                               name="status[{{ $siswa->id }}]"
                                                               id="hadir_{{ $siswa->id }}"
                                                               value="Hadir"
                                                               data-status="Hadir"
                                                               {{ old('status.' . $siswa->id) == 'Hadir' ? 'checked' : '' }}
                                                               required>
                                                        <label class="btn btn-outline-success btn-sm" for="hadir_{{ $siswa->id }}">
                                                            <i class="fas fa-check me-1"></i>Hadir
                                                        </label>

                                                        <input type="radio"
                                                               class="btn-check status-radio"
                                                               name="status[{{ $siswa->id }}]"
                                                               id="sakit_{{ $siswa->id }}"
                                                               value="Sakit"
                                                               data-status="Sakit"
                                                               {{ old('status.' . $siswa->id) == 'Sakit' ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-warning btn-sm" for="sakit_{{ $siswa->id }}">
                                                            <i class="fas fa-thermometer me-1"></i>Sakit
                                                        </label>

                                                        <input type="radio"
                                                               class="btn-check status-radio"
                                                               name="status[{{ $siswa->id }}]"
                                                               id="izin_{{ $siswa->id }}"
                                                               value="Izin"
                                                               data-status="Izin"
                                                               {{ old('status.' . $siswa->id) == 'Izin' ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-info btn-sm" for="izin_{{ $siswa->id }}">
                                                            <i class="fas fa-file-alt me-1"></i>Izin
                                                        </label>

                                                        <input type="radio"
                                                               class="btn-check status-radio"
                                                               name="status[{{ $siswa->id }}]"
                                                               id="alpa_{{ $siswa->id }}"
                                                               value="Alpa"
                                                               data-status="Alpa"
                                                               {{ old('status.' . $siswa->id) == 'Alpa' ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-danger btn-sm" for="alpa_{{ $siswa->id }}">
                                                            <i class="fas fa-times me-1"></i>Alpa
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text"
                                                           class="form-control form-control-sm"
                                                           name="keterangan[{{ $siswa->id }}]"
                                                           placeholder="Keterangan (opsional)"
                                                           value="{{ old('keterangan.' . $siswa->id) }}">
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">Tidak ada siswa di kelas ini</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('guru.absensi.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit">
                                    <i class="fas fa-save me-1"></i> Simpan Absensi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('style')
    <style>
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card-custom .card-header {
            border-bottom: none;
            padding: 1.25rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
        }

        .info-box {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .info-box strong {
            font-size: 1.1rem;
        }

        .table th {
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            font-size: 0.875rem;
            background-color: #f8f9fa;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
        }

        .avatar-initial {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            border-radius: 50%;
        }

        .btn-check:checked + label {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .alert {
            border-radius: 12px;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                border-radius: 0.25rem !important;
                margin-bottom: 5px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Set all status function
        function setAllStatus(status) {
            const radios = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
            radios.forEach(radio => {
                radio.checked = true;
            });
            updateCounter();

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: `Semua siswa diset ${status}`,
                timer: 1500,
                showConfirmButton: false
            });
        }

        // Update counter function
        function updateCounter() {
            const statuses = ['Hadir', 'Sakit', 'Izin', 'Alpa'];

            statuses.forEach(status => {
                const count = document.querySelectorAll(`input[type="radio"][value="${status}"]:checked`).length;
                const countElement = document.getElementById(`count${status}`);
                if (countElement) {
                    countElement.textContent = `${status}: ${count}`;
                }
            });
        }

        // Form validation
        document.getElementById('formAbsensi').addEventListener('submit', function(e) {
            const tanggal = document.getElementById('tanggal').value;

            if (!tanggal) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Tanggal harus diisi!'
                });
                return;
            }

            // Check if all students have status
            const totalSiswa = {{ $siswas->count() }};
            const totalChecked = document.querySelectorAll('input[type="radio"]:checked').length;

            if (totalChecked < totalSiswa) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Pastikan semua siswa sudah memiliki status absensi!'
                });
                return;
            }

            // Show loading
            const btnSubmit = document.getElementById('btnSubmit');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
        });

        // Update counter on page load and when radio changes
        document.addEventListener('DOMContentLoaded', function() {
            updateCounter();

            const radios = document.querySelectorAll('.status-radio');
            radios.forEach(radio => {
                radio.addEventListener('change', updateCounter);
            });
        });

        // Show success message
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Show error message
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}'
            });
        @endif

        // Validation errors
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: '<ul class="text-start">' +
                    @foreach($errors->all() as $error)
                        '<li>{{ $error }}</li>' +
                    @endforeach
                '</ul>'
            });
        @endif
    </script>
@endpush
