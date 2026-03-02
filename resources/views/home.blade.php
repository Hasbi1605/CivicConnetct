@extends('layouts.feature')

@section('title', 'Beranda')

@section('content')
<div class="home-page">
    {{-- Feed Column --}}
    <div class="home-feed">

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif

{{-- Rejected posts warning --}}
@if($myRejectedPosts->count() > 0)
<div class="stitch-card stitch-rejected-section">
    <div class="stitch-section-header stitch-header-danger">
        <span class="material-symbols-outlined text-alert">warning</span>
        <strong>Postingan Ditolak ({{ $myRejectedPosts->count() }})</strong>
    </div>
    @foreach($myRejectedPosts as $rp)
    <div class="stitch-rejected-item">
        <p class="stitch-rejected-body">{{ Str::limit($rp->body, 100) }}</p>
        <p class="stitch-rejected-reason"><strong>Alasan:</strong> {{ $rp->rejection_reason }}</p>
        <span class="stitch-time-label">{{ $rp->reviewed_at?->diffForHumans() }}</span>
    </div>
    @endforeach
</div>
@endif

{{-- Pending posts info --}}
@if($myPendingPosts->count() > 0)
<div class="stitch-card stitch-pending-section">
    <div class="stitch-section-header stitch-header-warning">
        <span class="material-symbols-outlined text-warning-icon">schedule</span>
        <strong>Menunggu Peninjauan ({{ $myPendingPosts->count() }})</strong>
    </div>
    @foreach($myPendingPosts as $pp)
    <div class="stitch-pending-item">
        <span class="stitch-pending-badge">Pending</span>
        <p>{{ Str::limit($pp->body, 120) }}</p>
        <span class="stitch-time-label">Dikirim {{ $pp->created_at->diffForHumans() }}</span>
    </div>
    @endforeach
</div>
@endif

