<aside class="stitch-left-sidebar">
    @auth
    {{-- Profile Card --}}
    <div class="stitch-card stitch-sidebar-profile">
        <div class="stitch-profile-top">
            <div class="stitch-profile-avatar-wrap">
                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="stitch-profile-avatar-img">
            </div>
            <h2 class="stitch-profile-name">{{ Auth::user()->name }}</h2>
            <div class="stitch-profile-meta">
                @if(Auth::user()->role_badge)
                    <span class="stitch-profile-role-tag {{ Auth::user()->role }}">{{ Auth::user()->role_badge }}</span>
                @endif
                <span class="stitch-profile-dept">{{ Auth::user()->jurusan ?? 'Mahasiswa' }}</span>
            </div>
        </div>
        <div class="stitch-profile-stats">
            <div class="stitch-stat-item">
                <div class="stitch-stat-value">{{ Auth::user()->posts()->count() }}</div>
                <div class="stitch-stat-label">Postingan</div>
            </div>
            <div class="stitch-stat-divider"></div>
            <div class="stitch-stat-item">
                <div class="stitch-stat-value stitch-text-verified">{{ Auth::user()->universitas ?? '-' }}</div>
                <div class="stitch-stat-label">Universitas</div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="stitch-card stitch-sidebar-nav">
        <a href="{{ route('home') }}" class="stitch-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('home') ? 'filled-icon' : '' }}">home</span>
            Beranda
        </a>
        <a href="{{ route('lab-room.index') }}" class="stitch-nav-item {{ request()->routeIs('lab-room*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">groups</span>
            L.A.B Room
        </a>
        <a href="{{ route('hoax-buster') }}" class="stitch-nav-item {{ request()->routeIs('hoax-buster*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">gpp_maybe</span>
            Hoax Buster
        </a>
        <a href="{{ route('policy-lab.index') }}" class="stitch-nav-item {{ request()->routeIs('policy-lab*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">policy</span>
            Policy Lab
        </a>

        @if(Auth::user()->isAgent())
        <div class="stitch-nav-divider"></div>
        <a href="{{ route('moderation.index') }}" class="stitch-nav-item {{ request()->routeIs('moderation.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined">admin_panel_settings</span>
            Moderasi
            @php
                $pendingCount = \App\Models\Post::where('status', 'pending')->count()
                    + \App\Models\PolicyBrief::where('status', 'pending')->count()
                    + \App\Models\HoaxClaim::where('status', 'pending')->count()
                    + \App\Models\HoaxVerdict::where('status', 'pending')->count();
            @endphp
            @if($pendingCount > 0)
                <span class="stitch-nav-badge">{{ $pendingCount }}</span>
            @endif
        </a>
        @endif

        <div class="stitch-nav-divider"></div>
        <a href="{{ route('profile.edit') }}" class="stitch-nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <span class="material-symbols-outlined">settings</span>
            Pengaturan
        </a>
    </nav>
    @endauth
</aside>
