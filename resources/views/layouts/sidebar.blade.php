<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-graduation-cap"></i>
        {{ Auth::user()->guru->roleLabel }}
    </div>
    <ul class="sidebar-menu">
        {{-- Dashboard - Untuk semua guru --}}
        <li>
            <a href="{{ route('guru.dashboard') }}"
                class="menu-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('guru.absensi.index') }}"
                class="menu-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i> Absensi
            </a>
        </li>

        {{-- Menu untuk Guru Mapel --}}
        @if(Auth::user()->guru->isGuruMapel())
            <li class="menu-header">
                <span>GURU MAPEL</span>
            </li>
            <li>
                <a href="{{ route('guru.jadwal.index') }}"
                    class="menu-link {{ request()->routeIs('guru.jadwal-mengajar') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i> Jadwal Mengajar
                </a>
            </li>
            <li>
                <a href="{{ route('guru.daftar-siswa') }}"
                    class="menu-link {{ request()->routeIs('guru.daftar-siswa') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i> Daftar Siswa
                </a>
            </li>
            <li>
                <a href="#" class="menu-link {{ request()->routeIs('guru.nilai.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> Input Nilai
                </a>
            </li>
        @endif

        {{-- Menu untuk Wali Kelas --}}
        @if(Auth::user()->guru->isWaliKelas())
            <li class="menu-header">
                <span>WALI KELAS</span>
            </li>
            <li>
                <a href="{{ route('guru.kelas-wali') }}"
                    class="menu-link {{ request()->routeIs('guru.kelas-wali') ? 'active' : '' }}">
                    <i class="fas fa-door-open"></i> Kelas {{ Auth::user()->guru->namaKelasWali }}
                </a>
            </li>
            <li>
                <a href="#" class="menu-link {{ request()->routeIs('guru.absensi.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-check"></i> Absensi Kelas
                </a>
            </li>
            <li>
                <a href="#" class="menu-link {{ request()->routeIs('guru.pelanggaran.*') ? 'active' : '' }}">
                    <i class="fas fa-exclamation-triangle"></i> Pelanggaran Siswa
                </a>
            </li>
            <li>
                <a href="#" class="menu-link {{ request()->routeIs('guru.rapor.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> Rapor Siswa
                </a>
            </li>
        @endif

        {{-- Menu umum --}}
        <li class="menu-header">
            <span>UMUM</span>
        </li>
        <li>
            <a href="#" class="menu-link {{ request()->routeIs('guru.profil') ? 'active' : '' }}">
                <i class="fas fa-user"></i> Profil Saya
            </a>
        </li>

    </ul>
</div>

<style>
    .menu-header {
        padding: 15px 20px 10px;
        margin-top: 10px;
    }

    .menu-header span {
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .menu-link {
        display: block;
        padding: 12px 20px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s;
    }

    .menu-link:hover,
    .menu-link.active {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        padding-left: 30px;
    }

    .menu-link i {
        margin-right: 10px;
        width: 20px;
    }
</style>
