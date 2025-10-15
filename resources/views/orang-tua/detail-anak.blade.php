@extends('template-orangtua.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-user-graduate me-2"></i>{{ $siswa->nama }}
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-id-card me-1"></i>NIS: {{ $siswa->nis }} |
                            <i class="fas fa-door-open me-1"></i>{{ $siswa->kelas->nama ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('orangtua.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Profile & Info Section --}}
        <div class="row mb-4">
            {{-- Profile Card --}}
            <div class="col-lg-4 mb-4">
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>Profil Siswa
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="avatar-circle mb-3">
                            <i class="fas fa-user fa-4x text-white"></i>
                        </div>
                        <h4 class="mb-1">{{ $siswa->nama }}</h4>
                        <p class="text-muted mb-3">{{ $siswa->nis }}</p>

                        <hr>

                        <div class="info-item">
                            <i class="fas fa-school text-primary me-2"></i>
                            <strong>Kelas:</strong>
                            <span class="float-end">{{ $siswa->kelas->nama ?? '-' }}</span>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                            <strong>Wali Kelas:</strong>
                            <span class="float-end">{{ $siswa->kelas->waliKelas->nama_guru ?? '-' }}</span>
                        </div>

                        @if($siswa->jenis_kelamin)
                            <div class="info-item">
                                <i
                                    class="fas {{ $siswa->jenis_kelamin == 'L' ? 'fa-mars' : 'fa-venus' }} text-primary me-2"></i>
                                <strong>Jenis Kelamin:</strong>
                                <span class="float-end">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Statistik Bulan Ini --}}
            <div class="col-lg-8 mb-4">
                <div class="card card-custom">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Statistik Kehadiran Bulan Ini
                        </h5>
                        <small>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM Y') }}</small>
                    </div>
                    <div class="card-body">
                        {{-- Progress Bar Kehadiran --}}
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span><strong>Persentase Kehadiran:</strong></span>
                                <span><strong>{{ $statistik['persentase'] }}%</strong></span>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar {{ $statistik['persentase'] >= 80 ? 'bg-success' : ($statistik['persentase'] >= 60 ? 'bg-warning' : 'bg-danger') }}"
                                    role="progressbar" style="width: {{ $statistik['persentase'] }}%">
                                    {{ $statistik['persentase'] }}%
                                </div>
                            </div>
                            <small class="text-muted">
                                Hadir {{ $statistik['hadir'] }} dari {{ $statistik['total_hari'] }} hari
                            </small>
                        </div>

                        {{-- Statistik Detail --}}
                        <div class="row text-center">
                            <div class="col-6 col-md-3 mb-3">
                                <div class="p-3 rounded" style="background-color: #d4edda;">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h3 class="mb-0 text-success">{{ $statistik['hadir'] }}</h3>
                                    <small class="text-muted">Hadir</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <div class="p-3 rounded" style="background-color: #fff3cd;">
                                    <i class="fas fa-thermometer fa-2x text-warning mb-2"></i>
                                    <h3 class="mb-0 text-warning">{{ $statistik['sakit'] }}</h3>
                                    <small class="text-muted">Sakit</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <div class="p-3 rounded" style="background-color: #d1ecf1;">
                                    <i class="fas fa-info-circle fa-2x text-info mb-2"></i>
                                    <h3 class="mb-0 text-info">{{ $statistik['izin'] }}</h3>
                                    <small class="text-muted">Izin</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <div class="p-3 rounded" style="background-color: #f8d7da;">
                                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                    <h3 class="mb-0 text-danger">{{ $statistik['alpa'] }}</h3>
                                    <small class="text-muted">Alpa</small>
                                </div>
                            </div>
                        </div>

                        {{-- Pelanggaran --}}
                        <div class="alert {{ $statistik['pelanggaran'] > 0 ? 'alert-danger' : 'alert-success' }} mb-0">
                            <div class="d-flex align-items-center">
                                <i
                                    class="fas {{ $statistik['pelanggaran'] > 0 ? 'fa-exclamation-triangle' : 'fa-check-circle' }} fa-2x me-3"></i>
                                <div>
                                    <strong>Catatan Pelanggaran:</strong><br>
                                    @if($statistik['pelanggaran'] > 0)
                                        <span class="text-danger">{{ $statistik['pelanggaran'] }} pelanggaran tercatat bulan
                                            ini</span>
                                    @else
                                        <span class="text-success">Tidak ada pelanggaran bulan ini. Pertahankan!</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rekapan Hari Ini --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>Rekapan Hari Ini
                        </h5>
                        <small>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Kehadiran --}}
                            <div class="col-md-6 mb-3">
                                <div class="rekapan-box">
                                    <h6 class="mb-3">
                                        <i class="fas fa-calendar-check text-primary me-2"></i>Kehadiran
                                    </h6>
                                    @if($rekapanHariIni->kehadiran)
                                        <div class="rekapan-content">
                                            {!! nl2br(e($rekapanHariIni->kehadiran)) !!}
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-3">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p class="mb-0">Belum ada data kehadiran</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Perilaku --}}
                            <div class="col-md-6 mb-3">
                                <div class="rekapan-box">
                                    <h6 class="mb-3">
                                        <i class="fas fa-user-check text-primary me-2"></i>Perilaku
                                    </h6>
                                    @if($rekapanHariIni->perilaku)
                                        <div class="rekapan-content">
                                            {!! nl2br(e($rekapanHariIni->perilaku)) !!}
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-3">
                                            <i class="fas fa-smile fa-2x mb-2"></i>
                                            <p class="mb-0">Tidak ada catatan</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Riwayat 7 Hari Terakhir --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header bg-warning text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>Riwayat 7 Hari Terakhir
                            </h5>
                            <a href="{{ route('orangtua.riwayat-anak', $siswa->id) }}" class="btn btn-light btn-sm">
                                Lihat Semua <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($riwayat7Hari->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="20%">Tanggal</th>
                                            <th width="15%">Hari</th>
                                            <th width="30%">Kehadiran</th>
                                            <th width="35%">Perilaku</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($riwayat7Hari as $riwayat)
                                            <tr>
                                                <td>
                                                    <strong>{{ $riwayat->tanggal->format('d/m/Y') }}</strong>
                                                    @if($riwayat->tanggal->isToday())
                                                        <span class="badge bg-success ms-1">Hari Ini</span>
                                                    @endif
                                                </td>
                                                <td>{{ $riwayat->tanggal->locale('id')->isoFormat('dddd') }}</td>
                                                <td>
                                                    @php
                                                        $kehadiranStatus = 'secondary';
                                                        $kehadiranIcon = 'fa-question';
                                                        $kehadiranText = 'Belum Ada';

                                                        if (str_contains($riwayat->kehadiran, 'Hadir')) {
                                                            $kehadiranStatus = 'success';
                                                            $kehadiranIcon = 'fa-check';
                                                            $kehadiranText = 'Hadir';
                                                        } elseif (str_contains($riwayat->kehadiran, 'Sakit')) {
                                                            $kehadiranStatus = 'warning';
                                                            $kehadiranIcon = 'fa-thermometer';
                                                            $kehadiranText = 'Sakit';
                                                        } elseif (str_contains($riwayat->kehadiran, 'Izin')) {
                                                            $kehadiranStatus = 'info';
                                                            $kehadiranIcon = 'fa-info-circle';
                                                            $kehadiranText = 'Izin';
                                                        } elseif (str_contains($riwayat->kehadiran, 'Alpa')) {
                                                            $kehadiranStatus = 'danger';
                                                            $kehadiranIcon = 'fa-times';
                                                            $kehadiranText = 'Alpa';
                                                        }
                                                    @endphp
                                                    <span class="badge bg-{{ $kehadiranStatus }}">
                                                        <i class="fas {{ $kehadiranIcon }} me-1"></i>{{ $kehadiranText }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $perilakuBaik = str_contains($riwayat->perilaku, 'Baik') ||
                                                            str_contains($riwayat->perilaku, 'Tidak ada pelanggaran');
                                                    @endphp
                                                    @if($perilakuBaik)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-smile me-1"></i>Baik
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-frown me-1"></i>Ada Pelanggaran
                                                        </span>
                                                    @endif
                                                    <small class="text-muted d-block mt-1">
                                                        {{ Str::limit($riwayat->perilaku, 50) }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum Ada Riwayat</h5>
                                <p class="text-muted mb-0">Riwayat rekapan akan muncul di sini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('orangtua.riwayat-anak', $siswa->id) }}" class="btn btn-primary">
                                <i class="fas fa-history me-2"></i>Lihat Semua Riwayat
                            </a>
                            <a href="{{ route('orangtua.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-home me-2"></i>Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .avatar-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .info-item {
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .rekapan-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            height: 100%;
        }

        .rekapan-content {
            background: white;
            padding: 15px;
            border-radius: 8px;
            line-height: 1.8;
            white-space: pre-line;
        }

        .progress {
            border-radius: 10px;
        }

        .progress-bar {
            border-radius: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
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
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
            });
        @endif
    </script>
@endpush
