@extends('layouts.fullwidth', ['backUrl' => route('policy-lab.index'), 'backLabel' => 'Kembali ke Policy Lab'])

@section('title', $brief->title . ' — Policy Lab')

@section('content')
<div class="pbr-layout">
    {{-- Main Article Area --}}
    <div class="pbr-main">
        <article class="pbr-article">
            {{-- Article Header --}}
            <div class="pbr-article-head">
                <div class="pbr-article-meta">
                    <span class="pbr-doc-id">{{ strtoupper(Str::limit($brief->template, 10)) }}-{{ $brief->id }}</span>
                    <span class="pbr-doc-date">Dipublikasikan: {{ $brief->created_at->isoFormat('D MMM Y') }}</span>
                </div>
                <h1 class="pbr-title">{{ $brief->title }}</h1>
                <div class="pbr-topic-badges">
                    <span class="pbr-topic-badge primary">{{ $brief->templateLabel() }}</span>
                    @if($brief->labRoom)
                    <span class="pbr-topic-badge">L.A.B Room</span>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom:16px">{{ session('success') }}</div>
            @endif

            {{-- Key Point Highlight --}}
            <div class="pbr-key-point">
                <div class="pbr-key-point-head">
                    <span class="material-symbols-outlined" style="font-size:20px">lightbulb</span>
                    <h3>Poin Kunci</h3>
                </div>
                <p>{{ Str::limit($brief->summary, 300) }}</p>
            </div>

            {{-- Section 1: Problem --}}
            <section class="pbr-section">
                <div class="pbr-section-head">
                    <span class="pbr-section-num">1</span>
                    <h2>Ringkasan Masalah</h2>
                </div>
                <div class="pbr-section-body">{!! nl2br(e($brief->problem)) !!}</div>
            </section>

            {{-- Section 2: Analysis --}}
            <section class="pbr-section">
                <div class="pbr-section-head">
                    <span class="pbr-section-num">2</span>
                    <h2>Analisis & Data</h2>
                </div>
                <div class="pbr-section-body">{!! nl2br(e($brief->analysis)) !!}</div>
            </section>

            {{-- Section 3: Recommendation --}}
            <section class="pbr-section">
                <div class="pbr-section-head">
                    <span class="pbr-section-num">3</span>
                    <h2>Rekomendasi Kebijakan</h2>
                </div>
                <div class="pbr-rec-box">
                    <div class="pbr-section-body">{!! nl2br(e($brief->recommendation)) !!}</div>
                </div>
            </section>

            <div class="pbr-article-end">
                <p>Akhir dokumen. Terakhir diperbarui {{ $brief->updated_at->isoFormat('D MMM Y') }}.</p>
            </div>
        </article>

        {{-- Floating Toolbar --}}
        <div class="pbr-floating-toolbar">
            <button class="pbr-toolbar-btn" title="Perkecil font" onclick="changeFontSize(-1)">
                <span class="material-symbols-outlined" style="font-size:20px">text_decrease</span>
            </button>
            <div class="pbr-toolbar-divider"></div>
            <button class="pbr-toolbar-btn" title="Perbesar font" onclick="changeFontSize(1)">
                <span class="material-symbols-outlined" style="font-size:20px">text_increase</span>
            </button>
            <div class="pbr-toolbar-divider"></div>
            <a href="{{ route('policy-lab.index') }}" class="pbr-toolbar-btn" title="Kembali ke repositori">
                <span class="material-symbols-outlined" style="font-size:20px">arrow_back</span>
            </a>
        </div>
    </div>

    {{-- Right Sidebar --}}
    <aside class="pbr-sidebar">
        {{-- Author Card --}}
        <div class="pbr-sidebar-section pbr-author-card">
            <h3 class="pbr-sidebar-label">Tentang Penulis</h3>
            <div class="pbr-author-row">
                <img src="{{ $brief->author->avatar_url }}" alt="" class="pbr-author-avatar">
                <div>
                    <h4 class="pbr-author-name">{{ $brief->author->name }}</h4>
                    <p class="pbr-author-role">{{ $brief->author->role === 'mahasiswa' ? 'Mahasiswa' : ($brief->author->role === 'dosen' ? 'Dosen / Peneliti' : 'Kontributor') }}</p>
                    @if($brief->author->universitas)
                    <div class="pbr-author-univ">
                        <span>{{ $brief->author->universitas }}</span>
                        @if($brief->author->is_verified)
                        <span class="material-symbols-outlined" style="font-size:14px;color:#10B981">verified</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @if($brief->author->civic_points)
            <div class="pbr-author-stats">
                <span class="pbr-author-stat">Poin: {{ $brief->author->civic_points }}</span>
                <span class="pbr-author-stat">Akurasi: {{ $brief->author->accuracy_score ?? '—' }}%</span>
            </div>
            @endif
        </div>

        {{-- LAB Collaboration Info --}}
        @if($brief->labRoom)
        <div class="pbr-sidebar-section pbr-lab-info">
            <div class="pbr-lab-info-head">
                <span class="material-symbols-outlined" style="font-size:20px">groups</span>
                <span>Kolaborasi Ruang L.A.B</span>
            </div>
            <p class="pbr-lab-info-label">Hasil dari:</p>
            <a href="{{ route('lab-room.show', $brief->labRoom) }}" class="pbr-lab-info-link">Room: {{ $brief->labRoom->title }}</a>
            <p class="pbr-lab-info-desc">Dokumen ini disusun melalui kolaborasi intensif di L.A.B Room.</p>
        </div>
        @endif

        {{-- Endorsement Section --}}
        @if($brief->isApproved())
        <div class="pbr-sidebar-section">
            <h3 class="pbr-sidebar-label">Dukungan Komunitas</h3>
            <div class="pbr-endorse-header">
                <span class="pbr-endorse-count">{{ $brief->endorsementCount() }}</span>
                <span class="pbr-endorse-label">Endorsements</span>
            </div>
            <div class="pbr-endorse-bar">
                @php $pct = min($brief->endorsementCount() * 5, 100); @endphp
                <div class="pbr-endorse-bar-fill" style="width:{{ $pct }}%"></div>
            </div>
            <form action="{{ route('policy-lab.endorse', $brief) }}" method="POST">
                @csrf
                <button type="submit" class="pbr-endorse-btn {{ $brief->isEndorsedBy(Auth::id()) ? 'endorsed' : '' }}">
                    <span class="material-symbols-outlined" style="font-size:20px">thumb_up</span>
                    {{ $brief->isEndorsedBy(Auth::id()) ? 'Anda mendukung' : 'Endorse Risalah Ini' }}
                </button>
            </form>
        </div>
        @endif

        {{-- Brief Info --}}
        <div class="pbr-sidebar-section">
            <h3 class="pbr-sidebar-label">Informasi Brief</h3>
            <div class="pbr-info-row">
                <span>Status</span>
                <span class="plab-status-{{ $brief->status }}" style="font-size:11px">{{ $brief->statusLabel() }}</span>
            </div>
            <div class="pbr-info-row">
                <span>Template</span>
                <span>{{ $brief->templateLabel() }}</span>
            </div>
            <div class="pbr-info-row">
                <span>Dibuat</span>
                <span>{{ $brief->created_at->diffForHumans() }}</span>
            </div>
            @if($brief->reviewer)
            <div class="pbr-info-row">
                <span>Reviewer</span>
                <span>{{ $brief->reviewer->name }}</span>
            </div>
            @endif
        </div>

        {{-- Actions --}}
        @if($brief->user_id === Auth::id() && ($brief->isDraft() || $brief->isRejected()))
        <a href="{{ route('policy-lab.edit', $brief) }}" class="plab-btn-primary" style="width:100%;justify-content:center;margin-top:12px">
            <span class="material-symbols-outlined" style="font-size:18px">edit</span> Edit Brief
        </a>
        @endif

        @if($brief->isRejected() && $brief->rejection_reason)
        <div class="pbr-sidebar-section pbr-rejection">
            <h3 class="pbr-sidebar-label" style="color:#EF4444">Alasan Penolakan</h3>
            <p>{{ $brief->rejection_reason }}</p>
        </div>
        @endif
    </aside>
</div>

@push('scripts')
<script>
let currentSize = 17;
function changeFontSize(delta) {
    currentSize = Math.max(14, Math.min(22, currentSize + delta));
    document.querySelectorAll('.pbr-section-body').forEach(el => el.style.fontSize = currentSize + 'px');
}
</script>
@endpush
@endsection
