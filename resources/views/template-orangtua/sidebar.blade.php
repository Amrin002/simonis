<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-graduation-cap"></i> Admin Panel
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('orangtua.dashboard') }}"
                class="menu-link {{ request()->routeIs('orangtua.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('orangtua.profil') }}"
                class="menu-link {{ request()->routeIs('orangtua.profil') ? 'active' : '' }}">
                <i class="fas fa-user"></i> Profil Orang Tua
            </a>
        </li>
    </ul>
</div>
