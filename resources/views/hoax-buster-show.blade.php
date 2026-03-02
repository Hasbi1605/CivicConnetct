@extends('layouts.feature')

@section('title', 'Verifikasi Klaim — Hoax Buster')

@section('content')
<div class="hbs-page">
    {{-- Back link --}}
    <a href="{{ route('hoax-buster') }}" class="hbs-back-link">
        <span class="material-symbols-outlined" style="font-size:18px">arrow_back</span>
        Kembali ke Hoax Buster
    </a>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Detail Header --}}
    <div class="hbs-header-card">
        <div class="hbs-header-top">
            <div class="hbs-badges">
                @if($hoaxClaim->isOpen())
                    <span class="hbc-badge hbc-badge-blue">
                        <span class="material-symbols-outlined" style="font-size:14px">pending_actions</span>
                        Terbuka untuk Verifikasi
                    </span>
                @elseif($hoaxClaim->isResolved())
                    <span class="hbc-badge hbc-badge-green-solid">
                        <span class="material-symbols-outlined" style="font-size:14px">check_circle</span>
                        Selesai — {{ $hoaxClaim->finalVerdictLabel() }}
                    </span>
                @endif
                <span class="hbc-badge hbc-badge-slate">{{ $hoaxClaim->categoryLabel() }}</span>
                <span class="hbs-case-id">KASUS #HB-{{ $hoaxClaim->id }}</span>
            </div>
        </div>
        <h1 class="hbs-title">"{{ $hoaxClaim->title }}"</h1>
        @if($hoaxClaim->description)
        <p class="hbs-description">{{ $hoaxClaim->description }}</p>
        @endif
        <div class="hbs-meta-row">
            <div class="hbs-meta-item">
                <span class="material-symbols-outlined" style="font-size:16px">person</span>
                Dilaporkan oleh <strong>{{ $hoaxClaim->reporter->name }}</strong>
            </div>
            <div class="hbs-meta-item">
                <span class="material-symbols-outlined" style="font-size:16px">language</span>
                {{ $hoaxClaim->platformLabel() }}
            </div>
            @if($hoaxClaim->source_url)
            <a href="{{ $hoaxClaim->source_url }}" target="_blank" class="hbs-meta-item hbs-meta-link">
                <span class="material-symbols-outlined" style="font-size:16px">open_in_new</span>
                Lihat Sumber
            </a>
            @endif
            <div class="hbs-meta-item">
                <span class="material-symbols-outlined" style="font-size:16px">schedule</span>
                {{ $hoaxClaim->created_at->translatedFormat('d M Y, H:i') }}
            </div>
        </div>
    </div>

    {{-- Evidence + Consensus Grid --}}
    <div class="hbs-grid">
        {{-- Community Consensus --}}
        <div class="hbs-card">
            <h3 class="hbs-section-label">
                <span class="material-symbols-outlined" style="font-size:18px">analytics</span>
                Konsensus Komunitas
            </h3>
            <div class="hbs-consensus-inner">
                @if($counts['total'] > 0)
                <div class="hbs-consensus-header">
                    <div>
                        @php
                            $pctLabel = match($consensus['verdict']) {
                                'hoax' => 'HOAKS',
                                'misleading' => 'MENYESATKAN',
                                'valid' => 'VALID',
                                default => '-',
                            };
                        @endphp
                        <div class="hbs-consensus-pct">{{ $consensus['percentage'] }}%</div>
                        <div class="hbs-consensus-label">Cenderung ke {{ $pctLabel }}</div>
                    </div>
                    <div class="hbs-consensus-votes">
                        <div class="hbs-consensus-total">{{ $counts['total'] }} Putusan Disetujui</div>
                    </div>
                </div>
                <div class="hbs-consensus-bar">
                    <div class="hbs-bar-hoax" style="width: {{ $counts['total'] > 0 ? round(($counts['hoax'] / $counts['total']) * 100) : 0 }}%"></div>
                    <div class="hbs-bar-misleading" style="width: {{ $counts['total'] > 0 ? round(($counts['misleading'] / $counts['total']) * 100) : 0 }}%"></div>
                    <div class="hbs-bar-valid" style="width: {{ $counts['total'] > 0 ? round(($counts['valid'] / $counts['total']) * 100) : 0 }}%"></div>
                </div>
                <div class="hbs-consensus-legend">
                    <div class="hbs-legend-item">
                        <div class="hbs-legend-dot" style="background:#ef4444"></div>
                        <span>Hoaks ({{ $counts['hoax'] }})</span>
                    </div>
                    <div class="hbs-legend-item">
                        <div class="hbs-legend-dot" style="background:#f59e0b"></div>
                        <span>Menyesatkan ({{ $counts['misleading'] }})</span>
                    </div>
                    <div class="hbs-legend-item">
                        <div class="hbs-legend-dot" style="background:#10b981"></div>
                        <span>Valid ({{ $counts['valid'] }})</span>
                    </div>
                </div>
                @else
                <div class="hbs-empty-consensus">
                    <span class="material-symbols-outlined" style="font-size:36px;color:#cbd5e1">how_to_vote</span>
                    <p>Belum ada putusan yang disetujui.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Approved Verdicts --}}
        <div class="hbs-card">
            <h3 class="hbs-section-label">
                <span class="material-symbols-outlined" style="font-size:18px">gavel</span>
                Putusan Komunitas ({{ $counts['total'] }})
            </h3>
            <div class="hbs-verdicts-list">
                @forelse($hoaxClaim->approvedVerdicts as $verdict)
                <div class="hbs-verdict-item">
                    <div class="hbs-verdict-item-header">
                        <div class="hbs-verdict-user">
                            <img src="{{ $verdict->user->avatar_url }}" alt="{{ $verdict->user->name }}" class="hbs-verdict-avatar">
                            <div>
                                <span class="hbs-verdict-name">{{ $verdict->user->name }}</span>
                                <span class="hbs-verdict-time">{{ $verdict->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <span class="hbs-verdict-badge" style="background:{{ $verdict->verdictColor() }}20;color:{{ $verdict->verdictColor() }}">
                            {{ $verdict->verdictLabel() }}
                        </span>
                    </div>
                    <p class="hbs-verdict-reasoning">{{ $verdict->reasoning }}</p>
                    @if($verdict->evidence_url)
                    <a href="{{ $verdict->evidence_url }}" target="_blank" class="hbs-verdict-evidence">
                        <span class="material-symbols-outlined" style="font-size:14px">link</span>
                        {{ Str::limit($verdict->evidence_url, 60) }}
                    </a>
                    @endif
                </div>
                @empty
                <p style="color:var(--text-secondary);font-size:13px;padding:12px 0">Belum ada putusan.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Submit Verdict Form (only if open + user hasn't voted + not anonim) --}}
    @if($hoaxClaim->isOpen() && !Auth::user()->isAnonim())
        @if($hasVoted)
        <div class="hbs-card hbs-voted-card">
            <span class="material-symbols-outlined" style="font-size:24px;color:#0f4c81">check_circle</span>
            <div>
                <strong>Anda sudah memberikan putusan untuk klaim ini.</strong>
                <p style="margin:4px 0 0;color:var(--text-secondary)">
                    Putusan Anda: <strong>{{ $userVerdict->verdictLabel() }}</strong>
                    — Status: {{ $userVerdict->isPending() ? 'Menunggu peninjauan' : ($userVerdict->isApproved() ? 'Disetujui' : 'Ditolak') }}
                </p>
            </div>
        </div>
        @else
        <div class="hbs-card hbs-verdict-form-card">
            <h2 class="hbs-verdict-title">Kirim Putusan Anda</h2>
            <p class="hbs-verdict-subtitle">Pilih klasifikasi dan sertakan alasan serta bukti pendukung.</p>

            <form action="{{ route('hoax-buster.verdict', $hoaxClaim) }}" method="POST">
                @csrf
                <div class="hbs-verdict-options">
                    <label class="hbs-verdict-option">
                        <input type="radio" name="verdict" value="valid" class="hbs-verdict-radio" {{ old('verdict') === 'valid' ? 'checked' : '' }}>
                        <div class="hbs-verdict-box hbs-verdict-valid">
                            <div class="hbs-verdict-icon" style="color:#10b981">
                                <span class="material-symbols-outlined">check_circle</span>
                            </div>
                            <h3>Valid</h3>
                            <p>Didukung oleh data yang dapat diverifikasi.</p>
                        </div>
                    </label>
                    <label class="hbs-verdict-option">
                        <input type="radio" name="verdict" value="misleading" class="hbs-verdict-radio" {{ old('verdict') === 'misleading' ? 'checked' : '' }}>
                        <div class="hbs-verdict-box hbs-verdict-misleading">
                            <div class="hbs-verdict-icon" style="color:#f59e0b">
                                <span class="material-symbols-outlined">warning</span>
                            </div>
                            <h3>Menyesatkan</h3>
                            <p>Konteks hilang atau data dipilih secara parsial.</p>
                        </div>
                    </label>
                    <label class="hbs-verdict-option">
                        <input type="radio" name="verdict" value="hoax" class="hbs-verdict-radio" {{ old('verdict', 'hoax') === 'hoax' ? 'checked' : '' }}>
                        <div class="hbs-verdict-box hbs-verdict-hoax">
                            <div class="hbs-verdict-icon" style="color:#ef4444">
                                <span class="material-symbols-outlined">block</span>
                            </div>
                            <h3>Hoaks</h3>
                            <p>Secara faktual salah atau media dimanipulasi.</p>
                        </div>
                    </label>
                </div>

                @error('verdict')
                    <p class="form-error">{{ $message }}</p>
                @enderror

                <div class="form-group" style="margin-top:20px">
                    <label class="form-label">Alasan / Penjelasan <span style="color:#cc1016">*</span></label>
                    <textarea name="reasoning" class="form-input form-textarea" rows="3" placeholder="Jelaskan alasan putusan Anda dengan bukti dan referensi..." required maxlength="2000">{{ old('reasoning') }}</textarea>
                    @error('reasoning')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">URL Bukti Pendukung (opsional)</label>
                    <input type="url" name="evidence_url" class="form-input" placeholder="https://doi.org/... atau URL artikel referensi" value="{{ old('evidence_url') }}">
                    @error('evidence_url')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="hbs-verdict-footer">
                    <div class="hbs-info-note">
                        <span class="material-symbols-outlined" style="font-size:16px">info</span>
                        <span>Putusan akan ditinjau CIVIC Agent sebelum dihitung dalam konsensus.</span>
                    </div>
                    <button type="submit" class="btn-primary">
                        <span class="material-symbols-outlined" style="font-size:18px">gavel</span>
                        Kirim Putusan
                    </button>
                </div>
            </form>
        </div>
        @endif
    @endif

    @if($hoaxClaim->isResolved())
    <div class="hbs-card hbs-resolved-card">
        <span class="material-symbols-outlined" style="font-size:28px;color:#10b981">verified</span>
        <div>
            <h3 style="margin:0">Klaim Ini Telah Diselesaikan</h3>
            <p style="margin:4px 0 0;color:var(--text-secondary)">
                Putusan akhir: <strong>{{ $hoaxClaim->finalVerdictLabel() }}</strong>
                — Diselesaikan {{ $hoaxClaim->resolved_at->diffForHumans() }}
            </p>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verdict option visual feedback
    document.querySelectorAll('.hbs-verdict-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.hbs-verdict-box').forEach(box => box.classList.remove('selected'));
            this.closest('.hbs-verdict-option').querySelector('.hbs-verdict-box').classList.add('selected');
        });
        if (radio.checked) {
            radio.closest('.hbs-verdict-option').querySelector('.hbs-verdict-box').classList.add('selected');
        }
    });
});
</script>
@endpush
@endsection
