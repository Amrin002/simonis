@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-clipboard-check me-2"></i>Pilih Kelas & Mata Pelajaran
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i> Pilih kelas dan mata pelajaran untuk input absensi
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.absensi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-info border-0 shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <h5 class="mb-1">Informasi</h5>
                            <p class="mb-0">Pilih salah satu kombinasi kelas dan mata pelajaran yang Anda ajar untuk
                                melakukan input absensi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kelas & Mapel Selection --}}
        @if($jadwals->count() > 0)
            <div class="row">
                @foreach($jadwals as $kelasId => $jadwalPerKelas)
                    @php
                        $kelas = $jadwalPerKelas->first()->kelas;
                        $mapels = $jadwalPerKelas->pluck('mapel')->unique('id');
                    @endphp
                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="card card-custom h-100">
                            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 text-white">
                                        <i class="fas fa-door-open me-2"></i>Kelas {{ $kelas->nama }}
                                    </h5>
                                    <span class="badge bg-light text-dark">
                                        {{ $mapels->count() }} Mapel
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                {{-- Kelas Info --}}
                                <div class="alert alert-light mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Tingkat</small>
                                            <strong>{{ $kelas->tingkat ?? '-' }}</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Jumlah Siswa</small>
                                            <strong>{{ $kelas->siswas->count() }} Siswa</strong>
                                        </div>
                                    </div>
                                </div>

                                {{-- Mapel List --}}
                                <div class="list-group">
                                    @foreach($mapels as $mapel)
                                        @php
                                            $jadwalMapel = $jadwalPerKelas->where('mapel_id', $mapel->id)->first();
                                            $today = now()->format('Y-m-d');
                                            $absenHariIni = \App\Models\Absen::where('kelas_id', $kelas->id)
                                                ->where('tanggal', $today)
                                                ->where('mata_pelajaran', $mapel->nama_matapelajaran)
                                                ->first();
                                        @endphp
                                        <a href="{{ route('guru.absensi.create-guru-mapel', ['kelas_id' => $kelas->id, 'mapel_id' => $mapel->id]) }}"
                                            class="list-group-item list-group-item-action {{ $absenHariIni ? 'list-group-item-success' : '' }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fas fa-book text-primary me-2"></i>
                                                        <strong>{{ $mapel->nama_matapelajaran }}</strong>
                                                        @if($absenHariIni)
                                                            <span class="badge bg-success ms-2">
                                                                <i class="fas fa-check me-1"></i>Sudah Absen Hari Ini
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-muted small">
                                                        <i class="fas fa-code me-1"></i>Kode: {{ $mapel->kode }}
                                                        @if($jadwalMapel)
                                                            <span class="ms-3">
                                                                <i class="fas fa-calendar-day me-1"></i>
                                                                Jadwal: {{ $jadwalMapel->hari }}, {{ $jadwalMapel->waktu_mulai_format }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    <i class="fas fa-chevron-right text-primary"></i>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>

                                {{-- Summary --}}
                                <div class="mt-3 p-3 bg-light rounded">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Total Jadwal</small>
                                            <strong class="text-primary">{{ $jadwalPerKelas->count() }} Jadwal</strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Mata Pelajaran</small>
                                            <strong class="text-success">{{ $mapels->count() }} Mapel</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="text-center py-5">
                                <i class="fas fa-chalkboard-teacher fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada jadwal mengajar</h5>
                                <p class="text-muted">Anda belum memiliki jadwal mengajar. Hubungi admin untuk informasi lebih
                                    lanjut.</p>
                                <a href="{{ route('guru.dashboard') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-home me-1"></i> Kembali ke Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Quick Stats --}}
        @if($jadwals->count() > 0)
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Ringkasan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3 mb-3">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-door-open fa-2x text-primary mb-2"></i>
                                        <h4 class="mb-0">{{ $jadwals->count() }}</h4>
                                        <small class="text-muted">Kelas Diajar</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-book fa-2x text-success mb-2"></i>
                                        @php
                                            $totalMapel = 0;
                                            foreach ($jadwals as $items) {
                                                $totalMapel += $items->pluck('mapel')->unique('id')->count();
                                            }
                                        @endphp
                                        <h4 class="mb-0">{{ $totalMapel }}</h4>
                                        <small class="text-muted">Total Mapel</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-calendar-alt fa-2x text-warning mb-2"></i>
                                        @php
                                            $totalJadwal = 0;
                                            foreach ($jadwals as $items) {
                                                $totalJadwal += $items->count();
                                            }
                                        @endphp
                                        <h4 class="mb-0">{{ $totalJadwal }}</h4>
                                        <small class="text-muted">Total Jadwal</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="p-3 bg-light rounded">
                                        <i class="fas fa-users fa-2x text-info mb-2"></i>
                                        @php
                                            $totalSiswa = 0;
                                            foreach ($jadwals as $items) {
                                                $kls = $items->first()->kelas;
                                                $totalSiswa += $kls->siswas->count();
                                            }
                                        @endphp
                                        <h4 class="mb-0">{{ $totalSiswa }}</h4>
                                        <small class="text-muted">Total Siswa</small>
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
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card-custom:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
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

        .list-group-item {
            border: 1px solid #dee2e6;
            margin-bottom: 10px;
            border-radius: 8px !important;
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            background-color: rgba(102, 126, 234, 0.05);
            border-color: #667eea;
            transform: translateX(10px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .list-group-item-success {
            background-color: rgba(40, 167, 69, 0.1);
            border-color: #28a745;
        }

        .list-group-item-success:hover {
            background-color: rgba(40, 167, 69, 0.15);
        }

        .alert {
            border-radius: 12px;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Add smooth scroll animation
        document.addEventListener('DOMContentLoaded', function () {
            const listItems = document.querySelectorAll('.list-group-item-action');

            listItems.forEach(item => {
                item.addEventListener('click', function (e) {
                    // Add loading animation or smooth transition here if needed
                });
            });
        });
    </script>
@endpush
