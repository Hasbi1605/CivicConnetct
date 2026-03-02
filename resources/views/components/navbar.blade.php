<header class="stitch-header">
    <div class="stitch-header-inner">
        {{-- Back button + Logo --}}
        <div class="stitch-header-left">
            @if(isset($backUrl) && $backUrl)
            <a href="{{ $backUrl }}" class="stitch-back-btn" title="{{ $backLabel ?? 'Kembali' }}">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <span class="stitch-back-divider"></span>
            @endif
            <a href="{{ route('home') }}" class="stitch-logo-link">
                <div class="stitch-logo-icon">
                    <span class="material-symbols-outlined">balance</span>
                </div>
                <h1 class="stitch-logo-text">CIVIC-Connect</h1>
            </a>
        </div>

        {{-- Search --}}
        <div class="stitch-header-search">
            <div class="stitch-search-box">
                <span class="material-symbols-outlined stitch-search-icon">search</span>
                <input type="text" placeholder="Cari DOI, Topik, atau Risalah Kebijakan..." class="stitch-search-input">
                <kbd class="stitch-search-kbd">⌘K</kbd>
            </div>
        </div>

        {{-- Right actions --}}
        <div class="stitch-header-right">
            @auth
            @if(!Auth::user()->isAnonim())
            {{-- Notification bell --}}
            <div class="notification-dropdown-wrapper">
                <button class="stitch-header-icon-btn notification-bell-btn" id="notification-bell" title="Notifikasi">
                    <span class="material-symbols-outlined">notifications</span>
                    @php $unreadCount = Auth::user()->unreadNotificationsCount(); @endphp
                    @if($unreadCount > 0)
                        <span class="stitch-notif-dot"></span>
                    @endif
                </button>
                <div class="notification-dropdown" id="notification-dropdown">
                    <div class="notification-dropdown-header">
                        <strong>Notifikasi</strong>
                        @if($unreadCount > 0)
                        <button class="mark-all-read-btn" id="mark-all-read-btn">Tandai semua dibaca</button>
                        @endif
                    </div>
                    <div class="notification-list" id="notification-list">
                        <div class="notification-loading">Memuat...</div>
                    </div>
                </div>
            </div>

            {{-- Mail icon --}}
            <button class="stitch-header-icon-btn" title="Pesan">
                <span class="material-symbols-outlined">mail</span>
            </button>

            <div class="stitch-header-divider"></div>
            @endif

            {{-- Profile --}}
            <div class="profile-dropdown">
                <div class="stitch-header-profile-trigger profile-trigger">
                    <div class="stitch-header-avatar">
                        <img src="{{ Auth::user()->avatar_url }}" alt="Profile">
                    </div>
                    <div class="stitch-header-user-info">
                        <p class="stitch-header-user-name">{{ Str::limit(Auth::user()->name, 16) }}</p>
                        <p class="stitch-header-user-uni">{{ Auth::user()->universitas ?? '' }}</p>
                    </div>
                    <span class="material-symbols-outlined stitch-header-expand">expand_more</span>
                </div>
                <div class="dropdown-menu">
                    @if(!Auth::user()->isAnonim())
                    <a href="{{ route('profile') }}">Profil Saya</a>
                    <a href="{{ route('profile.edit') }}">Edit Profil</a>
                    @if(Auth::user()->isAgent())
                    <a href="{{ route('moderation.index') }}">Moderasi</a>
                    @endif
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-link">Keluar</button>
                    </form>
                </div>
            </div>
            @else
            <a href="{{ route('login') }}" class="btn-outline" style="font-size:14px;">Masuk</a>
            @endauth
        </div>
    </div>
</header>

@auth
@push('scripts')
<script>
// Notification bell toggle
const notifBell = document.getElementById('notification-bell');
const notifDropdown = document.getElementById('notification-dropdown');
const notifList = document.getElementById('notification-list');

notifBell?.addEventListener('click', async (e) => {
    e.stopPropagation();
    const isOpen = notifDropdown.classList.toggle('show');
    if (isOpen) {
        await loadNotifications();
    }
});

document.addEventListener('click', (e) => {
    if (!e.target.closest('.notification-dropdown-wrapper')) {
        notifDropdown?.classList.remove('show');
    }
});

async function loadNotifications() {
    try {
        const res = await fetch('/notifications', {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.length === 0) {
            notifList.innerHTML = '<div class="notification-empty">Belum ada notifikasi</div>';
            return;
        }

        notifList.innerHTML = data.map(n => {
            const iconType = n.type === 'post_approved' ? 'approved' :
                             n.type === 'post_rejected' ? 'rejected' :
                             n.type === 'warning' ? 'warning' : 'info';
            const iconSvg = n.type === 'post_approved'
                ? '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>'
                : n.type === 'post_rejected'
                ? '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>'
                : '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>';

            const timeAgo = n.created_at_human || '';

            return `<div class="notification-item ${n.is_read ? '' : 'unread'}" data-id="${n.id}" onclick="markNotifRead(${n.id})">
                <div class="notification-icon ${iconType}">${iconSvg}</div>
                <div class="notification-text">
                    <div class="notification-title">${n.title}</div>
                    <div class="notification-message">${n.message}</div>
                    <div class="notification-time">${timeAgo}</div>
                </div>
            </div>`;
        }).join('');
    } catch (err) {
        notifList.innerHTML = '<div class="notification-empty">Gagal memuat notifikasi</div>';
    }
}

async function markNotifRead(id) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    await fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    });
    const item = document.querySelector(`.notification-item[data-id="${id}"]`);
    if (item) item.classList.remove('unread');
    updateBadge();
}

document.getElementById('mark-all-read-btn')?.addEventListener('click', async () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    await fetch('/notifications/read-all', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    });
    document.querySelectorAll('.notification-item.unread').forEach(el => el.classList.remove('unread'));
    updateBadge();
});

function updateBadge() {
    const unread = document.querySelectorAll('.notification-item.unread').length;
    const badge = notifBell?.querySelector('.notification-badge');
    if (unread > 0) {
        if (badge) {
            badge.textContent = unread;
        } else {
            const b = document.createElement('span');
            b.className = 'notification-badge';
            b.textContent = unread;
            notifBell.appendChild(b);
        }
    } else {
        badge?.remove();
    }
}
</script>
@endpush
@endauth
