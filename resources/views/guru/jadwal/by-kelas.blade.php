@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-door-open me-2"></i>Jadwal Kelas {{ $kelas->nama }}
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i> Menampilkan semua jadwal mengajar di kelas
                            {{ $kelas->nama }}
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

        {{-- Kelas Info Card --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-2">
                                    <i class="fas fa-door-open me-2"></i>{{ $kelas->nama }}
                                </h3>
                                <div class="d-flex gap-3 flex-wrap">
                                    @if($kelas->tingkat)
                                        <div>
                                            <i class="fas fa-layer-group me-1"></i>
                                            <strong>Tingkat:</strong> {{ $kelas->tingkat }}
                                        </div>
                                    @endif
                                    @if($kelas->jurusan)
                                        <div>
                                            <i class="fas fa-graduation-cap me-1"></i>
                                            <strong>Jurusan:</strong> {{ $kelas->jurusan }}
                                        </div>
                                    @endif
                                    @if($kelas->wali_kelas)
                                        <div>
                                            <i class="fas fa-user-tie me-1"></i>
                                            <strong>Wali Kelas:</strong> {{ $kelas->waliKelas->nama ?? '-' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="display-4">
                                    <i class="fas fa-users"></i>
                                </div>
                                @if(isset($kelas->siswa))
                                    <p class="mb-0">{{ $kelas->siswa->count() }} Siswa</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Summary --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            @php
                                $totalJadwal = 0;
                                foreach ($jadwal as $items) {
                                    $totalJadwal += $items->count();
                                }
                            @endphp
                            <h3 class="mb-0">{{ $totalJadwal }}</h3>
                            <p class="text-muted mb-0">Total Jadwal</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $jadwal->count() }}</h3>
                            <p class="text-muted mb-0">Hari Aktif</p>
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
                            @php
                                $uniqueMapel = collect();
                                foreach ($jadwal as $items) {
                                    $uniqueMapel = $uniqueMapel->merge($items->pluck('mapel_id'));
                                }
                            @endphp
                            <h3 class="mb-0">{{ $uniqueMapel->unique()->count() }}</h3>
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
                                $totalMenit = 0;
                                foreach ($jadwal as $items) {
                                    $totalMenit += $items->sum(fn($j) => $j->durasi);
                                }
                                $jam = floor($totalMenit / 60);
                                $menit = $totalMenit % 60;
                            @endphp
                            <h3 class="mb-0">{{ $jam }}j {{ $menit }}m</h3>
                            <p class="text-muted mb-0">Total Durasi/Minggu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($jadwal->count() > 0)
            {{-- Weekly Schedule View --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-week me-2"></i>Jadwal Mingguan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hariItem)
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
                                        $isToday = $hariIni == $hariItem;
                                    @endphp
                                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                        <div class="card h-100 border-0"
                                            style="border-left: 4px solid {{ $isToday ? '#28a745' : '#667eea' }} !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            <div class="card-body p-3">
                                                <h6 class="mb-2 d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold">{{ $hariItem }}</span>
                                                    @if($isToday)
                                                        <span class="badge bg-success" style="font-size: 0.65rem;">
                                                            <i class="fas fa-circle-dot"></i> Hari Ini
                                                        </span>
                                                    @endif
                                                </h6>

                                                @if(isset($jadwal[$hariItem]) && $jadwal[$hariItem]->count() > 0)
                                                    @foreach($jadwal[$hariItem] as $item)
                                                        <div class="mb-2 p-2"
                                                            style="background: {{ $isToday ? '#e8f5e9' : '#f8f9fa' }};
                                                                                       border-radius: 8px;
                                                                                       font-size: 0.85rem;
                                                                                       border-left: 3px solid {{ $isToday ? '#28a745' : '#667eea' }};">
                                                            <div class="fw-bold text-primary mb-1">
                                                                <i class="fas fa-clock"></i> {{ $item->waktu_mulai_format }}
                                                            </div>
                                                            <div class="text-truncate fw-bold"
                                                                title="{{ $item->mapel->nama_matapelajaran }}">
                                                                <i class="fas fa-book"></i> {{ $item->mapel->nama_matapelajaran }}
                                                            </div>
                                                            <div class="text-muted small">
                                                                <i class="fas fa-hourglass-half"></i> {{ $item->durasi_format }}
                                                            </div>
                                                            @if($isToday)
                                                                <div class="mt-1">
                                                                    <span class="badge bg-{{ $item->status_badge_color }}"
                                                                        style="font-size: 0.7rem;">
                                                                        {{ $item->status_label }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                    <div class="text-center mt-2">
                                                        <small class="badge bg-primary">
                                                            {{ $jadwal[$hariItem]->count() }} Jadwal
                                                        </small>
                                                    </div>
                                                @else
                                                    <div class="text-center py-3">
                                                        <i class="fas fa-calendar-times text-muted"></i>
                                                        <p class="text-muted mb-0 small">Tidak ada jadwal</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail per Hari --}}
            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hariItem)
                @if(isset($jadwal[$hariItem]) && $jadwal[$hariItem]->count() > 0)
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card card-custom">
                                <div class="card-header bg-info text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="fas fa-calendar-day me-2"></i>{{ $hariItem }}
                                        </h5>
                                        <div>
                                            <span class="badge bg-light text-dark">
                                                {{ $jadwal[$hariItem]->count() }} Jadwal
                                            </span>
                                            <a href="{{ route('guru.jadwal.by-day', $hariItem) }}" class="btn btn-light btn-sm ms-2">
                                                <i class="fas fa-external-link-alt"></i> Lihat Detail
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
                                                    <th width="35%">Mata Pelajaran</th>
                                                    <th width="15%">Durasi</th>
                                                    <th width="20%">Status</th>
                                                    <th width="10%" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($jadwal[$hariItem] as $index => $item)
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
                                                                Kode: {{ $item->mapel->kode }}
                                                            </small>
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
                @endif
            @endforeach
        @else
            {{-- Empty State --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada jadwal di kelas {{ $kelas->nama }}</h5>
                                <p class="text-muted">Anda tidak memiliki jadwal mengajar di kelas ini</p>
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

        /* Weekly Card Hover */
        .col-lg-2 .card {
            transition: all 0.3s ease;
        }

        .col-lg-2 .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        /* Info Card Gradient */
        .card-custom[style*="gradient"] {
            position: relative;
            overflow: hidden;
        }

        .card-custom[style*="gradient"]::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Schedule Item Hover */
        .col-lg-2 .card-body>div[style*="border-left"] {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .col-lg-2 .card-body>div[style*="border-left"]:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Highlight active schedule
        document.addEventListener('DOMContentLoaded', function () {
            const now = new Date();
            const currentTime = now.getHours() * 60 + now.getMinutes();

            document.querySelectorAll('[data-start-time]').forEach(function (element) {
                const startTime = parseInt(element.dataset.startTime);
                const endTime = parseInt(element.dataset.endTime);

                if (currentTime >= startTime && currentTime <= endTime) {
                    element.classList.add('border-success', 'border-3');
                    element.style.boxShadow = '0 0 10px rgba(40, 167, 69, 0.3)';
                }
            });
        });
    </script>
@endpush
