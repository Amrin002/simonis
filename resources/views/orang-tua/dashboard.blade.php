@extends('template-orangtua.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="page-title">Dashboard Orang Tua</h1>
                <div class="d-flex align-items-center text-muted">
                    <i class="fas fa-hand-wave text-warning me-2"></i>
                    <span>Selamat datang, {{ $orangTua->nama_orang_tua }}</span>
                </div>
                <p class="text-muted mb-0">
                    <i class="fas fa-calendar-day me-1"></i>
                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
        </div>

        {{-- Notifikasi Penting --}}
        @if(count($notifikasi) > 0)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-bell me-2"></i>Notifikasi Penting
                        </h5>
                        <hr>
                        <ul class="mb-0">
                            @foreach($notifikasi as $notif)
                                <li>
                                    <span class="badge bg-{{ $notif['badge'] }} me-2">
                                        @if($notif['type'] == 'kehadiran')
                                            <i class="fas fa-calendar-times"></i>
                                        @else
                                            <i class="fas fa-exclamation-triangle"></i>
                                        @endif
                                    </span>
                                    {{ $notif['message'] }}
                                </li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            {{-- Total Anak --}}
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $siswas->count() }}</h3>
                            <p class="text-muted mb-0">Total Anak</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hadir Hari Ini --}}
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            @php
                                $hadirCount = 0;
                                foreach ($siswas as $siswa) {
                                    $rekapan = $rekapanHariIni[$siswa->id] ?? null;
                                    if ($rekapan && $rekapan->kehadiran && str_contains($rekapan->kehadiran, 'Hadir')) {
                                        $hadirCount++;
                                    }
                                }
                            @endphp
                            <h3 class="mb-0">{{ $hadirCount }}</h3>
                            <p class="text-muted mb-0">Hadir Hari Ini</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tidak Hadir --}}
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div>
                            @php
                                $tidakHadirCount = $siswas->count() - $hadirCount;
                            @endphp
                            <h3 class="mb-0">{{ $tidakHadirCount }}</h3>
                            <p class="text-muted mb-0">Tidak Hadir</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pelanggaran Hari Ini --}}
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            @php
                                $pelanggaranCount = 0;
                                foreach ($siswas as $siswa) {
                                    $rekapan = $rekapanHariIni[$siswa->id] ?? null;
                                    if (
                                        $rekapan && $rekapan->perilaku &&
                                        !str_contains($rekapan->perilaku, 'Baik') &&
                                        !str_contains($rekapan->perilaku, 'Tidak ada pelanggaran')
                                    ) {
                                        $pelanggaranCount++;
                                    }
                                }
                            @endphp
                            <h3 class="mb-0">{{ $pelanggaranCount }}</h3>
                            <p class="text-muted mb-0">Pelanggaran</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rekapan Anak Hari Ini --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-clipboard-list me-2"></i>Rekapan Hari Ini -
                                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($siswas->count() > 0)
                            <div class="row">
                                @foreach($siswas as $siswa)
                                    @php
                                        $rekapan = $rekapanHariIni[$siswa->id] ?? null;

                                        // Tentukan status kehadiran
                                        $statusKehadiran = 'Belum Ada Data';
                                        $badgeKehadiran = 'secondary';
                                        $iconKehadiran = 'fa-question-circle';

                                        if ($rekapan && $rekapan->kehadiran) {
                                            if (str_contains($rekapan->kehadiran, 'Hadir')) {
                                                $statusKehadiran = 'Hadir';
                                                $badgeKehadiran = 'success';
                                                $iconKehadiran = 'fa-check-circle';
                                            } elseif (str_contains($rekapan->kehadiran, 'Sakit')) {
                                                $statusKehadiran = 'Sakit';
                                                $badgeKehadiran = 'warning';
                                                $iconKehadiran = 'fa-thermometer';
                                            } elseif (str_contains($rekapan->kehadiran, 'Izin')) {
                                                $statusKehadiran = 'Izin';
                                                $badgeKehadiran = 'info';
                                                $iconKehadiran = 'fa-info-circle';
                                            } elseif (str_contains($rekapan->kehadiran, 'Alpa')) {
                                                $statusKehadiran = 'Alpa';
                                                $badgeKehadiran = 'danger';
                                                $iconKehadiran = 'fa-times-circle';
                                            }
                                        }

                                        // Tentukan status perilaku
                                        $statusPerilaku = 'Baik';
                                        $badgePerilaku = 'success';
                                        $iconPerilaku = 'fa-smile';

                                        if ($rekapan && $rekapan->perilaku) {
                                            if (
                                                !str_contains($rekapan->perilaku, 'Baik') &&
                                                !str_contains($rekapan->perilaku, 'Tidak ada pelanggaran')
                                            ) {
                                                $statusPerilaku = 'Ada Pelanggaran';
                                                $badgePerilaku = 'danger';
                                                $iconPerilaku = 'fa-frown';
                                            }
                                        }
                                    @endphp

                                    <div class="col-lg-6 mb-4">
                                        <div class="card h-100 shadow-sm border-0 hover-card">
                                            <div class="card-header bg-gradient-primary text-white">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h5 class="mb-0">
                                                            <i class="fas fa-user-graduate me-2"></i>{{ $siswa->nama }}
                                                        </h5>
                                                        <small class="opacity-75">
                                                            <i class="fas fa-id-card me-1"></i>{{ $siswa->nis }}
                                                        </small>
                                                    </div>
                                                    <span class="badge bg-light text-dark">
                                                        {{ $siswa->kelas->nama ?? '-' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                {{-- Kehadiran --}}
                                                <div class="mb-3 p-3 rounded" style="background-color: #f8f9fa;">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0 text-muted">
                                                            <i class="fas fa-calendar-check me-2"></i>Kehadiran
                                                        </h6>
                                                        <span class="badge bg-{{ $badgeKehadiran }}">
                                                            <i class="fas {{ $iconKehadiran }} me-1"></i>{{ $statusKehadiran }}
                                                        </span>
                                                    </div>
                                                    @if($rekapan && $rekapan->kehadiran)
                                                        <small class="text-muted">
                                                            {{ Str::limit($rekapan->kehadiran, 80) }}
                                                        </small>
                                                    @else
                                                        <small class="text-muted">Belum ada data kehadiran</small>
                                                    @endif
                                                </div>

                                                {{-- Perilaku --}}
                                                <div class="mb-3 p-3 rounded" style="background-color: #f8f9fa;">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0 text-muted">
                                                            <i class="fas fa-user-check me-2"></i>Perilaku
                                                        </h6>
                                                        <span class="badge bg-{{ $badgePerilaku }}">
                                                            <i class="fas {{ $iconPerilaku }} me-1"></i>{{ $statusPerilaku }}
                                                        </span>
                                                    </div>
                                                    @if($rekapan && $rekapan->perilaku)
                                                        <small class="text-muted">
                                                            {{ Str::limit($rekapan->perilaku, 80) }}
                                                        </small>
                                                    @else
                                                        <small class="text-muted">Tidak ada catatan perilaku</small>
                                                    @endif
                                                </div>

                                                {{-- Info Tambahan --}}
                                                <div class="row text-center mb-3">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Wali Kelas</small>
                                                        <strong>{{ $siswa->kelas->waliKelas->nama_guru ?? '-' }}</strong>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Kelas</small>
                                                        <strong>{{ $siswa->kelas->nama ?? '-' }}</strong>
                                                    </div>
                                                </div>

                                                {{-- Action Button --}}
                                                <div class="d-grid">
                                                    <a href="{{ route('orangtua.detail-anak', $siswa->id) }}"
                                                        class="btn btn-primary">
                                                        <i class="fas fa-eye me-2"></i>Lihat Detail Lengkap
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Empty State --}}
                            <div class="text-center py-5">
                                <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum Ada Data Anak</h5>
                                <p class="text-muted mb-0">Silakan hubungi admin untuk menambahkan data anak Anda.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Info & Bantuan --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom border-primary">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-2">
                                    <i class="fas fa-info-circle text-primary me-2"></i>Informasi
                                </h5>
                                <p class="mb-0 text-muted">
                                    Rekapan harian akan dikirimkan otomatis melalui WhatsApp setiap hari oleh wali kelas.
                                    Anda juga dapat melihat riwayat lengkap rekapan anak Anda dengan mengklik tombol "Lihat
                                    Detail Lengkap" pada setiap card anak.
                                </p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fab fa-whatsapp fa-4x text-success mb-2"></i>
                                <p class="mb-0 small text-muted">Notifikasi via WhatsApp</p>
                            </div>
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
            margin-bottom: 0.5rem;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
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

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .hover-card {
            transition: all 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        }

        .opacity-75 {
            opacity: 0.75;
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
        // Show success message
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Show error message
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
            });
        @endif
    </script>
@endpush
