{{-- Left sidebar for feature pages — matches Stitch "Academic Professional" design --}}
<aside class="feat-sidebar expanded" id="feat-sidebar">
    {{-- Header with toggle --}}
    <div class="feat-sidebar-header">
        <span class="feat-sidebar-title">Navigasi Utama</span>
        <button class="feat-sidebar-toggle" id="feat-sidebar-toggle" title="Toggle sidebar">
            <span class="material-symbols-outlined">menu</span>
        </button>
    </div>

    {{-- Profile Card --}}
    @auth
    @if(!Auth::user()->isAnonim())
    <div class="feat-profile-card">
        <div class="feat-profile-top">
            <div class="feat-profile-avatar-wrap">
                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="feat-profile-avatar-img">
            </div>
            <h2 class="feat-profile-name">{{ Auth::user()->name }}</h2>
            <div class="feat-profile-meta">
                <span class="feat-profile-dept">{{ Auth::user()->jurusan ?? 'Mahasiswa' }}</span>
            </div>
        </div>
        <div class="feat-profile-stats">
            <div class="feat-profile-stat">
                <div class="feat-stat-value">{{ Auth::user()->posts()->count() }}</div>
                <div class="feat-stat-label">Postingan</div>
            </div>
            <div class="feat-stat-divider"></div>
            <div class="feat-profile-stat">
                <div class="feat-stat-value feat-stat-text">{{ Auth::user()->role_badge ?? 'Mahasiswa' }}</div>
            </div>
        </div>
    </div>
    @endif
    @endauth

    {{-- Navigation --}}
    <nav class="feat-sidebar-nav">
        <a href="{{ route('home') }}" class="feat-nav-item {{ request()->routeIs('home') ? 'active' : '' }}" title="Beranda">
            <span class="material-symbols-outlined">home</span>
            <span class="feat-nav-label">Beranda</span>
        </a>
        @if(!Auth::check() || !Auth::user()->isAnonim())
        <a href="{{ route('lab-room.index') }}" class="feat-nav-item {{ request()->routeIs('lab-room*') ? 'active' : '' }}" title="L.A.B Room">
            <span class="material-symbols-outlined">groups</span>
            <span class="feat-nav-label">L.A.B Room</span>
        </a>
        <a href="{{ route('hoax-buster') }}" class="feat-nav-item {{ request()->routeIs('hoax-buster*') ? 'active' : '' }}" title="Hoax Buster">
            <span class="material-symbols-outlined">gpp_maybe</span>
            <span class="feat-nav-label">Hoax Buster</span>
        </a>
        @endif
        <a href="{{ route('policy-lab.index') }}" class="feat-nav-item {{ request()->routeIs('policy-lab*') ? 'active' : '' }}" title="Policy Lab">
            <span class="material-symbols-outlined">policy</span>
            <span class="feat-nav-label">Policy Lab</span>
        </a>

        @if(Auth::check() && Auth::user()->isAgent())
        <div class="feat-nav-divider"></div>
        <a href="{{ route('moderation.index') }}" class="feat-nav-item {{ request()->routeIs('moderation.*') ? 'active' : '' }}" title="Moderasi">
            <span class="material-symbols-outlined">admin_panel_settings</span>
            <span class="feat-nav-label">Moderasi</span>
            @php
                $pendingCount = \App\Models\Post::where('status', 'pending')->count()
                    + \App\Models\PolicyBrief::where('status', 'pending')->count()
                    + \App\Models\HoaxClaim::where('status', 'pending')->count()
                    + \App\Models\HoaxVerdict::where('status', 'pending')->count();
            @endphp
            @if($pendingCount > 0)
                <span class="feat-nav-badge">{{ $pendingCount }}</span>
            @endif
        </a>
        @endif
    </nav>

    {{-- Bottom --}}
    <div class="feat-sidebar-bottom">
        @if(!Auth::check() || !Auth::user()->isAnonim())
        <a href="{{ route('profile') }}" class="feat-nav-item {{ request()->routeIs('profile') ? 'active' : '' }}" title="Profil">
            <span class="material-symbols-outlined">person</span>
            <span class="feat-nav-label">Profil</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="feat-nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" title="Pengaturan">
            <span class="material-symbols-outlined">settings</span>
            <span class="feat-nav-label">Pengaturan</span>
        </a>
        @endif
        @auth
        <div class="feat-nav-divider"></div>
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="feat-nav-item feat-nav-logout" title="Keluar">
                <span class="material-symbols-outlined">logout</span>
                <span class="feat-nav-label">Keluar</span>
            </button>
        </form>
        @endauth
    </div>
</aside>

<script>
(function() {
    const sidebar = document.getElementById('feat-sidebar');
    const toggle = document.getElementById('feat-sidebar-toggle');
    if (!sidebar || !toggle) return;

    // Restore saved state (default: expanded)
    const saved = localStorage.getItem('feat-sidebar-expanded');
    if (saved === '0') {
        sidebar.classList.remove('expanded');
        sidebar.classList.add('collapsed');
    }

    toggle.addEventListener('click', function() {
        const isExpanded = sidebar.classList.contains('expanded');
        if (isExpanded) {
            sidebar.classList.remove('expanded');
            sidebar.classList.add('collapsed');
            localStorage.setItem('feat-sidebar-expanded', '0');
        } else {
            sidebar.classList.remove('collapsed');
            sidebar.classList.add('expanded');
            localStorage.setItem('feat-sidebar-expanded', '1');
        }
    });
})();
</script>
