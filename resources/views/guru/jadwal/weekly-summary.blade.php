@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-calendar-week me-2"></i>Ringkasan Jadwal Mingguan
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i> Ringkasan jadwal mengajar Anda dalam satu minggu
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.jadwal.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Semua Jadwal
                        </a>
                        <a href="{{ route('guru.jadwal.today') }}" class="btn btn-primary me-2">
                            <i class="fas fa-clock me-1"></i> Jadwal Hari Ini
                        </a>
                        {{-- <a href="{{ route('guru.jadwal.export-pdf') }}" class="btn btn-danger me-2">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                        </a> --}}
                        <a href="{{ route('guru.jadwal.print') }}" class="btn btn-info" target="_blank">
                            <i class="fas fa-print me-1"></i> Print
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Statistics --}}
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
                                foreach($jadwal as $items) {
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
                            <p class="text-muted mb-0">Hari Mengajar</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $summary['total_jam'] }}j {{ $summary['sisa_menit'] }}m</h3>
                            <p class="text-muted mb-0">Total Jam Mengajar</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            @php
                                $rataRata = $jadwal->count() > 0 ? floor($summary['total_jam_mengajar'] / $jadwal->count()) : 0;
                            @endphp
                            <h3 class="mb-0">{{ floor($rataRata / 60) }}j {{ $rataRata % 60 }}m</h3>
                            <p class="text-muted mb-0">Rata-rata per Hari</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Weekly Chart --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Grafik Jam Mengajar per Hari
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="weeklyChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daily Summary Cards --}}
        <div class="row mb-4">
            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                @php
                    $hariData = $summary['hari'][$hari] ?? null;
                    $hasJadwal = isset($jadwal[$hari]) && $jadwal[$hari]->count() > 0;
                @endphp
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card card-custom h-100 {{ $hasJadwal ? '' : 'opacity-50' }}">
                        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-white">
                                    <i class="fas fa-calendar-day me-2"></i>{{ $hari }}
                                </h5>
                                @if($hasJadwal)
                                    <span class="badge bg-light text-dark">
                                        {{ $hariData['jumlah_jadwal'] }} Jadwal
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted">Libur</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if($hasJadwal)
                                {{-- Summary Info --}}
                                <div class="mb-3 p-3 bg-light rounded">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">
                                            <i class="fas fa-list me-1"></i>Jumlah Jadwal:
                                        </span>
                                        <strong>{{ $hariData['jumlah_jadwal'] }} kali</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">
                                            <i class="fas fa-clock me-1"></i>Total Durasi:
                                        </span>
                                        <strong class="text-primary">
                                            {{ $hariData['total_jam'] }}j {{ $hariData['sisa_menit'] }}m
                                        </strong>
                                    </div>
                                </div>

                                {{-- Schedule List --}}
                                <div class="schedule-list">
                                    @foreach($jadwal[$hari] as $item)
                                        <div class="schedule-item mb-2 p-3 border-start border-4 border-primary" style="background: #f8f9fa; border-radius: 8px;">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <strong class="text-primary">
                                                        <i class="fas fa-clock"></i>
                                                        {{ $item->waktu_mulai_format }} - {{ $item->waktu_selesai_format }}
                                                    </strong>
                                                </div>
                                                <span class="badge bg-warning text-dark">
                                                    {{ $item->durasi_format }}
                                                </span>
                                            </div>
                                            <div class="mb-1">
                                                <i class="fas fa-book text-primary me-1"></i>
                                                <strong>{{ $item->mapel->nama_matapelajaran }}</strong>
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-door-open me-1"></i>
                                                Kelas: {{ $item->kelas->nama }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Quick Action --}}
                                <div class="mt-3">
                                    <a href="{{ route('guru.jadwal.by-day', $hari) }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-eye me-1"></i> Lihat Detail {{ $hari }}
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Tidak ada jadwal di hari {{ $hari }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Summary Table --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-table me-2"></i>Tabel Ringkasan Mingguan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Hari</th>
                                        <th width="15%">Jumlah Jadwal</th>
                                        <th width="20%">Total Durasi</th>
                                        <th width="30%">Mata Pelajaran</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                        @if(isset($jadwal[$hari]) && $jadwal[$hari]->count() > 0)
                                            @php
                                                $hariData = $summary['hari'][$hari];
                                                $mapelList = $jadwal[$hari]->pluck('mapel.nama_matapelajaran')->unique();
                                            @endphp
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>
                                                    <strong>{{ $hari }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        {{ $hariData['jumlah_jadwal'] }} Jadwal
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="fas fa-clock text-warning me-1"></i>
                                                    <strong>{{ $hariData['total_jam'] }} jam {{ $hariData['sisa_menit'] }} menit</strong>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($mapelList as $mapel)
                                                            <span class="badge bg-info">{{ $mapel }}</span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('guru.jadwal.by-day', $hari) }}"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="2" class="text-end"><strong>Total Keseluruhan:</strong></td>
                                        <td>
                                            <span class="badge bg-success">
                                                @php
                                                    $totalJadwal = 0;
                                                    foreach($jadwal as $items) {
                                                        $totalJadwal += $items->count();
                                                    }
                                                @endphp
                                                {{ $totalJadwal }} Jadwal
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-clock text-success me-1"></i>
                                            <strong class="text-success">
                                                {{ $summary['total_jam'] }} jam {{ $summary['sisa_menit'] }} menit
                                            </strong>
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
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

        .schedule-item {
            transition: all 0.2s ease;
        }

        .schedule-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Data untuk chart
        const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const data = {!! json_encode(array_map(function($hari) use ($summary) {
            return isset($summary['hari'][$hari]) ? round($summary['hari'][$hari]['total_menit'] / 60, 1) : 0;
        }, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'])) !!};

        const ctx = document.getElementById('weeklyChart').getContext('2d');
        const weeklyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: days,
                datasets: [{
                    label: 'Jam Mengajar',
                    data: data,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(13, 202, 240, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ],
                    borderColor: [
                        'rgb(102, 126, 234)',
                        'rgb(25, 135, 84)',
                        'rgb(13, 202, 240)',
                        'rgb(255, 193, 7)',
                        'rgb(220, 53, 69)',
                        'rgb(108, 117, 125)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Durasi: ' + context.parsed.y + ' jam';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' jam';
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