{{-- Create Post Form — Facebook-style: collapsed trigger → expanded form --}}
@if(!Auth::user()->isAnonim())
<div class="stitch-card stitch-create-post" id="create-post-composer">
    {{-- Collapsed trigger --}}
    <div class="scp-trigger" id="scp-trigger">
        <div class="scp-trigger-top">
            <div class="stitch-avatar-sm">
                <img src="{{ Auth::user()->avatar_url }}" alt="Profile">
            </div>
            <button type="button" class="scp-trigger-input" id="scp-trigger-btn">
                Mulai analisis baru atau verifikasi klaim...
            </button>
        </div>
        <div class="scp-trigger-actions">
            <button type="button" class="scp-trigger-action" data-expand="artikel">
                <span class="material-symbols-outlined" style="color:#0f4c81">article</span>
                <span>Artikel</span>
            </button>
            <span class="scp-trigger-divider"></span>
            <button type="button" class="scp-trigger-action" data-expand="fact-check">
                <span class="material-symbols-outlined" style="color:#7c3aed">fact_check</span>
                <span>Fact-Check</span>
            </button>
            <span class="scp-trigger-divider"></span>
            <button type="button" class="scp-trigger-action" data-expand="image">
                <span class="material-symbols-outlined" style="color:#059669">image</span>
                <span>Gambar</span>
            </button>
        </div>
    </div>

    {{-- Expanded form --}}
    <div class="scp-expanded" id="scp-expanded" style="display:none;">
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" id="create-post-form">
            @csrf
            {{-- Header with close button --}}
            <div class="scp-form-header">
                <div class="scp-form-user">
                    <div class="stitch-avatar-sm">
                        <img src="{{ Auth::user()->avatar_url }}" alt="Profile">
                    </div>
                    <div>
                        <div class="scp-user-name">{{ Auth::user()->name }}</div>
                        <div class="scp-user-category" id="scp-category-label">
                            <span class="material-symbols-outlined">article</span> Artikel
                        </div>
                    </div>
                </div>
                <button type="button" class="scp-close-btn" id="scp-close-btn" title="Tutup">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            {{-- Textarea --}}
            <textarea name="body" class="scp-textarea" id="scp-textarea" placeholder="Apa yang ingin Anda analisis atau bagikan?" rows="4" required></textarea>

            {{-- Image preview --}}
            <div class="stitch-image-preview" id="image-preview-container" style="display:none;">
                <img id="post-image-preview" alt="Preview">
                <button type="button" class="stitch-remove-btn" id="remove-image-btn">&times;</button>
            </div>

            {{-- Citations container --}}
            <div class="stitch-citations-input" id="citations-container" style="display:none;">
                <div class="stitch-citations-header">
                    <span class="material-symbols-outlined">format_quote</span>
                    <strong>Sitasi / Sumber</strong>
                </div>
                <div id="citations-list">
                    <div class="stitch-citation-row">
                        <input type="text" name="citation_texts[]" placeholder="Judul atau deskripsi sumber..." class="stitch-citation-text">
                        <input type="text" name="citation_urls[]" placeholder="https://doi.org/... atau URL sumber" class="stitch-citation-url">
                    </div>
                </div>
                <button type="button" class="stitch-add-citation-btn" id="add-citation-btn">
                    <span class="material-symbols-outlined">add</span> Tambah Sitasi Lain
                </button>
            </div>

            <input type="hidden" name="category" id="post-category" value="artikel">

            {{-- Toolbar --}}
            <div class="scp-toolbar">
                <span class="scp-toolbar-label">Tambahkan ke postingan</span>
                <div class="scp-toolbar-actions">
                    <button type="button" class="scp-tool-btn stitch-category-btn active" data-category="artikel" title="Artikel">
                        <span class="material-symbols-outlined">article</span>
                    </button>
                    <button type="button" class="scp-tool-btn stitch-category-btn" data-category="fact-check" title="Fact-Check">
                        <span class="material-symbols-outlined">fact_check</span>
                    </button>
                    <button type="button" class="scp-tool-btn" id="toggle-citations-btn" title="Tambah Sitasi">
                        <span class="material-symbols-outlined">add_link</span>
                    </button>
                    <label class="scp-tool-btn" title="Upload Gambar">
                        <span class="material-symbols-outlined">image</span>
                        <input type="file" name="image" id="post-image-input" accept="image/*" style="display:none;">
                    </label>
                </div>
            </div>

            {{-- Notice + Submit --}}
            <div class="scp-footer">
                <div class="scp-notice">
                    <span class="material-symbols-outlined">info</span>
                    Postingan akan ditinjau CIVIC Agent sebelum dipublikasikan
                </div>
                <button type="submit" class="scp-submit-btn">
                    <span class="material-symbols-outlined">send</span>
                    Kirim
                </button>
            </div>
        </form>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" style="margin: 8px 16px 16px;">
            @foreach($errors->all() as $error)
                <p style="margin:0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif
</div>

