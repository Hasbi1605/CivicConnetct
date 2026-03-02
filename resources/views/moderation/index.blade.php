@extends('layouts.feature')

@section('title', 'Panel Moderasi')

@section('content')

@php
    $totalPending = $pendingPosts->count() + ($pendingBriefs->count() ?? 0) + ($pendingClaims->count() ?? 0) + ($pendingVerdicts->count() ?? 0);
    $totalAll = $totalPending + $reportedPosts->count();
@endphp

<div class="moderation-page">
    {{-- Header card (matches plab-header / ldb-header / hbc-header) --}}
    <div class="mod-header">
        <div class="mod-header-left">
            <h2 class="mod-title">Panel Moderasi</h2>
            <p class="mod-subtitle">Tinjau dan kelola konten yang membutuhkan persetujuan</p>
        </div>
        <div class="mod-stats-inline">
            <div class="mod-stat-inline">
                <span class="mod-stat-value primary">{{ $totalPending }}</span>
                <span class="mod-stat-label">Menunggu</span>
            </div>
            <div class="mod-stat-inline">
                <span class="mod-stat-value">{{ $recentReviewed->count() }}</span>
                <span class="mod-stat-label">Hari Ini</span>
            </div>
            <div class="mod-stat-inline no-border">
                <span class="mod-stat-value alert">{{ $reportedPosts->count() }}</span>
                <span class="mod-stat-label">Laporan</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tab Bar: border-bottom-2 style (Stitch) --}}
    <div class="mod-tab-bar">
        <nav class="mod-tab-nav">
            <button class="mod-tab active" onclick="switchModTab('semua', this)" data-tab="semua">
                Semua
                @if($totalAll > 0)<span class="mod-tab-badge active">{{ $totalAll }}</span>@endif
            </button>
            <button class="mod-tab" onclick="switchModTab('postingan', this)" data-tab="postingan">
                Postingan
                @if($pendingPosts->count() > 0)<span class="mod-tab-badge">{{ $pendingPosts->count() }}</span>@endif
            </button>
            <button class="mod-tab" onclick="switchModTab('policybrief', this)" data-tab="policybrief">
                Policy Brief
                @if(isset($pendingBriefs) && $pendingBriefs->count() > 0)<span class="mod-tab-badge">{{ $pendingBriefs->count() }}</span>@endif
            </button>
            <button class="mod-tab" onclick="switchModTab('laporan', this)" data-tab="laporan">
                Laporan
                @if($reportedPosts->count() > 0)<span class="mod-tab-badge">{{ $reportedPosts->count() }}</span>@endif
            </button>
            <button class="mod-tab" onclick="switchModTab('klaim', this)" data-tab="klaim">
                Klaim Hoaks
                @if(isset($pendingClaims) && $pendingClaims->count() > 0)<span class="mod-tab-badge">{{ $pendingClaims->count() }}</span>@endif
            </button>
            <button class="mod-tab" onclick="switchModTab('putusan', this)" data-tab="putusan">
                Putusan Hoaks
                @if(isset($pendingVerdicts) && $pendingVerdicts->count() > 0)<span class="mod-tab-badge">{{ $pendingVerdicts->count() }}</span>@endif
            </button>
        </nav>
    </div>

    {{-- Content Grid: 2/3 content + 1/3 preview (Stitch Screen 1 style) --}}
    <div class="mod-content-grid">
        <div class="mod-content-main">

            {{-- ═══════════════════════════════════════════ --}}
            {{-- Section: Pending Policy Briefs --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div class="moderation-section" data-category="policybrief">
                <div class="mod-section-header">
                    <span class="material-symbols-outlined">description</span>
                    Policy Brief Menunggu Review ({{ isset($pendingBriefs) ? $pendingBriefs->count() : 0 }})
                </div>

                @if(isset($pendingBriefs) && $pendingBriefs->count() > 0)
                @foreach($pendingBriefs as $brief)
                <div class="mod-card" id="mod-brief-{{ $brief->id }}" onclick="showBriefPreview({{ $brief->id }})" data-brief-problem="{{ $brief->problem ?? '' }}" data-brief-analysis="{{ $brief->analysis ?? '' }}" data-brief-recommendation="{{ $brief->recommendation ?? '' }}" data-brief-title="{{ $brief->title }}">
                    <div class="mod-card-header">
                        <div class="mod-card-user">
                            <div class="mod-avatar">{{ strtoupper(substr($brief->author->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $brief->author->name)[1] ?? '', 0, 1)) }}</div>
                            <div>
                                <div class="mod-user-name">{{ $brief->author->name }}</div>
                                <div class="mod-user-time">{{ $brief->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <span class="mod-type-badge brief">{{ $brief->templateLabel() }}</span>
                    </div>
                    <h3 class="mod-card-title">{{ $brief->title }}</h3>
                    <p class="mod-card-excerpt">{{ Str::limit($brief->summary, 200) }}</p>
                    @if($brief->labRoom)
                    <div class="mod-card-meta">
                        <span class="material-symbols-outlined" style="font-size:14px">science</span>
                        Dari L.A.B Room: {{ $brief->labRoom->title }}
                    </div>
                    @endif
                    <div class="mod-card-actions">
                        <button class="mod-btn-approve" onclick="event.stopPropagation();approveBrief({{ $brief->id }})">
                            <span class="material-symbols-outlined">check</span>
                            Setuju & Publikasi
                        </button>
                        <button class="mod-btn-reject" onclick="event.stopPropagation();openRejectModal({{ $brief->id }}, 'brief')">
                            <span class="material-symbols-outlined">close</span>
                            Tolak
                        </button>
                    </div>
                </div>
                @endforeach
                @else
                <div class="mod-empty-state">
                    <span class="material-symbols-outlined">check_circle</span>
                    <p>Tidak ada policy brief yang menunggu review.</p>
                </div>
                @endif
            </div>

            {{-- ═══════════════════════════════════════════ --}}
            {{-- Section: Pending Posts --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div class="moderation-section" data-category="postingan">
                <div class="mod-section-header">
                    <span class="material-symbols-outlined">article</span>
                    Postingan Menunggu Peninjauan ({{ $pendingPosts->count() }})
                </div>

                @forelse($pendingPosts as $post)
                <div class="mod-card" id="mod-post-{{ $post->id }}">
                    <div class="mod-card-header">
                        <div class="mod-card-user">
                            <div class="mod-avatar">{{ strtoupper(substr($post->user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $post->user->name)[1] ?? '', 0, 1)) }}</div>
                            <div>
                                <div class="mod-user-name">
                                    {{ $post->user->name }}
                                    @if($post->user->role_badge)
                                        <span class="mod-role-badge">{{ $post->user->role_badge }}</span>
                                    @endif
                                </div>
                                <div class="mod-user-detail">{{ $post->user->jurusan }} @ {{ $post->user->universitas }}</div>
                                <div class="mod-user-time">{{ $post->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <span class="mod-type-badge {{ $post->category }}">{{ ucfirst(str_replace('-', ' ', $post->category)) }}</span>
                    </div>
                    <p class="mod-card-body">{{ $post->body }}</p>
                    @if($post->image)
                        <img src="{{ $post->image_url }}" alt="Post image" class="mod-card-image">
                    @endif
                    @if($post->reports->count() > 0)
                    <div class="mod-reports-inline">
                        <span class="material-symbols-outlined" style="font-size:16px;color:#EF4444">flag</span>
                        <strong>{{ $post->reports->count() }} Laporan:</strong>
                        @foreach($post->reports as $report)
                            <span class="mod-report-tag">{{ ucfirst(str_replace('-', ' ', $report->reason)) }}</span>
                        @endforeach
                    </div>
                    @endif
                    <div class="mod-card-actions">
                        <button class="mod-btn-approve verified" onclick="approvePost({{ $post->id }})">
                            <span class="material-symbols-outlined">check</span>
                            Setujui
                        </button>
                        <button class="mod-btn-reject" onclick="openRejectModal({{ $post->id }}, 'post')">
                            <span class="material-symbols-outlined">close</span>
                            Tolak
                        </button>
                    </div>
                </div>
                @empty
                <div class="mod-empty-state">
                    <span class="material-symbols-outlined">check_circle</span>
                    <p>Tidak ada postingan yang menunggu peninjauan.</p>
                </div>
                @endforelse
            </div>

            {{-- ═══════════════════════════════════════════ --}}
            {{-- Section: Reported Approved Posts --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div class="moderation-section" data-category="laporan">
                <div class="mod-section-header">
                    <span class="material-symbols-outlined" style="color:#EF4444">flag</span>
                    Postingan Dilaporkan Pengguna ({{ $reportedPosts->count() }})
                </div>

                @forelse($reportedPosts as $post)
                <div class="mod-card" id="reported-post-{{ $post->id }}">
                    <div class="mod-card-header">
                        <div class="mod-card-user">
                            <div class="mod-avatar">{{ strtoupper(substr($post->user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $post->user->name)[1] ?? '', 0, 1)) }}</div>
                            <div>
                                <div class="mod-user-name">{{ $post->user->name }}</div>
                                <div class="mod-user-detail">{{ $post->user->jurusan }} @ {{ $post->user->universitas }}</div>
                                <div class="mod-user-time">{{ $post->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <span class="mod-type-badge {{ $post->category }}">{{ ucfirst(str_replace('-', ' ', $post->category)) }}</span>
                    </div>
                    <p class="mod-card-body">{{ Str::limit($post->body, 200) }}</p>
                    @if($post->image)
                        <img src="{{ $post->image_url }}" alt="Post image" class="mod-card-image">
                    @endif
                    <div class="mod-reports-inline">
                        <span class="material-symbols-outlined" style="font-size:16px;color:#EF4444">flag</span>
                        <strong>{{ $post->reports->count() }} Laporan:</strong>
                        @foreach($post->reports as $report)
                            <span class="mod-report-tag">{{ ucfirst(str_replace('-', ' ', $report->reason)) }}</span>
                        @endforeach
                    </div>
                    <div class="mod-card-actions">
                        <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus postingan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="mod-btn-reject">
                                <span class="material-symbols-outlined">delete</span>
                                Hapus Postingan
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="mod-empty-state">
                    <span class="material-symbols-outlined">check_circle</span>
                    <p>Tidak ada postingan yang dilaporkan.</p>
                </div>
                @endforelse
            </div>

            {{-- ═══════════════════════════════════════════ --}}
            {{-- Section: Pending Hoax Claims --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div class="moderation-section" data-category="klaim">
                <div class="mod-section-header">
                    <span class="material-symbols-outlined">fact_check</span>
                    Klaim Hoaks Menunggu Review ({{ isset($pendingClaims) ? $pendingClaims->count() : 0 }})
                </div>

                @if(isset($pendingClaims) && $pendingClaims->count() > 0)
                @foreach($pendingClaims as $claim)
                <div class="mod-card" id="mod-claim-{{ $claim->id }}">
                    <div class="mod-card-header">
                        <div class="mod-card-user">
                            <div class="mod-avatar">{{ strtoupper(substr($claim->reporter->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $claim->reporter->name)[1] ?? '', 0, 1)) }}</div>
                            <div>
                                <div class="mod-user-name">{{ $claim->reporter->name }}</div>
                                <div class="mod-user-time">{{ $claim->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <span class="mod-type-badge claim">{{ $claim->categoryLabel() }} &bull; {{ $claim->platformLabel() }}</span>
                    </div>
                    <h3 class="mod-card-title">"{{ $claim->title }}"</h3>
                    @if($claim->description)
                    <p class="mod-card-excerpt">{{ Str::limit($claim->description, 200) }}</p>
                    @endif
                    @if($claim->source_url)
                    <div class="mod-card-meta">
                        <a href="{{ $claim->source_url }}" target="_blank" class="mod-link">
                            <span class="material-symbols-outlined" style="font-size:14px">open_in_new</span>
                            {{ Str::limit($claim->source_url, 60) }}
                        </a>
                    </div>
                    @endif
                    <div class="mod-card-actions">
                        <button class="mod-btn-approve" onclick="approveClaim({{ $claim->id }})">
                            <span class="material-symbols-outlined">check</span>
                            Setujui & Buka Verifikasi
                        </button>
                        <button class="mod-btn-reject" onclick="rejectClaim({{ $claim->id }})">
                            <span class="material-symbols-outlined">close</span>
                            Tolak
                        </button>
                    </div>
                </div>
                @endforeach
                @else
                <div class="mod-empty-state">
                    <span class="material-symbols-outlined">check_circle</span>
                    <p>Tidak ada klaim hoaks yang menunggu review.</p>
                </div>
                @endif
            </div>

            {{-- ═══════════════════════════════════════════ --}}
            {{-- Section: Pending Hoax Verdicts --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div class="moderation-section" data-category="putusan">
                <div class="mod-section-header">
                    <span class="material-symbols-outlined">gavel</span>
                    Putusan Hoaks Menunggu Review ({{ isset($pendingVerdicts) ? $pendingVerdicts->count() : 0 }})
                </div>

                @if(isset($pendingVerdicts) && $pendingVerdicts->count() > 0)
                @foreach($pendingVerdicts as $verdict)
                <div class="mod-card" id="mod-verdict-{{ $verdict->id }}">
                    <div class="mod-card-header">
                        <div class="mod-card-user">
                            <div class="mod-avatar">{{ strtoupper(substr($verdict->user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $verdict->user->name)[1] ?? '', 0, 1)) }}</div>
                            <div>
                                <div class="mod-user-name">{{ $verdict->user->name }}</div>
                                <div class="mod-user-time">{{ $verdict->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <span class="mod-verdict-badge" style="background:{{ $verdict->verdictColor() }}15;color:{{ $verdict->verdictColor() }};border:1px solid {{ $verdict->verdictColor() }}30">
                            {{ $verdict->verdictLabel() }}
                        </span>
                    </div>
                    <div class="mod-card-meta">
                        Klaim: <a href="{{ route('hoax-buster.show', $verdict->claim) }}" class="mod-link">"{{ Str::limit($verdict->claim->title, 80) }}"</a>
                    </div>
                    <p class="mod-card-body">{{ $verdict->reasoning }}</p>
                    @if($verdict->evidence_url)
                    <div class="mod-card-meta">
                        <a href="{{ $verdict->evidence_url }}" target="_blank" class="mod-link">
                            <span class="material-symbols-outlined" style="font-size:14px">link</span>
                            {{ Str::limit($verdict->evidence_url, 60) }}
                        </a>
                    </div>
                    @endif
                    <div class="mod-card-actions">
                        <button class="mod-btn-approve" onclick="approveVerdict({{ $verdict->id }})">
                            <span class="material-symbols-outlined">check</span>
                            Setujui
                        </button>
                        <button class="mod-btn-reject" onclick="rejectVerdict({{ $verdict->id }})">
                            <span class="material-symbols-outlined">close</span>
                            Tolak
                        </button>
                    </div>
                </div>
                @endforeach
                @else
                <div class="mod-empty-state">
                    <span class="material-symbols-outlined">check_circle</span>
                    <p>Tidak ada putusan hoaks yang menunggu review.</p>
                </div>
                @endif
            </div>

        </div>{{-- end .mod-content-main --}}

        {{-- Right Preview Panel (Stitch Screen 1 style) --}}
        <div class="mod-preview-panel" id="mod-preview-panel">
            <div class="mod-preview-header">Preview Konten:</div>
            <div class="mod-preview-body" id="mod-preview-body">
                <div class="mod-preview-empty">
                    <span class="material-symbols-outlined" style="font-size:32px;color:#cbd5e1">preview</span>
                    <p>Pilih policy brief untuk melihat preview konten</p>
                </div>
            </div>
        </div>
    </div>{{-- end .mod-content-grid --}}

    {{-- ═══════════════════════════════════════════ --}}
    {{-- Section: Recently Reviewed (Stitch history style) --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="moderation-section" data-category="riwayat" style="margin-top:32px">
        <div class="mod-section-header">
            <span class="material-symbols-outlined">history</span>
            Telah Ditinjau Sebelumnya
        </div>

        <div class="mod-history-list">
            @forelse($recentReviewed as $post)
            <div class="mod-history-item {{ $post->isRejected() ? 'rejected-bg' : '' }}">
                <div class="mod-history-top">
                    <div class="mod-card-user">
                        <div class="mod-avatar small {{ $post->isApproved() ? 'approved' : 'rejected' }}">{{ strtoupper(substr($post->user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $post->user->name)[1] ?? '', 0, 1)) }}</div>
                        <div>
                            <div class="mod-user-name">{{ $post->user->name }}</div>
                            <div class="mod-user-time">{{ $post->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <span class="mod-status-badge {{ $post->isApproved() ? 'approved' : 'rejected' }}">
                        <span class="material-symbols-outlined" style="font-size:14px">{{ $post->isApproved() ? 'check' : 'close' }}</span>
                        {{ $post->isApproved() ? 'Disetujui' : 'Ditolak' }}
                    </span>
                </div>
                <p class="mod-history-body">{{ Str::limit($post->body, 150) }}</p>
                @if($post->isRejected() && $post->rejection_reason)
                <div class="mod-rejection-box">
                    <strong>Alasan penolakan:</strong> {{ $post->rejection_reason }}
                </div>
                @endif
                <div class="mod-history-meta">
                    Ditinjau {{ $post->reviewed_at?->diffForHumans() }}
                    @if($post->reviewer) oleh {{ $post->reviewer->name }} @endif
                </div>
            </div>
            @empty
            <div class="mod-empty-state">
                <span class="material-symbols-outlined">check_circle</span>
                <p>Belum ada konten yang ditinjau.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal-overlay" id="reject-modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="reject-modal-title">Tolak Konten</h3>
            <button class="modal-close" onclick="closeRejectModal()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="reject-item-id">
            <input type="hidden" id="reject-item-type">
            <div class="form-group">
                <label class="form-label">Alasan Penolakan <span style="color: #EF4444;">*</span></label>
                <textarea id="reject-reason" class="form-input form-textarea" rows="3" placeholder="Jelaskan alasan mengapa konten ini ditolak..." required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-outline" onclick="closeRejectModal()">Batal</button>
            <button class="btn-primary btn-danger-solid" onclick="submitReject()">Tolak</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// ===== Tab Switching =====
function switchModTab(category, btn) {
    document.querySelectorAll('.mod-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');

    // Update badge styles
    document.querySelectorAll('.mod-tab-badge').forEach(b => b.classList.remove('active'));
    const badge = btn.querySelector('.mod-tab-badge');
    if (badge) badge.classList.add('active');

    document.querySelectorAll('.moderation-section').forEach(section => {
        const cat = section.dataset.category;
        if (category === 'semua') {
            section.style.display = '';
        } else {
            section.style.display = (cat === category) ? '' : 'none';
        }
    });
}

// ===== Card Remove Animation =====
function removeCard(cardId) {
    const card = document.getElementById(cardId);
    if (!card) return;
    card.style.transition = 'opacity 0.3s, transform 0.3s';
    card.style.opacity = '0';
    card.style.transform = 'translateX(100px)';
    setTimeout(() => card.remove(), 300);
}

// ===== Brief Preview Panel =====
function showBriefPreview(briefId) {
    const card = document.getElementById(`mod-brief-${briefId}`);
    if (!card) return;
    const problem = card.dataset.briefProblem || '—';
    const analysis = card.dataset.briefAnalysis || '—';
    const recommendation = card.dataset.briefRecommendation || '—';
    const title = card.dataset.briefTitle || '';

    document.getElementById('mod-preview-body').innerHTML = `
        <div class="mod-preview-section">
            <strong class="mod-preview-label serif">Masalah:</strong>
            <p class="mod-preview-text">${problem}</p>
        </div>
        <div class="mod-preview-section">
            <strong class="mod-preview-label serif">Analisis:</strong>
            <p class="mod-preview-text">${analysis}</p>
        </div>
        <div class="mod-preview-section">
            <strong class="mod-preview-label serif">Rekomendasi:</strong>
            <p class="mod-preview-text">${recommendation}</p>
        </div>
    `;
}

// ===== Post Moderation =====
async function approvePost(postId) {
    try {
        const res = await fetch(`/moderation/${postId}/approve`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (res.ok) {
            removeCard(`mod-post-${postId}`);
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Gagal menyetujui postingan', 'error');
        }
    } catch (err) {
        showToast('Gagal menyetujui postingan', 'error');
    }
}

// ===== Reject Modal =====
function openRejectModal(itemId, type) {
    document.getElementById('reject-item-id').value = itemId;
    document.getElementById('reject-item-type').value = type;
    document.getElementById('reject-reason').value = '';

    const titles = {
        'post': 'Tolak Postingan',
        'brief': 'Tolak Policy Brief',
    };
    document.getElementById('reject-modal-title').textContent = titles[type] || 'Tolak Konten';
    document.getElementById('reject-modal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('reject-modal').style.display = 'none';
}

async function submitReject() {
    const itemId = document.getElementById('reject-item-id').value;
    const type = document.getElementById('reject-item-type').value;
    const reason = document.getElementById('reject-reason').value.trim();

    if (!reason) {
        showToast('Alasan penolakan wajib diisi', 'error');
        return;
    }

    let url = '';
    let cardId = '';

    if (type === 'post') {
        url = `/moderation/${itemId}/reject`;
        cardId = `mod-post-${itemId}`;
    } else if (type === 'brief') {
        url = `/moderation/briefs/${itemId}/reject`;
        cardId = `mod-brief-${itemId}`;
    }

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ rejection_reason: reason }),
        });
        const data = await res.json();
        if (res.ok) {
            closeRejectModal();
            removeCard(cardId);
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Gagal menolak konten', 'error');
        }
    } catch (err) {
        showToast('Gagal menolak konten', 'error');
    }
}

document.getElementById('reject-modal')?.addEventListener('click', (e) => {
    if (e.target.id === 'reject-modal') closeRejectModal();
});

// ===== Policy Brief Moderation =====
async function approveBrief(briefId) {
    try {
        const res = await fetch(`/moderation/briefs/${briefId}/approve`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (res.ok) {
            removeCard(`mod-brief-${briefId}`);
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Gagal menyetujui brief', 'error');
        }
    } catch (err) {
        showToast('Gagal menyetujui brief', 'error');
    }
}

// ===== Hoax Claim Moderation =====
async function approveClaim(claimId) {
    try {
        const res = await fetch(`/moderation/claims/${claimId}/approve`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (res.ok) {
            removeCard(`mod-claim-${claimId}`);
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Gagal menyetujui klaim', 'error');
        }
    } catch (err) {
        showToast('Gagal menyetujui klaim', 'error');
    }
}

async function rejectClaim(claimId) {
    if (!confirm('Apakah Anda yakin ingin menolak klaim ini?')) return;
    try {
        const res = await fetch(`/moderation/claims/${claimId}/reject`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (res.ok) {
            removeCard(`mod-claim-${claimId}`);
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Gagal menolak klaim', 'error');
        }
    } catch (err) {
        showToast('Gagal menolak klaim', 'error');
    }
}

// ===== Hoax Verdict Moderation =====
async function approveVerdict(verdictId) {
    try {
        const res = await fetch(`/moderation/verdicts/${verdictId}/approve`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (res.ok) {
            removeCard(`mod-verdict-${verdictId}`);
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Gagal menyetujui putusan', 'error');
        }
    } catch (err) {
        showToast('Gagal menyetujui putusan', 'error');
    }
}

async function rejectVerdict(verdictId) {
    if (!confirm('Apakah Anda yakin ingin menolak putusan ini?')) return;
    try {
        const res = await fetch(`/moderation/verdicts/${verdictId}/reject`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (res.ok) {
            removeCard(`mod-verdict-${verdictId}`);
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Gagal menolak putusan', 'error');
        }
    } catch (err) {
        showToast('Gagal menolak putusan', 'error');
    }
}
</script>
@endpush
@endsection
