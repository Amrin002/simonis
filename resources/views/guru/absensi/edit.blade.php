@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-edit me-2"></i>Edit Absensi
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i> Edit data absensi kelas {{ $absen->kelas->nama }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.absensi.show', $absen->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-info-circle me-2"></i>Informasi Absensi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <small class="text-muted d-block">Tanggal</small>
                                    <strong class="text-primary">
                                        <i class="fas fa-calendar me-1"></i>{{ $absen->tanggal->isoFormat('D MMMM Y') }}
                                    </strong>
                                    <br>
                                    <small class="text-muted">{{ $absen->tanggal->isoFormat('dddd') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <small class="text-muted d-block">Kelas</small>
                                    <strong class="text-success">
                                        <i class="fas fa-door-open me-1"></i>{{ $absen->kelas->nama }}
                                    </strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <small class="text-muted d-block">Mata Pelajaran</small>
                                    @if($absen->mata_pelajaran)
                                        <strong class="text-info">
                                            <i class="fas fa-book me-1"></i>{{ $absen->mata_pelajaran }}
                                        </strong>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-users me-1"></i>Absensi Harian
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <small class="text-muted d-block">Kehadiran Saat Ini</small>
                                    <strong class="text-warning">
                                        <i class="fas fa-percentage me-1"></i>{{ number_format($absen->presentase_kehadiran, 1) }}%
                                    </strong>
                                    <br>
                                    <small class="text-muted">{{ $absen->jumlah_hadir }}/{{ $absen->detailAbsens->count() }} siswa</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Edit Absensi --}}
        <form action="{{ route('guru.absensi.update', $absen->id) }}" method="POST" id="formAbsensi">
            @csrf
            @method('PUT')

            {{-- Quick Action Buttons --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-magic me-2"></i>Aksi Cepat
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-0">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <span><i class="fas fa-wand-magic me-2"></i><strong>Set semua siswa dengan status yang sama</strong></span>
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

            {{-- Daftar Siswa --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-users me-2"></i>Daftar Siswa ({{ $absen->detailAbsens->count() }})
                                </h5>
                                <div>
                                    <span class="badge bg-light text-dark" id="countHadir">Hadir: {{ $absen->jumlah_hadir }}</span>
                                    <span class="badge bg-warning text-dark" id="countSakit">Sakit: {{ $absen->jumlah_sakit }}</span>
                                    <span class="badge bg-info text-dark" id="countIzin">Izin: {{ $absen->jumlah_izin }}</span>
                                    <span class="badge bg-danger" id="countAlpa">Alpa: {{ $absen->jumlah_alpa }}</span>
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
                                        @forelse($absen->detailAbsens as $index => $detail)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $detail->siswa->nis }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar me-2">
                                                            <span class="avatar-initial">{{ substr($detail->siswa->nama, 0, 1) }}</span>
                                                        </div>
                                                        <strong>{{ $detail->siswa->nama }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group w-100" role="group">
                                                        <input type="radio"
                                                               class="btn-check status-radio"
                                                               name="status[{{ $detail->id }}]"
                                                               id="hadir_{{ $detail->id }}"
                                                               value="Hadir"
                                                               data-status="Hadir"
                                                               {{ $detail->status == 'Hadir' ? 'checked' : '' }}
                                                               required>
                                                        <label class="btn btn-outline-success btn-sm" for="hadir_{{ $detail->id }}">
                                                            <i class="fas fa-check me-1"></i>Hadir
                                                        </label>

                                                        <input type="radio"
                                                               class="btn-check status-radio"
                                                               name="status[{{ $detail->id }}]"
                                                               id="sakit_{{ $detail->id }}"
                                                               value="Sakit"
                                                               data-status="Sakit"
                                                               {{ $detail->status == 'Sakit' ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-warning btn-sm" for="sakit_{{ $detail->id }}">
                                                            <i class="fas fa-thermometer me-1"></i>Sakit
                                                        </label>

                                                        <input type="radio"
                                                               class="btn-check status-radio"
                                                               name="status[{{ $detail->id }}]"
                                                               id="izin_{{ $detail->id }}"
                                                               value="Izin"
                                                               data-status="Izin"
                                                               {{ $detail->status == 'Izin' ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-info btn-sm" for="izin_{{ $detail->id }}">
                                                            <i class="fas fa-file-alt me-1"></i>Izin
                                                        </label>

                                                        <input type="radio"
                                                               class="btn-check status-radio"
                                                               name="status[{{ $detail->id }}]"
                                                               id="alpa_{{ $detail->id }}"
                                                               value="Alpa"
                                                               data-status="Alpa"
                                                               {{ $detail->status == 'Alpa' ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-danger btn-sm" for="alpa_{{ $detail->id }}">
                                                            <i class="fas fa-times me-1"></i>Alpa
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text"
                                                           class="form-control form-control-sm"
                                                           name="keterangan[{{ $detail->id }}]"
                                                           placeholder="Keterangan (opsional)"
                                                           value="{{ $detail->keterangan }}">
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">Tidak ada data siswa</p>
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
                                <a href="{{ route('guru.absensi.show', $absen->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit">
                                    <i class="fas fa-save me-1"></i> Update Absensi
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
            // Check if all students have status
            const totalSiswa = {{ $absen->detailAbsens->count() }};
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

            // Confirm before update
            e.preventDefault();
            Swal.fire({
                title: 'Update Absensi?',
                text: "Data absensi akan diupdate!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Update!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    const btnSubmit = document.getElementById('btnSubmit');
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

                    // Submit form
                    e.target.submit();
                }
            });
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
