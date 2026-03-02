@extends('layouts.fullwidth', ['backUrl' => route('lab-room.index'), 'backLabel' => 'Kembali ke L.A.B Room'])

@section('title', $labRoom->title . ' — L.A.B Room')

@section('content')
<div class="lab-composer">
    {{-- Main Content Area --}}
    <div class="lab-composer-main">
        {{-- Phase Navigation Tabs --}}
        <div class="lab-composer-card">
            <nav class="lab-phase-nav">
                <button class="lab-phase-nav-btn {{ $labRoom->phase === 'literasi' ? 'active' : '' }}" onclick="showPhaseContent('literasi', this)">
                    <span class="lab-phase-nav-circle {{ $labRoom->phaseNumber() >= 1 ? 'reached' : '' }} {{ $labRoom->phase === 'literasi' ? 'current' : '' }}">L</span>
                    Literasi
                </button>
                <button class="lab-phase-nav-btn {{ $labRoom->phase === 'analisis' ? 'active' : '' }}" onclick="showPhaseContent('analisis', this)">
                    <span class="lab-phase-nav-circle {{ $labRoom->phaseNumber() >= 2 ? 'reached' : '' }} {{ $labRoom->phase === 'analisis' ? 'current' : '' }}">A</span>
                    Analisis
                </button>
                <button class="lab-phase-nav-btn {{ $labRoom->phase === 'output' ? 'active' : '' }}" onclick="showPhaseContent('output', this)">
                    <span class="lab-phase-nav-circle {{ $labRoom->phaseNumber() >= 3 ? 'reached' : '' }} {{ $labRoom->phase === 'output' ? 'current' : '' }}">B</span>
                    Basis Data
                </button>
            </nav>

            <div class="lab-composer-body">
                @if(session('success'))
                    <div class="alert alert-success" style="margin-bottom:16px">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" style="margin-bottom:16px">{{ session('error') }}</div>
                @endif

                {{-- Section Header --}}
                <div class="lab-composer-section-header">
                    <h2 class="lab-composer-section-title">
                        @if($labRoom->phase === 'literasi') Sumber & Referensi Terkumpul
                        @elseif($labRoom->phase === 'analisis') Diskusi Terstruktur: {{ Str::limit($labRoom->title, 40) }}
                        @else Susun Policy Brief
                        @endif
                    </h2>
                    @if($labRoom->isHost(Auth::id()) && $labRoom->status !== 'completed')
                    <form action="{{ route('lab-room.advance', $labRoom) }}" method="POST">
                        @csrf
                        <button type="submit" class="lab-action-btn" onclick="return confirm('Lanjutkan ke fase berikutnya?')">
                            <span class="material-symbols-outlined" style="font-size:16px">arrow_forward</span>
                            {{ $labRoom->phase === 'output' ? 'Selesaikan Room' : 'Lanjut Fase' }}
                        </button>
                    </form>
                    @endif
                </div>

                {{-- LITERASI TAB --}}
                <div class="lab-phase-content" id="phase-literasi" style="{{ $labRoom->phase !== 'literasi' ? 'display:none' : '' }}">
                    @if($labRoom->sources->count() > 0)
                    <div class="lab-thread-list">
                        @foreach($labRoom->sources as $source)
                        <div class="lab-thread-item">
                            <div class="lab-thread-avatar-col">
                                <div class="lab-thread-avatar">
                                    <img src="{{ $source->user->avatar_url }}" alt="">
                                </div>
                                @if(!$loop->last)<div class="lab-thread-line"></div>@endif
                            </div>
                            <div class="lab-thread-content">
                                <div class="lab-thread-card">
                                    <div class="lab-thread-header">
                                        <div>
                                            <span class="lab-thread-author">{{ $source->user->name }}</span>
                                            <span class="lab-thread-univ">{{ $source->user->universitas ?? '' }}</span>
                                        </div>
                                        <span class="lab-thread-time">{{ $source->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="lab-thread-body">
                                        <a href="{{ $source->url }}" target="_blank" class="lab-source-link">
                                            <span class="material-symbols-outlined" style="font-size:16px">link</span>
                                            {{ $source->title }}
                                            @if($source->is_verified)
                                            <span class="material-symbols-outlined lab-verified-icon" style="font-size:14px">verified</span>
                                            @endif
                                        </a>
                                        @if($source->summary)
                                        <div class="lab-ref-block">{{ $source->summary }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="lab-empty-phase">
                        <span class="material-symbols-outlined">search</span>
                        <p>Belum ada sumber. Tambahkan sumber pertama untuk memulai fase Literasi.</p>
                    </div>
                    @endif

                    {{-- Add Source Form --}}
                    @if($labRoom->isParticipant(Auth::id()) && $labRoom->status !== 'completed')
                    <div class="lab-compose-area">
                        <div class="lab-compose-avatar">
                            <img src="{{ Auth::user()->avatar_url }}" alt="">
                        </div>
                        <div class="lab-compose-form">
                            <form action="{{ route('lab-room.sources', $labRoom) }}" method="POST">
                                @csrf
                                <input type="text" name="title" class="lab-compose-input" placeholder="Judul sumber..." required maxlength="200" style="margin-bottom:8px">
                                <input type="url" name="url" class="lab-compose-input" placeholder="https://contoh.com/artikel" required maxlength="500" style="margin-bottom:8px">
                                <textarea name="summary" class="lab-compose-textarea" rows="2" placeholder="Ringkasan singkat isi sumber (opsional)" maxlength="1000"></textarea>
                                <div class="lab-compose-footer">
                                    <div class="lab-compose-tools">
                                        <button type="button" class="lab-compose-tool-btn lab-compose-tool-primary" title="Lampirkan Data">
                                            <span class="material-symbols-outlined" style="font-size:18px">add_link</span>
                                        </button>
                                    </div>
                                    <button type="submit" class="lab-compose-submit">Tambah Sumber</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- ANALISIS TAB --}}
                <div class="lab-phase-content" id="phase-analisis" style="{{ $labRoom->phase !== 'analisis' ? 'display:none' : '' }}">
                    @if($labRoom->discussions->count() > 0)
                    <div class="lab-thread-list">
                        @foreach($labRoom->discussions as $discussion)
                        <div class="lab-thread-item">
                            <div class="lab-thread-avatar-col">
                                <div class="lab-thread-avatar">
                                    <img src="{{ $discussion->user->avatar_url }}" alt="">
                                </div>
                                @if(!$loop->last)<div class="lab-thread-line"></div>@endif
                            </div>
                            <div class="lab-thread-content">
                                <div class="lab-thread-card">
                                    <div class="lab-thread-header">
                                        <div>
                                            <span class="lab-thread-author">{{ $discussion->user->name }}</span>
                                            <span class="lab-thread-univ">{{ $discussion->user->universitas ?? '' }}</span>
                                        </div>
                                        <span class="lab-thread-time">{{ $discussion->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="lab-thread-body">
                                        <p>{{ $discussion->claim }}</p>
                                        @if($discussion->evidence)
                                        <div class="lab-ref-block">{{ $discussion->evidence }}</div>
                                        @endif
                                    </div>
                                    <div class="lab-thread-actions">
                                        <button type="button" class="lab-thread-action-btn" onclick="this.closest('.lab-thread-card').querySelector('.lab-reply-form-wrap').style.display='block'">
                                            <span class="material-symbols-outlined" style="font-size:16px">reply</span> Balas Argumen
                                        </button>
                                    </div>

                                    {{-- Replies --}}
                                    @if($discussion->replies->count() > 0)
                                    <div class="lab-replies-wrap">
                                        @foreach($discussion->replies as $reply)
                                        <div class="lab-reply-item">
                                            <div class="lab-thread-header">
                                                <div>
                                                    <span class="lab-thread-author">{{ $reply->user->name }}</span>
                                                    <span class="lab-thread-univ">{{ $reply->user->universitas ?? '' }}</span>
                                                </div>
                                                <span class="lab-thread-time">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="lab-reply-text">{{ $reply->claim }}</p>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif

                                    {{-- Reply Form --}}
                                    @if($labRoom->isParticipant(Auth::id()) && $labRoom->status !== 'completed')
                                    <div class="lab-reply-form-wrap" style="display:none">
                                        <form action="{{ route('lab-room.discussions', $labRoom) }}" method="POST" class="lab-inline-reply">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $discussion->id }}">
                                            <input type="text" name="claim" class="lab-compose-input" placeholder="Balas argumen ini..." required>
                                            <button type="submit" class="lab-compose-submit">Balas</button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="lab-empty-phase">
                        <span class="material-symbols-outlined">forum</span>
                        <p>Belum ada diskusi. Mulai analisis kritis dengan menambahkan argumen pertama.</p>
                    </div>
                    @endif

                    {{-- Compose new argument --}}
                    @if($labRoom->isParticipant(Auth::id()) && $labRoom->status !== 'completed')
                    <div class="lab-compose-area">
                        <div class="lab-compose-avatar">
                            <img src="{{ Auth::user()->avatar_url }}" alt="">
                        </div>
                        <div class="lab-compose-form">
                            <form action="{{ route('lab-room.discussions', $labRoom) }}" method="POST">
                                @csrf
                                <textarea name="claim" class="lab-compose-textarea" rows="4" placeholder="Tulis analisis Anda... Sertakan klaim dan bukti pendukung." required maxlength="2000"></textarea>
                                <input type="text" name="evidence" class="lab-compose-input" placeholder="Referensi pendukung (opsional)" maxlength="2000" style="margin-top:8px">
                                <div class="lab-compose-footer">
                                    <div class="lab-compose-tools">
                                        <button type="button" class="lab-compose-tool-btn" title="Tebal"><span class="material-symbols-outlined" style="font-size:18px">format_bold</span></button>
                                        <button type="button" class="lab-compose-tool-btn" title="Miring"><span class="material-symbols-outlined" style="font-size:18px">format_italic</span></button>
                                        <button type="button" class="lab-compose-tool-btn" title="Kutip"><span class="material-symbols-outlined" style="font-size:18px">format_quote</span></button>
                                        <div style="width:1px;height:20px;background:#e2e8f0;margin:0 4px"></div>
                                        <button type="button" class="lab-compose-tool-btn lab-compose-tool-primary" title="Lampirkan Data"><span class="material-symbols-outlined" style="font-size:18px">add_link</span></button>
                                    </div>
                                    <button type="submit" class="lab-compose-submit">Kirim Argumen</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- OUTPUT TAB --}}
                <div class="lab-phase-content" id="phase-output" style="{{ $labRoom->phase !== 'output' ? 'display:none' : '' }}">
                    @php $brief = $labRoom->policyBrief; @endphp

                    @if($labRoom->isHost(Auth::id()) && $labRoom->status !== 'completed')
                    <div class="lab-brief-editor">
                        <form action="{{ route('lab-room.brief', $labRoom) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Judul Policy Brief <span style="color:#EF4444">*</span></label>
                                <input type="text" name="title" class="form-input" value="{{ $brief->title ?? '' }}" placeholder="Judul naskah kebijakan..." required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Ringkasan Eksekutif <span style="color:#EF4444">*</span></label>
                                <textarea name="summary" class="form-input form-textarea" rows="3" required placeholder="Ringkasan singkat...">{{ $brief->summary ?? '' }}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Ringkasan Masalah <span style="color:#EF4444">*</span></label>
                                <textarea name="problem" class="form-input form-textarea" rows="4" required placeholder="Deskripsikan permasalahan...">{{ $brief->problem ?? '' }}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Analisis & Data <span style="color:#EF4444">*</span></label>
                                <textarea name="analysis" class="form-input form-textarea" rows="5" required placeholder="Analisis berbasis data...">{{ $brief->analysis ?? '' }}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Rekomendasi Kebijakan <span style="color:#EF4444">*</span></label>
                                <textarea name="recommendation" class="form-input form-textarea" rows="4" required placeholder="Rekomendasi kebijakan...">{{ $brief->recommendation ?? '' }}</textarea>
                            </div>
                            <div class="lab-brief-actions">
                                <button type="submit" name="action" value="draft" class="btn-outline">Simpan Draft</button>
                                <button type="submit" name="action" value="submit" class="lab-compose-submit" onclick="return confirm('Submit ke review Agent?')">Submit ke Review</button>
                            </div>
                        </form>
                    </div>
                    @elseif($brief)
                    <div class="lab-brief-preview">
                        <div class="lab-brief-status-badge {{ $brief->status }}">{{ $brief->statusLabel() }}</div>
                        <h3 style="font-family:'IBM Plex Serif',serif;font-size:20px;margin-bottom:8px">{{ $brief->title }}</h3>
                        <p class="lab-brief-summary">{{ $brief->summary }}</p>
                        <div class="lab-brief-section"><h4>Ringkasan Masalah</h4><p>{{ $brief->problem }}</p></div>
                        <div class="lab-brief-section"><h4>Analisis & Data</h4><p>{{ $brief->analysis }}</p></div>
                        <div class="lab-brief-section"><h4>Rekomendasi Kebijakan</h4><p>{{ $brief->recommendation }}</p></div>
                        @if($brief->isApproved())
                        <a href="{{ route('policy-lab.show', $brief) }}" class="lab-compose-submit" style="display:inline-flex;margin-top:12px;text-decoration:none">Lihat di Policy Lab</a>
                        @endif
                    </div>
                    @else
                    <div class="lab-empty-phase">
                        <span class="material-symbols-outlined">description</span>
                        <p>Host belum mulai menyusun Policy Brief.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Status Alert --}}
        @if($labRoom->status !== 'completed' && $labRoom->policyBrief && $labRoom->policyBrief->isDraft())
        <div class="lab-status-alert">
            <span class="material-symbols-outlined">lightbulb</span>
            <div class="lab-status-alert-body">
                <h4>Status Draf Kebijakan</h4>
                <p>Pastikan setiap klaim didukung oleh setidaknya 2 sumber terverifikasi di tab <strong>Literasi</strong> sebelum disetujui untuk masuk ke <strong>Basis Data</strong>.</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Right Sidebar --}}
    <aside class="lab-composer-sidebar">
        {{-- Collaborators Panel --}}
        <div class="lab-collab-panel">
            <div class="lab-collab-header">
                <h3>Kolaborator ({{ $labRoom->participants->count() }}/{{ $labRoom->max_participants }})</h3>
                <span class="material-symbols-outlined" style="font-size:18px;color:#94a3b8">groups</span>
            </div>
            <div class="lab-collab-list">
                @php $colors = [['#fef3c7','#92400e'],['#dbeafe','#1e40af'],['#f3e8ff','#7e22ce'],['#f1f5f9','#475569']]; @endphp
                @foreach($labRoom->participants as $participant)
                @php $c = $colors[$loop->index % 4]; @endphp
                <div class="lab-collab-item">
                    <div class="lab-collab-avatar" style="background:{{ $c[0] }};color:{{ $c[1] }}">
                        {{ strtoupper(substr($participant->name, 0, 1)) }}
                    </div>
                    <div class="lab-collab-info">
                        <div class="lab-collab-name-row">
                            <p class="lab-collab-name">{{ $participant->name }}</p>
                            @if($labRoom->isHost($participant->id))
                                <span class="lab-collab-host-badge">Host</span>
                            @endif
                        </div>
                        <p class="lab-collab-univ">{{ $participant->universitas ?? '' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @if(!$labRoom->isFull() && $labRoom->status !== 'completed')
            <div class="lab-collab-footer">
                @if(!$labRoom->isParticipant(Auth::id()))
                <form action="{{ route('lab-room.join', $labRoom) }}" method="POST" style="width:100%">
                    @csrf
                    <button type="submit" class="lab-collab-invite-btn">
                        <span class="material-symbols-outlined" style="font-size:14px">add</span> Bergabung
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>

        {{-- Host Control --}}
        @if($labRoom->isHost(Auth::id()) && $labRoom->status !== 'completed')
        <div class="lab-host-control">
            <div class="lab-host-control-badge">
                <span class="lab-host-control-icon">B</span>
                <h3>Kontrol Host</h3>
            </div>
            <p>Setelah draf final disetujui kuorum, kirim ke Policy Lab untuk diterbitkan.</p>
            <form action="{{ route('lab-room.advance', $labRoom) }}" method="POST">
                @csrf
                <button type="submit" class="lab-host-control-btn" onclick="return confirm('Lanjutkan?')">
                    <span class="material-symbols-outlined" style="font-size:18px">rocket_launch</span>
                    {{ $labRoom->phase === 'output' ? 'Selesaikan Room' : 'Lanjut Fase' }}
                </button>
            </form>
        </div>
        @endif

        {{-- Session Stats --}}
        <div class="lab-session-stats">
            <p class="lab-session-stats-title">Statistik Sesi</p>
            <div class="lab-session-stat-row">
                <span>Sumber dikutip</span>
                <span class="lab-session-stat-value">{{ $labRoom->sources->count() }}</span>
            </div>
            <div class="lab-session-stat-row">
                <span>Argumen tervalidasi</span>
                <span class="lab-session-stat-value">{{ $labRoom->discussions->count() }}</span>
            </div>
            <div class="lab-session-stat-row">
                <span>Peserta</span>
                <span class="lab-session-stat-value">{{ $labRoom->participants->count() }}</span>
            </div>
        </div>

        {{-- Leave Room --}}
        @if($labRoom->isParticipant(Auth::id()) && !$labRoom->isHost(Auth::id()) && $labRoom->status !== 'completed')
        <form action="{{ route('lab-room.leave', $labRoom) }}" method="POST" style="margin-top:12px">
            @csrf
            <button type="submit" class="btn-outline btn-full" style="color:#EF4444;border-color:#EF4444;font-size:13px">Keluar dari Room</button>
        </form>
        @endif
    </aside>
</div>

@push('scripts')
<script>
function showPhaseContent(phase, btn) {
    // Sembunyikan semua konten fase
    document.querySelectorAll('.lab-phase-content').forEach(p => p.style.display = 'none');
    
    // Hapus class active dari semua tombol dan class current dari semua circle
    document.querySelectorAll('.lab-phase-nav-btn').forEach(t => {
        t.classList.remove('active');
        const circle = t.querySelector('.lab-phase-nav-circle');
        if (circle) circle.classList.remove('current');
    });

    // Tampilkan fase yang dipilih
    document.getElementById('phase-' + phase).style.display = 'block';
    
    // Tambahkan class active ke tombol yang diklik dan current ke circle-nya
    btn.classList.add('active');
    const btnCircle = btn.querySelector('.lab-phase-nav-circle');
    if (btnCircle) btnCircle.classList.add('current');

    // Update section title sesuai tab yang dibuka
    const sectionTitle = document.querySelector('.lab-composer-section-title');
    if (sectionTitle) {
        if (phase === 'literasi') {
            sectionTitle.innerHTML = 'Sumber & Referensi Terkumpul';
        } else if (phase === 'analisis') {
            sectionTitle.innerHTML = 'Diskusi Terstruktur: {{ Str::limit($labRoom->title, 40) }}';
        } else if (phase === 'output') {
            sectionTitle.innerHTML = 'Susun Policy Brief';
        }
    }
}
</script>
@endpush
@endsection
