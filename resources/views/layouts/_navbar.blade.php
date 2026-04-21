<style>
/* ── DHIS2-style top navbar ── */
#main-navbar {
    background: #1c3561;
    height: 48px;
    z-index: 1030;
    flex-shrink: 0;
}
.nb-icon-btn {
    color: rgba(255,255,255,.72) !important;
    border-radius: 6px;
    transition: background .15s, color .15s;
    line-height: 1;
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
}
.nb-icon-btn:hover,
.nb-icon-btn:focus,
.nb-icon-btn[aria-expanded="true"] {
    background: rgba(255,255,255,.16) !important;
    color: #fff !important;
    box-shadow: none !important;
}
/* App launcher grid */
.app-launcher-menu {
    width: 330px;
    border-radius: .75rem;
    border: none;
    margin-top: 4px !important;
}
.app-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: .55rem .3rem;
    border-radius: .5rem;
    text-decoration: none;
    transition: background .12s;
}
.app-item:hover { background: #f1f3f5; }
.app-icon-box {
    width: 50px;
    height: 50px;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.45rem;
    margin-bottom: .35rem;
}
.app-item-label {
    color: #1a1a2e;
    font-size: .71rem;
    font-weight: 500;
    text-align: center;
    line-height: 1.25;
}
/* Profile avatar circle */
.nb-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255,255,255,.18);
    border: 1.5px solid rgba(255,255,255,.38);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: .78rem;
    font-weight: 600;
    flex-shrink: 0;
    overflow: hidden;
}
.nb-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}
/* Profile / app-launcher dropdown panels */
.nb-dropdown-menu {
    border: none;
    border-radius: .75rem;
    box-shadow: 0 8px 30px rgba(0,0,0,.14);
    margin-top: 4px !important;
}
</style>

