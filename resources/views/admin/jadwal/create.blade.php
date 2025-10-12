@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Tambah Jadwal Pelajaran</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.jadwal.index') }}">Jadwal</a></li>
                        <li class="breadcrumb-item active">Tambah Jadwal</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('admin.jadwal.store') }}" method="POST">
            @csrf

            <div class="card card-custom mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Informasi Jadwal</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Catatan:</strong> Pilih mata pelajaran terlebih dahulu, maka guru yang mengajar mapel
                        tersebut akan otomatis ditampilkan. Sistem akan mengecek bentrok jadwal untuk kelas dan guru.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kelas_id" class="form-label">
                                    Kelas <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('kelas_id') is-invalid @enderror" id="kelas_id"
                                    name="kelas_id" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @forelse($kelas as $item)
                                        <option value="{{ $item->id }}" {{ old('kelas_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama }}
                                            @if($item->waliKelas)
                                                - Wali: {{ $item->waliKelas->nama_guru }}
                                            @endif
                                        </option>
                                    @empty
                                        <option value="" disabled>Tidak ada kelas tersedia</option>
                                    @endforelse
                                </select>
                                @error('kelas_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="mapel_id" class="form-label">
                                    Mata Pelajaran <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('mapel_id') is-invalid @enderror" id="mapel_id"
                                    name="mapel_id" required>
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    @forelse($mapels as $mapel)
                                        <option value="{{ $mapel->id }}" {{ old('mapel_id') == $mapel->id ? 'selected' : '' }}
                                            data-kode="{{ $mapel->kode_mapel }}"
                                            data-gurus="{{ $mapel->gurus->pluck('id')->toJson() }}">
                                            {{ $mapel->nama_matapelajaran }}
                                            @if($mapel->kode_mapel)
                                                ({{ $mapel->kode_mapel }})
                                            @endif
                                            @if($mapel->gurus->count() > 0)
                                                - {{ $mapel->gurus->count() }} Guru
                                            @endif
                                        </option>
                                    @empty
                                        <option value="" disabled>Tidak ada mapel tersedia</option>
                                    @endforelse
                                </select>
                                @error('mapel_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="mapel-info">Pilih mata pelajaran untuk melihat guru
                                    pengampu</small>
                            </div>

                            <div class="mb-3">
                                <label for="guru_id" class="form-label">
                                    Guru Pengajar <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('guru_id') is-invalid @enderror" id="guru_id"
                                    name="guru_id" required>
                                    <option value="">-- Pilih Mata Pelajaran Dahulu --</option>
                                </select>
                                @error('guru_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="guru-info">Guru akan terfilter berdasarkan mata pelajaran yang
                                    dipilih</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hari" class="form-label">
                                    Hari <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('hari') is-invalid @enderror" id="hari" name="hari"
                                    required>
                                    <option value="">-- Pilih Hari --</option>
                                    <option value="Senin" {{ old('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                                    <option value="Selasa" {{ old('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                                    <option value="Rabu" {{ old('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                                    <option value="Kamis" {{ old('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                                    <option value="Jumat" {{ old('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                                    <option value="Sabtu" {{ old('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                                </select>
                                @error('hari')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="waktu_mulai" class="form-label">
                                    Waktu Mulai <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    <input type="time" class="form-control @error('waktu_mulai') is-invalid @enderror"
                                        id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai') }}" required>
                                </div>
                                @error('waktu_mulai')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="waktu_selesai" class="form-label">
                                    Waktu Selesai <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    <input type="time" class="form-control @error('waktu_selesai') is-invalid @enderror"
                                        id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai') }}" required>
                                </div>
                                @error('waktu_selesai')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Waktu selesai harus lebih besar dari waktu mulai</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Jadwal
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // Data semua guru
            const allGurus = @json($gurus);

            // Element
            const mapelSelect = document.getElementById('mapel_id');
            const guruSelect = document.getElementById('guru_id');
            const mapelInfo = document.getElementById('mapel-info');
            const guruInfo = document.getElementById('guru-info');

            // Function untuk update guru dropdown
            function updateGuruDropdown() {
                const selectedOption = mapelSelect.options[mapelSelect.selectedIndex];

                // Reset guru dropdown
                guruSelect.innerHTML = '<option value="">-- Pilih Guru --</option>';

                if (!selectedOption.value) {
                    guruSelect.innerHTML = '<option value="">-- Pilih Mata Pelajaran Dahulu --</option>';
                    mapelInfo.innerHTML = 'Pilih mata pelajaran untuk melihat guru pengampu';
                    guruInfo.innerHTML = 'Guru akan terfilter berdasarkan mata pelajaran yang dipilih';
                    return;
                }

                // Get guru IDs yang mengajar mapel ini
                const guruIds = JSON.parse(selectedOption.getAttribute('data-gurus') || '[]');

                if (guruIds.length === 0) {
                    guruSelect.innerHTML = '<option value="">-- Tidak Ada Guru Untuk Mapel Ini --</option>';
                    mapelInfo.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Belum ada guru yang mengampu mata pelajaran ini</span>';
                    guruInfo.innerHTML = '<span class="text-danger">Silakan tambah guru pengampu di menu Mata Pelajaran</span>';
                    return;
                }

                // Filter dan tampilkan guru yang mengajar mapel ini
                const filteredGurus = allGurus.filter(guru => guruIds.includes(guru.id));

                filteredGurus.forEach(guru => {
                    const option = document.createElement('option');
                    option.value = guru.id;
                    option.textContent = `${guru.nama_guru} - ${guru.nip}`;

                    // Cek jika ini old value
                    if ("{{ old('guru_id') }}" == guru.id) {
                        option.selected = true;
                    }

                    guruSelect.appendChild(option);
                });

                // Update info
                mapelInfo.innerHTML = `<span class="text-success"><i class="fas fa-check-circle"></i> ${filteredGurus.length} guru mengampu mata pelajaran ini</span>`;
                guruInfo.innerHTML = `<span class="text-info">Menampilkan ${filteredGurus.length} guru pengampu</span>`;
            }

            // Event listener
            mapelSelect.addEventListener('change', updateGuruDropdown);

            // Trigger on page load jika ada old value
            @if(old('mapel_id'))
                updateGuruDropdown();
            @endif
        </script>
    @endpush
@endsection
