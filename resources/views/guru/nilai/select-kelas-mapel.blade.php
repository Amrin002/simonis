@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-clipboard-list me-2"></i>Pilih Kelas & Mata Pelajaran
                        </h1>
                        <p class="text-muted mb-0">
                            Pilih kelas dan mata pelajaran untuk input nilai siswa
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.nilai.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Petunjuk:</strong> Pilih kelas dan mata pelajaran yang ingin Anda input nilainya.
                    Anda hanya dapat menginput nilai untuk kelas dan mata pelajaran yang Anda ajar.
                </div>
            </div>
        </div>

        {{-- Jadwal Cards --}}
        @if($jadwals->count() > 0)
            <div class="row">
                @foreach($jadwals as $kelasId => $jadwalPerKelas)
                    @php
        $kelas = $jadwalPerKelas->first()->kelas;
                    @endphp
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card card-kelas">
                            <div class="card-header-gradient">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1 text-white">
                                            <i class="fas fa-door-open me-2"></i>{{ $kelas->nama }}
                                        </h5>
                                        <small class="text-white opacity-75">
                                            <i class="fas fa-users me-1"></i>{{ $kelas->jumlah_siswa }} Siswa
                                        </small>
                                    </div>
                                    <div class="badge-count">
                                        {{ $jadwalPerKelas->count() }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mapel-list">
                                    @foreach($jadwalPerKelas as $jadwal)
                                        <div class="mapel-item">
                                            <div class="mapel-info">
                                                <div class="mapel-icon">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                                <div class="mapel-detail">
                                                    <strong class="mapel-name">{{ $jadwal->mapel->nama_matapelajaran }}</strong>
                                                    <small class="text-muted d-block">
                                                        {{ $jadwal->mapel->kode_mapel ?? '-' }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-plus me-1"></i> Input
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('guru.nilai.tugas.create', ['kelas_id' => $kelas->id, 'mapel_id' => $jadwal->mapel->id]) }}">
                                                            <i class="fas fa-tasks text-primary me-2"></i> Nilai Tugas
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('guru.nilai.uts.create', ['kelas_id' => $kelas->id, 'mapel_id' => $jadwal->mapel->id]) }}">
                                                            <i class="fas fa-file-alt text-info me-2"></i> Nilai UTS
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('guru.nilai.uas.create', ['kelas_id' => $kelas->id, 'mapel_id' => $jadwal->mapel->id]) }}">
                                                            <i class="fas fa-graduation-cap text-success me-2"></i> Nilai UAS
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada jadwal mengajar</h5>
                                <p class="text-muted">
                                    Anda belum memiliki jadwal mengajar. Hubungi admin untuk menambahkan jadwal.
                                </p>
                                <a href="{{ route('guru.nilai.index') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Quick Stats --}}
        @if($jadwals->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="quick-stat">
                                        <i class="fas fa-door-open fa-2x text-primary mb-2"></i>
                                        <h4 class="mb-0">{{ $jadwals->count() }}</h4>
                                        <small class="text-muted">Total Kelas</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="quick-stat">
                                        <i class="fas fa-book fa-2x text-success mb-2"></i>
                                        <h4 class="mb-0">{{ $jadwals->flatten()->unique('mapel_id')->count() }}</h4>
                                        <small class="text-muted">Mata Pelajaran</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="quick-stat">
                                        <i class="fas fa-users fa-2x text-info mb-2"></i>
                                        <h4 class="mb-0">
                                            {{ $jadwals->flatten()->map(fn($j) => $j->kelas)->unique('id')->sum('jumlah_siswa') }}
                                        </h4>
                                        <small class="text-muted">Total Siswa</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="quick-stat">
                                        <i class="fas fa-clipboard-check fa-2x text-warning mb-2"></i>
                                        <h4 class="mb-0">{{ $jadwals->flatten()->count() }}</h4>
                                        <small class="text-muted">Total Jadwal</small>
                                    </div>
                                </div>
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
        .card-kelas {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: visible;
            /* ✅ Tetap visible */
            position: relative;
            /* ✅ TAMBAH */
            z-index: 1;
            /* ✅ TAMBAH - default z-index */
        }

        .card-kelas:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
            z-index: 10;
            /* ✅ TAMBAH - naik saat hover */
        }

        .card-header-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1.25rem;
            border: none;
            border-radius: 12px 12px 0 0;
        }

        .badge-count {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .mapel-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .mapel-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            transition: all 0.2s ease;
            position: relative;
        }

        .mapel-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .mapel-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .mapel-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .mapel-detail {
            display: flex;
            flex-direction: column;
        }

        .mapel-name {
            font-size: 0.95rem;
            color: #2d3748;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
        }

        .quick-stat {
            padding: 1rem;
        }

        .quick-stat h4 {
            font-weight: bold;
            color: #2d3748;
        }

        /* ✅ FIX DROPDOWN DENGAN Z-INDEX YANG BENAR */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            /* ✅ Shadow lebih tegas */
            border-radius: 8px;
            z-index: 9999 !important;
            /* ✅ Z-INDEX SUPER TINGGI */
            min-width: 200px;
            padding: 0.5rem 0;
            /* ✅ TAMBAH PADDING */
        }

        .dropdown-item {
            padding: 0.75rem 1.25rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .dropdown-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
            color: #667eea;
        }

        .dropdown-item i {
            width: 24px;
            /* ✅ Lebih lebar untuk icon */
            margin-right: 0.5rem;
        }

        /* ✅ TAMBAH: Pastikan card body tidak kepotong */
        .card-kelas .card-body {
            padding: 1.25rem;
            padding-bottom: 2rem;
            /* ✅ Extra padding bawah */
        }

        /* ✅ TAMBAH: Styling untuk row agar card tidak saling tumpuk */
        .row {
            position: relative;
        }

        /* ✅ TAMBAH: Pastikan dropdown tidak kepotong di card terakhir */
        .col-md-6:last-child .card-kelas,
        .col-lg-4:last-child .card-kelas {
            margin-bottom: 150px;
            /* ✅ Extra margin untuk dropdown */
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Smooth scroll animation
        document.addEventListener('DOMContentLoaded', function () {
            const cards = document.querySelectorAll('.card-kelas');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
@endpush
