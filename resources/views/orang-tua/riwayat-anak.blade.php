@extends('template-orangtua.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-history me-2"></i>Riwayat Rekapan
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-user-graduate me-1"></i>{{ $siswa->nama }} ({{ $siswa->nis }})
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('orangtua.detail-anak', $siswa->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Siswa Card --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="avatar-circle-small">
                                    <i class="fas fa-user fa-3x text-white"></i>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Nama Siswa</small>
                                        <strong class="fs-5">{{ $siswa->nama }}</strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted d-block">NIS</small>
                                        <strong>{{ $siswa->nis }}</strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted d-block">Kelas</small>
                                        <strong>{{ $siswa->kelas->nama ?? '-' }}</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Wali Kelas</small>
                                        <strong>{{ $siswa->kelas->waliKelas->nama_guru ?? '-' }}</strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted d-block">Total Rekapan</small>
                                        <strong class="text-primary">{{ $rekapans->total() }} record</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Statistik --}}
        <div class="row mb-4">
            {{-- Filter --}}
            <div class="col-lg-8 mb-3">
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i>Filter Riwayat
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('orangtua.riwayat-anak', $siswa->id) }}" method="GET" class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Bulan
                                </label>
                                <select name="bulan" class="form-select">
                                    <option value="">Semua Bulan</option>
                                    @foreach($bulanOptions as $key => $value)
                                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt me-1"></i>Tahun
                                </label>
                                <select name="tahun" class="form-select">
                                    @for($i = now()->year; $i >= now()->year - 2; $i--)
                                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                        </form>

                        @if($bulan || $tahun != now()->year)
                            <div class="mt-3">
                                <a href="{{ route('orangtua.riwayat-anak', $siswa->id) }}"
                                    class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Reset Filter
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Statistik Singkat --}}
            <div class="col-lg-4 mb-3">
                <div class="card card-custom">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Ringkasan
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $hadir = $rekapans->filter(function ($r) {
                                return str_contains($r->kehadiran, 'Hadir');
                            })->count();

                            $tidakHadir = $rekapans->filter(function ($r) {
                                return !str_contains($r->kehadiran, 'Hadir');
                            })->count();

                            $pelanggaran = $rekapans->filter(function ($r) {
                                return $r->perilaku &&
                                    !str_contains($r->perilaku, 'Baik') &&
                                    !str_contains($r->perilaku, 'Tidak ada pelanggaran');
                            })->count();

                            $persentase = $rekapans->count() > 0 ? round(($hadir / $rekapans->count()) * 100, 1) : 0;
                        @endphp

                        <div class="stat-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Kehadiran</span>
                                <span class="badge bg-success">{{ $persentase }}%</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $persentase }}%"></div>
                            </div>
                            <small class="text-muted">{{ $hadir }} dari {{ $rekapans->count() }} hari</small>
                        </div>

                        <hr>

                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-success mb-0">{{ $hadir }}</h4>
                                <small class="text-muted">Hadir</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-danger mb-0">{{ $pelanggaran }}</h4>
                                <small class="text-muted">Pelanggaran</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Riwayat List --}}
        <div class="row">
            <div class="col-md-12">
                @if($rekapans->count() > 0)
                    <div class="card card-custom">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Daftar Riwayat Rekapan
                                @if($bulan)
                                    <span class="badge bg-light text-dark ms-2">
                                        {{ $bulanOptions[$bulan] }} {{ $tahun }}
                                    </span>
                                @endif
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%" class="text-center">No</th>
                                            <th width="15%">Tanggal</th>
                                            <th width="35%">Kehadiran</th>
                                            <th width="35%">Perilaku</th>
                                            <th width="10%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rekapans as $index => $rekapan)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $rekapans->firstItem() + $index }}
                                                </td>
                                                <td>
                                                    <strong>{{ $rekapan->tanggal->format('d/m/Y') }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $rekapan->tanggal->locale('id')->isoFormat('dddd') }}
                                                    </small>
                                                    @if($rekapan->tanggal->isToday())
                                                        <br><span class="badge bg-success">Hari Ini</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $kehadiranStatus = 'secondary';
                                                        $kehadiranIcon = 'fa-question';
                                                        $kehadiranText = 'Belum Ada';

                                                        if (str_contains($rekapan->kehadiran, 'Hadir')) {
                                                            $kehadiranStatus = 'success';
                                                            $kehadiranIcon = 'fa-check';
                                                            $kehadiranText = 'Hadir';
                                                        } elseif (str_contains($rekapan->kehadiran, 'Sakit')) {
                                                            $kehadiranStatus = 'warning';
                                                            $kehadiranIcon = 'fa-thermometer';
                                                            $kehadiranText = 'Sakit';
                                                        } elseif (str_contains($rekapan->kehadiran, 'Izin')) {
                                                            $kehadiranStatus = 'info';
                                                            $kehadiranIcon = 'fa-info-circle';
                                                            $kehadiranText = 'Izin';
                                                        } elseif (str_contains($rekapan->kehadiran, 'Alpa')) {
                                                            $kehadiranStatus = 'danger';
                                                            $kehadiranIcon = 'fa-times';
                                                            $kehadiranText = 'Alpa';
                                                        }
                                                    @endphp
                                                    <span class="badge bg-{{ $kehadiranStatus }} mb-2">
                                                        <i class="fas {{ $kehadiranIcon }} me-1"></i>{{ $kehadiranText }}
                                                    </span>
                                                    <div class="preview-text">
                                                        {{ Str::limit($rekapan->kehadiran ?: 'Belum ada data', 80) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $perilakuBaik = str_contains($rekapan->perilaku, 'Baik') ||
                                                            str_contains($rekapan->perilaku, 'Tidak ada pelanggaran');
                                                    @endphp
                                                    <span class="badge bg-{{ $perilakuBaik ? 'success' : 'danger' }} mb-2">
                                                        <i class="fas {{ $perilakuBaik ? 'fa-smile' : 'fa-frown' }} me-1"></i>
                                                        {{ $perilakuBaik ? 'Baik' : 'Ada Pelanggaran' }}
                                                    </span>
                                                    <div class="preview-text">
                                                        {{ Str::limit($rekapan->perilaku ?: 'Tidak ada catatan', 80) }}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('orangtua.detail-rekapan', $rekapan->id) }}"
                                                        class="btn btn-sm btn-info" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    Menampilkan {{ $rekapans->firstItem() }} - {{ $rekapans->lastItem() }}
                                    dari {{ $rekapans->total() }} rekapan
                                </div>
                                <div>
                                    {{ $rekapans->appends(['bulan' => $bulan, 'tahun' => $tahun])->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum Ada Riwayat Rekapan</h5>
                                <p class="text-muted">
                                    @if($bulan)
                                        Tidak ada rekapan untuk bulan {{ $bulanOptions[$bulan] }} {{ $tahun }}.
                                    @else
                                        Riwayat rekapan akan muncul di sini setelah wali kelas mengirimkan rekapan.
                                    @endif
                                </p>
                                @if($bulan || $tahun != now()->year)
                                    <a href="{{ route('orangtua.riwayat-anak', $siswa->id) }}" class="btn btn-primary">
                                        <i class="fas fa-filter me-1"></i> Lihat Semua Riwayat
                                    </a>
                                @endif
                                <a href="{{ route('orangtua.detail-anak', $siswa->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
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

        .avatar-circle-small {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .preview-text {
            font-size: 0.9rem;
            line-height: 1.5;
            color: #4a5568;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody tr {
            transition: background-color 0.2s;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        .stat-item {
            padding: 10px 0;
        }

        .progress {
            border-radius: 10px;
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
