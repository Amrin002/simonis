@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-trophy me-2"></i>Nilai Akhir Siswa
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar-day me-1"></i> {{ now()->isoFormat('dddd, D MMMM YYYY') }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.nilai.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        {{-- <a href="{{ route('guru.nilai.export-pdf') }}" class="btn btn-danger me-2">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                        </a> --}}
                        <a href="{{ route('guru.nilai.print') }}" class="btn btn-info" target="_blank">
                            <i class="fas fa-print me-1"></i> Print
                        </a>
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
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <p class="text-muted mb-0">Total Siswa</p>
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
                            <h3 class="mb-0">{{ $stats['tuntas'] }}</h3>
                            <p class="text-muted mb-0">Tuntas (≥75)</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['tidak_tuntas'] }}</h3>
                            <p class="text-muted mb-0">Tidak Tuntas</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['rata_rata'] }}</h3>
                            <p class="text-muted mb-0">Rata-rata</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <form action="{{ route('guru.nilai.akhir') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="fas fa-door-open me-1"></i>Filter Kelas
                                    </label>
                                    <select name="kelas_id" class="form-select">
                                        <option value="">Semua Kelas</option>
                                        @foreach($kelasList as $k)
                                            <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                @if($guru->isGuruMapel())
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="fas fa-book me-1"></i>Filter Mata Pelajaran
                                        </label>
                                        <select name="mapel_id" class="form-select">
                                            <option value="">Semua Mapel</option>
                                            @foreach($mapelList as $m)
                                                <option value="{{ $m->id }}" {{ $mapelId == $m->id ? 'selected' : '' }}>
                                                    {{ $m->nama_matapelajaran }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-filter me-1"></i> Filter
                                        </button>
                                        <a href="{{ route('guru.nilai.akhir') }}" class="btn btn-secondary w-100">
                                            <i class="fas fa-redo me-1"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        @if($nilaiAkhirList->count() > 0)
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-white">
                                    <i class="fas fa-list me-2"></i>Daftar Nilai Akhir
                                </h5>
                                <span class="badge bg-light text-dark">
                                    Total: {{ $nilaiAkhirList->total() }} Data
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="12%">NIS</th>
                                            <th width="18%">Nama Siswa</th>
                                            <th width="10%">Kelas</th>
                                            <th width="15%">Mata Pelajaran</th>
                                            <th width="8%" class="text-center">Tugas</th>
                                            <th width="8%" class="text-center">UTS</th>
                                            <th width="8%" class="text-center">UAS</th>
                                            <th width="8%" class="text-center">Akhir</th>
                                            <th width="8%" class="text-center">Predikat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($nilaiAkhirList as $index => $nilai)
                                            <tr>
                                                <td>{{ $nilaiAkhirList->firstItem() + $index }}</td>
                                                <td>
                                                    <strong class="text-primary">{{ $nilai->siswa->nis }}</strong>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-circle me-2">
                                                            {{ substr($nilai->siswa->nama, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <strong>{{ $nilai->siswa->nama }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <span
                                                                    class="badge bg-{{ $nilai->isTuntas() ? 'success' : 'danger' }} badge-sm">
                                                                    {{ $nilai->status }}
                                                                </span>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-door-open me-1"></i>
                                                        {{ $nilai->kelas->nama }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="fas fa-book text-primary me-1"></i>
                                                    <small>{{ $nilai->mapel->nama_matapelajaran }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">
                                                        {{ number_format($nilai->nilai_tugas, 0) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">
                                                        {{ number_format($nilai->nilai_uts, 0) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">
                                                        {{ number_format($nilai->nilai_uas, 0) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <strong class="text-danger" style="font-size: 1.1rem;">
                                                        {{ number_format($nilai->nilai_akhir, 2) }}
                                                    </strong>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $nilai->predikat_color }}"
                                                        style="font-size: 1rem; padding: 0.5rem 0.75rem;">
                                                        {{ $nilai->predikat }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted">
                                    Menampilkan {{ $nilaiAkhirList->firstItem() ?? 0 }} - {{ $nilaiAkhirList->lastItem() ?? 0 }}
                                    dari {{ $nilaiAkhirList->total() }} data
                                </div>
                                <div>
                                    {{ $nilaiAkhirList->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart Section --}}
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <h5 class="mb-0 text-white">
                                <i class="fas fa-chart-bar me-2"></i>Distribusi Predikat
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                @php
                                    $predikatCount = $nilaiAkhirList->groupBy('predikat')->map->count();
                                @endphp
                                <div class="col-md-2">
                                    <div class="predikat-box">
                                        <div class="predikat-badge bg-success">A</div>
                                        <h3>{{ $predikatCount->get('A', 0) }}</h3>
                                        <small class="text-muted">Sangat Baik</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="predikat-box">
                                        <div class="predikat-badge bg-primary">B</div>
                                        <h3>{{ $predikatCount->get('B', 0) }}</h3>
                                        <small class="text-muted">Baik</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="predikat-box">
                                        <div class="predikat-badge bg-warning">C</div>
                                        <h3>{{ $predikatCount->get('C', 0) }}</h3>
                                        <small class="text-muted">Cukup</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="predikat-box">
                                        <div class="predikat-badge bg-danger">D</div>
                                        <h3>{{ $predikatCount->get('D', 0) }}</h3>
                                        <small class="text-muted">Kurang</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="predikat-box">
                                        <div class="predikat-badge bg-dark">E</div>
                                        <h3>{{ $predikatCount->get('E', 0) }}</h3>
                                        <small class="text-muted">Sangat Kurang</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="predikat-box">
                                        <div class="predikat-badge bg-info">
                                            <i class="fas fa-percentage"></i>
                                        </div>
                                        <h3>{{ $stats['total'] > 0 ? number_format(($stats['tuntas'] / $stats['total']) * 100, 1) : 0 }}%
                                        </h3>
                                        <small class="text-muted">Ketuntasan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="text-center py-5">
                                <i class="fas fa-trophy fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada data nilai akhir</h5>
                                <p class="text-muted">
                                    Nilai akhir akan muncul setelah semua komponen nilai (Tugas, UTS, UAS) diinput.
                                </p>
                                @if($guru->isGuruMapel())
                                    <a href="{{ route('guru.nilai.select-kelas-mapel') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-1"></i> Input Nilai Sekarang
                                    </a>
                                @endif
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

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
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
            background-color: rgba(240, 147, 251, 0.05);
        }

        .avatar-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        .badge-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .predikat-box {
            padding: 1.5rem;
        }

        .predikat-badge {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }

        .predikat-box h3 {
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Optional: Add chart visualization using Chart.js if needed
    </script>
@endpush
