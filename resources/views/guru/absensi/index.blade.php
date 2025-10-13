@extends('layouts.main')

@section('section')
        <div class="content-wrapper">
            {{-- Header Section --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="page-title">
                                <i class="fas fa-clipboard-check me-2"></i>Daftar Absensi
                            </h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle me-1"></i> Kelola absensi siswa
                            </p>
                        </div>
                        <div>
                            {{-- ✅ PERBAIKAN: Tampilan button yang lebih baik --}}
                            @if($guru->isWaliKelas() && $guru->isGuruMapel())
                                {{-- Jika guru adalah WALI KELAS DAN GURU MAPEL --}}
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-plus me-1"></i> Input Absensi
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('guru.absensi.create-wali-kelas') }}">
                                                <i class="fas fa-users text-info me-2"></i>
                                                Absensi Harian (Wali Kelas)
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('guru.absensi.select-kelas-mapel') }}">
                                                <i class="fas fa-book text-success me-2"></i>
                                                Absensi Mata Pelajaran
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @elseif($guru->isWaliKelas())
                                {{-- Jika guru HANYA WALI KELAS --}}
                                <a href="{{ route('guru.absensi.create-wali-kelas') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Input Absensi Harian
                                </a>
                            @elseif($guru->isGuruMapel())
                                {{-- Jika guru HANYA GURU MAPEL --}}
                                <a href="{{ route('guru.absensi.select-kelas-mapel') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Input Absensi
                                </a>
                            @endif
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
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ $absensis->total() }}</h3>
                                <p class="text-muted mb-0">Total Absensi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div>
                                @php
    $absenHariIni = $absensis->filter(fn($a) => $a->tanggal->isToday())->count();
                                @endphp
                                <h3 class="mb-0">{{ $absenHariIni }}</h3>
                                <p class="text-muted mb-0">Absen Hari Ini</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon me-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ $kelasList->count() }}</h3>
                                <p class="text-muted mb-0">Kelas</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon me-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div>
                                @php
    $avgKehadiran = $absensis->avg('presentase_kehadiran') ?? 0;
                                @endphp
                                <h3 class="mb-0">{{ number_format($avgKehadiran, 1) }}%</h3>
                                <p class="text-muted mb-0">Rata-rata Kehadiran</p>
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
                            <form action="{{ route('guru.absensi.index') }}" method="GET">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">
                                            <i class="fas fa-calendar me-1"></i>Tanggal
                                        </label>
                                        <input type="date" name="tanggal" class="form-control"
                                               value="{{ $tanggal }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">
                                            <i class="fas fa-door-open me-1"></i>Kelas
                                        </label>
                                        <select name="kelas" class="form-select">
                                            <option value="">Semua Kelas</option>
                                            @foreach($kelasList as $k)
                                                <option value="{{ $k->id }}" {{ $kelas == $k->id ? 'selected' : '' }}>
                                                    {{ $k->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if($guru->isGuruMapel())
                                        <div class="col-md-3">
                                            <label class="form-label">
                                                <i class="fas fa-book me-1"></i>Mata Pelajaran
                                            </label>
                                            <select name="mapel" class="form-select">
                                                <option value="">Semua Mapel</option>
                                                @foreach($mapelList as $m)
                                                    <option value="{{ $m->nama_matapelajaran }}"
                                                            {{ $mapel == $m->nama_matapelajaran ? 'selected' : '' }}>
                                                        {{ $m->nama_matapelajaran }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="col-md-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-filter me-1"></i> Filter
                                            </button>
                                            <a href="{{ route('guru.absensi.index') }}" class="btn btn-secondary w-100">
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

            {{-- Absensi List --}}
            @if($absensis->count() > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-custom">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>Daftar Absensi
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="12%">Tanggal</th>
                                                <th width="15%">Kelas</th>
                                                <th width="20%">Mata Pelajaran</th>
                                                <th width="10%" class="text-center">Total Siswa</th>
                                                <th width="8%" class="text-center">Hadir</th>
                                                <th width="8%" class="text-center">Sakit</th>
                                                <th width="8%" class="text-center">Izin</th>
                                                <th width="8%" class="text-center">Alpa</th>
                                                <th width="10%" class="text-center">Kehadiran</th>
                                                <th width="6%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($absensis as $index => $absen)
                                                <tr>
                                                    <td>{{ $absensis->firstItem() + $index }}</td>
                                                    <td>
                                                        <strong>{{ $absen->tanggal->isoFormat('D MMM Y') }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $absen->tanggal->isoFormat('dddd') }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-door-open me-1"></i>
                                                            {{ $absen->kelas->nama }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($absen->mata_pelajaran)
                                                            <i class="fas fa-book text-primary me-1"></i>
                                                            <strong>{{ $absen->mata_pelajaran }}</strong>
                                                        @else
                                                            <span class="badge bg-secondary">
                                                                <i class="fas fa-users me-1"></i>
                                                                Absensi Harian
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary">
                                                            {{ $absen->detailAbsens->count() }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success">
                                                            {{ $absen->jumlah_hadir }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-warning text-dark">
                                                            {{ $absen->jumlah_sakit }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info">
                                                            {{ $absen->jumlah_izin }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-danger">
                                                            {{ $absen->jumlah_alpa }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="progress" style="height: 25px;">
                                                            <div class="progress-bar bg-{{ $absen->presentase_kehadiran >= 80 ? 'success' : ($absen->presentase_kehadiran >= 60 ? 'warning' : 'danger') }}"
                                                                role="progressbar" style="width: {{ $absen->presentase_kehadiran }}%">
                                                                {{ number_format($absen->presentase_kehadiran, 0) }}%
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('guru.absensi.show', $absen->id) }}" class="btn btn-sm btn-info" title="Detail">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('guru.absensi.edit', $absen->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $absen->id }})"
                                                                title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>

                                                        <form id="delete-form-{{ $absen->id }}" action="{{ route('guru.absensi.destroy', $absen->id) }}"
                                                            method="POST" class="d-none">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Pagination --}}
                                <div class="mt-3">
                                    {{ $absensis->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Empty State --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-custom">
                            <div class="card-body">
                                <div class="text-center py-5">
                                    <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada data absensi</h5>
                                    <p class="text-muted">
                                        @if($tanggal || $kelas || $mapel)
                                            Tidak ada absensi yang sesuai dengan filter
                                        @else
                                            Silakan input absensi terlebih dahulu
                                        @endif
                                    </p>
                                    @if($tanggal || $kelas || $mapel)
                                        <a href="{{ route('guru.absensi.index') }}" class="btn btn-primary mt-3">
                                            <i class="fas fa-redo me-1"></i> Reset Filter
                                        </a>
                                    @else
                                        {{-- ✅ PERBAIKAN: Empty state button yang lebih baik --}}
                                        @if($guru->isWaliKelas() && $guru->isGuruMapel())
                                            <div class="btn-group mt-3" role="group">
                                                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-plus me-1"></i> Input Absensi
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('guru.absensi.create-wali-kelas') }}">
                                                            <i class="fas fa-users text-info me-2"></i>
                                                            Absensi Harian (Wali Kelas)
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('guru.absensi.select-kelas-mapel') }}">
                                                            <i class="fas fa-book text-success me-2"></i>
                                                            Absensi Mata Pelajaran
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        @elseif($guru->isWaliKelas())
                                            <a href="{{ route('guru.absensi.create-wali-kelas') }}" class="btn btn-primary mt-3">
                                                <i class="fas fa-plus me-1"></i> Input Absensi Harian
                                            </a>
                                        @elseif($guru->isGuruMapel())
                                            <a href="{{ route('guru.absensi.select-kelas-mapel') }}" class="btn btn-primary mt-3">
                                                <i class="fas fa-plus me-1"></i> Input Absensi
                                            </a>
                                        @endif
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
            font-size: 0.75rem;
        }

        .btn-group .btn {
            padding: 0.375rem 0.5rem;
        }

        /* ✅ TAMBAHAN: Style untuk dropdown menu */
        .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: none;
            padding: 0.5rem 0;
        }

        .dropdown-item {
            padding: 0.75rem 1.5rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: rgba(102, 126, 234, 0.1);
            padding-left: 1.75rem;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Absensi?',
                text: "Data absensi akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

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
