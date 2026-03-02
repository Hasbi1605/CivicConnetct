@extends('layouts.feature')

@section('title', 'Postingan — ' . Str::limit($post->body, 40))

@section('content')
<div class="post-detail-page">
    {{-- Back navigation --}}
    <a href="{{ route('home') }}" class="post-detail-back">
        <span class="material-symbols-outlined">arrow_back</span>
        Kembali ke Beranda
    </a>

    {{-- Main Post Card --}}
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
                        <form action="{{ route('posts.destroy', $post) }}" method="POST" class="delete-post-form" onsubmit="return confirm('Apakah Anda yakin ingin menghapus postingan ini?')">
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
            <div class="stitch-citation-block" id="citation-block">
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

        {{-- Post Footer --}}
        <div class="stitch-post-footer">
            <div class="stitch-footer-left">
                @if(!$post->isFactCheck())
                    @php
                        $endorseCount = $post->endorsementCount();
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
                        Setuju <span class="endorse-count">({{ $endorseCount }})</span>
                    </span>
                    @endif
                @endif
                <span class="stitch-footer-btn">
                    <span class="material-symbols-outlined">forum</span>
                    Diskusi <span class="comment-count-label">({{ $post->commentCount() }})</span>
                </span>
                @if($post->hasCitations())
                <button class="stitch-footer-btn cite-scroll-btn" onclick="document.getElementById('citation-block').scrollIntoView({behavior:'smooth',block:'center'}); document.getElementById('citation-block').classList.add('cite-highlight'); setTimeout(()=>document.getElementById('citation-block').classList.remove('cite-highlight'),2000);">
                    <span class="material-symbols-outlined">format_quote</span>
                    Lihat Sumber
                </button>
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

    {{-- Comments Section --}}
    <div class="comments-section">
        <div class="comments-header">
            <h3>
                <span class="material-symbols-outlined">forum</span>
                Diskusi <span class="comment-count-total">({{ $comments->count() + $comments->sum(fn($c) => $c->replies->count()) }})</span>
            </h3>
        </div>

        {{-- Comment Form --}}
        @if(!Auth::user()->isAnonim())
        <div class="comment-form-card" id="comment-form-main">
            <div class="comment-form-inner">
                <div class="stitch-avatar-sm">
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}">
                </div>
                <div class="comment-input-wrap">
                    <textarea class="comment-input" id="comment-body-main" placeholder="Tulis komentar diskusi..." rows="2" maxlength="2000"></textarea>
                    <button class="comment-submit-btn" id="comment-submit-main" data-post-id="{{ $post->id }}">
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </div>
            </div>
        </div>
        @else
        <div class="comment-form-card comment-form-disabled">
            <p><span class="material-symbols-outlined">lock</span> Masuk untuk berpartisipasi dalam diskusi.</p>
        </div>
        @endif

        {{-- Comments List --}}
        <div class="comments-list" id="comments-list">
            @forelse($comments as $comment)
            <div class="comment-card" id="comment-{{ $comment->id }}" data-comment-id="{{ $comment->id }}">
                <div class="comment-main">
                    <div class="stitch-avatar-sm comment-avatar">
                        <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}">
                    </div>
                    <div class="comment-body-wrap">
                        <div class="comment-author-line">
                            <strong>{{ $comment->user->name }}</strong>
                            @if($comment->user->role_badge)
                                <span class="stitch-role-badge {{ $comment->user->role }}">{{ $comment->user->role_badge }}</span>
                            @endif
                            <span class="comment-time">{{ $comment->created_at->diffForHumans(null, true) }} lalu</span>
                        </div>
                        <p class="comment-text">{{ $comment->body }}</p>
                        <div class="comment-actions">
                            @if(!Auth::user()->isAnonim())
                            <button class="comment-action-btn top-btn {{ $comment->isTopByUser(Auth::id()) ? 'topped' : '' }}" data-post-id="{{ $post->id }}" data-comment-id="{{ $comment->id }}">
                                <span class="material-symbols-outlined">arrow_upward</span>
                                Top <span class="top-count">{{ $comment->tops_count }}</span>
                            </button>
                            <button class="comment-action-btn reply-toggle-btn" data-comment-id="{{ $comment->id }}">
                                <span class="material-symbols-outlined">reply</span>
                                Balas
                            </button>
                            @endif
                            @if(Auth::id() === $comment->user_id || Auth::user()->isAgent())
                            <button class="comment-action-btn delete-comment-btn" data-post-id="{{ $post->id }}" data-comment-id="{{ $comment->id }}">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                            @endif
                        </div>

                        {{-- Reply Form (hidden by default) --}}
                        @if(!Auth::user()->isAnonim())
                        <div class="reply-form" id="reply-form-{{ $comment->id }}" style="display:none;">
                            <div class="comment-form-inner reply-form-inner">
                                <div class="stitch-avatar-sm" style="width:24px;height:24px;">
                                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}">
                                </div>
                                <div class="comment-input-wrap">
                                    <textarea class="comment-input reply-input" id="reply-body-{{ $comment->id }}" placeholder="Tulis balasan..." rows="1" maxlength="2000"></textarea>
                                    <button class="comment-submit-btn reply-submit-btn" data-post-id="{{ $post->id }}" data-parent-id="{{ $comment->id }}">
                                        <span class="material-symbols-outlined">send</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Replies --}}
                        @if($comment->replies->count() > 0)
                        <div class="comment-replies" id="replies-{{ $comment->id }}">
                            @foreach($comment->replies as $reply)
                            <div class="comment-card comment-reply" id="comment-{{ $reply->id }}" data-comment-id="{{ $reply->id }}">
                                <div class="comment-main">
                                    <div class="stitch-avatar-sm comment-avatar" style="width:24px;height:24px;">
                                        <img src="{{ $reply->user->avatar_url }}" alt="{{ $reply->user->name }}">
                                    </div>
                                    <div class="comment-body-wrap">
                                        <div class="comment-author-line">
                                            <strong>{{ $reply->user->name }}</strong>
                                            @if($reply->user->role_badge)
                                                <span class="stitch-role-badge {{ $reply->user->role }}">{{ $reply->user->role_badge }}</span>
                                            @endif
                                            <span class="comment-time">{{ $reply->created_at->diffForHumans(null, true) }} lalu</span>
                                        </div>
                                        <p class="comment-text">{{ $reply->body }}</p>
                                        <div class="comment-actions">
                                            @if(!Auth::user()->isAnonim())
                                            <button class="comment-action-btn top-btn {{ $reply->isTopByUser(Auth::id()) ? 'topped' : '' }}" data-post-id="{{ $post->id }}" data-comment-id="{{ $reply->id }}">
                                                <span class="material-symbols-outlined">arrow_upward</span>
                                                Top <span class="top-count">{{ $reply->tops()->count() }}</span>
                                            </button>
                                            @endif
                                            @if(Auth::id() === $reply->user_id || Auth::user()->isAgent())
                                            <button class="comment-action-btn delete-comment-btn" data-post-id="{{ $post->id }}" data-comment-id="{{ $reply->id }}">
                                                <span class="material-symbols-outlined">delete</span>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="comments-empty" id="comments-empty">
                <span class="material-symbols-outlined">chat_bubble_outline</span>
                <p>Belum ada diskusi. Jadilah yang pertama berkomentar!</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

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
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const postId = {{ $post->id }};