{{-- Composer expand/collapse script --}}
<script>
(function() {
    const composer = document.getElementById('create-post-composer');
    const trigger = document.getElementById('scp-trigger');
    const expanded = document.getElementById('scp-expanded');
    const triggerBtn = document.getElementById('scp-trigger-btn');
    const closeBtn = document.getElementById('scp-close-btn');
    const textarea = document.getElementById('scp-textarea');
    const categoryLabel = document.getElementById('scp-category-label');
    const categoryInput = document.getElementById('post-category');

    if (!composer || !trigger || !expanded) return;

    function openComposer(category) {
        trigger.style.display = 'none';
        expanded.style.display = 'block';
        if (category && category !== 'image') {
            setCategory(category);
        }
        if (category === 'image') {
            document.getElementById('post-image-input')?.click();
        }
        setTimeout(() => textarea.focus(), 100);
    }

    function closeComposer() {
        if (textarea.value.trim()) {
            if (!confirm('Postingan belum dikirim. Tutup tanpa menyimpan?')) return;
        }
        expanded.style.display = 'none';
        trigger.style.display = 'block';
        textarea.value = '';
    }

    function setCategory(cat) {
        categoryInput.value = cat;
        const icons = { 'artikel': 'article', 'fact-check': 'fact_check' };
        const labels = { 'artikel': 'Artikel', 'fact-check': 'Fact-Check' };
        categoryLabel.innerHTML = '<span class="material-symbols-outlined">' + (icons[cat] || 'article') + '</span> ' + (labels[cat] || 'Artikel');
        document.querySelectorAll('.scp-tool-btn.stitch-category-btn').forEach(b => {
            b.classList.toggle('active', b.dataset.category === cat);
        });
    }

    // Trigger clicks
    triggerBtn.addEventListener('click', () => openComposer(null));
    document.querySelectorAll('.scp-trigger-action').forEach(btn => {
        btn.addEventListener('click', () => openComposer(btn.dataset.expand));
    });

    // Close
    closeBtn.addEventListener('click', closeComposer);

    // Category buttons in expanded form
    document.querySelectorAll('.scp-tool-btn.stitch-category-btn').forEach(btn => {
        btn.addEventListener('click', () => setCategory(btn.dataset.category));
    });

    // Auto-open if there are validation errors
    @if($errors->any())
    openComposer(null);
    @endif
})();
</script>
@endif
{{-- Feed Label --}}
<div class="stitch-feed-label">
    <h3>Umpan L.A.B</h3>
    <div class="stitch-feed-sort">
        <span>Terbaru</span>
        <span class="material-symbols-outlined">sort</span>
    </div>
</div>

