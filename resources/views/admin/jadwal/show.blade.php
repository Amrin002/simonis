@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Detail Jadwal Pelajaran</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.jadwal.index') }}">Jadwal</a></li>
                        <li class="breadcrumb-item active">Detail Jadwal</li>
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
                            <i class="fas fa-calendar-alt"></i>
                        </div>

                        <h4 class="mb-1">{{ $jadwal->mapel->nama_matapelajaran }}</h4>
                        <p class="text-muted mb-3">{{ $jadwal->mapel->kode_mapel }}</p>

                        <div class="mb-3">
                            <span class="badge bg-{{ $jadwal->hari_badge_color }} mb-2" style="font-size: 1rem;">
                                <i class="fas fa-calendar-day me-1"></i>{{ $jadwal->hari }}
                            </span>
                            <br>
                            <span class="badge bg-primary mb-2" style="font-size: 1rem;">
                                <i class="fas fa-door-open me-1"></i>{{ $jadwal->kelas->nama }}
                            </span>
                            <br>
                            @if($jadwal->isToday())
                                <span class="badge bg-{{ $jadwal->status_badge_color }}" style="font-size: 0.9rem;">
                                    <i class="fas fa-circle me-1"></i>{{ $jadwal->status_label }}
                                </span>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.jadwal.edit', $jadwal->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit Jadwal
                            </a>
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash me-1"></i> Hapus Jadwal
                            </button>
                            <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>

                        <!-- Form Delete (Hidden) -->
                        <form id="delete-form" action="{{ route('admin.jadwal.destroy', $jadwal->id) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>

                <!-- Waktu Info -->
                <div class="card card-custom">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Informasi Waktu</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 pb-3 border-bottom">
                            <label class="text-muted small">Waktu Mulai</label>
                            <h5 class="mb-0">
                                <i class="fas fa-play-circle text-success me-2"></i>
                                {{ $jadwal->waktu_mulai_format }}
                            </h5>
                        </div>

                        <div class="mb-3 pb-3 border-bottom">
                            <label class="text-muted small">Waktu Selesai</label>
                            <h5 class="mb-0">
                                <i class="fas fa-stop-circle text-danger me-2"></i>
                                {{ $jadwal->waktu_selesai_format }}
                            </h5>
                        </div>

                        <div class="mb-0">
                            <label class="text-muted small">Durasi</label>
                            <h5 class="mb-0">
                                <i class="fas fa-hourglass-half text-warning me-2"></i>
                                {{ $jadwal->durasi_format }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Information -->
            <div class="col-lg-8">
                <!-- Info Mata Pelajaran -->
                <div class="card card-custom mb-3">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-book me-2"></i>Informasi Mata Pelajaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">Nama Mata Pelajaran</label>
                                    <h6>
                                        <i class="fas fa-book-open text-warning me-2"></i>
                                        {{ $jadwal->mapel->nama_matapelajaran }}
                                    </h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">Kode Mapel</label>
                                    <h6>
                                        <i class="fas fa-barcode text-primary me-2"></i>
                                        {{ $jadwal->mapel->kode_mapel }}
                                    </h6>
                                </div>
                            </div>
                            @if($jadwal->mapel->deskripsi)
                                <div class="col-md-12">
                                    <div class="mb-0">
                                        <label class="text-muted small">Deskripsi</label>
                                        <p class="mb-0">{{ $jadwal->mapel->deskripsi }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Info Kelas -->
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
                                        {{ $jadwal->kelas->nama }}
                                    </h6>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small">Wali Kelas</label>
                                    <h6>
                                        @if($jadwal->kelas->waliKelas)
                                            <i class="fas fa-user-tie text-success me-2"></i>
                                            {{ $jadwal->kelas->waliKelas->nama_guru }}
                                        @else
                                            <span class="text-muted">Belum ada wali kelas</span>
                                        @endif
                                    </h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">Jumlah Siswa</label>
                                    <h6>
                                        <i class="fas fa-users text-info me-2"></i>
                                        {{ $jadwal->kelas->siswas->count() }} Siswa
                                    </h6>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small">Total Jadwal Kelas</label>
                                    <h6>
                                        <i class="fas fa-calendar-week text-warning me-2"></i>
                                        {{ $jadwal->kelas->jadwals->count() }} Jadwal
                                    </h6>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('admin.kelas.show', $jadwal->kelas->id) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i> Lihat Detail Kelas
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Info Guru -->
                <div class="card card-custom mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Informasi Guru Pengajar</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">Nama Guru</label>
                                    <h6>
                                        <i class="fas fa-user text-success me-2"></i>
                                        {{ $jadwal->guru->nama_guru }}
                                    </h6>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small">NIP</label>
                                    <h6>
                                        <i class="fas fa-id-badge text-primary me-2"></i>
                                        {{ $jadwal->guru->nip }}
                                    </h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-muted small">Role</label>
                                    <h6>
                                        <span class="badge bg-info">{{ $jadwal->guru->role_label }}</span>
                                    </h6>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small">Total Jadwal Mengajar</label>
                                    <h6>
                                        <i class="fas fa-calendar-check text-warning me-2"></i>
                                        {{ $jadwal->guru->jadwals->count() }} Jadwal
                                    </h6>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-0">
                                    <label class="text-muted small">Mata Pelajaran yang Diampu</label>
                                    <div class="mt-2">
                                        @foreach($jadwal->guru->mapels as $mapel)
                                            <span class="badge bg-warning text-dark me-1 mb-1">
                                                {{ $mapel->nama_matapelajaran }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('admin.guru.show', $jadwal->guru->id) }}"
                                class="btn btn-sm btn-outline-success">
                                <i class="fas fa-eye me-1"></i> Lihat Profil Guru
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Jadwal Lain di Hari yang Sama -->
                <div class="card card-custom">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Jadwal Lain pada {{ $jadwal->hari }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $jadwalLain = \App\Models\Jadwal::with(['mapel', 'kelas', 'guru'])
                                ->where('hari', $jadwal->hari)
                                ->where('id', '!=', $jadwal->id)
                                ->orderBy('waktu_mulai')
                                ->get();
                        @endphp

                        @if($jadwalLain->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Kelas</th>
                                            <th>Guru</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($jadwalLain as $item)
                                            <tr>
                                                <td>
                                                    <strong>{{ date('H:i', strtotime($item->waktu_mulai)) }}</strong> -
                                                    {{ date('H:i', strtotime($item->waktu_selesai)) }}
                                                </td>
                                                <td>{{ $item->mapel->nama_matapelajaran }}</td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $item->kelas->nama }}</span>
                                                </td>
                                                <td>{{ $item->guru->nama_guru }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Tidak ada jadwal lain pada hari {{ $jadwal->hari }}
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
                if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?\n\nMapel: {{ $jadwal->mapel->nama_matapelajaran }}\nKelas: {{ $jadwal->kelas->nama }}\nHari: {{ $jadwal->hari }}')) {
                    document.getElementById('delete-form').submit();
                }
            }
        </script>
    @endpush
@endsection
