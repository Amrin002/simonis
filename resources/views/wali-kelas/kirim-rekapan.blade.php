@extends('layouts.main')

@section('section')
        <div class="content-wrapper">
            {{-- Header Section --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="page-title">
                                <i class="fab fa-whatsapp me-2"></i>Kirim Rekapan via WhatsApp
                            </h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('guru.rekapan.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter & Actions --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label">
                                        <i class="fas fa-calendar me-1"></i>Pilih Tanggal
                                    </label>
                                    <form action="{{ route('guru.rekapan.kirim') }}" method="GET" id="formTanggal">
                                        <input type="date"
                                               name="tanggal"
                                               class="form-control"
                                               value="{{ $tanggal }}"
                                               max="{{ today()->format('Y-m-d') }}"
                                               onchange="this.form.submit()">
                                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                                    </form>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">
                                        <i class="fas fa-filter me-1"></i>Filter Status
                                    </label>
                                    <form action="{{ route('guru.rekapan.kirim') }}" method="GET" id="formStatus">
                                        <select name="status" class="form-select" onchange="this.form.submit()">
                                            <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>
                                                Semua Status
                                            </option>
                                            <option value="belum_dikirim" {{ $statusFilter == 'belum_dikirim' ? 'selected' : '' }}>
                                                Belum Dikirim
                                            </option>
                                            <option value="dikirim" {{ $statusFilter == 'dikirim' ? 'selected' : '' }}>
                                                Sudah Dikirim
                                            </option>
                                            <option value="gagal" {{ $statusFilter == 'gagal' ? 'selected' : '' }}>
                                                Gagal
                                            </option>
                                        </select>
                                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                    </form>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button onclick="bukaSemuaWA()" class="btn btn-success me-2">
                                        <i class="fas fa-paper-plane me-1"></i> Buka Semua WhatsApp (Auto)
                                    </button>
                                    <button onclick="refreshPage()" class="btn btn-info">
                                        <i class="fas fa-sync me-1"></i> Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Progress Bar --}}
            @php
    $totalRekapan = $rekapans->count();
    $dikirim = $rekapans->where('status_kirim', 'dikirim')->count();
    $percentage = $totalRekapan > 0 ? ($dikirim / $totalRekapan) * 100 : 0;
            @endphp

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span><strong>Progress Pengiriman:</strong></span>
                                <span><strong>{{ $dikirim }}</strong> / {{ $totalRekapan }} siswa</span>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-{{ $percentage >= 80 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}"
                                    role="progressbar" style="width: {{ $percentage }}%">
                                    {{ round($percentage, 1) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rekapan List --}}
            @if($rekapans->count() > 0)
                <div class="row">
                    @foreach($rekapans as $rekapan)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card card-rekapan {{ $rekapan->isDikirim() ? 'card-sent' : '' }}"
                                 id="card-{{ $rekapan->id }}">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user-graduate me-2"></i>
                                            {{ $rekapan->siswa->nama }}
                                        </h6>
                                        <span class="badge bg-{{ $rekapan->status_badge_color }}">
                                            {{ $rekapan->status_text }}
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-id-card me-1"></i>{{ $rekapan->siswa->nis }}
                                    </small>
                                </div>
                                <div class="card-body">
                                    {{-- Info Orang Tua --}}
                                    <div class="mb-3">
                                        <small class="text-muted d-block">Orang Tua</small>
                                        <strong>{{ $rekapan->siswa->orangTua ? $rekapan->siswa->orangTua->nama_orang_tua : '-' }}</strong>
                                        {{-- ✅ FIX: ganti 'nama' jadi 'nama_orang_tua' --}}
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-phone me-1"></i>
                                            {{ $rekapan->siswa->orangTua ? $rekapan->siswa->orangTua->nomor_tlp : '-' }}
                                            {{-- ✅ FIX: ganti 'no_tlp' jadi 'nomor_tlp' --}}
                                        </small>
                                    </div>

                                    {{-- Preview Rekapan --}}
                                    <div class="rekapan-preview mb-3">
                                        <div class="preview-item">
                                            <small class="text-muted">Kehadiran:</small>
                                            <p class="mb-1">{{ Str::limit($rekapan->kehadiran ?: 'Belum ada data', 60) }}</p>
                                        </div>
                                        <div class="preview-item">
                                            <small class="text-muted">Perilaku:</small>
                                            <p class="mb-0">{{ Str::limit($rekapan->perilaku ?: 'Belum ada data', 60) }}</p>
                                        </div>
                                    </div>

                                    {{-- Dikirim Info --}}
                                    @if($rekapan->isDikirim())
                                        <div class="alert alert-success py-2 mb-3">
                                            <small>
                                                <i class="fas fa-check-circle me-1"></i>
                                                Dikirim {{ $rekapan->dikirim_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    @endif

                                    {{-- Gagal Info --}}
                                    @if($rekapan->isGagal())
                                        <div class="alert alert-danger py-2 mb-3">
                                            <small>
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                {{ $rekapan->catatan_pengiriman }}
                                            </small>
                                        </div>
                                    @endif

                                    {{-- Action Buttons --}}
                                    <div class="d-grid gap-2">
                                        @if($rekapan->isBelumDikirim() || $rekapan->isGagal())
                                            @if($rekapan->wa_link)
                                                <a href="{{ $rekapan->wa_link }}"
                                                   target="_blank"
                                                   class="btn btn-success btn-sm wa-link"
                                                   data-rekapan-id="{{ $rekapan->id }}"
                                                   onclick="markAsClicked({{ $rekapan->id }})">
                                                    <i class="fab fa-whatsapp me-1"></i> Kirim via WhatsApp
                                                </a>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="fas fa-exclamation-triangle me-1"></i> No HP Tidak Valid
                                                </button>
                                            @endif
                                        @endif

                                        <a href="{{ route('guru.rekapan.show', $rekapan->id) }}"
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye me-1"></i> Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Empty State --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-custom">
                            <div class="card-body">
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak Ada Rekapan</h5>
                                    <p class="text-muted">
                                        @if($statusFilter !== 'all')
                                            Tidak ada rekapan dengan status "{{ ucfirst(str_replace('_', ' ', $statusFilter)) }}" untuk tanggal ini.
                                        @else
                                            Belum ada rekapan untuk tanggal ini.<br>
                                            Pastikan absensi sudah diselesaikan.
                                        @endif
                                    </p>
                                    <a href="{{ route('guru.rekapan.kirim', ['tanggal' => $tanggal, 'status' => 'all']) }}"
                                       class="btn btn-primary me-2">
                                        <i class="fas fa-filter me-1"></i> Lihat Semua Status
                                    </a>
                                    <a href="{{ route('guru.rekapan.dashboard') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
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

        .card-rekapan {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card-rekapan:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .card-rekapan.card-sent {
            background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
            border: 2px solid #48bb78;
        }

        .card-rekapan .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
        }

        .card-sent .card-header {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        }

        .rekapan-preview {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #667eea;
        }

        .preview-item {
            margin-bottom: 10px;
        }

        .preview-item:last-child {
            margin-bottom: 0;
        }

        .preview-item p {
            font-size: 0.9rem;
            color: #4a5568;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #2d3748;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        .progress {
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }

        .opened {
            opacity: 0.7;
            position: relative;
        }

        .opened::after {
            content: "✓ Dibuka";
            position: absolute;
            top: 10px;
            right: 10px;
            background: #48bb78;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: bold;
            z-index: 10;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Mark sebagai clicked (dibuka)
        function markAsClicked(rekapanId) {
            // AJAX request untuk update status
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
                    // Visual feedback
                    const card = document.getElementById(`card-${rekapanId}`);
                    if (card) {
                        card.classList.add('card-sent');

                        // Update badge
                        const badge = card.querySelector('.badge');
                        if (badge) {
                            badge.className = 'badge bg-success';
                            badge.textContent = 'Sudah Dikirim';
                        }
                    }

                    console.log('Rekapan ditandai sebagai dikirim');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Buka semua WA dengan delay
        function bukaSemuaWA() {
            const waLinks = document.querySelectorAll('.wa-link');

            if (waLinks.length === 0) {
                Swal.fire('Info', 'Tidak ada rekapan yang perlu dikirim', 'info');
                return;
            }

            Swal.fire({
                title: 'Buka Semua WhatsApp?',
                html: `Akan membuka <strong>${waLinks.length}</strong> tab WhatsApp.<br>
                       Proses memakan waktu <strong>${(waLinks.length * 5)} detik</strong>.<br><br>
                       <small class="text-muted">Anda tetap harus klik "Send" di setiap chat WhatsApp</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Buka Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    let delay = 0;
                    let opened = 0;

                    Swal.fire({
                        title: 'Membuka WhatsApp...',
                        html: `Dibuka: <strong id="counter">0</strong> / ${waLinks.length}`,
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    waLinks.forEach((link, index) => {
                        setTimeout(() => {
                            // Buka tab baru
                            window.open(link.href, '_blank');

                            // Mark as clicked
                            const rekapanId = link.dataset.rekapanId;
                            markAsClicked(rekapanId);

                            // Visual feedback
                            link.closest('.card-rekapan').classList.add('opened');

                            // Update counter
                            opened++;
                            const counter = document.getElementById('counter');
                            if (counter) {
                                counter.textContent = opened;
                            }

                            // Jika selesai semua
                            if (opened === waLinks.length) {
                                setTimeout(() => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Selesai!',
                                        text: `${waLinks.length} tab WhatsApp berhasil dibuka`,
                                        timer: 3000
                                    }).then(() => {
                                        location.reload();
                                    });
                                }, 1000);
                            }
                        }, delay);

                        delay += 5000; // 5 detik per link
                    });
                }
            });
        }

        // Refresh page
        function refreshPage() {
            location.reload();
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
