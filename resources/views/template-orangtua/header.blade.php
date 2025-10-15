<div class="top-navbar">
    <div class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </div>
    <div class="user-info">
        <div class="user-dropdown">
            <div class="user-info-clickable d-flex align-items-center gap-2" id="userDropdownToggle">
                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <i class="fas fa-chevron-down d-none d-md-inline" style="font-size: 0.8rem;"></i>
            </div>
            <div class="dropdown-menu-custom" id="userDropdownMenu">
                <a href="#" class="dropdown-item-custom"
                    onclick="showModal('modalUpdateProfile'); closeUserDropdown(); return false;">
                    <i class="fas fa-user-edit"></i> Update Profile
                </a>
                <a href="#" class="dropdown-item-custom"
                    onclick="showModal('modalChangePassword'); closeUserDropdown(); return false;">
                    <i class="fas fa-key"></i> Ganti Password
                </a>
                <div class="dropdown-divider-custom"></div>
                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                    @csrf
                    <button type="button" class="dropdown-item-custom w-100 text-start border-0 bg-transparent"
                        onclick="confirmLogout()">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
