@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-user-graduate me-2"></i>Detail Nilai Siswa
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-user me-1"></i>{{ $siswa->nama }} ({{ $siswa->nis }})
                            <span class="mx-2">•</span>
                            <i class="fas fa-book me-1"></i>{{ $mapel->nama_matapelajaran }}
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

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Berhasil!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Gagal!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- Left Column: Info Siswa --}}
            <div class="col-md-4 mb-4">
                <div class="card card-custom">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-user-circle me-2"></i>Informasi Siswa
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="avatar-large mb-3">
                            {{ substr($siswa->nama, 0, 1) }}
                        </div>
                        <h4 class="mb-1">{{ $siswa->nama }}</h4>
                        <p class="text-muted mb-3">{{ $siswa->nis }}</p>

                        <div class="info-item">
                            <i class="fas fa-door-open text-primary"></i>
                            <strong>Kelas:</strong> {{ $siswa->kelas->nama }}
                        </div>

                        @if($siswa->orang_tua)
                            <div class="info-item">
                                <i class="fas fa-user-friends text-success"></i>
                                <strong>Orang Tua:</strong> {{ $siswa->orang_tua->nama_orang_tua }}
                            </div>
                        @endif

                        <div class="info-item">
                            <i class="fas fa-book text-info"></i>
                            <strong>Mata Pelajaran:</strong> {{ $mapel->nama_matapelajaran }}
                        </div>
                    </div>
                </div>

                {{-- Status Card --}}
                @if($nilaiAkhir)
                    <div class="card card-custom mt-3">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-2">Status Kelulusan</h6>
                            <div class="status-badge mb-2">
                                <span class="badge bg-{{ $nilaiAkhir->predikat_color }}"
                                    style="font-size: 3rem; padding: 1rem 2rem;">
                                    {{ $nilaiAkhir->predikat }}
                                </span>
                            </div>
                            <p class="mb-2">
                                <strong>{{ $nilaiAkhir->predikat_description }}</strong>
                            </p>
                            <span class="badge bg-{{ $nilaiAkhir->isTuntas() ? 'success' : 'danger' }}"
                                style="font-size: 1rem; padding: 0.5rem 1rem;">
                                {{ $nilaiAkhir->status }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Column: Detail Nilai --}}
            <div class="col-md-8">
                {{-- Nilai Tugas --}}
                <div class="card card-custom mb-3">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white">
                                <i class="fas fa-tasks me-2"></i>Nilai Tugas
                            </h5>
                            @if($nilaiTugas && $guru->isGuruMapel())
                                <a href="{{ route('guru.nilai.edit', [$nilaiTugas->id, 'tugas']) }}"
                                    class="btn btn-sm btn-light">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @elseif(!$nilaiTugas && $guru->isGuruMapel())
                                {{-- ✅ TOMBOL INPUT JIKA NILAI BELUM ADA --}}
                                <a href="{{ route('guru.nilai.tugas.create', ['kelas_id' => $siswa->kelas_id, 'mapel_id' => $mapel->id]) }}"
                                    class="btn btn-sm btn-light">
                                    <i class="fas fa-plus"></i> Input Nilai
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if($nilaiTugas)
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="nilai-display">
                                        <small class="text-muted d-block">Nilai</small>
                                        <h2 class="mb-0 text-primary">{{ number_format($nilaiTugas->nilai_tugas, 2) }}</h2>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="nilai-info">
                                        <small class="text-muted d-block">Bobot</small>
                                        <h4 class="mb-0">30%</h4>
                                    </div>
                                    <div class="nilai-info mt-2">
                                        <small class="text-muted d-block">Kontribusi Nilai Akhir</small>
                                        <h5 class="mb-0 text-success">{{ number_format($nilaiTugas->nilai_tugas * 0.30, 2) }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            @if($nilaiTugas->keterangan)
                                <div class="mt-3">
                                    <small class="text-muted d-block">Keterangan:</small>
                                    <p class="mb-0">{{ $nilaiTugas->keterangan }}</p>
                                </div>
                            @endif
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>Diupdate: {{ $nilaiTugas->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-3">Nilai Tugas belum diinput</p>

                                {{-- ✅ TOMBOL INPUT DI TENGAH --}}
                                @if($guru->isGuruMapel())
                                    <a href="{{ route('guru.nilai.tugas.create', ['kelas_id' => $siswa->kelas_id, 'mapel_id' => $mapel->id]) }}"
                                        class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Input Nilai Tugas
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Nilai UTS --}}
                <div class="card card-custom mb-3">
                    <div class="card-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white">
                                <i class="fas fa-file-alt me-2"></i>Nilai UTS
                            </h5>
                            @if($nilaiUts && $guru->isGuruMapel())
                                <a href="{{ route('guru.nilai.edit', [$nilaiUts->id, 'uts']) }}" class="btn btn-sm btn-light">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @elseif(!$nilaiUts && $guru->isGuruMapel())
                                {{-- ✅ TOMBOL INPUT JIKA NILAI BELUM ADA --}}
                                <a href="{{ route('guru.nilai.uts.create', ['kelas_id' => $siswa->kelas_id, 'mapel_id' => $mapel->id]) }}"
                                    class="btn btn-sm btn-light">
                                    <i class="fas fa-plus"></i> Input Nilai
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if($nilaiUts)
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="nilai-display">
                                        <small class="text-muted d-block">Nilai</small>
                                        <h2 class="mb-0 text-info">{{ number_format($nilaiUts->nilai_uts, 2) }}</h2>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="nilai-info">
                                        <small class="text-muted d-block">Bobot</small>
                                        <h4 class="mb-0">35%</h4>
                                    </div>
                                    <div class="nilai-info mt-2">
                                        <small class="text-muted d-block">Kontribusi Nilai Akhir</small>
                                        <h5 class="mb-0 text-success">{{ number_format($nilaiUts->nilai_uts * 0.35, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                            @if($nilaiUts->keterangan)
                                <div class="mt-3">
                                    <small class="text-muted d-block">Keterangan:</small>
                                    <p class="mb-0">{{ $nilaiUts->keterangan }}</p>
                                </div>
                            @endif
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>Diupdate: {{ $nilaiUts->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-3">Nilai UTS belum diinput</p>

                                {{-- ✅ TOMBOL INPUT DI TENGAH --}}
                                @if($guru->isGuruMapel())
                                    <a href="{{ route('guru.nilai.uts.create', ['kelas_id' => $siswa->kelas_id, 'mapel_id' => $mapel->id]) }}"
                                        class="btn btn-info">
                                        <i class="fas fa-plus me-1"></i> Input Nilai UTS
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Nilai UAS --}}
                <div class="card card-custom mb-3">
                    <div class="card-header" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white">
                                <i class="fas fa-graduation-cap me-2"></i>Nilai UAS
                            </h5>
                            @if($nilaiUas && $guru->isGuruMapel())
                                <a href="{{ route('guru.nilai.edit', [$nilaiUas->id, 'uas']) }}" class="btn btn-sm btn-light">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @elseif(!$nilaiUas && $guru->isGuruMapel())
                                {{-- ✅ TOMBOL INPUT JIKA NILAI BELUM ADA --}}
                                <a href="{{ route('guru.nilai.uas.create', ['kelas_id' => $siswa->kelas_id, 'mapel_id' => $mapel->id]) }}"
                                    class="btn btn-sm btn-light">
                                    <i class="fas fa-plus"></i> Input Nilai
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if($nilaiUas)
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="nilai-display">
                                        <small class="text-muted d-block">Nilai</small>
                                        <h2 class="mb-0 text-success">{{ number_format($nilaiUas->nilai_uas, 2) }}</h2>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="nilai-info">
                                        <small class="text-muted d-block">Bobot</small>
                                        <h4 class="mb-0">35%</h4>
                                    </div>
                                    <div class="nilai-info mt-2">
                                        <small class="text-muted d-block">Kontribusi Nilai Akhir</small>
                                        <h5 class="mb-0 text-success">{{ number_format($nilaiUas->nilai_uas * 0.35, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                            @if($nilaiUas->keterangan)
                                <div class="mt-3">
                                    <small class="text-muted d-block">Keterangan:</small>
                                    <p class="mb-0">{{ $nilaiUas->keterangan }}</p>
                                </div>
                            @endif
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>Diupdate: {{ $nilaiUas->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-3">Nilai UAS belum diinput</p>

                                {{-- ✅ TOMBOL INPUT DI TENGAH --}}
                                @if($guru->isGuruMapel())
                                    <a href="{{ route('guru.nilai.uas.create', ['kelas_id' => $siswa->kelas_id, 'mapel_id' => $mapel->id]) }}"
                                        class="btn btn-success">
                                        <i class="fas fa-plus me-1"></i> Input Nilai UAS
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Nilai Akhir --}}
                @if($nilaiAkhir)
                    <div class="card card-custom">
                        <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <h5 class="mb-0 text-white">
                                <i class="fas fa-trophy me-2"></i>Nilai Akhir
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-3">
                                <small class="text-muted d-block mb-2">Nilai Akhir</small>
                                <h1 class="display-3 mb-3 fw-bold text-danger">{{ number_format($nilaiAkhir->nilai_akhir, 2) }}
                                </h1>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="nilai-summary">
                                            <i class="fas fa-calculator text-primary fa-2x mb-2"></i>
                                            <p class="mb-0 text-muted">Perhitungan:</p>
                                            <small class="text-muted">
                                                ({{ number_format($nilaiAkhir->nilai_tugas, 0) }} × 30%) +
                                                ({{ number_format($nilaiAkhir->nilai_uts, 0) }} × 35%) +
                                                ({{ number_format($nilaiAkhir->nilai_uas, 0) }} × 35%)
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="nilai-summary">
                                            <i class="fas fa-chart-line text-success fa-2x mb-2"></i>
                                            <p class="mb-0 text-muted">KKM: 75</p>
                                            <small class="text-muted">
                                                {{ $nilaiAkhir->isTuntas() ? 'Melampaui KKM' : 'Di bawah KKM' }}
                                                ({{ $nilaiAkhir->nilai_akhir >= 75 ? '+' : '' }}{{ number_format($nilaiAkhir->nilai_akhir - 75, 2) }})
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                @if($nilaiAkhir->keterangan)
                                    <div class="mt-4 text-start">
                                        <small class="text-muted d-block">Keterangan:</small>
                                        <p class="mb-0">{{ $nilaiAkhir->keterangan }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card card-custom">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-calculator fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Nilai Akhir Belum Tersedia</h5>
                            <p class="text-muted">
                                Nilai akhir akan otomatis dihitung setelah semua komponen nilai (Tugas, UTS, UAS) diinput.
                            </p>
                        </div>
                    </div>
                @endif
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

        .avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 2.5rem;
            margin: 0 auto;
        }

        .info-item {
            padding: 0.75rem;
            margin: 0.5rem 0;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: left;
        }

        .info-item i {
            margin-right: 0.5rem;
        }

        .nilai-display {
            padding: 1rem;
            text-align: center;
        }

        .nilai-display h2 {
            font-size: 3rem;
            font-weight: bold;
        }

        .nilai-info {
            padding: 0.5rem;
        }

        .nilai-summary {
            padding: 1rem;
        }

        .status-badge {
            margin: 1rem 0;
        }

        .alert {
            border-radius: 12px;
            border: none;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Auto hide alerts
        setTimeout(function () {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
@endpush
