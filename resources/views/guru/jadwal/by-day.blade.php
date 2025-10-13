@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-calendar-day me-2"></i>Jadwal Hari {{ $hari }}
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar-alt me-1"></i> Menampilkan semua jadwal di hari {{ $hari }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.jadwal.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Semua Jadwal
                        </a>
                        <a href="{{ route('guru.jadwal.today') }}" class="btn btn-primary me-2">
                            <i class="fas fa-clock me-1"></i> Jadwal Hari Ini
                        </a>
                        {{-- <a href="{{ route('guru.jadwal.export-pdf') }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                        </a> --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Day Navigation --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hariItem)
                                <a href="{{ route('guru.jadwal.by-day', $hariItem) }}"
                                    class="btn btn-{{ $hari == $hariItem ? 'primary' : 'outline-primary' }} {{ $hari == $hariItem ? 'active' : '' }}">
                                    <i class="fas fa-calendar-day me-1"></i>
                                    {{ $hariItem }}
                                    @php
                                        $hariIniMap = [
                                            'Sunday' => 'Minggu',
                                            'Monday' => 'Senin',
                                            'Tuesday' => 'Selasa',
                                            'Wednesday' => 'Rabu',
                                            'Thursday' => 'Kamis',
                                            'Friday' => 'Jumat',
                                            'Saturday' => 'Sabtu',
                                        ];
                                        $hariIni = $hariIniMap[now()->format('l')];
                                    @endphp
                                    @if($hariIni == $hariItem)
                                        <span class="badge bg-success ms-1">Hari Ini</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($jadwal->count() > 0)
            {{-- Statistics Summary --}}
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-list"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ $jadwal->count() }}</h3>
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
                                <h3 class="mb-0">{{ $jadwal->pluck('kelas_id')->unique()->count() }}</h3>
                                <p class="text-muted mb-0">Kelas Berbeda</p>
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
                                <h3 class="mb-0">{{ $jadwal->pluck('mapel_id')->unique()->count() }}</h3>
                                <p class="text-muted mb-0">Mata Pelajaran</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div>
                                @php
                                    $totalMenit = $jadwal->sum(fn($j) => $j->durasi);
                                    $jam = floor($totalMenit / 60);
                                    $menit = $totalMenit % 60;
                                @endphp
                                <h3 class="mb-0">{{ $jam }}j {{ $menit }}m</h3>
                                <p class="text-muted mb-0">Total Durasi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timeline View --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <h5 class="mb-0 text-white">
                                <i class="fas fa-stream me-2"></i>Timeline Jadwal
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="timeline">
                                @foreach($jadwal as $index => $item)
                                    <div class="timeline-item">
                                        <div class="timeline-marker"
                                            style="background: {{ $item->hari_badge_color == 'primary' ? '#667eea' : ($item->hari_badge_color == 'success' ? '#43e97b' : ($item->hari_badge_color == 'info' ? '#4facfe' : ($item->hari_badge_color == 'warning' ? '#fa709a' : ($item->hari_badge_color == 'danger' ? '#f5576c' : '#6c757d')))) }}">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="timeline-time">
                                                <span class="badge bg-primary">
                                                    {{ $item->waktu_mulai_format }} - {{ $item->waktu_selesai_format }}
                                                </span>
                                                <span class="badge bg-secondary ms-2">
                                                    <i class="fas fa-hourglass-half me-1"></i>{{ $item->durasi_format }}
                                                </span>
                                            </div>
                                            <div class="card shadow-sm mt-2">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-6">
                                                            <h5 class="mb-2">
                                                                <i class="fas fa-book text-primary me-2"></i>
                                                                {{ $item->mapel->nama_matapelajaran }}
                                                            </h5>
                                                            <p class="text-muted mb-2">
                                                                <i class="fas fa-code me-1"></i>
                                                                Kode: {{ $item->mapel->kode }}
                                                            </p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-2">
                                                                <span class="badge bg-info">
                                                                    <i class="fas fa-door-open me-1"></i>
                                                                    Kelas: {{ $item->kelas->nama }}
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <span class="badge bg-{{ $item->status_badge_color }}">
                                                                    {{ $item->status_label }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 text-end">
                                                            <a href="{{ route('guru.jadwal.show', $item->id) }}"
                                                                class="btn btn-primary btn-sm">
                                                                <i class="fas fa-eye me-1"></i> Detail
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table View --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-table me-2"></i>Daftar Jadwal
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
                                            <th width="15%">Kelas</th>
                                            <th width="15%">Durasi</th>
                                            <th width="12%">Status</th>
                                            <th width="8%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($jadwal as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong class="text-primary">
                                                        {{ $item->waktu_mulai_format }}
                                                    </strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $item->waktu_selesai_format }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div>
                                                        <i class="fas fa-book text-primary me-2"></i>
                                                        <strong>{{ $item->mapel->nama_matapelajaran }}</strong>
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ $item->mapel->kode }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-door-open me-1"></i>
                                                        {{ $item->kelas->nama }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="fas fa-hourglass-half text-warning me-1"></i>
                                                    {{ $item->durasi_format }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $item->status_badge_color }}">
                                                        {{ $item->status_label }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('guru.jadwal.show', $item->id) }}" class="btn btn-sm btn-info"
                                                        title="Detail">
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
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada jadwal di hari {{ $hari }}</h5>
                                <p class="text-muted">Anda tidak memiliki jadwal mengajar di hari ini</p>
                                <a href="{{ route('guru.jadwal.index') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-calendar-alt me-1"></i> Lihat Semua Jadwal
                                </a>
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

        /* Timeline Styles */
        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #667eea, #764ba2);
        }

        .timeline-item {
            position: relative;
            padding-left: 80px;
            margin-bottom: 30px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            left: 18px;
            top: 0;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #667eea;
            border: 3px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
            z-index: 1;
        }

        .timeline-content {
            position: relative;
        }

        .timeline-time {
            margin-bottom: 10px;
        }

        .timeline-content .card {
            border-radius: 8px;
            border: none;
            transition: all 0.3s ease;
        }

        .timeline-content .card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Day Navigation */
        .btn-outline-primary:not(.active):hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn.active {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Smooth scroll to active timeline item
        document.addEventListener('DOMContentLoaded', function () {
            const activeItem = document.querySelector('.timeline-item .badge.bg-success');
            if (activeItem) {
                activeItem.closest('.timeline-item').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    </script>
@endpush
