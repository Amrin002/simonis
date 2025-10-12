@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Detail Data Kelas</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.kelas.index') }}">Data Kelas</a></li>
                        <li class="breadcrumb-item active">Detail Kelas</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Info Card -->
            <div class="col-lg-4">
                <div class="card card-custom mb-3">
                    <div class="card-body text-center">
                        <div class="mb-3" style="font-size: 4rem; color: #007bff;">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <h3 class="mb-1">{{ $kelas->nama }}</h3>
                        <p class="text-muted mb-3">Kelas</p>

                        <div class="mb-3">
                            @if($kelas->waliKelas && $kelas->siswas->count() > 0)
                                <span class="badge bg-success mb-1">
                                    <i class="fas fa-check-circle me-1"></i>Lengkap
                                </span>
                            @elseif($kelas->waliKelas || $kelas->siswas->count() > 0)
                                <span class="badge bg-warning text-dark mb-1">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Belum Lengkap
                                </span>
                            @else
                                <span class="badge bg-secondary mb-1">
                                    <i class="fas fa-info-circle me-1"></i>Baru Dibuat
                                </span>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.kelas.edit', $kelas->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit Data
                            </a>
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash me-1"></i> Hapus Kelas
                            </button>
                            <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>

                        <!-- Form Delete (Hidden) -->
                        <form id="delete-form" action="{{ route('admin.kelas.destroy', $kelas->id) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>

                <!-- Statistik -->
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Statistik Kelas</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-users text-primary me-2"></i>
                                    <strong>Jumlah Siswa</strong>
                                </div>
                                <h4 class="mb-0 text-primary">{{ $kelas->siswas->count() }}</h4>
                            </div>
                        </div>

                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-book text-info me-2"></i>
                                    <strong>Mata Pelajaran</strong>
                                </div>
                                <h4 class="mb-0 text-info">{{ $kelas->jadwals->unique('mapel_id')->count() }}</h4>
                            </div>
                        </div>

                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-calendar-alt text-warning me-2"></i>
                                    <strong>Jadwal</strong>
                                </div>
                                <h4 class="mb-0 text-warning">{{ $kelas->jadwals->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Information -->
            <div class="col-lg-8">
                <!-- Info Wali Kelas -->
                <div class="card card-custom mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Wali Kelas</h5>
                    </div>
                    <div class="card-body">
                        @if($kelas->waliKelas)
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="text-muted small">Nama Wali Kelas</label>
                                        <h6>
                                            <i class="fas fa-user text-success me-2"></i>
                                            {{ $kelas->waliKelas->nama_guru }}
                                        </h6>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="text-muted small">NIP</label>
                                        <h6>
                                            <i class="fas fa-id-badge text-primary me-2"></i>
                                            {{ $kelas->waliKelas->nip }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('admin.guru.show', $kelas->waliKelas->id) }}"
                                    class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-eye me-1"></i> Lihat Profil Guru
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Belum ada wali kelas</strong> yang ditugaskan untuk kelas ini.
                                <a href="{{ route('admin.kelas.edit', $kelas->id) }}" class="alert-link">
                                    Atur wali kelas sekarang
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Daftar Siswa -->
                <div class="card card-custom mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Daftar Siswa</h5>
                    </div>
                    <div class="card-body">
                        @if($kelas->siswas->count() > 0)
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Total <strong>{{ $kelas->siswas->count() }} siswa</strong> terdaftar di kelas ini
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>NIS</th>
                                            <th>Nama Siswa</th>
                                            <th>Orang Tua</th>
                                            <th width="10%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kelas->siswas as $index => $siswa)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $siswa->nis }}</strong></td>
                                                <td>
                                                    <i class="fas fa-user-graduate text-primary me-2"></i>
                                                    {{ $siswa->nama }}
                                                </td>
                                                <td>
                                                    @if($siswa->orangTua)
                                                        {{ $siswa->orangTua->nama_orang_tua }}
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-phone"></i> {{ $siswa->orangTua->nomor_tlp }}
                                                        </small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.siswa.show', $siswa->id) }}"
                                                        class="btn btn-info btn-sm" title="Lihat Detail Siswa">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Belum ada siswa</strong> yang terdaftar di kelas ini.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Jadwal Pelajaran -->
                <div class="card card-custom">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Jadwal Pelajaran</h5>
                    </div>
                    <div class="card-body">
                        @if($kelas->jadwals->count() > 0)
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Total <strong>{{ $kelas->jadwals->count() }} jadwal</strong> pelajaran
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Hari</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Guru Pengajar</th>
                                            <th>Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                        @endphp
                                        @foreach($days as $day)
                                            @php
                                                $jadwalsPerDay = $kelas->jadwals->where('hari', $day)->sortBy('waktu_mulai');
                                            @endphp
                                            @if($jadwalsPerDay->count() > 0)
                                                @foreach($jadwalsPerDay as $index => $jadwal)
                                                    <tr>
                                                        @if($index === 0)
                                                            <td rowspan="{{ $jadwalsPerDay->count() }}" class="align-middle">
                                                                <strong>{{ $day }}</strong>
                                                            </td>
                                                        @endif
                                                        <td>{{ $jadwal->mapel->nama_matapelajaran }}</td>
                                                        <td>
                                                            @php
                                                                $guruMapel = $jadwal->mapel->guruMapels->where('guru_id', $jadwal->guru_id)->first();
                                                                $guru = $guruMapel ? $guruMapel->guru : null;
                                                            @endphp
                                                            @if($guru)
                                                                {{ $guru->nama_guru }}
                                                            @else
                                                                <span class="text-muted">Belum ditentukan</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ date('H:i', strtotime($jadwal->waktu_mulai)) }} -
                                                            {{ date('H:i', strtotime($jadwal->waktu_selesai)) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Belum ada jadwal</strong> yang dibuat untuk kelas ini.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete() {
                @if($kelas->siswas->count() > 0)
                    alert('Tidak dapat menghapus kelas yang masih memiliki siswa!\n\nSilakan pindahkan atau hapus siswa terlebih dahulu.');
                @else
                    if (confirm('Apakah Anda yakin ingin menghapus kelas "{{ $kelas->nama }}"?')) {
                        document.getElementById('delete-form').submit();
                    }
                @endif
            }
        </script>
    @endpush
@endsection
