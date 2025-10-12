@extends('template.main')

@section('section')
    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="page-title">Dashboard Admin</h1>
                <p class="text-muted">Selamat datang di Sistem Monitoring Siswa</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary me-3">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $totalGuru }}</h3>
                            <p class="text-muted mb-0">Total Guru</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success me-3">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $totalWaliKelas }}</h3>
                            <p class="text-muted mb-0">Wali Kelas</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-danger me-3">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $totalKelas }}</h3>
                            <p class="text-muted mb-0">Total Kelas</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-warning me-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $totalSiswa }}</h3>
                            <p class="text-muted mb-0">Total Siswa</p>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-info me-3">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ \App\Models\OrangTua::count() }}</h3>
                            <p class="text-muted mb-0">Orang Tua</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Menu Cepat</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('admin.guru.index') }}" class="text-decoration-none">
                                    <div class="card border-primary h-100 hover-shadow">
                                        <div class="card-body text-center">
                                            <i class="fas fa-chalkboard-teacher fa-3x text-primary mb-3"></i>
                                            <h6 class="card-title">Kelola Guru</h6>
                                            <p class="card-text text-muted small">
                                                Tambah, edit, atau hapus data guru
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('admin.kelas.index') }}" class="text-decoration-none">
                                    <div class="card border-success h-100 hover-shadow">
                                        <div class="card-body text-center">
                                            <i class="fas fa-door-open fa-3x text-success mb-3"></i>
                                            <h6 class="card-title">Kelola Kelas</h6>
                                            <p class="card-text text-muted small">
                                                Atur kelas dan wali kelas
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('admin.siswa.index') }}" class="text-decoration-none">
                                    <div class="card border-warning h-100 hover-shadow">
                                        <div class="card-body text-center">
                                            <i class="fas fa-user-graduate fa-3x text-warning mb-3"></i>
                                            <h6 class="card-title">Kelola Siswa</h6>
                                            <p class="card-text text-muted small">
                                                Manajemen data siswa
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('admin.orangtua.index') }}" class="text-decoration-none">
                                    <div class="card border-info h-100 hover-shadow">
                                        <div class="card-body text-center">
                                            <i class="fas fa-user-friends fa-3x text-info mb-3"></i>
                                            <h6 class="card-title">Kelola Orang Tua</h6>
                                            <p class="card-text text-muted small">
                                                Manajemen data orang tua siswa
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('style')
        <style>
            .hover-shadow {
                transition: all 0.3s ease;
            }

            .hover-shadow:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                transform: translateY(-5px);
            }
        </style>
    @endpush
@endsection
