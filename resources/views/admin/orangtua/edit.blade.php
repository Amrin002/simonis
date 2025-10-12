@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-md-12">
                <h1 class="page-title">Edit Data Orang Tua</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.orangtua.index') }}">Data Orang Tua</a></li>
                        <li class="breadcrumb-item active">Edit Orang Tua</li>
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

        <form action="{{ route('admin.orangtua.update', $orangtua->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card card-custom mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Data Orang Tua</h5>
                </div>
                <div class="card-body">
                    @if($orangtua->user)
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            Orang tua ini sudah memiliki akun: <strong>{{ $orangtua->user->username }}</strong>
                        </div>
                    @else
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Orang tua ini belum memiliki akun login. Akun dapat dibuat melalui menu Kelola User.
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_orang_tua" class="form-label">
                                    Nama Lengkap Orang Tua <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('nama_orang_tua') is-invalid @enderror"
                                        id="nama_orang_tua" name="nama_orang_tua"
                                        value="{{ old('nama_orang_tua', $orangtua->nama_orang_tua) }}"
                                        placeholder="Masukkan nama lengkap orang tua" required>
                                </div>
                                @error('nama_orang_tua')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nomor_tlp" class="form-label">
                                    Nomor Telepon <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control @error('nomor_tlp') is-invalid @enderror"
                                        id="nomor_tlp" name="nomor_tlp" value="{{ old('nomor_tlp', $orangtua->nomor_tlp) }}"
                                        placeholder="Contoh: 08123456789" required>
                                </div>
                                @error('nomor_tlp')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="alamat" class="form-label">
                                    Alamat Lengkap <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat"
                                    name="alamat" rows="5" placeholder="Masukkan alamat lengkap"
                                    required>{{ old('alamat', $orangtua->alamat) }}</textarea>
                                @error('alamat')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.orangtua.index') }}" class="btn btn-secondary">
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
