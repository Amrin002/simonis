@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-info-circle me-2"></i>Detail Jadwal
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar-day me-1"></i> {{ $jadwal->hari }}, {{ $jadwal->waktu }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.jadwal.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <a href="{{ route('guru.jadwal.by-day', $jadwal->hari) }}" class="btn btn-primary me-2">
                            <i class="fas fa-calendar-day me-1"></i> Jadwal {{ $jadwal->hari }}
                        </a>
                        <a href="{{ route('guru.jadwal.by-kelas', $jadwal->kelas_id) }}" class="btn btn-info">
                            <i class="fas fa-door-open me-1"></i> Jadwal Kelas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Badge --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-{{ $jadwal->status == 'active' ? 'success' : ($jadwal->status == 'upcoming' ? 'warning' : ($jadwal->status == 'finished' ? 'secondary' : 'info')) }} border-0 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">
                                @if($jadwal->status == 'active')
                                    <i class="fas fa-circle-dot me-2 blink-animation"></i>
                                @elseif($jadwal->status == 'upcoming')
                                    <i class="fas fa-clock me-2"></i>
                                @elseif($jadwal->status == 'finished')
                                    <i class="fas fa-check-circle me-2"></i>
                                @else
                                    <i class="fas fa-calendar-check me-2"></i>
                                @endif
                                Status: {{ $jadwal->status_label }}
                            </h4>
                            <p class="mb-0">
                                @if($jadwal->status == 'active')
                                    Jadwal sedang berlangsung saat ini
                                @elseif($jadwal->status == 'upcoming')
                                    Jadwal akan dimulai pada {{ $jadwal->waktu_mulai_format }}
                                @elseif($jadwal->status == 'finished')
                                    Jadwal telah selesai pada {{ $jadwal->waktu_selesai_format }}
                                @else
                                    Jadwal dijadwalkan pada hari {{ $jadwal->hari }}
                                @endif
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="display-6">
                                <i class="fas fa-{{ $jadwal->status == 'active' ? 'play-circle' : ($jadwal->status == 'upcoming' ? 'hourglass-start' : ($jadwal->status == 'finished' ? 'check-circle' : 'calendar')) }}"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Main Info Card --}}
            <div class="col-lg-8 mb-4">
                <div class="card card-custom">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-info-circle me-2"></i>Informasi Jadwal
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Mata Pelajaran --}}
                        <div class="detail-item mb-4 pb-4 border-bottom">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-book me-2"></i>Mata Pelajaran
                                    </h6>
                                </div>
                                <div class="col-md-8">
                                    <h5 class="mb-2">{{ $jadwal->mapel->nama_matapelajaran }}</h5>
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-primary">
                                            <i class="fas fa-code me-1"></i>Kode: {{ $jadwal->mapel->kode }}
                                        </span>
                                        @if($jadwal->mapel->kategori)
                                            <span class="badge bg-info">
                                                <i class="fas fa-tag me-1"></i>{{ $jadwal->mapel->kategori }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kelas --}}
                        <div class="detail-item mb-4 pb-4 border-bottom">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-door-open me-2"></i>Kelas
                                    </h6>
                                </div>
                                <div class="col-md-8">
                                    <h5 class="mb-2">{{ $jadwal->kelas->nama }}</h5>
                                    <div class="d-flex gap-2 flex-wrap">
                                        @if($jadwal->kelas->tingkat)
                                            <span class="badge bg-success">
                                                <i class="fas fa-layer-group me-1"></i>Tingkat {{ $jadwal->kelas->tingkat }}
                                            </span>
                                        @endif
                                        @if($jadwal->kelas->jurusan)
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-graduation-cap me-1"></i>{{ $jadwal->kelas->jurusan }}
                                            </span>
                                        @endif
                                        <span class="badge bg-info">
                                            <i class="fas fa-users me-1"></i>{{ optional($jadwal->kelas->siswas)->count() ?? 0 }} Siswa
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Guru --}}
                        <div class="detail-item mb-4 pb-4 border-bottom">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-user-tie me-2"></i>Guru Pengampu
                                    </h6>
                                </div>
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3" style="width: 50px; height: 50px; font-size: 1.2rem; background: #667eea;">
                                            {{ strtoupper(substr($jadwal->guru->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h5 class="mb-1">{{ $jadwal->guru->nama }}</h5>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-id-card me-1"></i>NIP: {{ $jadwal->guru->nip }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Waktu --}}
                        <div class="detail-item mb-4 pb-4 border-bottom">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-calendar-day me-2"></i>Hari
                                    </h6>
                                </div>
                                <div class="col-md-8">
                                    <h5 class="mb-2">{{ $jadwal->hari }}</h5>
                                    <span class="badge bg-{{ $jadwal->hari_badge_color }} fs-6">
                                        {{ $jadwal->hari }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Jam --}}
                        <div class="detail-item mb-4 pb-4 border-bottom">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-clock me-2"></i>Waktu Pelaksanaan
                                    </h6>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <h5 class="mb-1">{{ $jadwal->waktu }}</h5>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <small class="text-muted d-block mb-1">Waktu Mulai</small>
                                                <h5 class="mb-0 text-success">
                                                    <i class="fas fa-play-circle me-2"></i>{{ $jadwal->waktu_mulai_format }}
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <small class="text-muted d-block mb-1">Waktu Selesai</small>
                                                <h5 class="mb-0 text-danger">
                                                    <i class="fas fa-stop-circle me-2"></i>{{ $jadwal->waktu_selesai_format }}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Durasi --}}
                        <div class="detail-item">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-hourglass-half me-2"></i>Durasi
                                    </h6>
                                </div>
                                <div class="col-md-8">
                                    <h5 class="mb-2">{{ $jadwal->durasi_format }}</h5>
                                    <div class="progress" style="height: 25px;">
                                        @php
                                            $percentage = ($jadwal->durasi / 180) * 100; // 180 menit = 3 jam (max)
                                            $percentage = min($percentage, 100);
                                        @endphp
                                        <div class="progress-bar bg-warning" role="progressbar"
                                             style="width: {{ $percentage }}%;"
                                             aria-valuenow="{{ $percentage }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                            {{ $jadwal->durasi }} menit
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4 mb-4">
                {{-- Quick Info --}}
                <div class="card card-custom mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Quick Info
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="quick-info-item mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-calendar-check text-primary me-2"></i>
                                    <span class="text-muted">Status</span>
                                </div>
                                <span class="badge bg-{{ $jadwal->status_badge_color }}">
                                    {{ $jadwal->status_label }}
                                </span>
                            </div>
                        </div>

                        <div class="quick-info-item mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-calendar-day text-primary me-2"></i>
                                    <span class="text-muted">Hari</span>
                                </div>
                                <strong>{{ $jadwal->hari }}</strong>
                            </div>
                        </div>

                        <div class="quick-info-item mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    <span class="text-muted">Durasi</span>
                                </div>
                                <strong>{{ $jadwal->durasi_format }}</strong>
                            </div>
                        </div>

                        <div class="quick-info-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-users text-primary me-2"></i>
                                    <span class="text-muted">Jumlah Siswa</span>
                                </div>
                                <strong>{{ optional($jadwal->kelas->siswas)->count() ?? 0 }} Siswa</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card card-custom">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Aksi Cepat
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('guru.jadwal.by-day', $jadwal->hari) }}" class="btn btn-outline-primary">
                                <i class="fas fa-calendar-day me-2"></i>Lihat Jadwal {{ $jadwal->hari }}
                            </a>
                            <a href="{{ route('guru.jadwal.by-kelas', $jadwal->kelas_id) }}" class="btn btn-outline-info">
                                <i class="fas fa-door-open me-2"></i>Jadwal Kelas {{ $jadwal->kelas->nama }}
                            </a>
                            <a href="{{ route('guru.jadwal.today') }}" class="btn btn-outline-success">
                                <i class="fas fa-clock me-2"></i>Jadwal Hari Ini
                            </a>
                            <a href="{{ route('guru.jadwal.weekly-summary') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-calendar-week me-2"></i>Ringkasan Mingguan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Additional Info --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>Informasi Tambahan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-info-circle me-2"></i>Nama Lengkap Jadwal
                                    </h6>
                                    <p class="mb-0 fw-bold">{{ $jadwal->nama_lengkap }}</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-calendar-plus me-2"></i>Dibuat Pada
                                    </h6>
                                    <p class="mb-0 fw-bold">{{ $jadwal->created_at->isoFormat('dddd, D MMMM YYYY HH:mm') }}</p>
                                </div>
                            </div>
                            @if($jadwal->updated_at != $jadwal->created_at)
                                <div class="col-md-6 mb-3">
                                    <div class="p-3 bg-light rounded">
                                        <h6 class="text-muted mb-2">
                                            <i class="fas fa-calendar-check me-2"></i>Terakhir Diupdate
                                        </h6>
                                        <p class="mb-0 fw-bold">{{ $jadwal->updated_at->isoFormat('dddd, D MMMM YYYY HH:mm') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
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

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
        }

        .detail-item {
            transition: all 0.3s ease;
        }

        .detail-item:hover {
            background-color: rgba(102, 126, 234, 0.02);
            border-radius: 8px;
            padding: 10px;
            margin: -10px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        .quick-info-item {
            transition: all 0.2s ease;
        }

        .quick-info-item:hover {
            transform: translateX(5px);
        }

        @keyframes blink {
            0%, 50%, 100% { opacity: 1; }
            25%, 75% { opacity: 0.5; }
        }

        .blink-animation {
            animation: blink 2s infinite;
        }

        .alert {
            border-radius: 12px;
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

        .btn-outline-primary:hover,
        .btn-outline-info:hover,
        .btn-outline-success:hover,
        .btn-outline-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Update status real-time
        function updateStatus() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const currentTime = hours * 60 + minutes;

            // Convert waktu mulai dan selesai ke menit
            const waktuMulai = "{{ $jadwal->waktu_mulai }}".split(':');
            const waktuSelesai = "{{ $jadwal->waktu_selesai }}".split(':');
            const mulai = parseInt(waktuMulai[0]) * 60 + parseInt(waktuMulai[1]);
            const selesai = parseInt(waktuSelesai[0]) * 60 + parseInt(waktuSelesai[1]);

            // Check if today
            const hariIniMap = {
                'Sunday': 'Minggu',
                'Monday': 'Senin',
                'Tuesday': 'Selasa',
                'Wednesday': 'Rabu',
                'Thursday': 'Kamis',
                'Friday': 'Jumat',
                'Saturday': 'Sabtu'
            };
            const hariIni = hariIniMap[new Date().toLocaleDateString('en-US', { weekday: 'long' })];
            const jadwalHari = "{{ $jadwal->hari }}";

            if (hariIni === jadwalHari) {
                if (currentTime >= mulai && currentTime <= selesai) {
                    // Jadwal sedang berlangsung - refresh halaman untuk update status
                    // location.reload();
                }
            }
        }

        // Check setiap 1 menit
        setInterval(updateStatus, 60000);
    </script>
@endpush
