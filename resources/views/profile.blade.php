@extends('layouts.feature')

@section('title', 'Profil Akademik')

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="sp-wrapper">
    {{-- Profile Header Card --}}
    <div class="sp-card sp-header-card">
        {{-- Cover Banner --}}
        <div class="sp-cover">
            <div class="sp-cover-overlay"></div>
            <div class="sp-cover-pattern"></div>
            @if($user->isIdentityVerified())
            <span class="sp-verified-flag">
                <span class="material-symbols-outlined">shield</span> Terverifikasi
            </span>
            @endif
        </div>

        {{-- Profile Info Section --}}
        <div class="sp-profile-body">
            <div class="sp-profile-top">
                {{-- Avatar --}}
                <div class="sp-avatar-wrap">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="sp-avatar-img">
                    @if($user->isIdentityVerified())
                    <div class="sp-avatar-check" title="Identitas Terverifikasi">
                        <span class="material-symbols-outlined">check_circle</span>
                    </div>
                    @endif
                </div>

                {{-- Name & Info --}}
                <div class="sp-info-block">
                    <div class="sp-info-main">
                        <div>
                            <h2 class="sp-user-name">{{ $user->name }}</h2>
                            <div class="sp-user-meta">
                                @if($user->universitas)
                                <div class="sp-meta-item">
                                    <span class="material-symbols-outlined">school</span>
                                    <span>{{ $user->universitas }}</span>
                                </div>
                                <span class="sp-meta-dot">•</span>
                                @endif
                                <span>{{ $user->jurusan ?? '-' }}</span>
                            </div>
                            <div class="sp-badges">
                                <span class="sp-badge sp-badge-role">{{ $user->role_badge }}</span>
                                @if($user->isAgent())
                                <span class="sp-badge sp-badge-agent">
                                    <span class="material-symbols-outlined">admin_panel_settings</span> CIVIC Agent
                                </span>
                                @endif
                                @if($user->isIdentityVerified())
                                <span class="sp-badge sp-badge-verified">
                                    <span class="material-symbols-outlined">verified</span> KYA Verified
                                </span>
                                @endif
                            </div>
                            @if($user->nim_nidn)
                            <div class="sp-nim-display">
                                <span class="material-symbols-outlined" style="font-size:14px">badge</span>
                                {{ $user->identity_card_type === 'ktd' ? 'NIDN' : 'NIM' }}: {{ $user->nim_nidn }}
                            </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="sp-actions">
                            @if(!$user->isIdentityVerified() && !$user->isAnonim() && !$user->isAgent())
                            <a href="{{ route('identity.verify') }}" class="sp-btn sp-btn-verify">
                                <span class="material-symbols-outlined">verified_user</span> Verifikasi Identitas
                            </a>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="sp-btn sp-btn-primary">
                                <span class="material-symbols-outlined">edit</span> Edit Profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if($user->bio)
            <p class="sp-bio">{{ $user->bio }}</p>
            @endif

            {{-- Stats Grid --}}
            <div class="sp-stats-grid">
                <div class="sp-stat-item">
                    <span class="sp-stat-label">Postingan</span>
                    <div class="sp-stat-row">
                        <span class="sp-stat-value">{{ $user->posts()->count() }}</span>
                    </div>
                    <span class="sp-stat-sub">Total Konten</span>
                </div>
                <div class="sp-stat-item">
                    <span class="sp-stat-label">Fact-Check</span>
                    <div class="sp-stat-row">
                        <span class="sp-stat-value">{{ $user->posts()->where('category', 'fact-check')->count() }}</span>
                    </div>
                    <span class="sp-stat-sub">Verifikasi Fakta</span>
                </div>
                <div class="sp-stat-item">
                    <span class="sp-stat-label">Vote Diberikan</span>
                    <div class="sp-stat-row">
                        <span class="sp-stat-value">{{ $user->votes()->count() }}</span>
                    </div>
                    <span class="sp-stat-sub">Partisipasi</span>
                </div>
                <div class="sp-stat-item">
                    <span class="sp-stat-label">Risalah Kebijakan</span>
                    <div class="sp-stat-row">
                        <span class="sp-stat-value">{{ $user->policyBriefs()->count() }}</span>
                    </div>
                    <span class="sp-stat-sub">Analisis Dipublikasi</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Section --}}
    <div class="sp-tabs-section">
        <div class="sp-tabs-bar">
            <nav class="sp-tabs-nav">
                <button class="sp-tab active" data-tab="posts">Postingan Terbaru</button>
                <button class="sp-tab" data-tab="activity">Aktivitas</button>
            </nav>
        </div>
        <div class="sp-tab-content">
            {{-- Posts Tab --}}
            <div class="sp-tab-pane active" id="tab-posts">
                @forelse($user->posts()->latest()->take(5)->get() as $post)
                <div class="sp-post-item">
                    <div class="sp-post-header">
                        <div class="sp-post-author-icon">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                        <span class="sp-post-author-name">{{ $user->name }}</span>
                        <span class="sp-post-time">• {{ $post->created_at->diffForHumans() }}</span>
                        @if($post->isFactCheck() && $post->status === 'approved')
                        <span class="sp-post-verdict sp-verdict-valid">
                            <span class="material-symbols-outlined">check_circle</span> VALID
                        </span>
                        @endif
                    </div>
                    <h4 class="sp-post-title">{{ Str::limit($post->body, 120) }}</h4>
                    <div class="sp-post-meta-row">
                        <span class="sp-post-category">{{ ucfirst(str_replace('-', ' ', $post->category)) }}</span>
                    </div>
                    <div class="sp-post-actions">
                        <button class="sp-post-action">
                            <span class="material-symbols-outlined">thumb_up</span> {{ $post->votes()->count() }}
                        </button>
                        <button class="sp-post-action">
                            <span class="material-symbols-outlined">chat_bubble</span> Diskusi
                        </button>
                    </div>
                </div>
                @empty
                <div class="sp-empty-state">
                    <span class="material-symbols-outlined">article</span>
                    <p>Belum ada postingan.</p>
                </div>
                @endforelse
            </div>

            {{-- Activity Tab --}}
            <div class="sp-tab-pane" id="tab-activity">
                <div class="sp-empty-state">
                    <span class="material-symbols-outlined">timeline</span>
                    <p>Log aktivitas akan ditampilkan di sini.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.sp-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.sp-tab').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.sp-tab-pane').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
    });
});
</script>
@endpush
@endsection
