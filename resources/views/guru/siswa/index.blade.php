@extends('layouts.main')

@section('section')
        <div class="content-wrapper">
            {{-- Header Section --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="page-title">
                                <i class="fas fa-users me-2"></i>Daftar Siswa {{ $kelas->nama }}
                            </h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-chalkboard-teacher me-1"></i>
                                Wali Kelas: {{ $kelas->nama_wali_kelas }}
                            </p>
                        </div>
                        <div>
                            @if($guru->isWaliKelas())
                                <a href="{{ route('guru.pelanggaran.index') }}" class="btn btn-warning me-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Daftar Pelanggaran
                                </a>
                            @endif
                            <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
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
                                <h3 class="mb-0">{{ $stats['total_siswa'] }}</h3>
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
                                <h3 class="mb-0">{{ $stats['siswa_lengkap'] }}</h3>
                                <p class="text-muted mb-0">Data Lengkap</p>
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
                                <h3 class="mb-0">{{ $stats['total_pelanggaran'] }}</h3>
                                <p class="text-muted mb-0">Total Pelanggaran</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon me-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ $stats['pelanggaran_bulan_ini'] }}</h3>
                                <p class="text-muted mb-0">Pelanggaran Bulan Ini</p>
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
                    <form action="{{ route('guru.siswa.index') }}" method="GET">
                        <div class="row g-3">
                            {{-- Filter Kelas (Untuk Guru Mapel atau Dual Role) --}}
                            @if(($guru->isGuruMapel() || ($guru->isWaliKelas() && $guru->isGuruMapel())) && $kelasList->count() > 0)
                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="fas fa-door-open me-1"></i>Filter Kelas
                                    </label>
                                    <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                                        @foreach($kelasList as $k)
                                            <option value="{{ $k->id }}" {{ ($kelasId ?? '') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama }} ({{ $k->jumlah_siswa }} siswa)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                            @else
                                    <div class="col-md-10">
                                @endif
                                    <label class="form-label">
                                        <i class="fas fa-search me-1"></i>Cari Siswa
                                    </label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Cari berdasarkan nama atau NIS..." value="{{ $search ?? '' }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search me-1"></i> Cari
                                        </button>
                                        @if($search ?? false)
                                            <a href="{{ route('guru.siswa.index', (($guru->isGuruMapel() || ($guru->isWaliKelas() && $guru->isGuruMapel())) && isset($kelasId)) ? ['kelas_id' => $kelasId] : []) }}"
                                                class="btn btn-secondary">
                                                <i class="fas fa-redo"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

            {{-- Siswa Table --}}
            @if($siswas->count() > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-custom">
                            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 text-white">
                                        <i class="fas fa-list me-2"></i>Daftar Siswa
                                    </h5>
                                    <span class="badge bg-light text-dark">
                                        {{ $siswas->total() }} Siswa
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
                                                <th width="25%">Nama Siswa</th>
                                                <th width="20%">Orang Tua</th>
                                                <th width="15%">Status Data</th>
                                                <th width="10%" class="text-center">Pelanggaran</th>
                                                <th width="10%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($siswas as $index => $siswa)
                                                <tr>
                                                    <td>{{ $siswas->firstItem() + $index }}</td>
                                                    <td>
                                                        <strong class="text-primary">{{ $siswa->nis }}</strong>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-circle me-2">
                                                                {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                                            </div>
                                                            <div>
                                                                <strong>{{ $siswa->nama }}</strong>
                                                                @if($siswa->hasSiblings())
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-users me-1"></i>
                                                                        {{ $siswa->siblings->count() }} Saudara
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($siswa->hasOrangTua())
                                                            <i class="fas fa-user-friends text-success me-1"></i>
                                                            {{ $siswa->orangTua->nama }}
                                                        @else
                                                            <span class="text-muted">
                                                                <i class="fas fa-times-circle me-1"></i>
                                                                Belum Ada
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($siswa->status_lengkap == 'Lengkap')
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle me-1"></i>
                                                                Lengkap
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                                Belum Lengkap
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($siswa->jumlah_pelanggaran > 0)
                                                            <span class="badge bg-danger">
                                                                {{ $siswa->jumlah_pelanggaran }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check"></i>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('guru.siswa.show', $siswa->id) }}" class="btn btn-sm btn-info"
                                                            title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Pagination --}}
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <small class="text-muted">
                                            Menampilkan {{ $siswas->firstItem() }} - {{ $siswas->lastItem() }}
                                            dari {{ $siswas->total() }} siswa
                                        </small>
                                    </div>
                                    <div>
                                        {{ $siswas->appends(request()->except('page'))->links() }}
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
                                    <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak ada siswa ditemukan</h5>
                                    <p class="text-muted">
                                        @if($search)
                                            Tidak ada siswa dengan kata kunci "{{ $search }}"
                                        @else
                                            Belum ada siswa terdaftar di kelas ini
                                        @endif
                                    </p>
                                    @if($search)
                                        <a href="{{ route('guru.siswa.index', $guru->isGuruMapel() ? ['kelas_id' => $kelasId] : []) }}"
                                            class="btn btn-primary mt-3">
                                            <i class="fas fa-redo me-1"></i> Reset Pencarian
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
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }
    </style>
@endpush
