@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Tambah Mata Pelajaran Baru</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.mapel.index') }}">Mata Pelajaran</a></li>
                        <li class="breadcrumb-item active">Tambah Mata Pelajaran</li>
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

        <form action="{{ route('admin.mapel.store') }}" method="POST">
            @csrf

            <div class="card card-custom mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-book me-2"></i>Informasi Mata Pelajaran</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Catatan:</strong> Guru pengampu bersifat opsional dan dapat diatur kemudian. Sistem akan
                        otomatis membuat relasi GuruMapel.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_matapelajaran" class="form-label">
                                    Nama Mata Pelajaran <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-book-open"></i></span>
                                    <input type="text"
                                        class="form-control @error('nama_matapelajaran') is-invalid @enderror"
                                        id="nama_matapelajaran" name="nama_matapelajaran"
                                        value="{{ old('nama_matapelajaran') }}"
                                        placeholder="Contoh: Matematika, Bahasa Indonesia" required>
                                </div>
                                @error('nama_matapelajaran')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Nama harus unik</small>
                            </div>

                            <div class="mb-3">
                                <label for="kode_mapel" class="form-label">
                                    Kode Mata Pelajaran <span class="text-muted">(Opsional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                    <input type="text" class="form-control @error('kode_mapel') is-invalid @enderror"
                                        id="kode_mapel" name="kode_mapel" value="{{ old('kode_mapel') }}"
                                        placeholder="Contoh: MTK, IPA, IND" maxlength="10">
                                </div>
                                @error('kode_mapel')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Maksimal 10 karakter</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">
                                    Deskripsi <span class="text-muted">(Opsional)</span>
                                </label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi"
                                    name="deskripsi" rows="5"
                                    placeholder="Deskripsi singkat tentang mata pelajaran">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Guru Pengampu</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Opsional:</strong> Anda dapat memilih guru pengampu sekarang atau menambahkannya nanti saat
                        edit.
                    </div>

                    <label class="form-label">Pilih Guru Pengampu <span class="text-muted">(Opsional)</span></label>

                    @if($gurus->count() > 0)
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            @foreach($gurus as $guru)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="guru_ids[]" value="{{ $guru->id }}"
                                        id="guru_{{ $guru->id }}" {{ in_array($guru->id, old('guru_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="guru_{{ $guru->id }}">
                                        <strong>{{ $guru->nama_guru }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            NIP: {{ $guru->nip }}
                                            @if($guru->mapels->count() > 0)
                                                | Mengajar: {{ $guru->mapels->pluck('nama_matapelajaran')->join(', ') }}
                                            @endif
                                        </small>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Belum ada guru dengan role Guru Mapel.
                            <a href="{{ route('admin.guru.create') }}" target="_blank">Tambah guru</a> terlebih dahulu.
                        </div>
                    @endif

                    @error('guru_ids')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                    <small class="text-muted d-block mt-2">
                        Hanya guru dengan role "Guru Mapel" yang ditampilkan. Relasi GuruMapel akan dibuat otomatis.
                    </small>
                </div>
            </div>

            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.mapel.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Data
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