{{-- Feed Posts (approved only) --}}
@forelse($posts as $post)
<article class="stitch-card stitch-post {{ $post->isFactCheck() ? 'stitch-post-factcheck' : '' }}" data-post-id="{{ $post->id }}">

    <div class="stitch-post-header">
        <div class="stitch-post-author">
            <div class="stitch-avatar-sm">
                <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}">
            </div>
            <div>
                <div class="stitch-author-line">
                    <h4>{{ $post->user->name }}</h4>
                    @if($post->user->role_badge)
                        <span class="stitch-role-badge {{ $post->user->role }}">{{ $post->user->role_badge }}</span>
                    @endif
                </div>
                <div class="stitch-author-meta">{{ $post->user->jurusan }} · {{ $post->user->universitas }} · {{ $post->created_at->diffForHumans(null, true) }} lalu</div>
            </div>
        </div>
        <div class="stitch-post-badges">
            @if($post->isFactCheck())
                @php
                    $faktaCount = $post->faktaCount();
                    $hoaksCount = $post->hoaksCount();
                    $totalVotes = $faktaCount + $hoaksCount;
                    $userVote = $post->userVote(Auth::id());
                @endphp
                @if($totalVotes >= 3 && $hoaksCount > $faktaCount)
                    <span class="stitch-status-badge stitch-badge-danger">
                        <span class="material-symbols-outlined filled-icon">dangerous</span>
                        Hoaks
                    </span>
                @elseif($totalVotes >= 3 && $faktaCount > $hoaksCount)
                    <span class="stitch-status-badge stitch-badge-valid">
                        <span class="material-symbols-outlined filled-icon">check_circle</span>
                        Valid
                    </span>
                @else
                    <span class="stitch-status-badge stitch-badge-warning">
                        <span class="material-symbols-outlined">warning</span>
                        Perlu Verifikasi
                    </span>
                @endif
            @else
                <span class="stitch-status-badge stitch-badge-info">
                    <span class="material-symbols-outlined">article</span>
                    {{ ucfirst($post->category) }}
                </span>
            @endif
            {{-- 3-dot menu --}}
            @if(!Auth::user()->isAnonim() && (($post->user_id !== Auth::id() && !Auth::user()->isAgent()) || Auth::user()->isAgent()))
            <div class="post-more-menu">
                <button class="stitch-more-btn post-more-btn" title="Lainnya">
                    <span class="material-symbols-outlined">more_horiz</span>
                </button>
                <div class="post-more-dropdown">
                    @if(!Auth::user()->isAgent() && $post->user_id !== Auth::id())
                    <button class="report-post-btn" data-post-id="{{ $post->id }}">
                        <span class="material-symbols-outlined text-alert">flag</span>
                        Laporkan Hoaks
                    </button>
                    <button class="report-post-btn" data-post-id="{{ $post->id }}" data-reason="spam">
                        <span class="material-symbols-outlined text-warning-icon">report</span>
                        Laporkan Spam
                    </button>
                    @endif
                    @if(Auth::user()->isAgent())
                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="delete-post-form" onsubmit="return confirm('Apakah Anda yakin ingin menghapus postingan ini? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-post-btn">
                            <span class="material-symbols-outlined text-alert">delete</span>
                            Hapus Postingan
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="stitch-post-content">
        <p class="stitch-post-body">{{ $post->body }}</p>

        @if($post->image)
            <div class="stitch-post-image-wrap">
                <img src="{{ $post->image_url }}" alt="Post image" class="stitch-post-image">
            </div>
        @endif

        {{-- Citations block --}}
        @if($post->hasCitations())
        <div class="stitch-citation-block">
            <span class="material-symbols-outlined stitch-citation-icon">format_quote</span>
            <div class="stitch-citation-content">
                <p class="stitch-citation-label">Sumber Primer:</p>
                @foreach($post->citations as $citation)
                <div class="stitch-citation-entry">
                    <p class="stitch-citation-text-display">{{ $citation['text'] }}</p>
                    @if(!empty($citation['url']))
                    <a href="{{ $citation['url'] }}" target="_blank" rel="noopener" class="stitch-citation-link">
                        {{ Str::limit($citation['url'], 60) }}
                        <span class="material-symbols-outlined">open_in_new</span>
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @elseif($post->isFactCheck())
        <div class="stitch-missing-citation">
            <span class="material-symbols-outlined text-alert">link_off</span>
            <div class="stitch-missing-citation-text">
                <p class="stitch-missing-label">Sitasi Hilang</p>
                <p>Postingan ini membuat klaim tanpa sumber primer yang terverifikasi.</p>
            </div>
        </div>
        @endif

        {{-- Vote section for fact-check posts --}}
        @if($post->isFactCheck())
        @php
            $faktaPct = $totalVotes > 0 ? round($faktaCount / $totalVotes * 100) : 50;
            $hoaksPct = $totalVotes > 0 ? round($hoaksCount / $totalVotes * 100) : 50;
        @endphp
        <div class="stitch-vote-section">
            <div class="stitch-vote-bar">
                <div class="stitch-vote-fact" style="width: {{ $faktaPct }}%;" id="vote-fact-bar-{{ $post->id }}">{{ $faktaCount }} Fakta</div>
                <div class="stitch-vote-hoax" style="width: {{ $hoaksPct }}%;" id="vote-hoax-bar-{{ $post->id }}">{{ $hoaksCount }} Hoaks</div>
            </div>
            <div class="stitch-vote-actions">
                @if(!Auth::user()->isAnonim())
                <button class="stitch-vote-btn fact {{ $userVote === 'fakta' ? 'voted' : '' }}" data-post-id="{{ $post->id }}" data-vote="fakta">
                    <span class="material-symbols-outlined">verified</span> Fakta
                </button>
                <button class="stitch-vote-btn hoax {{ $userVote === 'hoaks' ? 'voted' : '' }}" data-post-id="{{ $post->id }}" data-vote="hoaks">
                    <span class="material-symbols-outlined">gpp_maybe</span> Hoaks
                </button>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="stitch-post-footer">
        <div class="stitch-footer-left">
            @if(!$post->isFactCheck())
                @php
                    $endorseCount = $post->endorsements_count ?? 0;
                    $isEndorsed = $post->isEndorsedByUser(Auth::id());
                @endphp
                @if(!Auth::user()->isAnonim())
                <button class="stitch-footer-btn endorse-btn {{ $isEndorsed ? 'endorsed' : '' }}" data-post-id="{{ $post->id }}">
                    <span class="material-symbols-outlined">thumb_up</span>
                    Setuju <span class="endorse-count">({{ $endorseCount }})</span>
                </button>
                @else
                <span class="stitch-footer-btn">
                    <span class="material-symbols-outlined">thumb_up</span>
                    Setuju ({{ $endorseCount }})
                </span>
                @endif
            @endif
            <a href="{{ route('posts.show', $post) }}" class="stitch-footer-btn stitch-footer-link">
                <span class="material-symbols-outlined">forum</span>
                Diskusi <span class="comment-count-label">({{ $post->comments_count ?? 0 }})</span>
            </a>
            @if($post->hasCitations())
            <span class="stitch-footer-btn cite-scroll-btn" onclick="const cb=this.closest('.stitch-post').querySelector('.stitch-citation-block');if(cb){cb.scrollIntoView({behavior:'smooth',block:'center'});cb.classList.add('cite-highlight');setTimeout(()=>cb.classList.remove('cite-highlight'),2000);}">
                <span class="material-symbols-outlined">format_quote</span>
                Lihat Sumber
            </span>
            @elseif($post->isFactCheck())
            <span class="stitch-footer-btn cite-missing-label">
                <span class="material-symbols-outlined">link_off</span>
                Tanpa Sumber
            </span>
            @endif
        </div>
        <button class="stitch-footer-btn share-btn" data-url="{{ route('posts.show', $post) }}" data-title="{{ Str::limit($post->body, 80) }}">
            <span class="material-symbols-outlined">share</span>
        </button>
    </div>
