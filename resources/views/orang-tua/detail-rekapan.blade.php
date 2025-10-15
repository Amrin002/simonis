@extends('template-orangtua.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-file-alt me-2"></i>Detail Rekapan Harian
                        </h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $rekapan->tanggal->locale('id')->isoFormat('dddd, D MMMM Y') }}
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

        <div class="row">
            {{-- Info Siswa --}}
            <div class="col-lg-4 mb-4">
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-graduate me-2"></i>Informasi Siswa
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="avatar-circle mb-3">
                                <i class="fas fa-user fa-4x text-white"></i>
                            </div>
                            <h4 class="mb-1">{{ $rekapan->siswa->nama }}</h4>
                            <p class="text-muted mb-0">
                                <i class="fas fa-id-card me-1"></i>{{ $rekapan->siswa->nis }}
                            </p>
                        </div>

                        <hr>

                        <div class="info-item">
                            <i class="fas fa-school text-primary me-2"></i>
                            <strong>Kelas:</strong>
                            <span class="float-end">{{ $rekapan->siswa->kelas->nama ?? '-' }}</span>
                        </div>

                        <div class="info-item">
                            <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                            <strong>Wali Kelas:</strong>
                            <span class="float-end">
                                {{ $rekapan->siswa->kelas && $rekapan->siswa->kelas->waliKelas
        ? $rekapan->siswa->kelas->waliKelas->nama_guru
        : '-' }}
                            </span>
                        </div>

                        <hr>

                        <h6 class="text-muted mb-3">
                            <i class="fas fa-users me-2"></i>Data Orang Tua
                        </h6>

                        @if($rekapan->siswa->orangTua)
                            <div class="info-item">
                                <i class="fas fa-user text-success me-2"></i>
                                <strong>Nama:</strong>
                                <span class="float-end">{{ $rekapan->siswa->orangTua->nama_orang_tua }}</span>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-phone text-success me-2"></i>
                                <strong>No. HP:</strong>
                                <span class="float-end">{{ $rekapan->siswa->orangTua->nomor_tlp }}</span>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-map-marker-alt text-success me-2"></i>
                                <strong>Alamat:</strong>
                                <p class="mb-0 mt-2">{{ $rekapan->siswa->orangTua->alamat }}</p>
                            </div>
                        @else
                            <div class="alert alert-warning py-2">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Data orang tua belum tersedia
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Status Pengiriman --}}
                @if($rekapan->exists)
                    <div class="card card-custom mt-3">
                        <div class="card-header bg-{{ $rekapan->status_badge_color }} text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Status Pengiriman
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="status-badge-large mb-3">
                                <span class="badge bg-{{ $rekapan->status_badge_color }} fs-5 w-100 py-2">
                                    {{ $rekapan->status_text }}
                                </span>
                            </div>

                            @if($rekapan->isDikirim())
                                <div class="alert alert-success mb-0">
                                    <i class="fas fa-check-circle me-1"></i>
                                    <strong>Dikirim:</strong><br>
                                    {{ $rekapan->dikirim_at->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }} WIB<br>
                                    <small>({{ $rekapan->dikirim_at->diffForHumans() }})</small>
                                </div>
                            @elseif($rekapan->isGagal())
                                <div class="alert alert-danger mb-0">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    <strong>Keterangan:</strong><br>
                                    {{ $rekapan->catatan_pengiriman }}
                                </div>
                            @else
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-clock me-1"></i>
                                    Rekapan belum dikirim via WhatsApp
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Konten Rekapan --}}
            <div class="col-lg-8 mb-4">
                {{-- Kehadiran --}}
                <div class="card card-custom mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i>Data Kehadiran
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($rekapan->kehadiran)
                            <div class="rekapan-content">
                                {!! nl2br(e($rekapan->kehadiran)) !!}
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>Belum ada data kehadiran</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Perilaku --}}
                <div class="card card-custom mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-check me-2"></i>Data Perilaku
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($rekapan->perilaku)
                            @php
                                $perilakuBaik = str_contains($rekapan->perilaku, 'Baik') ||
                                    str_contains($rekapan->perilaku, 'Tidak ada pelanggaran');
                            @endphp

                            @if(!$perilakuBaik)
                                <div class="alert alert-danger mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Perhatian:</strong> Siswa memiliki catatan pelanggaran pada hari ini.
                                </div>
                            @else
                                <div class="alert alert-success mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Baik:</strong> Tidak ada catatan pelanggaran pada hari ini.
                                </div>
                            @endif

                            <div class="rekapan-content">
                                {!! nl2br(e($rekapan->perilaku)) !!}
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-smile fa-3x mb-3 text-success"></i>
                                <p>Tidak ada catatan perilaku</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Preview Pesan WhatsApp --}}
                @if($rekapan->exists)
                    <div class="card card-custom mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fab fa-whatsapp me-2"></i>Preview Pesan WhatsApp
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="whatsapp-preview">
                                <div class="wa-bubble">
                                    {!! nl2br(e($rekapan->generateWaMessage())) !!}
                                </div>
                            </div>

                            <hr>

                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Informasi:</strong> Pesan di atas adalah preview pesan yang dikirimkan melalui WhatsApp
                                kepada orang tua.
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Navigasi --}}
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('orangtua.detail-anak', $rekapan->siswa->id) }}" class="btn btn-primary">
                                <i class="fas fa-user-graduate me-2"></i>Lihat Profil Siswa
                            </a>
                            <a href="{{ route('orangtua.riwayat-anak', $rekapan->siswa->id) }}" class="btn btn-info">
                                <i class="fas fa-history me-2"></i>Lihat Semua Riwayat
                            </a>
                            <a href="{{ route('orangtua.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-home me-2"></i>Ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .avatar-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .info-item {
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .rekapan-content {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            font-size: 0.95rem;
            line-height: 1.8;
            white-space: pre-line;
        }

        .whatsapp-preview {
            background: #e5ddd5;
            padding: 20px;
            border-radius: 12px;
        }

        .wa-bubble {
            background: #dcf8c6;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 0.9rem;
            line-height: 1.6;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            white-space: pre-line;
        }

        .status-badge-large {
            text-align: center;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        .card-header {
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
            });
        @endif
    </script>
@endpush
