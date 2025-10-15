@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-edit me-2"></i>Edit Nilai {{ strtoupper($jenis) }}
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-user me-1"></i>{{ $nilai->siswa->nama }} ({{ $nilai->siswa->nis }})
                            <span class="mx-2">•</span>
                            <i class="fas fa-book me-1"></i>{{ $nilai->mapel->nama_matapelajaran }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.nilai.show', [$nilai->siswa_id, $nilai->mapel_id]) }}"
                            class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Gagal!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- Left Column: Info Siswa --}}
            <div class="col-md-4 mb-4">
                <div class="card card-custom">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-user-circle me-2"></i>Informasi Siswa
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="avatar-large mb-3">
                            {{ substr($nilai->siswa->nama, 0, 1) }}
                        </div>
                        <h4 class="mb-1">{{ $nilai->siswa->nama }}</h4>
                        <p class="text-muted mb-3">{{ $nilai->siswa->nis }}</p>

                        <div class="info-item">
                            <i class="fas fa-door-open text-primary"></i>
                            <strong>Kelas:</strong> {{ $nilai->kelas->nama }}
                        </div>

                        <div class="info-item">
                            <i class="fas fa-book text-info"></i>
                            <strong>Mata Pelajaran:</strong> {{ $nilai->mapel->nama_matapelajaran }}
                        </div>

                        <div class="info-item">
                            <i class="fas fa-calendar text-success"></i>
                            <strong>Terakhir Update:</strong><br>
                            <small>{{ $nilai->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>

                {{-- Info Card --}}
                <div class="card card-custom mt-3">
                    <div class="card-body">
                        <div class="alert alert-warning mb-0" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian!</strong><br>
                            <small>
                                Perubahan nilai akan mempengaruhi perhitungan nilai akhir siswa.
                                Pastikan nilai yang diinput sudah benar.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Form Edit --}}
            <div class="col-md-8">
                <div class="card card-custom">
                    <div class="card-header" style="background: linear-gradient(135deg,
                                {{ $jenis == 'tugas' ? '#667eea 0%, #764ba2 100%' :
        ($jenis == 'uts' ? '#4facfe 0%, #00f2fe 100%' :
            '#43e97b 0%, #38f9d7 100%') }});">
                        <h5 class="mb-0 text-white">
                            <i
                                class="fas fa-{{ $jenis == 'tugas' ? 'tasks' : ($jenis == 'uts' ? 'file-alt' : 'graduation-cap') }} me-2"></i>
                            Form Edit Nilai {{ strtoupper($jenis) }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('guru.nilai.update', [$nilai->id, $jenis]) }}" method="POST"
                            id="formEditNilai">
                            @csrf
                            @method('PUT')

                            {{-- Nilai Lama --}}
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Nilai Lama:</strong>
                                            </div>
                                            <div>
                                                <span class="badge bg-primary"
                                                    style="font-size: 1.5rem; padding: 0.75rem 1.5rem;">
                                                    {{ number_format($nilai->{'nilai_' . $jenis}, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Input Nilai Baru --}}
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label class="form-label">
                                        <i class="fas fa-star me-1"></i>
                                        Nilai {{ strtoupper($jenis) }} Baru <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="nilai_{{ $jenis }}" id="nilaiInput"
                                        class="form-control form-control-lg nilai-input" min="0" max="100" step="0.01"
                                        value="{{ old('nilai_' . $jenis, $nilai->{'nilai_' . $jenis}) }}"
                                        placeholder="Masukkan nilai (0-100)" required autofocus>
                                    @error('nilai_' . $jenis)
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Nilai harus antara 0 - 100. Gunakan desimal untuk nilai lebih presisi (misal: 85.50)
                                    </small>
                                </div>
                            </div>

                            {{-- Preview Perubahan --}}
                            <div class="row mb-4" id="previewSection" style="display: none;">
                                <div class="col-md-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="mb-3">
                                                <i class="fas fa-eye me-2"></i>Preview Perubahan
                                            </h6>
                                            <div class="row text-center">
                                                <div class="col-md-4">
                                                    <div class="preview-box">
                                                        <small class="text-muted d-block">Nilai Lama</small>
                                                        <h3 class="text-muted" id="nilaiLama">
                                                            {{ number_format($nilai->{'nilai_' . $jenis}, 2) }}</h3>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="preview-box">
                                                        <small class="text-muted d-block">Perubahan</small>
                                                        <h3 id="selisih" class="text-primary">0.00</h3>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="preview-box">
                                                        <small class="text-muted d-block">Nilai Baru</small>
                                                        <h3 class="text-success" id="nilaiBaru">
                                                            {{ number_format($nilai->{'nilai_' . $jenis}, 2) }}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Keterangan --}}
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label class="form-label">
                                        <i class="fas fa-comment me-1"></i>Keterangan (Opsional)
                                    </label>
                                    <textarea name="keterangan" class="form-control" rows="4"
                                        placeholder="Tambahkan keterangan jika diperlukan...">{{ old('keterangan', $nilai->keterangan) }}</textarea>
                                    @error('keterangan')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="fas fa-lightbulb me-1"></i>
                                        Contoh: "Nilai diperbaiki setelah remedial", "Ada bonus nilai keaktifan", dll.
                                    </small>
                                </div>
                            </div>

                            {{-- Info Dampak --}}
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="alert alert-success">
                                        <i class="fas fa-calculator me-2"></i>
                                        <strong>Info Perhitungan:</strong><br>
                                        <small>
                                            Nilai {{ strtoupper($jenis) }} memiliki bobot
                                            <strong>{{ $jenis == 'tugas' ? '30%' : '35%' }}</strong>
                                            dalam perhitungan nilai akhir.<br>
                                            Nilai akhir akan otomatis diperbarui setelah Anda menyimpan perubahan.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal">
                                            <i class="fas fa-trash me-1"></i> Hapus Nilai
                                        </button>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('guru.nilai.show', [$nilai->siswa_id, $nilai->mapel_id]) }}"
                                                class="btn btn-secondary">
                                                <i class="fas fa-times me-1"></i> Batal
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-trash-alt fa-4x text-danger"></i>
                    </div>
                    <h5 class="text-center mb-3">Apakah Anda yakin ingin menghapus nilai ini?</h5>
                    <p class="text-center text-muted">
                        Nilai {{ strtoupper($jenis) }} untuk <strong>{{ $nilai->siswa->nama }}</strong> akan dihapus
                        permanen.
                        <br>
                        <strong class="text-danger">Tindakan ini tidak dapat dibatalkan!</strong>
                    </p>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>
                            Jika nilai ini dihapus, nilai akhir siswa juga akan terpengaruh dan mungkin akan dihapus jika
                            komponen nilai lain tidak lengkap.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <form action="{{ route('guru.nilai.destroy', [$nilai->id, $jenis]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Ya, Hapus Nilai
                        </button>
                    </form>
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

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
        }

        .avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 2.5rem;
            margin: 0 auto;
        }

        .info-item {
            padding: 0.75rem;
            margin: 0.5rem 0;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: left;
        }

        .info-item i {
            margin-right: 0.5rem;
        }

        .nilai-input {
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            border: 2px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .nilai-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: scale(1.02);
        }

        .nilai-input:valid {
            border-color: #48bb78;
            background-color: #f0fff4;
        }

        .nilai-input:invalid {
            border-color: #f56565;
            background-color: #fff5f5;
        }

        .preview-box {
            padding: 1rem;
        }

        .preview-box h3 {
            font-weight: bold;
            margin-bottom: 0;
        }

        .alert {
            border-radius: 12px;
            border: none;
        }

        .modal-content {
            border-radius: 12px;
            border: none;
        }

        .modal-header {
            border-radius: 12px 12px 0 0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const nilaiLamaValue = {{ $nilai->{'nilai_' . $jenis} }};

        // Real-time preview perubahan
        document.getElementById('nilaiInput').addEventListener('input', function () {
            const nilaiBaru = parseFloat(this.value);

            if (!isNaN(nilaiBaru) && nilaiBaru >= 0 && nilaiBaru <= 100) {
                // Show preview section
                document.getElementById('previewSection').style.display = 'block';

                // Calculate difference
                const selisih = nilaiBaru - nilaiLamaValue;

                // Update preview values
                document.getElementById('nilaiBaru').textContent = nilaiBaru.toFixed(2);

                const selisihElement = document.getElementById('selisih');
                selisihElement.textContent = (selisih >= 0 ? '+' : '') + selisih.toFixed(2);

                // Change color based on increase/decrease
                if (selisih > 0) {
                    selisihElement.className = 'text-success';
                } else if (selisih < 0) {
                    selisihElement.className = 'text-danger';
                } else {
                    selisihElement.className = 'text-muted';
                }
            } else {
                document.getElementById('previewSection').style.display = 'none';
            }
        });

        // Format nilai saat blur (2 desimal)
        document.getElementById('nilaiInput').addEventListener('blur', function () {
            const nilai = parseFloat(this.value);

            if (!isNaN(nilai) && nilai >= 0 && nilai <= 100) {
                this.value = nilai.toFixed(2);
            }
        });

        // Validasi sebelum submit
        document.getElementById('formEditNilai').addEventListener('submit', function (e) {
            const nilaiInput = document.getElementById('nilaiInput');
            const nilai = parseFloat(nilaiInput.value);

            // Validasi range
            if (isNaN(nilai) || nilai < 0 || nilai > 100) {
                e.preventDefault();
                alert('Nilai harus antara 0 dan 100!');
                nilaiInput.focus();
                return false;
            }

            // Konfirmasi jika nilai berubah
            if (nilai !== nilaiLamaValue) {
                const selisih = nilai - nilaiLamaValue;
                const message = `Apakah Anda yakin ingin mengubah nilai?\n\n` +
                    `Nilai Lama: ${nilaiLamaValue.toFixed(2)}\n` +
                    `Nilai Baru: ${nilai.toFixed(2)}\n` +
                    `Perubahan: ${(selisih >= 0 ? '+' : '')}${selisih.toFixed(2)}\n\n` +
                    `Nilai akhir siswa akan otomatis diperbarui.`;

                if (!confirm(message)) {
                    e.preventDefault();
                    return false;
                }
            } else {
                // Tidak ada perubahan nilai
                e.preventDefault();
                alert('Tidak ada perubahan nilai!\n\nSilakan ubah nilai terlebih dahulu atau klik Batal.');
                return false;
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function (e) {
            // Ctrl + S untuk save
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                document.getElementById('formEditNilai').submit();
            }

            // Escape untuk cancel
            if (e.key === 'Escape') {
                if (confirm('Batalkan perubahan dan kembali?')) {
                    window.location.href = "{{ route('guru.nilai.show', [$nilai->siswa_id, $nilai->mapel_id]) }}";
                }
            }
        });

        // Auto hide alerts
        setTimeout(function () {
            $('.alert-danger').fadeOut('slow');
        }, 8000);
    </script>
@endpush
