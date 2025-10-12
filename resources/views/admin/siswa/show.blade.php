@extends('template.main')

@section('section')
<div class="content-wrapper">
    <div class="row mb-3">
        <div class="col-md-12">
            <h1 class="page-title">Detail Data Siswa</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.siswa.index') }}">Data Siswa</a></li>
                    <li class="breadcrumb-item active">Detail Siswa</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="card card-custom mb-3">
                <div class="card-body text-center">
                    <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem; background: #28a745;">
                        {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                    </div>
                    <h4 class="mb-1">{{ $siswa->nama }}</h4>
                    <p class="text-muted mb-3">NIS: {{ $siswa->nis }}</p>

                    <div class="mb-3">
                        @if($siswa->kelas)
                            <span class="badge bg-primary mb-1">
                                <i class="fas fa-door-open me-1"></i>{{ $siswa->kelas->nama }}
                            </span>
                        @else
                            <span class="badge bg-warning text-dark mb-1">
                                <i class="fas fa-exclamation-circle me-1"></i>Belum Ada Kelas
                            </span>
                        @endif
                        <br>
                        @if($siswa->kelas && $siswa->orangTua)
                            <span class="badge bg-success mt-1">
                                <i class="fas fa-check-circle me-1"></i>Data Lengkap
                            </span>
                        @else
                            <span class="badge bg-warning text-dark mt-1">
                                <i class="fas fa-exclamation-triangle me-1"></i>Belum Lengkap
                            </span>
                        @endif
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.siswa.edit', $siswa->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit Data
                        </a>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-1"></i> Hapus Siswa
                        </button>
                        <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>

                    <!-- Form Delete (Hidden) -->
                    <form id="delete-form"
                          action="{{ route('admin.siswa.destroy', $siswa->id) }}"
                          method="POST"
                          style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card card-custom">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Ringkasan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-door-open text-primary me-2"></i>
                                <strong>Kelas</strong>
                            </div>
                            <div class="text-end">
                                @if($siswa->kelas)
                                    <strong class="text-primary">{{ $siswa->kelas->nama }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-user-tie text-success me-2"></i>
                                <strong>Wali Kelas</strong>
                            </div>
                            <div class="text-end">
                                @if($siswa->kelas && $siswa->kelas->waliKelas)
                                    <small class="text-success">{{ $siswa->kelas->waliKelas->nama_guru }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-calendar-plus text-info me-2"></i>
                                <strong>Terdaftar</strong>
                            </div>
                            <div class="text-end">
                                <small>{{ $siswa->created_at->format('d M Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Information -->
        <div class="col-lg-8">
            <!-- Info Kelas -->
            @if($siswa->kelas)
            <div class="card card-custom mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-door-open me-2"></i>Informasi Kelas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Nama Kelas</label>
                                <h6>
                                    <i class="fas fa-graduation-cap text-primary me-2"></i>
                                    {{ $siswa->kelas->nama }}
                                </h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Wali Kelas</label>
                                <h6>
                                    @if($siswa->kelas->waliKelas)
                                        <i class="fas fa-user-tie text-success me-2"></i>
                                        {{ $siswa->kelas->waliKelas->nama_guru }}
                                    @else
                                        <span class="text-muted">Belum ada wali kelas</span>
                                    @endif
                                </h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-0">
                                <label class="text-muted small">Jumlah Siswa di Kelas</label>
                                <h6>
                                    <i class="fas fa-users text-info me-2"></i>
                                    {{ $siswa->kelas->siswas->count() }} Siswa
                                </h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-0">
                                <label class="text-muted small">Mata Pelajaran</label>
                                <h6>
                                    <i class="fas fa-book text-warning me-2"></i>
                                    {{ $siswa->kelas->jadwals->unique('mapel_id')->count() }} Mapel
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.kelas.show', $siswa->kelas->id) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> Lihat Detail Kelas
                        </a>
                    </div>
                </div>
            </div>
            @else
            <div class="card card-custom mb-3">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Informasi Kelas</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Siswa ini belum memiliki kelas.</strong>
                        <a href="{{ route('admin.siswa.edit', $siswa->id) }}" class="alert-link">
                            Atur kelas sekarang
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Info Orang Tua -->
            <div class="card card-custom mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Informasi Orang Tua</h5>
                </div>
                <div class="card-body">
                    @if($siswa->orangTua)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Nama Orang Tua</label>
                                <h6>
                                    <i class="fas fa-user text-info me-2"></i>
                                    {{ $siswa->orangTua->nama_orang_tua }}
                                </h6>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small">Nomor Telepon</label>
                                <h6>
                                    <i class="fas fa-phone text-success me-2"></i>
                                    <a href="tel:{{ $siswa->orangTua->nomor_tlp }}" class="text-decoration-none">
                                        {{ $siswa->orangTua->nomor_tlp }}
                                    </a>
                                </h6>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">Alamat Lengkap</label>
                                <p class="mb-0">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    {{ $siswa->orangTua->alamat }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-0">
                                <label class="text-muted small">Anak Lainnya</label>
                                @php
                                    $siblings = $siswa->orangTua->siswas->where('id', '!=', $siswa->id);
                                @endphp
                                @if($siblings->count() > 0)
                                <div class="mt-2">
                                    @foreach($siblings as $sibling)
                                    <span class="badge bg-secondary me-1">
                                        {{ $sibling->nama }} ({{ $sibling->kelas ? $sibling->kelas->nama : 'Belum ada kelas' }})
                                    </span>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-muted mb-0">Tidak ada anak lain</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.orangtua.show', $siswa->orangTua->id) }}"
                           class="btn btn-sm btn-outline-info">
                            <i class="fas fa-eye me-1"></i> Lihat Profil Orang Tua
                        </a>
                    </div>
                    @else
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Data orang tua tidak ditemukan!</strong> Ini tidak seharusnya terjadi.
                        <a href="{{ route('admin.siswa.edit', $siswa->id) }}" class="alert-link">
                            Edit data siswa
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Riwayat Akademik (Placeholder) -->
            <div class="card card-custom">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Riwayat Akademik</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Fitur riwayat akademik (nilai, absensi, pelanggaran) akan tersedia setelah modul terkait dibuat.
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <i class="fas fa-clipboard-list fa-2x text-primary mb-2"></i>
                                <h6 class="mb-0">Nilai</h6>
                                <small class="text-muted">Belum tersedia</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <i class="fas fa-calendar-check fa-2x text-success mb-2"></i>
                                <h6 class="mb-0">Absensi</h6>
                                <small class="text-muted">Belum tersedia</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                                <h6 class="mb-0">Pelanggaran</h6>
                                <small class="text-muted">Belum tersedia</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('Apakah Anda yakin ingin menghapus siswa "{{ $siswa->nama }}"?\n\nSemua data terkait siswa ini akan ikut terhapus.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection
