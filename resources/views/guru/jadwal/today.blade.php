@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-clock me-2"></i>Jadwal Hari Ini
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar-day me-1"></i> {{ $hari }}, {{ now()->isoFormat('D MMMM YYYY') }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.jadwal.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Semua Jadwal
                        </a>
                        <a href="{{ route('guru.jadwal.weekly-summary') }}" class="btn btn-info me-2">
                            <i class="fas fa-calendar-week me-1"></i> Ringkasan Mingguan
                        </a>
                        {{-- <a href="{{ route('guru.jadwal.export-pdf') }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                        </a> --}}
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
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $jadwalToday->count() }}</h3>
                            <p class="text-muted mb-0">Total Jadwal</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-circle-dot"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $jadwalAktif->count() }}</h3>
                            <p class="text-muted mb-0">Sedang Berlangsung</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $jadwalUpcoming->count() }}</h3>
                            <p class="text-muted mb-0">Akan Datang</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $jadwalFinished->count() }}</h3>
                            <p class="text-muted mb-0">Selesai</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($jadwalToday->count() > 0)
            {{-- Jadwal Sedang Berlangsung --}}
            @if($jadwalAktif->count() > 0)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card card-custom border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-circle-dot me-2 blink-animation"></i>
                                    Sedang Berlangsung
                                </h5>
                            </div>
                            <div class="card-body">
                                @foreach($jadwalAktif as $jadwal)
                                    <div class="alert alert-success border-0 shadow-sm mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-2">
                                                <div class="text-center">
                                                    <i class="fas fa-clock fa-3x text-success mb-2"></i>
                                                    <div>
                                                        <strong class="d-block fs-5">{{ $jadwal->waktu_mulai_format }}</strong>
                                                        <small class="text-muted">s/d {{ $jadwal->waktu_selesai_format }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <h4 class="mb-2">
                                                    <i class="fas fa-book text-success me-2"></i>
                                                    {{ $jadwal->mapel->nama_matapelajaran }}
                                                </h4>
                                                <div class="d-flex gap-3">
                                                    <span class="badge bg-success fs-6">
                                                        <i class="fas fa-door-open me-1"></i>
                                                        Kelas: {{ $jadwal->kelas->nama }}
                                                    </span>
                                                    <span class="badge bg-light text-dark fs-6">
                                                        <i class="fas fa-hourglass-half me-1"></i>
                                                        Durasi: {{ $jadwal->durasi_format }}
                                                    </span>
                                                    <span class="badge bg-success fs-6">
                                                        <i class="fas fa-circle-dot me-1"></i>
                                                        BERLANGSUNG
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <a href="{{ route('guru.jadwal.show', $jadwal->id) }}" class="btn btn-success">
                                                    <i class="fas fa-eye me-1"></i> Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Jadwal Akan Datang --}}
            @if($jadwalUpcoming->count() > 0)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card card-custom">
                            <div class="card-header bg-warning text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-hourglass-half me-2"></i>
                                    Akan Datang
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="15%">Waktu</th>
                                                <th width="30%">Mata Pelajaran</th>
                                                <th width="20%">Kelas</th>
                                                <th width="15%">Durasi</th>
                                                <th width="15%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($jadwalUpcoming as $index => $jadwal)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <strong class="text-warning">
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
                                                    <td class="text-center">
                                                        <a href="{{ route('guru.jadwal.show', $jadwal->id) }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye me-1"></i> Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Jadwal Selesai --}}
            @if($jadwalFinished->count() > 0)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card card-custom">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Selesai
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="15%">Waktu</th>
                                                <th width="30%">Mata Pelajaran</th>
                                                <th width="20%">Kelas</th>
                                                <th width="15%">Durasi</th>
                                                <th width="15%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($jadwalFinished as $index => $jadwal)
                                                <tr class="opacity-75">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <strong class="text-muted">
                                                            {{ $jadwal->waktu_mulai_format }}
                                                        </strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $jadwal->waktu_selesai_format }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <i class="fas fa-book text-muted me-2"></i>
                                                            <span class="text-muted">{{ $jadwal->mapel->nama_matapelajaran }}</span>
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ $jadwal->mapel->kode }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-door-open me-1"></i>
                                                            {{ $jadwal->kelas->nama }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-hourglass-half text-muted me-1"></i>
                                                        {{ $jadwal->durasi_format }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('guru.jadwal.show', $jadwal->id) }}"
                                                            class="btn btn-sm btn-secondary">
                                                            <i class="fas fa-eye me-1"></i> Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada jadwal hari ini</h5>
                                <p class="text-muted">Anda bebas untuk hari ini! 🎉</p>
                                <a href="{{ route('guru.jadwal.index') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-calendar-alt me-1"></i> Lihat Semua Jadwal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Quick Navigation --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-compass me-2"></i>Navigasi Cepat
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hariItem)
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <a href="{{ route('guru.jadwal.by-day', $hariItem) }}"
                                        class="btn btn-outline-{{ $hari == $hariItem ? 'success' : 'primary' }} w-100">
                                        <i class="fas fa-calendar-day me-1"></i>
                                        {{ $hariItem }}
                                        @if($hari == $hariItem)
                                            <br><small>(Hari Ini)</small>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

        @keyframes blink {

            0%,
            50%,
            100% {
                opacity: 1;
            }

            25%,
            75% {
                opacity: 0.5;
            }
        }

        .blink-animation {
            animation: blink 2s infinite;
        }

        .alert {
            border-radius: 12px;
        }

        .border-success {
            border-left: 5px solid #28a745 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Tidak ada auto refresh, biarkan user refresh manual jika perlu

        // Update waktu real-time
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            document.querySelectorAll('.current-time').forEach(el => {
                el.textContent = `${hours}:${minutes}:${seconds}`;
            });
        }

        setInterval(updateTime, 1000);
        updateTime();
    </script>
@endpush