<nav class="navbar py-0 px-3" id="main-navbar">

    {{-- ── Left: logo + dynamic context title ── --}}
    <div class="d-flex align-items-center h-100 flex-grow-1 overflow-hidden me-3">
        <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('clinical.dashboard') }}"
           class="text-white text-decoration-none d-flex align-items-center me-2" style="flex-shrink:0;">
            <i class="bi bi-hospital-fill" style="font-size:1.4rem;"></i>
        </a>
        <div class="vr me-2 opacity-25" style="height:22px; flex-shrink:0;"></div>
        <span class="text-white fw-semibold text-truncate" style="font-size:.875rem;" id="nb-context-title">
            {{ $navContextTitle }}
        </span>
    </div>

    {{-- ── Right: action icons ── --}}
    <div class="d-flex align-items-center gap-1">

        {{-- Messages (future) --}}
        <button class="btn nb-icon-btn px-2 py-1" title="Messages" disabled style="opacity:.4;">
            <i class="bi bi-chat-dots" style="font-size:1.1rem;"></i>
        </button>

        {{-- Notifications (future) --}}
        <button class="btn nb-icon-btn px-2 py-1" title="Notifications" disabled style="opacity:.4;">
            <i class="bi bi-bell" style="font-size:1.1rem;"></i>
        </button>

        {{-- ── App Launcher ── --}}
        <div class="dropdown">
            <button class="btn nb-icon-btn px-2 py-1"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false"
                    title="Applications">
                <i class="bi bi-grid-3x3-gap-fill" style="font-size:1.15rem;"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-end nb-dropdown-menu app-launcher-menu p-3">
                <p class="text-uppercase text-muted fw-semibold mb-2 px-1"
                   style="font-size:.62rem; letter-spacing:.08em;">Applications</p>

                <div class="row g-1">
                    {{-- Statistics --}}
                    <div class="col-4">
                        <a href="#" class="app-item">
                            <div class="app-icon-box" style="background:#e3f2fd; color:#1565c0;">
                                <i class="bi bi-bar-chart-line-fill"></i>
                            </div>
                            <span class="app-item-label">Statistics</span>
                        </a>
                    </div>

                    {{-- Reports --}}
                    <div class="col-4">
                        <a href="{{ route('reports.index') }}" class="app-item">
                            <div class="app-icon-box" style="background:#fff3e0; color:#e65100;">
                                <i class="bi bi-file-earmark-bar-graph-fill"></i>
                            </div>
                            <span class="app-item-label">Reports</span>
                        </a>
                    </div>

                    @if(Auth::user()->isAdmin())
                    {{-- Units --}}
                    <div class="col-4">
                        <a href="{{ route('admin.units.index') }}" class="app-item">
                            <div class="app-icon-box" style="background:#e8f5e9; color:#2e7d32;">
                                <i class="bi bi-building-fill"></i>
                            </div>
                            <span class="app-item-label">Units</span>
                        </a>
                    </div>

                    {{-- Staff --}}
                    <div class="col-4">
                        <a href="{{ route('admin.users.index') }}" class="app-item">
                            <div class="app-icon-box" style="background:#f3e5f5; color:#6a1b9a;">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <span class="app-item-label">Staff</span>
                        </a>
                    </div>

                    {{-- Customization --}}
                    <div class="col-4">
                        <a href="{{ route('admin.unit-templates.index') }}" class="app-item">
                            <div class="app-icon-box" style="background:#e0f7fa; color:#006064;">
                                <i class="bi bi-sliders2"></i>
                            </div>
                            <span class="app-item-label">Customization</span>
                        </a>
                    </div>
                    @endif

                    {{-- Profile Management --}}
                    <div class="col-4">
                        <a href="{{ route('profile.edit') }}" class="app-item">
                            <div class="app-icon-box" style="background:#fce4ec; color:#880e4f;">
                                <i class="bi bi-person-badge-fill"></i>
                            </div>
                            <span class="app-item-label">Profile Mgmt</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="vr mx-1 opacity-25" style="height:22px;"></div>

        {{-- ── Profile ── --}}
        <div class="dropdown">
            <button class="btn nb-icon-btn px-2 py-1 d-flex align-items-center"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    title="My Account">
                <div class="nb-avatar">
                    @if(Auth::user()->profileImageUrl())
                        <img src="{{ Auth::user()->profileImageUrl() }}" alt="{{ Auth::user()->name }}">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </div>
            </button>

            <div class="dropdown-menu dropdown-menu-end nb-dropdown-menu" style="min-width:230px;">
                {{-- User info header --}}
                <div class="px-3 py-2 border-bottom d-flex align-items-center gap-2">
                    <div class="flex-shrink-0" style="width:40px;height:40px;border-radius:50%;overflow:hidden;background:#e9ecef;display:flex;align-items:center;justify-content:center;font-weight:700;color:#6c757d;font-size:1rem;">
                        @if(Auth::user()->profileImageUrl())
                            <img src="{{ Auth::user()->profileImageUrl() }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-semibold text-truncate" style="font-size:.875rem;">
                            {{ Auth::user()->designation ? Auth::user()->designation . ' ' : '' }}{{ Auth::user()->name }}
                        </div>
                        <div class="text-muted text-truncate" style="font-size:.75rem;">{{ Auth::user()->email }}</div>
                        @if(Auth::user()->specialty)
                            <div class="text-muted text-truncate" style="font-size:.7rem;">{{ Auth::user()->specialty }}</div>
                        @elseif(Auth::user()->institution)
                            <div class="text-muted text-truncate" style="font-size:.7rem;">
                                <i class="bi bi-building me-1"></i>{{ Auth::user()->institution->name }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="py-1">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item d-flex align-items-center gap-2 py-2"
                       style="font-size:.875rem;">
                        <i class="bi bi-person-circle text-secondary"></i> Edit Profile
                    </a>
                    <div class="dropdown-divider my-1"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger"
                                style="font-size:.875rem;">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</nav>
