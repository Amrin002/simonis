@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Detail Data Guru</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.guru.index') }}">Data Guru</a></li>
                        <li class="breadcrumb-item active">Detail Guru</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Profile Card -->
            <div class="col-lg-4">
                <div class="card card-custom mb-3">
                    <div class="card-body text-center">
                        <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            {{ strtoupper(substr($guru->nama_guru, 0, 1)) }}
                        </div>
                        <h4 class="mb-1">{{ $guru->nama_guru }}</h4>
                        <p class="text-muted mb-3">NIP: {{ $guru->nip }}</p>

                        <div class="mb-3">
                            @if($guru->is_wali_kelas && $guru->is_guru_mapel)
                                <span class="badge bg-success mb-1">
                                    <i class="fas fa-user-tie me-1"></i>Wali Kelas
                                </span><br>
                                <span class="badge bg-info">
                                    <i class="fas fa-book me-1"></i>Guru Mapel
                                </span>
                            @elseif($guru->is_wali_kelas)
                                <span class="badge bg-success">
                                    <i class="fas fa-user-tie me-1"></i>Wali Kelas
                                </span>
                            @elseif($guru->is_guru_mapel)
                                <span class="badge bg-info">
                                    <i class="fas fa-book me-1"></i>Guru Mapel
                                </span>
                            @else
                                <span class="badge bg-secondary">Guru</span>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.guru.edit', $guru->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit Data
                            </a>
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash me-1"></i> Hapus Guru
                            </button>
                            <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>

                        <!-- Form Delete (Hidden) -->
                        <form id="delete-form" action="{{ route('admin.guru.destroy', $guru->id) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>

                <!-- Info Akun -->
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-user-circle me-2"></i>Informasi Akun</h6>
                    </div>
                    <div class="card-body">
                        @if($guru->user)
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Sudah Memiliki Akun</strong>
                            </div>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="40%"><i class="fas fa-user text-muted me-2"></i>Username</td>
                                    <td><strong>{{ $guru->user->username }}</strong></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-shield-alt text-muted me-2"></i>Role</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($guru->user->role) }}</span></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-plus text-muted me-2"></i>Dibuat</td>
                                    <td><small>{{ $guru->user->created_at->format('d M Y') }}</small></td>
                                </tr>
                            </table>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Belum Memiliki Akun</strong>
                                <p class="mb-0 small mt-2">Akun login dapat dibuat melalui menu Kelola User</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detail Information -->
            <div class="col-lg-8">
                <!-- Wali Kelas Info -->
                @if($guru->is_wali_kelas)
                    <div class="card card-custom mb-3">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Informasi Wali Kelas</h5>
                        </div>
                        <div class="card-body">
                            @if($guru->kelasWali)
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Wali Kelas: {{ $guru->kelasWali->nama }}</strong>
                                </div>

                                <h6 class="mb-3">Daftar Siswa di Kelas {{ $guru->kelasWali->nama }}</h6>

                                @if($guru->kelasWali->siswas->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th>NIS</th>
                                                    <th>Nama Siswa</th>
                                                    <th>Orang Tua</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($guru->kelasWali->siswas as $index => $siswa)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td><strong>{{ $siswa->nis }}</strong></td>
                                                        <td>{{ $siswa->nama }}</td>
                                                        <td>{{ $siswa->orangTua->nama ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-end mt-2">
                                        <strong>Total Siswa: {{ $guru->kelasWali->siswas->count() }} siswa</strong>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Belum ada siswa di kelas ini
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Belum ditugaskan ke kelas manapun
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Guru Mapel Info -->
                @if($guru->is_guru_mapel)
                    <div class="card card-custom mb-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-book-open me-2"></i>Informasi Guru Mata Pelajaran</h5>
                        </div>
                        <div class="card-body">
                            @if($guru->mapels->count() > 0)
                                <h6 class="mb-3">Mata Pelajaran yang Diampu</h6>
                                <div class="row">
                                    @foreach($guru->mapels as $mapel)
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-info">
                                                <div class="card-body">
                                                    <h6 class="card-title text-info">
                                                        <i class="fas fa-book me-2"></i>{{ $mapel->nama_matapelajaran }}
                                                    </h6>
                                                    <p class="card-text mb-1">
                                                        <small><strong>Kode:</strong> {{ $mapel->kode_mapel }}</small>
                                                    </p>
                                                    @if($mapel->deskripsi)
                                                        <p class="card-text">
                                                            <small class="text-muted">{{ $mapel->deskripsi }}</small>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Total mengajar: {{ $guru->mapels->count() }} mata pelajaran</strong>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Belum mengajar mata pelajaran apapun
                                </div>
                            @endif

                            @if($guru->jadwals->count() > 0)
                                <hr>
                                <h6 class="mb-3">Jadwal Mengajar</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Hari</th>
                                                <th>Mata Pelajaran</th>
                                                <th>Kelas</th>
                                                <th>Waktu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($guru->jadwals as $jadwal)
                                                <tr>
                                                    <td><strong>{{ $jadwal->hari }}</strong></td>
                                                    <td>{{ $jadwal->mapel->nama_matapelajaran }}</td>
                                                    <td><span class="badge bg-primary">{{ $jadwal->kelas->nama }}</span></td>
                                                    <td>
                                                        {{ date('H:i', strtotime($jadwal->waktu_mulai)) }} -
                                                        {{ date('H:i', strtotime($jadwal->waktu_selesai)) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Additional Stats -->
                <div class="card card-custom">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            @if($guru->is_wali_kelas)
                                <div class="col-md-4 mb-3">
                                    <div class="stats-card border border-success">
                                        <div class="stats-icon bg-success mx-auto mb-2">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <h3 class="mb-0">{{ $guru->getJumlahSiswaKelasWali() }}</h3>
                                        <p class="text-muted mb-0">Siswa di Kelas</p>
                                    </div>
                                </div>
                            @endif

                            @if($guru->is_guru_mapel)
                                <div class="col-md-4 mb-3">
                                    <div class="stats-card border border-info">
                                        <div class="stats-icon bg-info mx-auto mb-2">
                                            <i class="fas fa-book"></i>
                                        </div>
                                        <h3 class="mb-0">{{ $guru->mapels->count() }}</h3>
                                        <p class="text-muted mb-0">Mata Pelajaran</p>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="stats-card border border-primary">
                                        <div class="stats-icon bg-primary mx-auto mb-2">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <h3 class="mb-0">{{ $guru->jadwals->count() }}</h3>
                                        <p class="text-muted mb-0">Jadwal Mengajar</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete() {
                if (confirm('Apakah Anda yakin ingin menghapus guru "{{ $guru->nama_guru }}"?\n\nSemua data terkait akan dilepas dari guru ini.')) {
                    document.getElementById('delete-form').submit();
                }
            }
        </script>
    @endpush
@endsection
