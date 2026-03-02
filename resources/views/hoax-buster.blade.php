@extends('layouts.feature')

@section('title', 'Pusat Penumpas Hoaks')

@section('content')
<div class="hbc-page">
    {{-- Page Header --}}
    <div class="hbc-header">
        <div>
            <h2 class="hbc-header-title">Pusat Penumpas Hoaks</h2>
            <p class="hbc-header-subtitle">Verifikasi klaim dan informasi dengan keahlian akademik komunitas.</p>
        </div>
        @if(!Auth::user()->isAnonim())
        <div class="hbc-header-actions">
            <button class="hbc-btn-primary-header" onclick="document.getElementById('claim-modal').style.display='flex'">
                <span class="material-symbols-outlined" style="font-size:20px">add</span> Laporkan Klaim
            </button>
        </div>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Stats Row removed as requested --}}

    {{-- Filter Bar --}}
    <div class="hbc-filter-bar">
        <div class="hbc-filter-tabs">
            <a href="{{ route('hoax-buster', ['status' => $status]) }}" class="hbc-filter-tab {{ (!$category || $category === 'semua') ? 'active' : '' }}">Semua</a>
            <a href="{{ route('hoax-buster', ['category' => 'politik', 'status' => $status]) }}" class="hbc-filter-tab {{ $category === 'politik' ? 'active' : '' }}">Politik</a>
            <a href="{{ route('hoax-buster', ['category' => 'kesehatan', 'status' => $status]) }}" class="hbc-filter-tab {{ $category === 'kesehatan' ? 'active' : '' }}">Kesehatan</a>
            <a href="{{ route('hoax-buster', ['category' => 'teknologi', 'status' => $status]) }}" class="hbc-filter-tab {{ $category === 'teknologi' ? 'active' : '' }}">Teknologi</a>
            <a href="{{ route('hoax-buster', ['category' => 'sosial', 'status' => $status]) }}" class="hbc-filter-tab {{ $category === 'sosial' ? 'active' : '' }}">Sosial</a>
        </div>
        <div class="hbc-filter-right">
            <span class="hbc-filter-label">Status:</span>
            <select class="hbc-filter-select" onchange="window.location.href=this.value">
                <option value="{{ route('hoax-buster', ['category' => $category, 'status' => 'open']) }}" {{ $status === 'open' ? 'selected' : '' }}>Terbuka ({{ $totalOpen }})</option>
                <option value="{{ route('hoax-buster', ['category' => $category, 'status' => 'resolved']) }}" {{ $status === 'resolved' ? 'selected' : '' }}>Selesai ({{ $totalVerified }})</option>
                <option value="{{ route('hoax-buster', ['category' => $category, 'status' => 'all']) }}" {{ $status === 'all' ? 'selected' : '' }}>Semua</option>
            </select>
        </div>
    </div>

    {{-- Grid: Claims + Sidebar --}}
    <div class="hbc-grid">
        {{-- Claims List --}}
        <div class="hbc-claims-list">
            @forelse($claims as $claim)
            @php
                $consensus = $claim->consensusResult();
                $counts = $claim->verdictCounts();
            @endphp
            <div class="hbc-claim-card {{ $claim->isResolved() ? 'hbc-claim-faded' : '' }}">
                <div class="hbc-claim-top-row">
                    <div class="hbc-claim-badges">
                        @if($claim->isOpen())
                            <span class="hbc-badge hbc-badge-blue">Terbuka</span>
                        @else
                            <span class="hbc-badge hbc-badge-green-solid">Selesai — {{ $claim->finalVerdictLabel() }}</span>
                        @endif
                        <span class="hbc-badge hbc-badge-slate">{{ $claim->categoryLabel() }}</span>
                    </div>
                    <span class="hbc-claim-id">#HB-{{ $claim->id }}</span>
                </div>
                <div class="hbc-claim-body">
                    <div class="hbc-claim-content">
                        <h3 class="hbc-claim-title">"{{ $claim->title }}"</h3>
                        @if($claim->description)
                        <p class="hbc-claim-desc">{{ Str::limit($claim->description, 180) }}</p>
                        @endif
                        <div class="hbc-claim-meta">
                            <div class="hbc-meta-item">
                                <span class="material-symbols-outlined hbc-meta-icon">person</span>
                                <span>{{ $claim->reporter->name }}</span>
                            </div>
                            <div class="hbc-meta-item">
                                <span class="material-symbols-outlined hbc-meta-icon">language</span>
                                <span>{{ $claim->platformLabel() }}</span>
                            </div>
                            <div class="hbc-meta-item">
                                <span class="material-symbols-outlined hbc-meta-icon">how_to_vote</span>
                                <span class="hbc-meta-bold">{{ $counts['total'] }} Putusan</span>
                            </div>
                            <div class="hbc-meta-item">
                                <span class="material-symbols-outlined hbc-meta-icon">schedule</span>
                                <span>{{ $claim->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="hbc-claim-sentiment">
                        @if($counts['total'] > 0)
                        <div class="hbc-sentiment-head">
                            <div class="hbc-sentiment-label">Konsensus Saat Ini</div>
                            <div class="hbc-sentiment-result">
                                @php
                                    $verdictLabel = match($consensus['verdict']) {
                                        'hoax' => 'HOAKS',
                                        'misleading' => 'MENYESATKAN',
                                        'valid' => 'VALID',
                                        default => '-',
                                    };
                                    $dotClass = match($consensus['verdict']) {
                                        'hoax' => 'hbc-dot-red',
                                        'misleading' => 'hbc-dot-amber',
                                        'valid' => 'hbc-dot-green',
                                        default => '',
                                    };
                                @endphp
                                <span class="hbc-sentiment-pct">{{ $consensus['percentage'] }}% {{ $verdictLabel }}</span>
                                <div class="hbc-sentiment-dot {{ $dotClass }}"></div>
                            </div>
                            <div class="hbc-sentiment-bar">
                                @if($counts['total'] > 0)
                                <div class="hbc-bar-fill hbc-bar-red" style="width:{{ round(($counts['hoax'] / $counts['total']) * 100) }}%"></div>
                                <div class="hbc-bar-fill hbc-bar-amber" style="width:{{ round(($counts['misleading'] / $counts['total']) * 100) }}%"></div>
                                <div class="hbc-bar-fill hbc-bar-green" style="width:{{ round(($counts['valid'] / $counts['total']) * 100) }}%"></div>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="hbc-sentiment-head">
                            <div class="hbc-sentiment-label">Belum ada putusan</div>
                        </div>
                        @endif

                        @if($claim->isOpen())
                        <a href="{{ route('hoax-buster.show', $claim) }}" class="hbc-btn-verify">Verifikasi</a>
                        @elseif($claim->isResolved())
                        <a href="{{ route('hoax-buster.show', $claim) }}" class="hbc-btn-detail">Lihat Hasil</a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="hbc-empty-state">
                <span class="material-symbols-outlined" style="font-size:48px;color:#cbd5e1">fact_check</span>
                <p>Belum ada klaim untuk ditampilkan.</p>
            </div>
            @endforelse

            {{ $claims->links() }}
        </div>

        {{-- Sidebar --}}
        <aside class="hbc-aside">
            {{-- Leaderboard --}}
            <div class="hbc-aside-card">
                <h3 class="hbc-aside-heading">
                    <span class="material-symbols-outlined" style="font-size:18px">leaderboard</span>
                    Leaderboard Verifikator
                </h3>
                <div class="hbc-leader-list">
                    @forelse($leaderboard as $index => $entry)
                    @php
                        $rankColors = ['#eab308', '#94a3b8', '#c2410c'];
                    @endphp
                    <div class="hbc-leader-item">
                        <div class="hbc-leader-avatar-wrap">
                            <img src="{{ $entry->user->avatar_url }}" alt="{{ $entry->user->name }}" class="hbc-leader-avatar-img">
                            <div class="hbc-leader-rank" style="background:{{ $rankColors[$index] ?? '#64748b' }}">{{ $index + 1 }}</div>
                        </div>
                        <div class="hbc-leader-info">
                            <p class="hbc-leader-name">{{ $entry->user->name }}</p>
                            <p class="hbc-leader-points">{{ $entry->total_verdicts }} Putusan Disetujui</p>
                        </div>
                    </div>
                    @empty
                    <p style="color:var(--text-secondary);font-size:13px;padding:8px 0">Belum ada kontributor.</p>
                    @endforelse
                </div>
            </div>

            {{-- Status Summary --}}
            <div class="hbc-aside-card">
                <h3 class="hbc-aside-heading">
                    <span class="material-symbols-outlined" style="font-size:18px">analytics</span>
                    Status Verifikasi
                </h3>
                <div class="hbc-status-list-aside">
                    <div class="hbc-status-row">
                        <span class="hbc-status-dot" style="background:#f59e0b"></span>
                        <span class="hbc-status-name">Menunggu Peninjauan</span>
                        <span class="hbc-status-count">{{ $totalPending }}</span>
                    </div>
                    <div class="hbc-status-row">
                        <span class="hbc-status-dot" style="background:#0f4c81"></span>
                        <span class="hbc-status-name">Terbuka</span>
                        <span class="hbc-status-count">{{ $totalOpen }}</span>
                    </div>
                    <div class="hbc-status-row">
                        <span class="hbc-status-dot" style="background:#10b981"></span>
                        <span class="hbc-status-name">Selesai</span>
                        <span class="hbc-status-count">{{ $totalVerified }}</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

{{-- Submit Claim Modal --}}
@if(!Auth::user()->isAnonim())
<div class="modal-overlay" id="claim-modal" style="display:none;">
    <div class="modal-content" style="max-width:560px">
        <div class="modal-header">
            <h3>Laporkan Klaim Baru</h3>
            <button class="modal-close" onclick="document.getElementById('claim-modal').style.display='none'">&times;</button>
        </div>
        <form action="{{ route('hoax-buster.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Klaim yang Ingin Diverifikasi <span style="color:#cc1016">*</span></label>
                    <textarea name="title" class="form-input form-textarea" rows="3" placeholder='Contoh: "Vaksin COVID-19 menyebabkan autisme pada anak."' required maxlength="500">{{ old('title') }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi / Konteks Tambahan</label>
                    <textarea name="description" class="form-input form-textarea" rows="2" placeholder="Jelaskan konteks di mana klaim ini ditemukan..." maxlength="2000">{{ old('description') }}</textarea>
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Platform Sumber <span style="color:#cc1016">*</span></label>
                        <select name="source_platform" class="form-input" required>
                            <option value="">Pilih platform</option>
                            <option value="twitter" {{ old('source_platform') === 'twitter' ? 'selected' : '' }}>Twitter/X</option>
                            <option value="whatsapp" {{ old('source_platform') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                            <option value="facebook" {{ old('source_platform') === 'facebook' ? 'selected' : '' }}>Facebook</option>
                            <option value="instagram" {{ old('source_platform') === 'instagram' ? 'selected' : '' }}>Instagram</option>
                            <option value="website" {{ old('source_platform') === 'website' ? 'selected' : '' }}>Website/Blog</option>
                            <option value="lainnya" {{ old('source_platform') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori <span style="color:#cc1016">*</span></label>
                        <select name="category" class="form-input" required>
                            <option value="">Pilih kategori</option>
                            <option value="politik" {{ old('category') === 'politik' ? 'selected' : '' }}>Politik</option>
                            <option value="kesehatan" {{ old('category') === 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                            <option value="teknologi" {{ old('category') === 'teknologi' ? 'selected' : '' }}>Teknologi</option>
                            <option value="sosial" {{ old('category') === 'sosial' ? 'selected' : '' }}>Sosial</option>
                            <option value="lainnya" {{ old('category') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">URL Sumber (opsional)</label>
                    <input type="url" name="source_url" class="form-input" placeholder="https://..." value="{{ old('source_url') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="document.getElementById('claim-modal').style.display='none'">Batal</button>
                <button type="submit" class="btn-primary">Kirim Klaim</button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
// Close modal on overlay click
document.getElementById('claim-modal')?.addEventListener('click', (e) => {
    if (e.target.id === 'claim-modal') e.target.style.display = 'none';
});
</script>
@endpush
@endsection