</article>
@empty
<div class="stitch-card stitch-empty">
    <span class="material-symbols-outlined stitch-empty-icon">article</span>
    <p>Belum ada postingan. Jadilah yang pertama!</p>
</div>
@endforelse

{{ $posts->links() }}

    </div>{{-- end home-feed --}}

    {{-- Right Aside --}}
    <aside class="home-aside">
        {{-- Hoax Queue Widget --}}
        <div class="home-aside-card">
            <div class="home-aside-header">
                <h3>
                    <span class="material-symbols-outlined" style="font-size:18px;color:#ef4444">gpp_maybe</span>
                    Antrean Pantau Hoaks
                </h3>
                <span class="home-live-badge">LIVE</span>
            </div>
            <div class="home-hoax-list">
                @forelse($sidebarHoaxClaims as $claim)
                    @php
                        $approvedCount = $claim->approved_verdicts_count;
                        $needed = max(0, 10 - $approvedCount);
                        $isUrgent = $approvedCount < 4;
                    @endphp
                    <a href="{{ route('hoax-buster.show', $claim) }}" class="home-hoax-item" style="text-decoration:none;color:inherit;">
                        <span class="material-symbols-outlined" style="font-size:16px;color:{{ $isUrgent ? '#ef4444' : '#f59e0b' }}">{{ $isUrgent ? 'report' : 'warning' }}</span>
                        <div>
                            <p class="home-hoax-text">{{ Str::limit($claim->title, 60) }}</p>
                            <span class="home-hoax-meta">{{ $needed }} verifikasi dibutuhkan</span>
                        </div>
                    </a>
                @empty
                    <div style="padding: 8px 0; color: #94a3b8; font-size: 13px;">
                        Tidak ada klaim yang perlu diverifikasi.
                    </div>
                @endforelse
            </div>
            <a href="{{ route('hoax-buster') }}" class="home-aside-link">Lihat Pusat Penumpas Hoaks &rarr;</a>
        </div>

        {{-- Trending Topics --}}
        <div class="home-aside-card">
            <h3 class="home-aside-title">
                <span class="material-symbols-outlined" style="font-size:18px">trending_up</span>
                Wacana Tren
            </h3>
            <div class="home-trend-list">
                @forelse($sidebarTrending as $trending)
                    <a href="{{ route('posts.show', $trending) }}" class="home-trend-item">
                        <span class="home-trend-cat">{{ ucfirst($trending->category === 'fact-check' ? 'Cek Fakta' : $trending->category) }} &bull; {{ $trending->votes_count }} suara</span>
                        <strong>{{ Str::limit($trending->body, 50) }}</strong>
                    </a>
                @empty
                    <div style="padding: 8px 0; color: #94a3b8; font-size: 13px;">
                        Belum ada wacana tren.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Footer --}}
        <div class="home-aside-footer">
            <p>&copy; {{ date('Y') }} CIVIC-Connect. Inisiatif untuk integritas akademik.</p>
        </div>
    </aside>
