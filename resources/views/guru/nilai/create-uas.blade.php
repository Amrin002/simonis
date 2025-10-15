@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-graduation-cap me-2"></i>Input Nilai UAS
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-book me-1"></i>{{ $mapel->nama_matapelajaran }}
                            <span class="mx-2">•</span>
                            <i class="fas fa-door-open me-1"></i>{{ $kelas->nama }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.nilai.select-kelas-mapel') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Alert --}}
        <div class="row mb-4">
            <div class="col-md-12">
                @if($existingNilai->count() > 0)
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian!</strong> Beberapa siswa sudah memiliki nilai UAS.
                        Jika Anda mengisi ulang, nilai lama akan diupdate dengan nilai baru.
                    </div>
                @else
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Petunjuk:</strong> Masukkan nilai UAS untuk semua siswa (0-100).
                        Nilai akan otomatis dihitung untuk nilai akhir jika Tugas dan UTS sudah diinput.
                    </div>
                @endif

                {{-- Info Nilai Akhir --}}
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-calculator me-2"></i>
                    <strong>Info Perhitungan Nilai Akhir:</strong><br>
                    Nilai Akhir = (Tugas × 30%) + (UTS × 35%) + (UAS × 35%)<br>
                    <small class="text-muted">Nilai akhir akan otomatis dihitung setelah semua komponen nilai
                        lengkap.</small>
                </div>
            </div>
        </div>

        {{-- Form Input Nilai --}}
        <form action="{{ route('guru.nilai.uas.store') }}" method="POST" id="formNilai">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
            <input type="hidden" name="mapel_id" value="{{ $mapel->id }}">

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-white">
                                    <i class="fas fa-list me-2"></i>Daftar Siswa - {{ $kelas->nama }}
                                </h5>
                                <span class="badge bg-light text-dark">
                                    Total: {{ $siswas->count() }} Siswa
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Quick Action Buttons --}}
                            <div class="mb-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillAll(75)">
                                        <i class="fas fa-magic me-1"></i> Isi Semua 75
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="fillAll(80)">
                                        <i class="fas fa-magic me-1"></i> Isi Semua 80
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="fillAll(85)">
                                        <i class="fas fa-magic me-1"></i> Isi Semua 85
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAll()">
                                        <i class="fas fa-eraser me-1"></i> Kosongkan Semua
                                    </button>
                                </div>
                                <div>
                                    <span class="badge bg-success" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                                        <i class="fas fa-info-circle me-1"></i> KKM: 75
                                    </span>
                                </div>
                            </div>

                            {{-- Table Nilai --}}
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="15%">NIS</th>
                                            <th width="30%">Nama Siswa</th>
                                            <th width="20%">Nilai UAS (0-100)</th>
                                            <th width="30%">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($siswas as $index => $siswa)
                                            @php
                                                $existing = $existingNilai->get($siswa->id);
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong class="text-primary">{{ $siswa->nis }}</strong>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-circle me-2">
                                                            {{ substr($siswa->nama, 0, 1) }}
                                                        </div>
                                                        <strong>{{ $siswa->nama }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" name="nilai_uas[{{ $siswa->id }}]"
                                                        class="form-control nilai-input" min="0" max="100" step="0.01"
                                                        value="{{ $existing ? $existing->nilai_uas : '' }}" placeholder="0-100"
                                                        required>
                                                    @error("nilai_uas.{$siswa->id}")
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" name="keterangan[{{ $siswa->id }}]"
                                                        class="form-control form-control-sm"
                                                        value="{{ $existing ? $existing->keterangan : '' }}"
                                                        placeholder="Opsional...">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Statistik Real-time --}}
                            <div class="row mt-4" id="statistik" style="display: none;">
                                <div class="col-md-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-md-3">
                                                    <div class="stat-box">
                                                        <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                                        <h4 class="mb-0" id="totalInput">0</h4>
                                                        <small class="text-muted">Total Terisi</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="stat-box">
                                                        <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                                                        <h4 class="mb-0" id="rataRata">0.00</h4>
                                                        <small class="text-muted">Rata-rata</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="stat-box">
                                                        <i class="fas fa-arrow-up fa-2x text-info mb-2"></i>
                                                        <h4 class="mb-0" id="nilaiTertinggi">0</h4>
                                                        <small class="text-muted">Tertinggi</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="stat-box">
                                                        <i class="fas fa-arrow-down fa-2x text-warning mb-2"></i>
                                                        <h4 class="mb-0" id="nilaiTerendah">0</h4>
                                                        <small class="text-muted">Terendah</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Buttons --}}
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('guru.nilai.select-kelas-mapel') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Simpan Nilai
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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

        .table th {
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            font-size: 0.875rem;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(67, 233, 123, 0.05);
        }

        .avatar-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .nilai-input {
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            border: 2px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .nilai-input:focus {
            border-color: #43e97b;
            box-shadow: 0 0 0 3px rgba(67, 233, 123, 0.1);
        }

        .nilai-input:valid {
            border-color: #48bb78;
            background-color: #f0fff4;
        }

        .nilai-input:invalid {
            border-color: #f56565;
        }

        .nilai-input.is-invalid {
            border-color: #f56565;
            background-color: #fff5f5;
        }

        .alert {
            border-radius: 12px;
            border: none;
        }

        .stat-box {
            padding: 1rem;
        }

        .stat-box h4 {
            font-weight: bold;
            color: #2d3748;
        }

        .bg-light {
            background-color: #f7fafc !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Function untuk fill all nilai
        function fillAll(nilai) {
            const inputs = document.querySelectorAll('.nilai-input');
            inputs.forEach(input => {
                input.value = nilai;
            });
            updateStatistik(); // Update statistik setelah fill
        }

        // Function untuk clear all nilai
        function clearAll() {
            if (confirm('Apakah Anda yakin ingin mengosongkan semua nilai?')) {
                const inputs = document.querySelectorAll('.nilai-input');
                inputs.forEach(input => {
                    input.value = '';
                });
                document.getElementById('statistik').style.display = 'none';
            }
        }

        // Function untuk update statistik real-time
        function updateStatistik() {
            const inputs = document.querySelectorAll('.nilai-input');
            let nilai = [];

            inputs.forEach(input => {
                if (input.value && !isNaN(input.value)) {
                    nilai.push(parseFloat(input.value));
                }
            });

            if (nilai.length > 0) {
                document.getElementById('statistik').style.display = 'block';

                // Total input
                document.getElementById('totalInput').textContent = nilai.length;

                // Rata-rata
                const rataRata = nilai.reduce((a, b) => a + b, 0) / nilai.length;
                document.getElementById('rataRata').textContent = rataRata.toFixed(2);

                // Tertinggi
                document.getElementById('nilaiTertinggi').textContent = Math.max(...nilai).toFixed(0);

                // Terendah
                document.getElementById('nilaiTerendah').textContent = Math.min(...nilai).toFixed(0);
            } else {
                document.getElementById('statistik').style.display = 'none';
            }
        }

        // Validasi sebelum submit
        document.getElementById('formNilai').addEventListener('submit', function (e) {
            const inputs = document.querySelectorAll('.nilai-input');
            let isValid = true;
            let emptyCount = 0;
            let invalidCount = 0;

            inputs.forEach(input => {
                const nilai = parseFloat(input.value);

                if (!input.value) {
                    isValid = false;
                    input.classList.add('is-invalid');
                    emptyCount++;
                } else if (nilai < 0 || nilai > 100) {
                    isValid = false;
                    input.classList.add('is-invalid');
                    invalidCount++;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                let message = 'Perhatian!\n\n';
                if (emptyCount > 0) {
                    message += `${emptyCount} siswa belum diisi nilainya.\n`;
                }
                if (invalidCount > 0) {
                    message += `${invalidCount} nilai tidak valid (harus 0-100).\n`;
                }
                message += '\nPastikan semua nilai sudah diisi dengan benar.';

                alert(message);

                // Scroll ke input pertama yang error
                const firstInvalid = document.querySelector('.nilai-input.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
            } else {
                // Konfirmasi sebelum submit
                const totalSiswa = inputs.length;
                if (!confirm(`Apakah Anda yakin ingin menyimpan nilai UAS untuk ${totalSiswa} siswa?\n\nNilai akhir akan otomatis dihitung jika semua komponen nilai sudah lengkap.`)) {
                    e.preventDefault();
                }
            }
        });

        // Auto calculate statistik dan validasi real-time
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('.nilai-input');

            inputs.forEach(input => {
                // Event saat input berubah
                input.addEventListener('input', function () {
                    const nilai = parseFloat(this.value);

                    // Validasi real-time
                    if (this.value && (nilai < 0 || nilai > 100)) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }

                    // Update statistik
                    updateStatistik();
                });

                // Event saat focus out (blur)
                input.addEventListener('blur', function () {
                    const nilai = parseFloat(this.value);

                    // Format nilai jadi 2 desimal jika valid
                    if (this.value && nilai >= 0 && nilai <= 100) {
                        this.value = nilai.toFixed(2);
                    }
                });
            });

            // Update statistik saat load jika ada nilai existing
            updateStatistik();
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function (e) {
            // Ctrl + S untuk save
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                document.getElementById('formNilai').submit();
            }
        });
    </script>
@endpush
