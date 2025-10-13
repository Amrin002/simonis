@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="page-title">Dashboard {{ $guru->roleLabel }}</h1>
                <div class="d-flex align-items-center text-muted">
                    <i class="fas fa-hand-wave text-warning me-2"></i>
                    <span>{{ $guru->sambutanDashboard }}</span>
                </div>
                <p class="text-muted mb-0">
                    <i class="fas fa-calendar-day me-1"></i> {{ $hariIni }}, {{ $tanggalHariIni }}
                </p>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            @if($guru->isGuruMapel())
                {{-- Card Mata Pelajaran --}}
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-book"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ $jumlahMapel }}</h3>
                                <p class="text-muted mb-0">Mata Pelajaran</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Kelas Diajar --}}
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon me-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ $jumlahKelasMapel }}</h3>
                                <p class="text-muted mb-0">Kelas Diajar</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($guru->isWaliKelas())
                {{-- Card Siswa Kelas Wali --}}
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon me-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ $jumlahSiswaKelas }}</h3>
                                <p class="text-muted mb-0">Siswa Kelas {{ $kelasWali->nama }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Card Jadwal Hari Ini --}}
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $jadwalHariIniCount }}</h3>
                            <p class="text-muted mb-0">Jadwal Hari Ini</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Total Jadwal --}}
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $totalJadwalMingguIni }}</h3>
                            <p class="text-muted mb-0">Total Jadwal</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Jadwal Hari Ini Section (Guru Mapel) --}}
        @if($guru->isGuruMapel())
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-clock me-2"></i>Jadwal Mengajar Hari Ini
                                </h5>
                                <a href="{{ route('guru.jadwal.today') }}" class="btn btn-light btn-sm">
                                    Lihat Semua <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($jadwalHariIni->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="15%">Waktu</th>
                                                <th width="30%">Mata Pelajaran</th>
                                                <th width="20%">Kelas</th>
                                                <th width="35%">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($jadwalHariIni as $jadwal)
                                                <tr>
                                                    <td>
                                                        <strong class="text-primary">{{ substr($jadwal->waktu_mulai, 0, 5) }}</strong>
                                                        <span class="text-muted"> - {{ substr($jadwal->waktu_selesai, 0, 5) }}</span>
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-book text-primary me-2"></i>
                                                        {{ $jadwal->mapel->nama_matapelajaran }}
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-door-open me-1"></i>
                                                            {{ $jadwal->kelas->nama }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $now = \Carbon\Carbon::now();
                                                            $waktuMulai = \Carbon\Carbon::createFromFormat('H:i:s', $jadwal->waktu_mulai);
                                                            $waktuSelesai = \Carbon\Carbon::createFromFormat('H:i:s', $jadwal->waktu_selesai);
                                                        @endphp
                                                        @if($now->between($waktuMulai, $waktuSelesai))
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-circle-dot me-1"></i> Sedang Berlangsung
                                                            </span>
                                                        @elseif($now->lt($waktuMulai))
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="fas fa-clock me-1"></i> Akan Datang
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">
                                                                <i class="fas fa-check-circle me-1"></i> Selesai
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                    <h6 class="text-muted">Tidak ada jadwal mengajar hari ini</h6>
                                    <p class="text-muted small mb-0">Anda bebas untuk hari ini! 🎉</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Jadwal Minggu Ini (Guru Mapel) --}}
        @if($guru->isGuruMapel() && isset($jadwalMingguIni))
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-week me-2"></i>Jadwal Mengajar Minggu Ini
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                        <div class="card h-100 border-0"
                                            style="border-left: 4px solid {{ $hari == $hariIni ? '#28a745' : '#6c757d' }} !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            <div class="card-body p-3">
                                                <h6 class="mb-2 d-flex justify-content-between align-items-center">
                                                    <span>{{ $hari }}</span>
                                                    @if($hari == $hariIni)
                                                        <span class="badge bg-success" style="font-size: 0.65rem;">
                                                            <i class="fas fa-circle-dot"></i> Hari Ini
                                                        </span>
                                                    @endif
                                                </h6>

                                                @if(isset($jadwalMingguIni[$hari]) && $jadwalMingguIni[$hari]->count() > 0)
                                                    @foreach($jadwalMingguIni[$hari] as $jadwal)
                                                        <div class="mb-2 p-2"
                                                            style="background: {{ $hari == $hariIni ? '#e8f5e9' : '#f8f9fa' }};
                                                                                    border-radius: 5px;
                                                                                    font-size: 0.85rem;
                                                                                    border-left: 3px solid {{ $hari == $hariIni ? '#28a745' : '#6c757d' }};">
                                                            <div class="fw-bold text-primary">
                                                                <i class="fas fa-clock"></i> {{ substr($jadwal->waktu_mulai, 0, 5) }}
                                                            </div>
                                                            <div class="text-truncate" title="{{ $jadwal->mapel->nama_matapelajaran }}">
                                                                {{ $jadwal->mapel->nama_matapelajaran }}
                                                            </div>
                                                            <div class="text-muted small">
                                                                <i class="fas fa-door-open"></i> {{ $jadwal->kelas->nama }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <div class="text-center mt-2">
                                                        <small class="badge bg-secondary">
                                                            {{ $jadwalMingguIni[$hari]->count() }} Jadwal
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
        @endif

        {{-- Quick Actions & Siswa Section --}}
        <div class="row">
            {{-- Quick Actions (Wali Kelas) --}}
            @if($guru->isWaliKelas())
                <div class="col-lg-8 mb-4">
                    <div class="card card-custom">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2"></i>Aksi Cepat Wali Kelas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="#" class="text-decoration-none">
                                        <div class="card border-primary h-100 hover-shadow">
                                            <div class="card-body text-center">
                                                <i class="fas fa-clipboard-check fa-3x text-primary mb-3"></i>
                                                <h6 class="card-title">Input Absensi</h6>
                                                <p class="card-text text-muted small">Catat kehadiran siswa</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="#" class="text-decoration-none">
                                        <div class="card border-warning h-100 hover-shadow">
                                            <div class="card-body text-center">
                                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                                <h6 class="card-title">Catat Pelanggaran</h6>
                                                <p class="card-text text-muted small">Buat catatan pelanggaran</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="#" class="text-decoration-none">
                                        <div class="card border-success h-100 hover-shadow">
                                            <div class="card-body text-center">
                                                <i class="fas fa-file-alt fa-3x text-success mb-3"></i>
                                                <h6 class="card-title">Lihat Rapor</h6>
                                                <p class="card-text text-muted small">Akses rapor siswa</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="{{ route('guru.kelas-wali') }}" class="text-decoration-none">
                                        <div class="card border-info h-100 hover-shadow">
                                            <div class="card-body text-center">
                                                <i class="fas fa-users fa-3x text-info mb-3"></i>
                                                <h6 class="card-title">Kelola Siswa</h6>
                                                <p class="card-text text-muted small">Manajemen data siswa</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Siswa Kelas Wali --}}
                <div class="col-lg-4 mb-4">
                    <div class="card card-custom">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-users me-2"></i>Kelas {{ $kelasWali->nama }}
                                </h5>
                                <span class="badge bg-light text-dark">{{ $jumlahSiswaKelas }} Siswa</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($siswaTerbaru->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($siswaTerbaru as $siswa)
                                        <div class="list-group-item px-0 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="user-avatar"
                                                        style="width: 40px; height: 40px; font-size: 1rem;
                                                                            background: {{ $siswa->jenis_kelamin == 'L' ? '#3498db' : '#e74c3c' }}">
                                                        {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold">{{ $siswa->nama }}</div>
                                                    <small class="text-muted">NIS: {{ $siswa->nis }}</small>
                                                </div>
                                                <div>
                                                    @if($siswa->jenis_kelamin == 'L')
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-mars"></i>
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-venus"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('guru.kelas-wali') }}" class="btn btn-success w-100">
                                        <i class="fas fa-eye me-1"></i> Lihat Semua Siswa
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-user-slash fa-4x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Belum ada siswa di kelas ini</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('style')
    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .list-group-item {
            transition: all 0.2s ease;
        }

        .list-group-item:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Auto refresh setiap 5 menit
        setTimeout(function () {
            location.reload();
        }, 300000);
    </script>
@endpush