// ---- 3-dot menu ----
document.querySelectorAll('.post-more-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        btn.nextElementSibling.classList.toggle('show');
    });
});
document.addEventListener('click', () => {
    document.querySelectorAll('.post-more-dropdown.show').forEach(d => d.classList.remove('show'));
});

// ---- Report Modal ----
const reportModal = document.getElementById('report-post-modal');
document.querySelectorAll('.report-post-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        document.getElementById('report-post-id').value = btn.dataset.postId;
        document.getElementById('report-reason').value = btn.dataset.reason || 'hoaks';
        document.getElementById('report-description').value = '';
        reportModal.style.display = 'flex';
        document.querySelectorAll('.post-more-dropdown.show').forEach(d => d.classList.remove('show'));
    });
});
document.getElementById('close-report-post-modal')?.addEventListener('click', () => reportModal.style.display = 'none');
document.getElementById('cancel-report-post')?.addEventListener('click', () => reportModal.style.display = 'none');
reportModal?.addEventListener('click', (e) => { if (e.target === reportModal) reportModal.style.display = 'none'; });
document.getElementById('submit-report-post')?.addEventListener('click', async () => {
    const pid = document.getElementById('report-post-id').value;
    try {
        const res = await fetch(`/posts/${pid}/report`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ reason: document.getElementById('report-reason').value, description: document.getElementById('report-description').value || null }),
        });
        const data = await res.json();
        showToast(res.ok ? data.message : (data.message || 'Gagal mengirim laporan'), res.ok ? 'success' : 'error');
        reportModal.style.display = 'none';
    } catch (err) { showToast('Gagal mengirim laporan', 'error'); }
});

