@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-chart-line me-2"></i>Daftar Nilai Siswa
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar-day me-1"></i> {{ now()->isoFormat('dddd, D MMMM YYYY') }}
                        </p>
                    </div>
                    <div>
                        @if($guru->isGuruMapel())
                            <a href="{{ route('guru.nilai.select-kelas-mapel') }}" class="btn btn-success me-2">
                                <i class="fas fa-plus me-1"></i> Input Nilai
                            </a>
                        @endif
                        <a href="{{ route('guru.nilai.akhir') }}" class="btn btn-primary me-2">
                            <i class="fas fa-trophy me-1"></i> Nilai Akhir
                        </a>
                        {{-- <a href="{{ route('guru.nilai.export-pdf') }}" class="btn btn-danger me-2">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                        </a> --}}
                        <a href="#" class="btn btn-secondary" target="_blank">
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
                            <h3 class="mb-0">{{ $nilaiList->total() }}</h3>
                            <p class="text-muted mb-0">Total Nilai</p>
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
                            <h3 class="mb-0">{{ $nilaiList->where('nilai_akhir', '>=', 75)->count() }}</h3>
                            <p class="text-muted mb-0">Tuntas</p>
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
                            <h3 class="mb-0">{{ $nilaiList->where('nilai_akhir', '<', 75)->count() }}</h3>
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
                            <h3 class="mb-0">
                                {{ $nilaiList->count() > 0 ? number_format($nilaiList->avg('nilai_akhir'), 2) : '0.00' }}
                            </h3>
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
                        <form action="{{ route('guru.nilai.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-3">
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
                                    <div class="col-md-3">
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

                                <div class="col-md-3">
                                    <label class="form-label">
                                        <i class="fas fa-search me-1"></i>Cari Siswa
                                    </label>
                                    <input type="text" name="search" class="form-control" placeholder="Nama / NIS siswa..."
                                        value="{{ $search ?? '' }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-filter me-1"></i> Filter
                                        </button>
                                        <a href="{{ route('guru.nilai.index') }}" class="btn btn-secondary w-100">
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

        {{-- Table Section --}}
        @if($nilaiList->count() > 0)
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-white">
                                    <i class="fas fa-list me-2"></i>Daftar Nilai Siswa
                                </h5>
                                <span class="badge bg-light text-dark">
                                    Total: {{ $nilaiList->total() }} Nilai
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="15%">NIS</th>
                                            <th width="20%">Nama Siswa</th>
                                            <th width="10%">Kelas</th>
                                            <th width="15%">Mata Pelajaran</th>
                                            <th width="8%" class="text-center">Tugas</th>
                                            <th width="8%" class="text-center">UTS</th>
                                            <th width="8%" class="text-center">UAS</th>
                                            <th width="8%" class="text-center">Akhir</th>
                                            <th width="8%" class="text-center">Predikat</th>
                                            <th width="10%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($nilaiList as $index => $nilai)
                                            <tr>
                                                <td>{{ $nilaiList->firstItem() + $index }}</td>
                                                <td>
                                                    <strong class="text-primary">{{ $nilai->siswa->nis }}</strong>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-circle me-2">
                                                            {{ substr($nilai->siswa->nama, 0, 1) }}
                                                        </div>
                                                        <strong>{{ $nilai->siswa->nama }}</strong>
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
                                                    {{ $nilai->mapel->nama_matapelajaran }}
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
                                                    <strong class="text-primary">
                                                        {{ number_format($nilai->nilai_akhir, 2) }}
                                                    </strong>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $nilai->predikat_color }}">
                                                        {{ $nilai->predikat }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('guru.nilai.show', [$nilai->siswa_id, $nilai->mapel_id]) }}"
                                                        class="btn btn-sm btn-info" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted">
                                    Menampilkan {{ $nilaiList->firstItem() ?? 0 }} - {{ $nilaiList->lastItem() ?? 0 }}
                                    dari {{ $nilaiList->total() }} data
                                </div>
                                <div>
                                    {{ $nilaiList->links() }}
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
                                <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada data nilai</h5>
                                <p class="text-muted">
                                    @if($search || $kelasId || $mapelId)
                                        Tidak ada nilai yang sesuai dengan filter pencarian.
                                    @else
                                        Mulai input nilai siswa untuk melihat data di sini.
                                    @endif
                                </p>
                                @if($search || $kelasId || $mapelId)
                                    <a href="{{ route('guru.nilai.index') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-redo me-1"></i> Reset Filter
                                    </a>
                                @elseif($guru->isGuruMapel())
                                    <a href="{{ route('guru.nilai.select-kelas-mapel') }}" class="btn btn-success mt-3">
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
            background-color: rgba(102, 126, 234, 0.05);
        }

        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
        }

        .avatar-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .alert {
            border-radius: 12px;
            border: none;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(function () {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
@endpush
