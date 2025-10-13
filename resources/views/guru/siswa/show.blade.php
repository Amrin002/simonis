@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-user-graduate me-2"></i>Detail Siswa
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Informasi lengkap data siswa
                        </p>
                    </div>
                    <div>
                        @if($guru->isWaliKelas() && $siswa->kelas_id === $guru->kelasWali->id)
                            <a href="{{ route('guru.pelanggaran.create', ['siswa_id' => $siswa->id]) }}"
                                class="btn btn-warning me-2">
                                <i class="fas fa-exclamation-triangle me-1"></i> Tambah Pelanggaran
                            </a>
                        @endif
                        <a href="{{ route('guru.siswa.index', request()->only('kelas_id')) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Profile Card --}}
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card card-custom">
                    <div class="card-body text-center py-5">
                        <div class="avatar-large mb-3">
                            {{ strtoupper(substr($siswa->nama, 0, 2)) }}
                        </div>
                        <h4 class="mb-1">{{ $siswa->nama }}</h4>
                        <p class="text-muted mb-3">
                            <i class="fas fa-id-card me-1"></i>
                            NIS: {{ $siswa->nis }}
                        </p>
                        <span class="badge bg-{{ $siswa->status_lengkap == 'Lengkap' ? 'success' : 'warning' }} px-3 py-2">
                            <i
                                class="fas fa-{{ $siswa->status_lengkap == 'Lengkap' ? 'check-circle' : 'exclamation-circle' }} me-1"></i>
                            {{ $siswa->status_lengkap }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-3">
                <div class="card card-custom">
                    <div class="card-header bg-gradient-primary">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-info-circle me-2"></i>Informasi Siswa
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">
                                    <i class="fas fa-user me-1"></i>Nama Lengkap
                                </label>
                                <p class="form-control-plaintext fw-bold">{{ $siswa->nama }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">
                                    <i class="fas fa-id-card me-1"></i>NIS
                                </label>
                                <p class="form-control-plaintext fw-bold">{{ $siswa->nis }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">
                                    <i class="fas fa-door-open me-1"></i>Kelas
                                </label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-primary">{{ $siswa->nama_kelas }}</span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>Wali Kelas
                                </label>
                                <p class="form-control-plaintext fw-bold">
                                    {{ $siswa->nama_wali_kelas ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Orang Tua & Siblings --}}
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card card-custom h-100">
                    <div class="card-header bg-gradient-success">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-user-friends me-2"></i>Data Orang Tua
                        </h5>
                    </div>
                    {{-- Data Orang Tua Section di guru.siswa.show --}}
                    <div class="card-body">
                        @if($siswa->hasOrangTua())
                            <div class="mb-3">
                                <label class="form-label text-muted">
                                    <i class="fas fa-user me-1"></i>Nama Orang Tua
                                </label>
                                <p class="form-control-plaintext fw-bold">{{ $siswa->orangTua->nama_orang_tua }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>Alamat
                                </label>
                                <p class="form-control-plaintext">{{ $siswa->orangTua->alamat ?? '-' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">
                                    <i class="fas fa-phone me-1"></i>No. HP
                                </label>
                                <p class="form-control-plaintext">{{ $siswa->orangTua->nomor_tlp ?? '-' }}</p>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-user-times fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Belum ada data orang tua</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card card-custom h-100">
                    <div class="card-header bg-gradient-info">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-users me-2"></i>Saudara Kandung
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($siswa->hasSiblings())
                            <p class="text-muted mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                Ditemukan {{ $siswa->siblings->count() }} saudara kandung
                            </p>
                            <div class="list-group">
                                @foreach($siswa->siblings as $sibling)
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle-small me-2">
                                                {{ strtoupper(substr($sibling->nama, 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <strong>{{ $sibling->nama }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-id-card me-1"></i>{{ $sibling->nis }}
                                                    @if($sibling->hasKelas())
                                                        | <i class="fas fa-door-open me-1"></i>{{ $sibling->kelas->nama }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Tidak ada saudara kandung</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card-small">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-small me-3"
                            style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $stats['total_pelanggaran'] }}</h4>
                            <p class="text-muted mb-0 small">Total Pelanggaran</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card-small">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-small me-3"
                            style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $stats['pelanggaran_ringan'] }}</h4>
                            <p class="text-muted mb-0 small">Ringan</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card-small">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-small me-3"
                            style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $stats['pelanggaran_sedang'] }}</h4>
                            <p class="text-muted mb-0 small">Sedang</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card-small">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-small me-3"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $stats['pelanggaran_berat'] }}</h4>
                            <p class="text-muted mb-0 small">Berat</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Riwayat Pelanggaran --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white">
                                <i class="fas fa-history me-2"></i>Riwayat Pelanggaran
                            </h5>
                            <span class="badge bg-light text-dark">
                                {{ $siswa->pelanggarans->count() }} Pelanggaran
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($siswa->pelanggarans->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="15%">Tanggal</th>
                                            <th width="30%">Jenis Pelanggaran</th>
                                            <th width="15%" class="text-center">Kategori</th>
                                            <th width="20%">Dicatat Oleh</th>
                                            <th width="15%">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($siswa->pelanggarans as $index => $pelanggaran)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong class="text-primary">
                                                        {{ $pelanggaran->tanggal_format }}
                                                    </strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $pelanggaran->tanggal->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <strong>{{ $pelanggaran->jenis_pelanggaran }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $pelanggaran->badge_color }}">
                                                        {{ $pelanggaran->kategori }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="fas fa-user-tie text-primary me-1"></i>
                                                    {{ $pelanggaran->nama_wali_kelas }}
                                                </td>
                                                <td>
                                                    @if($pelanggaran->keterangan)
                                                        <small class="text-muted">
                                                            {{ Str::limit($pelanggaran->keterangan, 50) }}
                                                        </small>
                                                    @else
                                                        <small class="text-muted">-</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                <h5 class="text-muted">Tidak Ada Pelanggaran</h5>
                                <p class="text-muted">Siswa ini belum memiliki catatan pelanggaran</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-custom .card-header {
            border-bottom: none;
            padding: 1.25rem;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 3rem;
            margin: 0 auto;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .avatar-circle-small {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .stats-card-small {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stats-card-small:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-3px);
        }

        .stats-icon-small {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
        }

        .stats-card-small h4 {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
        }

        .form-control-plaintext {
            padding: 0.5rem 0;
            margin-bottom: 0;
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

        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
        }

        .list-group-item {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
        }

        .list-group-item:hover {
            background-color: rgba(102, 126, 234, 0.05);
            border-color: #667eea;
        }
    </style>
@endpush