// ---- AJAX Voting ----
document.querySelectorAll('.stitch-vote-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const pid = btn.dataset.postId;
        const vote = btn.dataset.vote;
        try {
            const res = await fetch(`/posts/${pid}/vote`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ vote }),
            });
            if (!res.ok) throw new Error();
            const data = await res.json();
            const total = data.fakta_count + data.hoaks_count;
            const faktaPct = total > 0 ? Math.round(data.fakta_count / total * 100) : 50;
            const hoaksPct = total > 0 ? Math.round(data.hoaks_count / total * 100) : 50;
            const faktaBar = document.getElementById(`vote-fact-bar-${pid}`);
            const hoaksBar = document.getElementById(`vote-hoax-bar-${pid}`);
            if (faktaBar) { faktaBar.style.width = faktaPct + '%'; faktaBar.textContent = data.fakta_count + ' Fakta'; }
            if (hoaksBar) { hoaksBar.style.width = hoaksPct + '%'; hoaksBar.textContent = data.hoaks_count + ' Hoaks'; }
            const postCard = document.querySelector(`[data-post-id="${pid}"]`);
            postCard.querySelectorAll('.stitch-vote-btn').forEach(b => b.classList.remove('voted'));
            if (data.user_vote) postCard.querySelector(`.stitch-vote-btn[data-vote="${data.user_vote}"]`)?.classList.add('voted');
            showToast(data.user_vote ? `Vote ${data.user_vote.toUpperCase()} tercatat` : 'Vote dihapus', 'success');
        } catch (err) { showToast('Gagal melakukan vote', 'error'); }
    });
});

