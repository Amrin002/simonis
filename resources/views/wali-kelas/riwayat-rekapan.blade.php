@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-history me-2"></i>Riwayat Rekapan
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-user-graduate me-1"></i>
                            {{ $siswa->nama }} ({{ $siswa->nis }})
                        </p>
                    </div>
                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Siswa Card --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="avatar-circle-small">
                                    <i class="fas fa-user fa-3x text-white"></i>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Nama Siswa</small>
                                        <strong class="fs-5">{{ $siswa->nama }}</strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted d-block">NIS</small>
                                        <strong>{{ $siswa->nis }}</strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted d-block">Kelas</small>
                                        <strong>{{ $siswa->kelas->nama ?? '-' }}</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Orang Tua</small>
                                        <strong>{{ $siswa->orangTua->nama_orang_tua ?? '-' }}</strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted d-block">Total Rekapan</small>
                                        <strong class="text-primary">{{ $rekapans->total() }} record</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistik Singkat --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-stat bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $rekapans->where('status_kirim', 'dikirim')->count() }}</h3>
                                <small>Sudah Dikirim</small>
                            </div>
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $rekapans->where('status_kirim', 'belum_dikirim')->count() }}</h3>
                                <small>Belum Dikirim</small>
                            </div>
                            <i class="fas fa-clock fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $rekapans->where('status_kirim', 'gagal')->count() }}</h3>
                                <small>Gagal</small>
                            </div>
                            <i class="fas fa-times-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $rekapans->total() }}</h3>
                                <small>Total Rekapan</small>
                            </div>
                            <i class="fas fa-file-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Riwayat List --}}
        <div class="row">
            <div class="col-md-12">
                @if($rekapans->count() > 0)
                    <div class="card card-custom">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Daftar Riwayat Rekapan
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%" class="text-center">No</th>
                                            <th width="15%">Tanggal</th>
                                            <th width="30%">Kehadiran</th>
                                            <th width="25%">Perilaku</th>
                                            <th width="15%" class="text-center">Status</th>
                                            <th width="10%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rekapans as $index => $rekapan)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $rekapans->firstItem() + $index }}
                                                </td>
                                                <td>
                                                    <strong>{{ $rekapan->tanggal->isoFormat('D MMM Y') }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $rekapan->tanggal->isoFormat('dddd') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="preview-text">
                                                        {{ Str::limit($rekapan->kehadiran ?: 'Belum ada data', 80) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="preview-text">
                                                        {{ Str::limit($rekapan->perilaku ?: 'Tidak ada catatan', 60) }}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $rekapan->status_badge_color }} py-2 px-3">
                                                        {{ $rekapan->status_text }}
                                                    </span>
                                                    @if($rekapan->isDikirim())
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $rekapan->dikirim_at->diffForHumans() }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('guru.rekapan.show', $rekapan->id) }}"
                                                            class="btn btn-info" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($rekapan->wa_link && ($rekapan->isBelumDikirim() || $rekapan->isGagal()))
                                                            <a href="{{ $rekapan->wa_link }}" target="_blank" class="btn btn-success"
                                                                title="Kirim WA" onclick="markAsClicked({{ $rekapan->id }})">
                                                                <i class="fab fa-whatsapp"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    Menampilkan {{ $rekapans->firstItem() }} - {{ $rekapans->lastItem() }}
                                    dari {{ $rekapans->total() }} rekapan
                                </div>
                                <div>
                                    {{ $rekapans->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum Ada Riwayat Rekapan</h5>
                                <p class="text-muted">
                                    Siswa ini belum memiliki riwayat rekapan.<br>
                                    Rekapan akan dibuat otomatis setiap hari.
                                </p>
                                <a href="{{ route('guru.rekapan.kirim') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Buat Rekapan Hari Ini
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
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

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
        }

        .avatar-circle-small {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .card-stat {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .card-stat .opacity-50 {
            opacity: 0.3;
        }

        .preview-text {
            font-size: 0.9rem;
            line-height: 1.5;
            color: #4a5568;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody tr {
            transition: background-color 0.2s;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            font-weight: 500;
            font-size: 0.85rem;
        }

        .btn-group-sm .btn {
            padding: 0.375rem 0.75rem;
        }

        .pagination {
            margin-bottom: 0;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.85rem;
            }

            .card-stat h3 {
                font-size: 1.5rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Mark sebagai clicked (dibuka)
        function markAsClicked(rekapanId) {
            fetch(`/guru/rekapan/${rekapanId}/mark-dikirim`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Rekapan ditandai sebagai dikirim',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan!',
                    });
                });
        }

        // Show success message
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Show error message
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
            });
        @endif
    </script>
@endpush
