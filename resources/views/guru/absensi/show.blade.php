@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-eye me-2"></i>Detail Absensi
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i> Informasi lengkap absensi kelas
                            {{ $absen->kelas->nama }}
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
                                    <br>
                                    <small class="text-muted">{{ $absen->detailAbsens->count() }} siswa</small>
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
                                        <span class="badge bg-secondary" style="font-size: 0.9rem;">
                                            <i class="fas fa-users me-1"></i>Absensi Harian
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <small class="text-muted d-block">Waktu Input</small>
                                    <strong class="text-warning">
                                        <i class="fas fa-clock me-1"></i>{{ $absen->created_at->isoFormat('D MMM Y') }}
                                    </strong>
                                    <br>
                                    <small class="text-muted">{{ $absen->created_at->format('H:i') }} WIB</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card stats-hadir">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $absen->jumlah_hadir }}</h3>
                            <p class="text-muted mb-0">Hadir</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card stats-sakit">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);">
                            <i class="fas fa-thermometer"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $absen->jumlah_sakit }}</h3>
                            <p class="text-muted mb-0">Sakit</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card stats-izin">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $absen->jumlah_izin }}</h3>
                            <p class="text-muted mb-0">Izin</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card stats-alpa">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #fd79a8 0%, #e84393 100%);">
                            <i class="fas fa-times"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $absen->jumlah_alpa }}</h3>
                            <p class="text-muted mb-0">Alpa</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Presentase Kehadiran --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Presentase Kehadiran
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="progress" style="height: 40px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $absen->presentase_kehadiran }}%"
                                        aria-valuenow="{{ $absen->presentase_kehadiran }}" aria-valuemin="0"
                                        aria-valuemax="100">
                                        <strong
                                            style="font-size: 1.2rem;">{{ number_format($absen->presentase_kehadiran, 1) }}%</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <h2 class="mb-0">
                                    @if($absen->presentase_kehadiran >= 80)
                                        <span class="badge bg-success" style="font-size: 1.5rem;">
                                            <i class="fas fa-smile me-1"></i>Sangat Baik
                                        </span>
                                    @elseif($absen->presentase_kehadiran >= 60)
                                        <span class="badge bg-warning text-dark" style="font-size: 1.5rem;">
                                            <i class="fas fa-meh me-1"></i>Cukup
                                        </span>
                                    @else
                                        <span class="badge bg-danger" style="font-size: 1.5rem;">
                                            <i class="fas fa-frown me-1"></i>Perlu Perhatian
                                        </span>
                                    @endif
                                </h2>
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
                                <i class="fas fa-users me-2"></i>Daftar Siswa
                            </h5>
                            <div>
                                <a href="{{ route('guru.absensi.edit', $absen->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i> Edit Absensi
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">NIS</th>
                                        <th width="30%">Nama Siswa</th>
                                        <th width="15%" class="text-center">Status</th>
                                        <th width="38%">Keterangan</th>
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
                                                        <span
                                                            class="avatar-initial">{{ substr($detail->siswa->nama, 0, 1) }}</span>
                                                    </div>
                                                    <strong>{{ $detail->siswa->nama }}</strong>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($detail->status == 'Hadir')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Hadir
                                                    </span>
                                                @elseif($detail->status == 'Sakit')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-thermometer me-1"></i>Sakit
                                                    </span>
                                                @elseif($detail->status == 'Izin')
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-file-alt me-1"></i>Izin
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>Alpa
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($detail->keterangan)
                                                    <span class="text-muted">
                                                        <i class="fas fa-comment-dots me-1"></i>{{ $detail->keterangan }}
                                                    </span>
                                                @else
                                                    <span class="text-muted fst-italic">-</span>
                                                @endif
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

        {{-- Action Buttons --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('guru.absensi.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                            </a>
                            <div>
                                <a href="{{ route('guru.absensi.edit', $absen->id) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $absen->id }})">
                                    <i class="fas fa-trash me-1"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete Form --}}
        <form id="delete-form-{{ $absen->id }}" action="{{ route('guru.absensi.destroy', $absen->id) }}" method="POST"
            class="d-none">
            @csrf
            @method('DELETE')
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

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .stats-card h3 {
            font-size: 2rem;
            font-weight: bold;
            color: #2d3748;
        }

        .table th {
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            font-size: 0.875rem;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
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

        .progress {
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
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
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Absensi?',
                text: "Data absensi akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

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
    </script>
@endpush