</div>{{-- end home-page --}}

{{-- Report Modal --}}
<div class="modal-overlay" id="report-post-modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Laporkan Postingan</h3>
            <button class="modal-close" id="close-report-post-modal">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="report-post-id">
            <div class="form-group">
                <label class="form-label">Alasan Laporan</label>
                <select id="report-reason" class="form-input">
                    <option value="hoaks">Mengandung Hoaks / Informasi Palsu</option>
                    <option value="spam">Spam / Konten Tidak Relevan</option>
                    <option value="ujaran-kebencian">Ujaran Kebencian</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi (opsional)</label>
                <textarea id="report-description" class="form-input form-textarea" rows="3" placeholder="Jelaskan mengapa Anda melaporkan postingan ini..." maxlength="500"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-outline" id="cancel-report-post">Batal</button>
            <button class="btn-primary btn-danger-solid" id="submit-report-post">Kirim Laporan</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Category selection
document.querySelectorAll('.stitch-category-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.stitch-category-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('post-category').value = btn.dataset.category;
    });
});

// Toggle citations
const citationsContainer = document.getElementById('citations-container');
document.getElementById('toggle-citations-btn')?.addEventListener('click', () => {
    const isVisible = citationsContainer.style.display !== 'none';
    citationsContainer.style.display = isVisible ? 'none' : 'block';
});

// Add citation row
document.getElementById('add-citation-btn')?.addEventListener('click', () => {
    const list = document.getElementById('citations-list');
    if (list.children.length >= 5) return;
    const row = document.createElement('div');
    row.className = 'stitch-citation-row';
    row.innerHTML = `
        <input type="text" name="citation_texts[]" placeholder="Judul atau deskripsi sumber..." class="stitch-citation-text">
        <input type="text" name="citation_urls[]" placeholder="https://doi.org/... atau URL sumber" class="stitch-citation-url">
        <button type="button" class="stitch-remove-citation" onclick="this.parentElement.remove()">×</button>
    `;
    list.appendChild(row);
});

// Image upload preview
const imageInput = document.getElementById('post-image-input');
const previewContainer = document.getElementById('image-preview-container');
const previewImg = document.getElementById('post-image-preview');
const removeBtn = document.getElementById('remove-image-btn');

imageInput?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

removeBtn?.addEventListener('click', () => {
    imageInput.value = '';
    previewContainer.style.display = 'none';
});

// 3-dot menu toggle
document.querySelectorAll('.post-more-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        document.querySelectorAll('.post-more-dropdown.show').forEach(d => {
            if (d !== btn.nextElementSibling) d.classList.remove('show');
        });
        btn.nextElementSibling.classList.toggle('show');
    });
});

document.addEventListener('click', () => {
    document.querySelectorAll('.post-more-dropdown.show').forEach(d => d.classList.remove('show'));
});

// Report modal
const reportModal = document.getElementById('report-post-modal');
const reportPostId = document.getElementById('report-post-id');
const reportReason = document.getElementById('report-reason');
const reportDesc = document.getElementById('report-description');

