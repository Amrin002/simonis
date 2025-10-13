@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-file-alt me-2"></i>Detail Pelanggaran
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Informasi lengkap data pelanggaran
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.pelanggaran.edit', $pelanggaran->id) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <button type="button" class="btn btn-danger me-2" onclick="confirmDelete()">
                            <i class="fas fa-trash me-1"></i> Hapus
                        </button>
                        <a href="{{ route('guru.pelanggaran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Info Card --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white">
                                <i class="fas fa-exclamation-triangle me-2"></i>Informasi Pelanggaran
                            </h5>
                            <span class="badge bg-{{ $pelanggaran->badge_color }} px-3 py-2">
                                {{ $pelanggaran->kategori }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Jenis Pelanggaran --}}
                            <div class="col-md-12 mb-4">
                                <div class="info-box">
                                    <label class="info-label">
                                        <i class="fas fa-list me-2"></i>Jenis Pelanggaran
                                    </label>
                                    <h4 class="info-value text-primary">{{ $pelanggaran->jenis_pelanggaran }}</h4>
                                </div>
                            </div>

                            {{-- Tanggal --}}
                            <div class="col-md-6 mb-3">
                                <div class="info-box">
                                    <label class="info-label">
                                        <i class="fas fa-calendar me-2"></i>Tanggal Pelanggaran
                                    </label>
                                    <p class="info-value mb-1">
                                        <strong>{{ $pelanggaran->tanggal_format }}</strong>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $pelanggaran->tanggal->diffForHumans() }}
                                    </small>
                                </div>
                            </div>

                            {{-- Kategori --}}
                            <div class="col-md-6 mb-3">
                                <div class="info-box">
                                    <label class="info-label">
                                        <i class="fas fa-tag me-2"></i>Kategori Pelanggaran
                                    </label>
                                    <p class="info-value">
                                        <span class="badge bg-{{ $pelanggaran->badge_color }} px-4 py-2"
                                            style="font-size: 1rem;">
                                            {{ $pelanggaran->kategori }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            {{-- Keterangan --}}
                            <div class="col-md-12 mb-3">
                                <div class="info-box">
                                    <label class="info-label">
                                        <i class="fas fa-comment me-2"></i>Keterangan
                                    </label>
                                    @if($pelanggaran->keterangan)
                                        <p class="info-value">{{ $pelanggaran->keterangan }}</p>
                                    @else
                                        <p class="info-value text-muted">
                                            <em>Tidak ada keterangan</em>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Student & Class Info --}}
        <div class="row mb-4">
            {{-- Info Siswa --}}
            <div class="col-md-6 mb-3">
                <div class="card card-custom h-100">
                    <div class="card-header bg-gradient-primary">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-user-graduate me-2"></i>Data Siswa
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="avatar-large">
                                {{ strtoupper(substr($pelanggaran->siswa->nama, 0, 2)) }}
                            </div>
                        </div>

                        <div class="info-box mb-3">
                            <label class="info-label">
                                <i class="fas fa-user me-2"></i>Nama Lengkap
                            </label>
                            <p class="info-value">{{ $pelanggaran->siswa->nama }}</p>
                        </div>

                        <div class="info-box mb-3">
                            <label class="info-label">
                                <i class="fas fa-id-card me-2"></i>NIS
                            </label>
                            <p class="info-value">{{ $pelanggaran->siswa->nis }}</p>
                        </div>

                        <div class="info-box mb-3">
                            <label class="info-label">
                                <i class="fas fa-door-open me-2"></i>Kelas
                            </label>
                            <p class="info-value">
                                <span class="badge bg-primary">{{ $pelanggaran->siswa->nama_kelas }}</span>
                            </p>
                        </div>

                        <div class="info-box">
                            <label class="info-label">
                                <i class="fas fa-exclamation-triangle me-2"></i>Total Pelanggaran
                            </label>
                            <p class="info-value">
                                <span class="badge bg-danger">
                                    {{ $pelanggaran->siswa->jumlah_pelanggaran }} Pelanggaran
                                </span>
                            </p>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('guru.siswa.show', $pelanggaran->siswa->id) }}"
                                class="btn btn-outline-primary">
                                <i class="fas fa-eye me-1"></i> Lihat Detail Siswa
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Kelas & Pelapor --}}
            <div class="col-md-6 mb-3">
                <div class="card card-custom h-100">
                    <div class="card-header bg-gradient-success">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-info-circle me-2"></i>Informasi Lainnya
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Info Kelas --}}
                        <div class="info-box mb-4">
                            <label class="info-label">
                                <i class="fas fa-door-open me-2"></i>Kelas
                            </label>
                            <p class="info-value mb-2">
                                <strong>{{ $pelanggaran->kelas->nama }}</strong>
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-users me-1"></i>
                                {{ $pelanggaran->kelas->jumlah_siswa }} Siswa
                            </small>
                        </div>

                        {{-- Info Wali Kelas --}}
                        <div class="info-box mb-4">
                            <label class="info-label">
                                <i class="fas fa-user-tie me-2"></i>Dicatat Oleh
                            </label>
                            <p class="info-value mb-2">
                                <strong>{{ $pelanggaran->waliKelas->nama_guru }}</strong>
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-chalkboard-teacher me-1"></i>
                                Wali Kelas {{ $pelanggaran->kelas->nama }}
                            </small>
                        </div>

                        {{-- Waktu Pencatatan --}}
                        <div class="info-box">
                            <label class="info-label">
                                <i class="fas fa-clock me-2"></i>Waktu Pencatatan
                            </label>
                            <p class="info-value mb-1">
                                <strong>{{ $pelanggaran->created_at->isoFormat('dddd, D MMMM YYYY') }}</strong>
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-history me-1"></i>
                                Pukul {{ $pelanggaran->created_at->format('H:i') }} WIB
                            </small>

                            @if($pelanggaran->created_at != $pelanggaran->updated_at)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-edit me-1"></i>
                                        Terakhir diupdate: {{ $pelanggaran->updated_at->diffForHumans() }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons Bottom --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('guru.pelanggaran.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                            </a>
                            <div class="d-flex gap-2">
                                <a href="{{ route('guru.pelanggaran.create', ['siswa_id' => $pelanggaran->siswa_id]) }}"
                                    class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Tambah Pelanggaran Lagi
                                </a>
                                <a href="{{ route('guru.pelanggaran.edit', $pelanggaran->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> Edit Pelanggaran
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                    <i class="fas fa-trash me-1"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden Delete Form --}}
        <form id="delete-form" action="{{ route('guru.pelanggaran.destroy', $pelanggaran->id) }}" method="POST"
            class="d-none">
            @csrf
            @method('DELETE')
        </form>
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

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .info-box {
            padding: 1rem;
            background: #f7fafc;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .info-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #718096;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            display: block;
        }

        .info-value {
            font-size: 1rem;
            color: #2d3748;
            margin-bottom: 0;
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
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
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
        function confirmDelete() {
            Swal.fire({
                title: 'Hapus Pelanggaran?',
                html: `
                        <p>Data pelanggaran akan dihapus permanen!</p>
                        <div class="text-start mt-3">
                            <strong>Detail:</strong><br>
                            <small>Siswa: {{ $pelanggaran->siswa->nama }}</small><br>
                            <small>Jenis: {{ $pelanggaran->jenis_pelanggaran }}</small><br>
                            <small>Tanggal: {{ $pelanggaran->tanggal_format }}</small>
                        </div>
                    `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
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
