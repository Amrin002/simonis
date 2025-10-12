<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-graduation-cap"></i> Admin Panel
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('admin.dashboard') }}"
                class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('admin.siswa.index') }}"
                class="menu-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i> Kelola Siswa
            </a>
        </li>
        <li>
            <a href="{{ route('admin.guru.index') }}"
                class="menu-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
                <i class="fas fa-chalkboard-teacher"></i> Kelola Guru
            </a>
        </li>
        <li>
            <a href="{{ route('admin.mapel.index') }}" class="menu-link {{ request()->routeIs('admin.mapel.*') ? 'active' : '' }}">
                <i class="fas fa-book"></i> Kelola Mata Pelajaran
            </a>
        </li>
        <li>
            <a href="{{ route('admin.kelas.index') }}"
                class="menu-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                <i class="fas fa-door-open"></i> Kelola Kelas
            </a>
        </li>
        <li>
            <a href="{{ route('admin.jadwal.index') }}"
                class="menu-link {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Kelola Jadwal
            </a>
        </li>
        <li>
            <a href="{{ route('admin.orangtua.index') }}"
                class="menu-link {{ request()->routeIs('admin.orangtua.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Kelola Orang Tua
            </a>
        </li>
        <li>
            <a href="#" class="menu-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Laporan
            </a>
        </li>
        <li>
            <a href="#" class="menu-link {{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> Pengaturan
            </a>
        </li>
    </ul>
</div>
