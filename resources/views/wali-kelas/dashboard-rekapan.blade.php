@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-chart-line me-2"></i>Dashboard Rekapan
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-door-open me-1"></i>
                            Kelas {{ Auth::user()->guru->kelasWali->nama }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.rekapan.kirim') }}" class="btn btn-success me-2">
                            <i class="fab fa-whatsapp me-1"></i> Kirim Rekapan via WhatsApp
                        </a>
                        <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tanggal Selector --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <form action="{{ route('guru.rekapan.dashboard') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Pilih Tanggal
                                </label>
                                <input type="date"
                                       name="tanggal"
                                       class="form-control"
                                       value="{{ $tanggal->format('Y-m-d') }}"
                                       max="{{ today()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Lihat
                                </button>
                            </div>
                            <div class="col-md-7 text-end">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar-day me-2"></i>
                                    {{ $tanggal->isoFormat('dddd, D MMMM Y') }}
                                </h5>
                            </div>
                        </form>
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
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $totalSiswa }}</h3>
                            <p class="text-muted mb-0">Total Siswa</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $belumDikirim }}</h3>
                            <p class="text-muted mb-0">Belum Dikirim</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $sudahDikirim }}</h3>
                            <p class="text-muted mb-0">Sudah Dikirim</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $gagal }}</h3>
                            <p class="text-muted mb-0">Gagal Dikirim</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progress Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="mb-3">
                            <i class="fas fa-chart-pie me-2"></i>Progress Pengiriman Rekapan
                        </h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>
                                <strong>{{ $sudahDikirim }}</strong> dari <strong>{{ $totalSiswa }}</strong> siswa
                            </span>
                            <span>
                                <strong>{{ $totalSiswa > 0 ? round(($sudahDikirim / $totalSiswa) * 100, 1) : 0 }}%</strong>
                            </span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            @php
                                $percentage = $totalSiswa > 0 ? ($sudahDikirim / $totalSiswa) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-{{ $percentage >= 80 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}"
                                 role="progressbar"
                                 style="width: {{ $percentage }}%">
                                {{ round($percentage, 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('guru.rekapan.kirim', ['tanggal' => $tanggal->format('Y-m-d')]) }}"
                                   class="action-card">
                                    <div class="d-flex align-items-center">
                                        <div class="action-icon me-3">
                                            <i class="fab fa-whatsapp"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Kirim Rekapan</h6>
                                            <small class="text-muted">Kirim via WhatsApp</small>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-4 mb-3">
                                <form action="{{ route('guru.rekapan.regenerate') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit"
                                            class="action-card w-100 border-0 bg-transparent"
                                            onclick="return confirm('Generate ulang semua rekapan untuk hari ini?')">
                                        <div class="d-flex align-items-center">
                                            <div class="action-icon me-3">
                                                <i class="fas fa-sync"></i>
                                            </div>
                                            <div class="text-start">
                                                <h6 class="mb-1">Regenerate Rekapan</h6>
                                                <small class="text-muted">Update data terbaru</small>
                                            </div>
                                        </div>
                                    </button>
                                </form>
                            </div>

                            <div class="col-md-4 mb-3">
                                <a href="{{ route('guru.absensi.index') }}" class="action-card">
                                    <div class="d-flex align-items-center">
                                        <div class="action-icon me-3">
                                            <i class="fas fa-clipboard-check"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Lihat Absensi</h6>
                                            <small class="text-muted">Data kehadiran siswa</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Section --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="mb-3">
                            <i class="fas fa-info-circle me-2"></i>Informasi Rekapan Harian
                        </h5>
                        <div class="alert alert-info mb-0">
                            <ul class="mb-0 ps-3">
                                <li>Rekapan harian berisi <strong>kehadiran</strong> dan <strong>perilaku</strong> siswa</li>
                                <li>Rekapan dibuat otomatis setelah absensi dan pelanggaran <strong>diselesaikan</strong></li>
                                <li>WhatsApp akan dikirim ke <strong>nomor HP orang tua</strong> yang terdaftar</li>
                                <li>Rekapan akademik (nilai) dikirim <strong>terpisah</strong> di akhir semester</li>
                                <li>Pastikan data absensi sudah <strong>diselesaikan</strong> sebelum kirim rekapan</li>
                            </ul>
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

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
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
            font-size: 0.875rem;
        }

        .action-card {
            display: block;
            padding: 20px;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }

        .action-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
            transform: translateY(-3px);
        }

        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .action-card h6 {
            font-weight: 600;
            color: #2d3748;
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
