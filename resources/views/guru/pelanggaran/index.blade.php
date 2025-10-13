@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>Daftar Pelanggaran
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-door-open me-1"></i>
                            Kelas {{ $kelas->nama }} | Wali Kelas: {{ $guru->nama_guru }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.pelanggaran.create') }}" class="btn btn-primary me-2">
                            <i class="fas fa-plus me-1"></i> Tambah Pelanggaran
                        </a>
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
                            <i class="fas fa-list"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <p class="text-muted mb-0">Total Pelanggaran</p>
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
                            <h3 class="mb-0">{{ $stats['ringan'] }}</h3>
                            <p class="text-muted mb-0">Pelanggaran Ringan</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['sedang'] }}</h3>
                            <p class="text-muted mb-0">Pelanggaran Sedang</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['berat'] }}</h3>
                            <p class="text-muted mb-0">Pelanggaran Berat</p>
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
                        <form action="{{ route('guru.pelanggaran.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">
                                        <i class="fas fa-search me-1"></i>Cari Siswa
                                    </label>
                                    <input type="text" name="search" class="form-control" placeholder="Nama atau NIS..."
                                        value="{{ $search ?? '' }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">
                                        <i class="fas fa-tag me-1"></i>Kategori
                                    </label>
                                    <select name="kategori" class="form-select">
                                        <option value="">Semua Kategori</option>
                                        <option value="Ringan" {{ ($kategori ?? '') == 'Ringan' ? 'selected' : '' }}>
                                            Ringan
                                        </option>
                                        <option value="Sedang" {{ ($kategori ?? '') == 'Sedang' ? 'selected' : '' }}>
                                            Sedang
                                        </option>
                                        <option value="Berat" {{ ($kategori ?? '') == 'Berat' ? 'selected' : '' }}>
                                            Berat
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">
                                        <i class="fas fa-calendar-day me-1"></i>Tanggal
                                    </label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ $tanggal ?? '' }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">
                                        <i class="fas fa-calendar-alt me-1"></i>Bulan
                                    </label>
                                    <input type="month" name="bulan" class="form-control" value="{{ $bulan ?? '' }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-filter me-1"></i> Filter
                                        </button>
                                        @if($search || $kategori || $tanggal || $bulan)
                                            <a href="{{ route('guru.pelanggaran.index') }}" class="btn btn-secondary">
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

        {{-- Pelanggaran Table --}}
        @if($pelanggarans->count() > 0)
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-white">
                                    <i class="fas fa-list me-2"></i>Daftar Pelanggaran
                                </h5>
                                <span class="badge bg-light text-dark">
                                    {{ $pelanggarans->total() }} Pelanggaran
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="12%">Tanggal</th>
                                            <th width="20%">Siswa</th>
                                            <th width="23%">Jenis Pelanggaran</th>
                                            <th width="12%" class="text-center">Kategori</th>
                                            <th width="18%">Keterangan</th>
                                            <th width="10%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pelanggarans as $index => $pelanggaran)
                                            <tr>
                                                <td>{{ $pelanggarans->firstItem() + $index }}</td>
                                                <td>
                                                    <strong class="text-primary">
                                                        {{ $pelanggaran->tanggal_format }}
                                                    </strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $pelanggaran->tanggal->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-circle me-2">
                                                            {{ strtoupper(substr($pelanggaran->siswa->nama, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <strong>{{ $pelanggaran->siswa->nama }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fas fa-id-card me-1"></i>
                                                                {{ $pelanggaran->siswa->nis }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong>{{ $pelanggaran->jenis_pelanggaran }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $pelanggaran->badge_color }} px-3 py-2">
                                                        {{ $pelanggaran->kategori }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($pelanggaran->keterangan)
                                                        <small class="text-muted">
                                                            {{ Str::limit($pelanggaran->keterangan, 50) }}
                                                        </small>
                                                    @else
                                                        <small class="text-muted">-</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('guru.pelanggaran.show', $pelanggaran->id) }}"
                                                            class="btn btn-sm btn-info" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('guru.pelanggaran.edit', $pelanggaran->id) }}"
                                                            class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="confirmDelete({{ $pelanggaran->id }})" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>

                                                    <form id="delete-form-{{ $pelanggaran->id }}"
                                                        action="{{ route('guru.pelanggaran.destroy', $pelanggaran->id) }}"
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
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <small class="text-muted">
                                        Menampilkan {{ $pelanggarans->firstItem() }} - {{ $pelanggarans->lastItem() }}
                                        dari {{ $pelanggarans->total() }} pelanggaran
                                    </small>
                                </div>
                                <div>
                                    {{ $pelanggarans->appends(request()->except('page'))->links() }}
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
                                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                <h5 class="text-muted">Tidak ada pelanggaran ditemukan</h5>
                                <p class="text-muted">
                                    @if($search || $kategori || $tanggal || $bulan)
                                        Tidak ada pelanggaran yang sesuai dengan filter
                                    @else
                                        Belum ada catatan pelanggaran untuk kelas ini
                                    @endif
                                </p>
                                @if($search || $kategori || $tanggal || $bulan)
                                    <a href="{{ route('guru.pelanggaran.index') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-redo me-1"></i> Reset Filter
                                    </a>
                                @else
                                    <a href="{{ route('guru.pelanggaran.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-1"></i> Tambah Pelanggaran
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
            flex-shrink: 0;
        }

        .btn-group .btn {
            border-radius: 0;
        }

        .btn-group .btn:first-child {
            border-top-left-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }

        .btn-group .btn:last-child {
            border-top-right-radius: 0.25rem;
            border-bottom-right-radius: 0.25rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Pelanggaran?',
                text: "Data pelanggaran akan dihapus permanen!",
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

        // Show success/error message
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
                title: 'Gagal!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
@endpush
