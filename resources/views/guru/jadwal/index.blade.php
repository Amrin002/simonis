@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-calendar-alt me-2"></i>Jadwal Mengajar
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar-day me-1"></i> {{ now()->isoFormat('dddd, D MMMM YYYY') }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.jadwal.today') }}" class="btn btn-primary me-2">
                            <i class="fas fa-clock me-1"></i> Jadwal Hari Ini
                        </a>
                        <a href="{{ route('guru.jadwal.weekly-summary') }}" class="btn btn-info me-2">
                            <i class="fas fa-calendar-week me-1"></i> Ringkasan Mingguan
                        </a>
                        {{-- <a href="{{ route('guru.jadwal.export-pdf') }}" class="btn btn-danger me-2">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                        </a> --}}
                        <a href="{{ route('guru.jadwal.print') }}" class="btn btn-secondary" target="_blank">
                            <i class="fas fa-print me-1"></i> Print
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total_jadwal'] }}</h3>
                            <p class="text-muted mb-0">Total Jadwal</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total_kelas'] }}</h3>
                            <p class="text-muted mb-0">Kelas Diajar</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-book"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total_mapel'] }}</h3>
                            <p class="text-muted mb-0">Mata Pelajaran</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['jadwal_hari_ini'] }}</h3>
                            <p class="text-muted mb-0">Jadwal Hari Ini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <form action="{{ route('guru.jadwal.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="fas fa-calendar-day me-1"></i>Filter Hari
                                    </label>
                                    <select name="hari" class="form-select">
                                        <option value="">Semua Hari</option>
                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                                            <option value="{{ $h }}" {{ $hari == $h ? 'selected' : '' }}>
                                                {{ $h }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="fas fa-door-open me-1"></i>Filter Kelas
                                    </label>
                                    <select name="kelas" class="form-select">
                                        <option value="">Semua Kelas</option>
                                        @foreach($kelasList as $k)
                                            <option value="{{ $k->id }}" {{ $kelas == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-filter me-1"></i> Filter
                                        </button>
                                        <a href="{{ route('guru.jadwal.index') }}" class="btn btn-secondary w-100">
                                            <i class="fas fa-redo me-1"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Jadwal per Hari --}}
        @if($jadwalGrouped->count() > 0)
            <div class="row">
                <div class="col-md-12">
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hariItem)
                        @if(isset($jadwalGrouped[$hariItem]) && $jadwalGrouped[$hariItem]->count() > 0)
                            <div class="card card-custom mb-4">
                                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 text-white">
                                            <i class="fas fa-calendar-day me-2"></i>{{ $hariItem }}
                                        </h5>
                                        <div>
                                            <span class="badge bg-light text-dark">
                                                {{ $jadwalGrouped[$hariItem]->count() }} Jadwal
                                            </span>
                                            <a href="{{ route('guru.jadwal.by-day', $hariItem) }}" class="btn btn-light btn-sm ms-2">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th width="15%">Waktu</th>
                                                    <th width="25%">Mata Pelajaran</th>
                                                    <th width="15%">Kelas</th>
                                                    <th width="15%">Durasi</th>
                                                    <th width="15%">Status</th>
                                                    <th width="10%" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($jadwalGrouped[$hariItem] as $index => $jadwal)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            <strong class="text-primary">
                                                                {{ $jadwal->waktu_mulai_format }}
                                                            </strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                {{ $jadwal->waktu_selesai_format }}
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <i class="fas fa-book text-primary me-2"></i>
                                                                <strong>{{ $jadwal->mapel->nama_matapelajaran }}</strong>
                                                            </div>
                                                            <small class="text-muted">
                                                                {{ $jadwal->mapel->kode }}
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">
                                                                <i class="fas fa-door-open me-1"></i>
                                                                {{ $jadwal->kelas->nama }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <i class="fas fa-hourglass-half text-warning me-1"></i>
                                                            {{ $jadwal->durasi_format }}
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $jadwal->status_badge_color }}">
                                                                {{ $jadwal->status_label }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="{{ route('guru.jadwal.show', $jadwal->id) }}"
                                                                class="btn btn-sm btn-info" title="Detail">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada jadwal ditemukan</h5>
                                <p class="text-muted">
                                    @if($hari || $kelas)
                                        Coba ubah filter atau reset untuk melihat semua jadwal
                                    @else
                                        Anda belum memiliki jadwal mengajar
                                    @endif
                                </p>
                                @if($hari || $kelas)
                                    <a href="{{ route('guru.jadwal.index') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-redo me-1"></i> Reset Filter
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('style')
    <style>
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
    </style>
@endpush

@push('scripts')
    <script>
        // Auto refresh status setiap 1 menit
        setInterval(function () {
            location.reload();
        }, 60000);
    </script>
@endpush
