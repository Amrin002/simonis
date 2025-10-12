@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Detail Mata Pelajaran</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.mapel.index') }}">Mata Pelajaran</a></li>
                        <li class="breadcrumb-item active">Detail Mata Pelajaran</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Info Card -->
            <div class="col-lg-4">
                <div class="card card-custom mb-3">
                    <div class="card-body text-center">
                        <div class="mb-3" style="font-size: 4rem; color: #ffc107;">
                            <i class="fas fa-book"></i>
                        </div>

                        <h3 class="mb-1">{{ $mapel->nama_matapelajaran }}</h3>
                        @if($mapel->kode_mapel)
                            <p class="text-muted mb-3">Kode: {{ $mapel->kode_mapel }}</p>
                        @else
                            <p class="text-muted mb-3">-</p>
                        @endif

                        <div class="mb-3">
                            <span class="badge bg-{{ $mapel->status_badge_color }}" style="font-size: 1rem;">
                                {{ $mapel->status_penggunaan }}
                            </span>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.mapel.edit', $mapel->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit Mata Pelajaran
                            </a>
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash me-1"></i> Hapus Mata Pelajaran
                            </button>
                            <a href="{{ route('admin.mapel.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>

                        <!-- Form Delete (Hidden) -->
                        <form id="delete-form" action="{{ route('admin.mapel.destroy', $mapel->id) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>

                <!-- Statistik -->
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-chalkboard-teacher text-success me-2"></i>
                                    <strong>Guru Pengampu</strong>
                                </div>
                                <h4 class="mb-0 text-success">{{ $mapel->jumlah_guru }}</h4>
                            </div>
                        </div>

                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-calendar-alt text-info me-2"></i>
                                    <strong>Total Jadwal</strong>
                                </div>
                                <h4 class="mb-0 text-info">{{ $mapel->jumlah_jadwal }}</h4>
                            </div>
                        </div>

                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-door-open text-warning me-2"></i>
                                    <strong>Kelas Diajar</strong>
                                </div>
                                <h4 class="mb-0 text-warning">{{ $mapel->jumlah_kelas }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Information -->
            <div class="col-lg-8">
                <!-- Deskripsi -->
                @if($mapel->deskripsi)
                    <div class="card card-custom mb-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Deskripsi</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $mapel->deskripsi }}</p>
                        </div>
                    </div>
                @endif

                <!-- Guru Pengampu -->
                <div class="card card-custom mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Guru Pengampu</h5>
                    </div>
                    <div class="card-body">
                        @if($mapel->gurus->count() > 0)
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                Total <strong>{{ $mapel->gurus->count() }} guru</strong> mengampu mata pelajaran ini
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>NIP</th>
                                            <th>Nama Guru</th>
                                            <th>Role</th>
                                            <th width="10%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($mapel->gurus as $index => $guru)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $guru->nip }}</strong></td>
                                                <td>
                                                    <i class="fas fa-user text-success me-2"></i>
                                                    {{ $guru->nama_guru }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $guru->role_label }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.guru.show', $guru->id) }}" class="btn btn-info btn-sm"
                                                        title="Lihat Profil Guru">
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
                                <strong>Belum ada guru</strong> yang mengampu mata pelajaran ini.
                                <a href="{{ route('admin.mapel.edit', $mapel->id) }}" class="alert-link">
                                    Tambah guru pengampu
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Jadwal Pelajaran -->
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-week me-2"></i>Jadwal Pelajaran</h5>
                    </div>
                    <div class="card-body">
                        @if($mapel->jadwals->count() > 0)
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Total <strong>{{ $mapel->jadwals->count() }} jadwal</strong> untuk mata pelajaran ini
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Hari</th>
                                            <th>Waktu</th>
                                            <th>Kelas</th>
                                            <th>Guru</th>
                                            <th width="10%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($mapel->jadwals->sortBy('hari')->sortBy('waktu_mulai') as $jadwal)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-{{ $jadwal->hari_badge_color }}">
                                                        {{ $jadwal->hari }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="fas fa-clock text-info me-1"></i>
                                                    {{ date('H:i', strtotime($jadwal->waktu_mulai)) }} -
                                                    {{ date('H:i', strtotime($jadwal->waktu_selesai)) }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $jadwal->kelas->nama }}</span>
                                                </td>
                                                <td>{{ $jadwal->guru->nama_guru }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.jadwal.show', $jadwal->id) }}"
                                                        class="btn btn-info btn-sm" title="Lihat Detail Jadwal">
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
                                <strong>Belum ada jadwal</strong> untuk mata pelajaran ini.
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
                @if($mapel->jadwals->count() > 0)
                    alert('Tidak dapat menghapus mata pelajaran yang masih digunakan di jadwal!\n\nSilakan hapus jadwal terkait terlebih dahulu.');
                @else
                    if (confirm('Apakah Anda yakin ingin menghapus mata pelajaran "{{ $mapel->nama_matapelajaran }}"?')) {
                        document.getElementById('delete-form').submit();
                    }
                @endif
            }
        </script>
    @endpush
@endsection