// ---- Endorsement ----
document.querySelectorAll('.endorse-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const pid = btn.dataset.postId;
        try {
            const res = await fetch(`/posts/${pid}/endorse`, {
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

// ---- Share ----
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

// ---- Submit Comment ----
document.getElementById('comment-submit-main')?.addEventListener('click', async () => {
    const body = document.getElementById('comment-body-main').value.trim();
    if (!body) return;
    await submitComment(postId, body, null);
    document.getElementById('comment-body-main').value = '';
});

// ---- Submit Reply ----
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.reply-submit-btn');
    if (!btn) return;
    const parentId = btn.dataset.parentId;
    const textarea = document.getElementById(`reply-body-${parentId}`);
    const body = textarea.value.trim();
    if (!body) return;
    await submitComment(postId, body, parentId);
    textarea.value = '';
    document.getElementById(`reply-form-${parentId}`).style.display = 'none';
});

// ---- Toggle Reply Form ----
document.addEventListener('click', (e) => {
    const btn = e.target.closest('.reply-toggle-btn');
    if (!btn) return;
    const cid = btn.dataset.commentId;
    const form = document.getElementById(`reply-form-${cid}`);
    if (form) {
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
        if (form.style.display === 'block') form.querySelector('textarea')?.focus();
    }
});

// ---- Toggle Top ----
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.top-btn');
    if (!btn) return;
    const pid = btn.dataset.postId;
    const cid = btn.dataset.commentId;
    try {
        const res = await fetch(`/posts/${pid}/comments/${cid}/top`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        if (!res.ok) throw new Error();
        const data = await res.json();
        btn.classList.toggle('topped', data.is_topped);
        btn.querySelector('.top-count').textContent = data.top_count;
    } catch (err) { showToast('Gagal', 'error'); }
});

// ---- Delete Comment ----
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.delete-comment-btn');
    if (!btn) return;
    if (!confirm('Hapus komentar ini?')) return;
    const pid = btn.dataset.postId;
    const cid = btn.dataset.commentId;
    try {
        const res = await fetch(`/posts/${pid}/comments/${cid}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        if (!res.ok) throw new Error();
        const data = await res.json();
        const card = document.getElementById(`comment-${cid}`);
        if (card) { card.style.opacity = '0'; setTimeout(() => card.remove(), 300); }
        updateCommentCounts(data.comment_count);
        showToast('Komentar dihapus', 'success');
    } catch (err) { showToast('Gagal menghapus', 'error'); }
});

// ---- Helper: Submit Comment via AJAX ----
async function submitComment(pid, body, parentId) {
    try {
        const res = await fetch(`/posts/${pid}/comments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ body, parent_id: parentId }),
        });
        if (!res.ok) {
            const err = await res.json();
            showToast(err.message || 'Gagal mengirim komentar', 'error');
            return;
        }
        const data = await res.json();
        const c = data.comment;

        // Remove empty state
        document.getElementById('comments-empty')?.remove();

        // Build comment HTML
        const roleBadge = c.user.role_badge ? `<span class="stitch-role-badge ${c.user.role}">${c.user.role_badge}</span>` : '';
        const html = `
            <div class="comment-card ${parentId ? 'comment-reply' : ''}" id="comment-${c.id}" data-comment-id="${c.id}" style="opacity:0;transition:opacity 0.3s">
                <div class="comment-main">
                    <div class="stitch-avatar-sm comment-avatar" ${parentId ? 'style="width:24px;height:24px;"' : ''}>
                        <img src="${c.user.avatar_url}" alt="${c.user.name}">
                    </div>
                    <div class="comment-body-wrap">
                        <div class="comment-author-line">
                            <strong>${c.user.name}</strong>
                            ${roleBadge}
                            <span class="comment-time">${c.created_at}</span>
                        </div>
                        <p class="comment-text">${escapeHtml(c.body)}</p>
                        <div class="comment-actions">
                            <button class="comment-action-btn top-btn" data-post-id="${pid}" data-comment-id="${c.id}">
                                <span class="material-symbols-outlined">arrow_upward</span>
                                Top <span class="top-count">0</span>
                            </button>
                            ${!parentId ? `<button class="comment-action-btn reply-toggle-btn" data-comment-id="${c.id}">
                                <span class="material-symbols-outlined">reply</span> Balas
                            </button>` : ''}
                            <button class="comment-action-btn delete-comment-btn" data-post-id="${pid}" data-comment-id="${c.id}">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        if (parentId) {
            let repliesDiv = document.getElementById(`replies-${parentId}`);
            if (!repliesDiv) {
                repliesDiv = document.createElement('div');
                repliesDiv.className = 'comment-replies';
                repliesDiv.id = `replies-${parentId}`;
                const parentWrap = document.querySelector(`#comment-${parentId} .comment-body-wrap`);
                parentWrap.appendChild(repliesDiv);
            }
            repliesDiv.insertAdjacentHTML('beforeend', html);
        } else {
            document.getElementById('comments-list').insertAdjacentHTML('afterbegin', html);
        }

        // Animate in
        setTimeout(() => {
            document.getElementById(`comment-${c.id}`).style.opacity = '1';
        }, 50);

        updateCommentCounts(data.comment_count);
        showToast('Komentar berhasil dikirim', 'success');
    } catch (err) { showToast('Gagal mengirim komentar', 'error'); }
}

function updateCommentCounts(count) {
    document.querySelectorAll('.comment-count-label, .comment-count-total').forEach(el => {
        el.textContent = `(${count})`;
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endpush
@endsection
