@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Tambah Kelas Baru</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.kelas.index') }}">Data Kelas</a></li>
                        <li class="breadcrumb-item active">Tambah Kelas</li>
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

        <form action="{{ route('admin.kelas.store') }}" method="POST">
            @csrf

            <div class="card card-custom mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Data Kelas</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Catatan:</strong> Wali kelas bersifat opsional dan dapat diatur kemudian. Pastikan guru yang
                        dipilih sudah memiliki role sebagai wali kelas.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">
                                    Nama Kelas <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-door-open"></i></span>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                        name="nama" value="{{ old('nama') }}"
                                        placeholder="Contoh: X IPA 1, VII A, XII IPS 2" required>
                                </div>
                                @error('nama')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Nama kelas harus unik</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="wali_guru_id" class="form-label">
                                    Wali Kelas <span class="text-muted">(Opsional)</span>
                                </label>
                                <select class="form-select @error('wali_guru_id') is-invalid @enderror" id="wali_guru_id"
                                    name="wali_guru_id">
                                    <option value="">-- Pilih Wali Kelas (Bisa nanti) --</option>
                                    @forelse($guruTersedia as $guru)
                                        <option value="{{ $guru->id }}" {{ old('wali_guru_id') == $guru->id ? 'selected' : '' }}>
                                            {{ $guru->nama_guru }} - {{ $guru->nip }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Tidak ada guru yang tersedia sebagai wali kelas</option>
                                    @endforelse
                                </select>
                                @error('wali_guru_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Hanya guru dengan role wali kelas yang bisa dipilih</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
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
