@extends('template.main')

@section('section')
<div class="content-wrapper">
    <div class="row mb-3">
        <div class="col-md-12">
            <h1 class="page-title">Edit Data Siswa</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.siswa.index') }}">Data Siswa</a></li>
                    <li class="breadcrumb-item active">Edit Siswa</li>
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

    <form action="{{ route('admin.siswa.update', $siswa->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card card-custom mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Data Siswa</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Lengkap Siswa <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text"
                                       class="form-control @error('nama') is-invalid @enderror"
                                       id="nama"
                                       name="nama"
                                       value="{{ old('nama', $siswa->nama) }}"
                                       placeholder="Masukkan nama lengkap siswa"
                                       required>
                            </div>
                            @error('nama')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nis" class="form-label">
                                NIS (Nomor Induk Siswa) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                <input type="text"
                                       class="form-control @error('nis') is-invalid @enderror"
                                       id="nis"
                                       name="nis"
                                       value="{{ old('nis', $siswa->nis) }}"
                                       placeholder="Contoh: 2024001"
                                       required>
                            </div>
                            @error('nis')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="orang_tua_id" class="form-label">
                                Orang Tua <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('orang_tua_id') is-invalid @enderror"
                                    id="orang_tua_id"
                                    name="orang_tua_id"
                                    required>
                                <option value="">-- Pilih Orang Tua --</option>
                                @foreach($orangtuas as $orangtua)
                                <option value="{{ $orangtua->id }}"
                                        {{ old('orang_tua_id', $siswa->orang_tua_id) == $orangtua->id ? 'selected' : '' }}>
                                    {{ $orangtua->nama_orang_tua }} - {{ $orangtua->nomor_tlp }}
                                </option>
                                @endforeach
                            </select>
                            @error('orang_tua_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            @if($siswa->orangTua)
                            <small class="text-info">
                                <i class="fas fa-info-circle"></i> Saat ini: <strong>{{ $siswa->orangTua->nama_orang_tua }}</strong>
                            </small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">
                                Kelas <span class="text-muted">(Opsional)</span>
                            </label>
                            <select class="form-select @error('kelas_id') is-invalid @enderror"
                                    id="kelas_id"
                                    name="kelas_id">
                                <option value="">-- Tidak Ada Kelas --</option>
                                @foreach($kelas as $item)
                                <option value="{{ $item->id }}"
                                        {{ old('kelas_id', $siswa->kelas_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                    @if($item->waliKelas)
                                        - Wali: {{ $item->waliKelas->nama_guru }}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            @error('kelas_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            @if($siswa->kelas)
                            <small class="text-info">
                                <i class="fas fa-info-circle"></i> Saat ini: <strong>{{ $siswa->kelas->nama }}</strong>
                            </small>
                            @else
                            <small class="text-warning">
                                <i class="fas fa-exclamation-triangle"></i> Belum ada kelas
                            </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Data
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
