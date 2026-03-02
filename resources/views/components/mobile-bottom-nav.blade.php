{{-- Mobile Bottom Navigation — Instagram-style, visible ≤ 768px --}}
<nav class="mobile-bottom-nav" id="mobile-bottom-nav">
    <a href="{{ route('home') }}" class="mob-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
        <span class="material-symbols-outlined">home</span>
        <span class="mob-nav-label">Beranda</span>
    </a>

    @if(!Auth::check() || !Auth::user()->isAnonim())
    <a href="{{ route('lab-room.index') }}" class="mob-nav-item {{ request()->routeIs('lab-room*') ? 'active' : '' }}">
        <span class="material-symbols-outlined">groups</span>
        <span class="mob-nav-label">L.A.B</span>
    </a>
    <a href="{{ route('hoax-buster') }}" class="mob-nav-item {{ request()->routeIs('hoax-buster*') ? 'active' : '' }}">
        <span class="material-symbols-outlined">gpp_maybe</span>
        <span class="mob-nav-label">Hoax</span>
    </a>
    @endif

    <a href="{{ route('policy-lab.index') }}" class="mob-nav-item {{ request()->routeIs('policy-lab*') ? 'active' : '' }}">
        <span class="material-symbols-outlined">policy</span>
        <span class="mob-nav-label">Policy</span>
    </a>

    @auth
    @if(!Auth::user()->isAnonim())
    {{-- More menu for authenticated users --}}
    <button class="mob-nav-item mob-nav-more-btn {{ request()->routeIs('profile') || request()->routeIs('profile.edit') || request()->routeIs('moderation.*') ? 'active' : '' }}" id="mob-nav-more-btn" type="button">
        <span class="material-symbols-outlined">menu</span>
        <span class="mob-nav-label">Lainnya</span>
    </button>
    @else
    {{-- Anonymous: just show login/profile link --}}
    <a href="{{ route('profile') }}" class="mob-nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
        <span class="material-symbols-outlined">person</span>
        <span class="mob-nav-label">Profil</span>
    </a>
    @endif
    @else
    <a href="{{ route('login') }}" class="mob-nav-item">
        <span class="material-symbols-outlined">login</span>
        <span class="mob-nav-label">Masuk</span>
    </a>
    @endauth
</nav>

{{-- More menu popup --}}
@auth
@if(!Auth::user()->isAnonim())
<div class="mob-nav-more-menu" id="mob-nav-more-menu">
    <div class="mob-more-overlay" id="mob-more-overlay"></div>
    <div class="mob-more-sheet">
        <div class="mob-more-header">
            <span class="mob-more-title">Menu Lainnya</span>
            <button class="mob-more-close" id="mob-more-close" type="button">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="mob-more-items">
            <a href="{{ route('profile') }}" class="mob-more-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                <span class="material-symbols-outlined">person</span>
                <span>Profil Saya</span>
            </a>

            @if(Auth::user()->isAgent())
            <a href="{{ route('moderation.index') }}" class="mob-more-item {{ request()->routeIs('moderation.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined">admin_panel_settings</span>
                <span>Moderasi</span>
                @php
                    $pendingCount = \App\Models\Post::where('status', 'pending')->count()
                        + \App\Models\PolicyBrief::where('status', 'pending')->count()
                        + \App\Models\HoaxClaim::where('status', 'pending')->count()
                        + \App\Models\HoaxVerdict::where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="mob-more-badge">{{ $pendingCount }}</span>
                @endif
            </a>
            @endif

            <a href="{{ route('profile.edit') }}" class="mob-more-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <span class="material-symbols-outlined">settings</span>
                <span>Pengaturan</span>
            </a>

            <div class="mob-more-divider"></div>

            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="mob-more-item mob-more-logout">
                    <span class="material-symbols-outlined">logout</span>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endif
@endauth

<script>
(function() {
    const moreBtn = document.getElementById('mob-nav-more-btn');
    const moreMenu = document.getElementById('mob-nav-more-menu');
    const overlay = document.getElementById('mob-more-overlay');
    const closeBtn = document.getElementById('mob-more-close');
    if (!moreBtn || !moreMenu) return;

    function openMore() {
        moreMenu.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeMore() {
        moreMenu.classList.remove('open');
        document.body.style.overflow = '';
    }

    moreBtn.addEventListener('click', openMore);
    if (overlay) overlay.addEventListener('click', closeMore);
    if (closeBtn) closeBtn.addEventListener('click', closeMore);

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && moreMenu.classList.contains('open')) closeMore();
    });
})();
</script>
