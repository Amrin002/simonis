@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-plus-circle me-2"></i>Tambah Pelanggaran
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-door-open me-1"></i>
                            Kelas {{ $kelas->nama }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.pelanggaran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Create --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header bg-gradient-primary">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-edit me-2"></i>Form Tambah Pelanggaran
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('guru.pelanggaran.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                {{-- Pilih Siswa --}}
                                <div class="col-md-6 mb-3">
                                    <label for="siswa_id" class="form-label required">
                                        <i class="fas fa-user me-1"></i>Pilih Siswa
                                    </label>
                                    <select name="siswa_id" id="siswa_id"
                                        class="form-select @error('siswa_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Siswa --</option>
                                        @foreach($siswas as $siswa)
                                            <option value="{{ $siswa->id }}" {{ old('siswa_id', $selectedSiswa?->id) == $siswa->id ? 'selected' : '' }}>
                                                {{ $siswa->nama }} ({{ $siswa->nis }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('siswa_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Tanggal --}}
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal" class="form-label required">
                                        <i class="fas fa-calendar me-1"></i>Tanggal Pelanggaran
                                    </label>
                                    <input type="date" name="tanggal" id="tanggal"
                                        class="form-control @error('tanggal') is-invalid @enderror"
                                        value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Jenis Pelanggaran --}}
                                <div class="col-md-8 mb-3">
                                    <label for="jenis_pelanggaran" class="form-label required">
                                        <i class="fas fa-list me-1"></i>Jenis Pelanggaran
                                    </label>
                                    <div class="input-group">
                                        <input type="text" name="jenis_pelanggaran" id="jenis_pelanggaran"
                                            class="form-control @error('jenis_pelanggaran') is-invalid @enderror"
                                            placeholder="Masukkan jenis pelanggaran..."
                                            value="{{ old('jenis_pelanggaran') }}" list="jenisPelanggaranList" required>
                                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                                            data-bs-target="#jenisPelanggaranModal">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    <datalist id="jenisPelanggaranList">
                                        @foreach($jenisPelanggaranOptions as $option)
                                            <option value="{{ $option }}">
                                        @endforeach
                                    </datalist>
                                    @error('jenis_pelanggaran')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Ketik atau pilih dari daftar
                                    </small>
                                </div>

                                {{-- Kategori --}}
                                <div class="col-md-4 mb-3">
                                    <label for="kategori" class="form-label required">
                                        <i class="fas fa-tag me-1"></i>Kategori
                                    </label>
                                    <select name="kategori" id="kategori"
                                        class="form-select @error('kategori') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="Ringan" {{ old('kategori') == 'Ringan' ? 'selected' : '' }}>
                                            Ringan
                                        </option>
                                        <option value="Sedang" {{ old('kategori') == 'Sedang' ? 'selected' : '' }}>
                                            Sedang
                                        </option>
                                        <option value="Berat" {{ old('kategori') == 'Berat' ? 'selected' : '' }}>
                                            Berat
                                        </option>
                                    </select>
                                    @error('kategori')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Keterangan --}}
                                <div class="col-md-12 mb-3">
                                    <label for="keterangan" class="form-label">
                                        <i class="fas fa-comment me-1"></i>Keterangan
                                    </label>
                                    <textarea name="keterangan" id="keterangan" rows="4"
                                        class="form-control @error('keterangan') is-invalid @enderror"
                                        placeholder="Keterangan tambahan (opsional)...">{{ old('keterangan') }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Maksimal 1000 karakter
                                    </small>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('guru.pelanggaran.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                                <button type="reset" class="btn btn-warning">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Simpan Pelanggaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Jenis Pelanggaran --}}
    <div class="modal fade" id="jenisPelanggaranModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-list me-2"></i>Pilih Jenis Pelanggaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="list-group">
                        @foreach($jenisPelanggaranOptions as $option)
                            <button type="button" class="list-group-item list-group-item-action"
                                onclick="selectJenisPelanggaran('{{ $option }}')">
                                <i class="fas fa-chevron-right me-2"></i>{{ $option }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-custom .card-header {
            border-bottom: none;
            padding: 1.25rem;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .form-label.required::after {
            content: " *";
            color: red;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
        }

        .list-group-item {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .list-group-item:hover {
            background-color: rgba(102, 126, 234, 0.1);
            border-left: 3px solid #667eea;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function selectJenisPelanggaran(value) {
            document.getElementById('jenis_pelanggaran').value = value;
            var modal = bootstrap.Modal.getInstance(document.getElementById('jenisPelanggaranModal'));
            modal.hide();
        }
    </script>
@endpush
