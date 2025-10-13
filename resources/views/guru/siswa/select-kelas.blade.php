@extends('layouts.main')

@section('section')
    <div class="content-wrapper">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-users me-2"></i>Daftar Siswa
                        </h1>
                        <p class="text-muted mb-0">
                            Pilih kelas untuk melihat daftar siswa
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pilih Kelas --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-door-open me-2"></i>Pilih Kelas
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($kelasList->count() > 0)
                            <div class="row">
                                @foreach($kelasList as $kelas)
                                    <div class="col-md-4 mb-3">
                                        <div class="kelas-card"
                                            onclick="window.location='{{ route('guru.siswa.index', ['kelas_id' => $kelas->id]) }}'">
                                            <div class="kelas-icon">
                                                <i class="fas fa-door-open"></i>
                                            </div>
                                            <div class="kelas-info">
                                                <h5 class="mb-1">{{ $kelas->nama }}</h5>
                                                <p class="text-muted mb-2">
                                                    <i class="fas fa-users me-1"></i>
                                                    {{ $kelas->jumlah_siswa }} Siswa
                                                </p>
                                                <p class="text-muted mb-0">
                                                    <i class="fas fa-chalkboard-teacher me-1"></i>
                                                    {{ $kelas->nama_wali_kelas }}
                                                </p>
                                            </div>
                                            <div class="kelas-arrow">
                                                <i class="fas fa-chevron-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada kelas ditemukan</h5>
                                <p class="text-muted">Anda belum mengajar di kelas manapun</p>
                            </div>
                        @endif
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

        .kelas-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .kelas-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
            transform: translateY(-5px);
        }

        .kelas-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .kelas-info {
            flex-grow: 1;
        }

        .kelas-info h5 {
            font-weight: bold;
            color: #2d3748;
        }

        .kelas-arrow {
            color: #cbd5e0;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .kelas-card:hover .kelas-arrow {
            color: #667eea;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
        }
    </style>
@endpush