document.querySelectorAll('.report-post-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        reportPostId.value = btn.dataset.postId;
        reportReason.value = btn.dataset.reason || 'hoaks';
        reportDesc.value = '';
        reportModal.style.display = 'flex';
        document.querySelectorAll('.post-more-dropdown.show').forEach(d => d.classList.remove('show'));
    });
});

document.getElementById('close-report-post-modal')?.addEventListener('click', () => reportModal.style.display = 'none');
document.getElementById('cancel-report-post')?.addEventListener('click', () => reportModal.style.display = 'none');
reportModal?.addEventListener('click', (e) => { if (e.target === reportModal) reportModal.style.display = 'none'; });

document.getElementById('submit-report-post')?.addEventListener('click', async () => {
    const postId = reportPostId.value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    try {
        const res = await fetch(`/posts/${postId}/report`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ reason: reportReason.value, description: reportDesc.value || null }),
        });
        const data = await res.json();
        showToast(res.ok ? data.message : (data.message || 'Gagal mengirim laporan'), res.ok ? 'success' : 'error');
        reportModal.style.display = 'none';
    } catch (err) { showToast('Gagal mengirim laporan', 'error'); }
});

// AJAX Voting
document.querySelectorAll('.stitch-vote-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const postId = btn.dataset.postId;
        const vote = btn.dataset.vote;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        try {
            const res = await fetch(`/posts/${postId}/vote`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ vote }),
            });
            if (!res.ok) throw new Error('Vote failed');
            const data = await res.json();
            const total = data.fakta_count + data.hoaks_count;
            const faktaPct = total > 0 ? Math.round(data.fakta_count / total * 100) : 50;
            const hoaksPct = total > 0 ? Math.round(data.hoaks_count / total * 100) : 50;
            const faktaBar = document.getElementById(`vote-fact-bar-${postId}`);
            const hoaksBar = document.getElementById(`vote-hoax-bar-${postId}`);
            const statsEl = document.getElementById(`vote-stats-${postId}`);
            if (faktaBar) { faktaBar.style.width = faktaPct + '%'; faktaBar.textContent = data.fakta_count + ' Fakta'; }
            if (hoaksBar) { hoaksBar.style.width = hoaksPct + '%'; hoaksBar.textContent = data.hoaks_count + ' Hoaks'; }
            if (statsEl) { statsEl.textContent = '(' + total + ')'; }
            const postCard = document.querySelector(`[data-post-id="${postId}"]`);
            postCard.querySelectorAll('.stitch-vote-btn').forEach(b => b.classList.remove('voted'));
            if (data.user_vote) postCard.querySelector(`.stitch-vote-btn[data-vote="${data.user_vote}"]`)?.classList.add('voted');
            showToast(data.user_vote ? `Vote ${data.user_vote.toUpperCase()} tercatat` : 'Vote dihapus', 'success');
        } catch (err) { showToast('Gagal melakukan vote', 'error'); }
    });
});

// AJAX Endorsement
document.querySelectorAll('.endorse-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const postId = btn.dataset.postId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        try {
            const res = await fetch(`/posts/${postId}/endorse`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            });
            if (!res.ok) throw new Error();
            const data = await res.json();
            btn.classList.toggle('endorsed', data.endorsed);
            btn.querySelector('.endorse-count').textContent = `(${data.endorsement_count})`;
            showToast(data.endorsed ? 'Anda menyetujui postingan ini' : 'Setuju dibatalkan', 'success');
        } catch (err) { showToast('Gagal', 'error'); }
    });
});

// Share
document.querySelectorAll('.share-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const url = btn.dataset.url;
        const title = btn.dataset.title;
        if (navigator.share) {
            try { await navigator.share({ title, url }); } catch {}
        } else {
            try {
                await navigator.clipboard.writeText(url);
                showToast('Link disalin!', 'success');
            } catch { showToast('Gagal menyalin link', 'error'); }
        }
    });
});
</script>
@endpush
@endsection
