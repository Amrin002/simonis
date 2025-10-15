@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-tasks me-2"></i>Input Nilai Tugas
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
                        <strong>Perhatian!</strong> Beberapa siswa sudah memiliki nilai tugas.
                        Jika Anda mengisi ulang, nilai lama akan diupdate dengan nilai baru.
                    </div>
                @else
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Petunjuk:</strong> Masukkan nilai tugas untuk semua siswa (0-100).
                        Nilai akan otomatis dihitung untuk nilai akhir jika UTS dan UAS sudah diinput.
                    </div>
                @endif
            </div>
        </div>

        {{-- Form Input Nilai --}}
        <form action="{{ route('guru.nilai.tugas.store') }}" method="POST" id="formNilai">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
            <input type="hidden" name="mapel_id" value="{{ $mapel->id }}">

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
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
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAll()">
                                        <i class="fas fa-eraser me-1"></i> Kosongkan Semua
                                    </button>
                                </div>
                                <div>
                                    <span class="badge bg-info">
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
                                            <th width="20%">Nilai Tugas (0-100)</th>
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
                                                    <input type="number" name="nilai_tugas[{{ $siswa->id }}]"
                                                        class="form-control nilai-input" min="0" max="100" step="0.01"
                                                        value="{{ $existing ? $existing->nilai_tugas : '' }}"
                                                        placeholder="0-100" required>
                                                    @error("nilai_tugas.{$siswa->id}")
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

                            {{-- Submit Buttons --}}
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('guru.nilai.select-kelas-mapel') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
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
            background-color: rgba(102, 126, 234, 0.05);
        }

        .avatar-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .nilai-input:valid {
            border-color: #48bb78;
        }

        .nilai-input:invalid {
            border-color: #f56565;
        }

        .alert {
            border-radius: 12px;
            border: none;
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
        }

        // Function untuk clear all nilai
        function clearAll() {
            if (confirm('Apakah Anda yakin ingin mengosongkan semua nilai?')) {
                const inputs = document.querySelectorAll('.nilai-input');
                inputs.forEach(input => {
                    input.value = '';
                });
            }
        }

        // Validasi sebelum submit
        document.getElementById('formNilai').addEventListener('submit', function (e) {
            const inputs = document.querySelectorAll('.nilai-input');
            let isValid = true;
            let emptyCount = 0;

            inputs.forEach(input => {
                const nilai = parseFloat(input.value);

                if (!input.value || nilai < 0 || nilai > 100) {
                    isValid = false;
                    input.classList.add('is-invalid');
                    emptyCount++;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert(`Perhatian!\n\n${emptyCount} siswa belum diisi nilainya atau nilai tidak valid.\n\nPastikan semua nilai berada di antara 0-100.`);

                // Scroll ke input pertama yang error
                const firstInvalid = document.querySelector('.nilai-input.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
            } else {
                // Konfirmasi sebelum submit
                if (!confirm('Apakah Anda yakin ingin menyimpan semua nilai tugas?')) {
                    e.preventDefault();
                }
            }
        });

        // Auto calculate statistik
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('.nilai-input');

            inputs.forEach(input => {
                input.addEventListener('input', function () {
                    const nilai = parseFloat(this.value);

                    // Validasi real-time
                    if (nilai < 0 || nilai > 100) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>
@endpush
