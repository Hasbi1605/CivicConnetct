@php
    $categoryColors = [
        'fact-check' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe'],
        'kebijakan' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0'],
        'sosial' => ['bg' => '#faf5ff', 'text' => '#7e22ce', 'border' => '#e9d5ff'],
        'lainnya' => ['bg' => '#f8fafc', 'text' => '#475569', 'border' => '#e2e8f0'],
    ];
    $cc = $categoryColors[$room->category] ?? $categoryColors['lainnya'];
    $isDraft = $room->status === 'open' && $room->participants->count() <= 1;
@endphp
<a href="{{ route('lab-room.show', $room) }}" class="ldb-project-card {{ $room->status === 'completed' ? 'ldb-card-completed' : '' }}" style="text-decoration:none;color:inherit;">
    <div class="ldb-card-inner">
        {{-- Top row: tags + menu --}}
        <div class="ldb-card-top">
            <div class="ldb-card-tags">
                <span class="ldb-tag" style="background:{{ $cc['bg'] }};color:{{ $cc['text'] }};border-color:{{ $cc['border'] }}">{{ ucfirst(str_replace('-', ' ', $room->category)) }}</span>
                @if($room->is_private)
                    <span class="ldb-tag" style="background:#fef2f2;color:#dc2626;border-color:#fecaca">Privat</span>
                @endif
            </div>
            @if($isDraft)
                <span class="ldb-draft-badge">DRAFT</span>
            @endif
        </div>

        {{-- Title --}}
        <h3 class="ldb-card-title">{{ $room->title }}</h3>

        {{-- Description --}}
        @if($room->description)
        <p class="ldb-card-desc">{{ Str::limit($room->description, 120) }}</p>
        @endif

        {{-- Footer: collaborators + L.A.B phases --}}
        <div class="ldb-card-footer">
            <div class="ldb-card-collab">
                <div class="ldb-avatar-stack">
                    @foreach($room->participants->take(3) as $p)
                    <div class="ldb-avatar-circle" title="{{ $p->name }}">{{ strtoupper(substr($p->name, 0, 2)) }}</div>
                    @endforeach
                    @if($room->participants->count() > 3)
                    <div class="ldb-avatar-circle ldb-avatar-more">+{{ $room->participants->count() - 3 }}</div>
                    @endif
                </div>
                <span class="ldb-card-time">{{ $room->updated_at->diffForHumans() }}</span>
            </div>
            <div class="ldb-phase-dots">
                @php $pn = $room->phaseNumber(); @endphp
                <div class="ldb-pdot-group">
                    <span class="ldb-pdot-label {{ $pn >= 1 ? ($pn == 1 ? 'ldb-pdot-active' : 'ldb-pdot-done') : '' }}">L</span>
                    <div class="ldb-pdot {{ $pn >= 1 ? ($pn == 1 ? 'ldb-pdot-pulse' : 'ldb-pdot-done') : '' }}"></div>
                </div>
                <div class="ldb-pdot-line {{ $pn >= 2 ? 'ldb-pdot-done' : '' }}"></div>
                <div class="ldb-pdot-group">
                    <span class="ldb-pdot-label {{ $pn >= 2 ? ($pn == 2 ? 'ldb-pdot-active' : 'ldb-pdot-done') : '' }}">A</span>
                    <div class="ldb-pdot {{ $pn >= 2 ? ($pn == 2 ? 'ldb-pdot-pulse' : 'ldb-pdot-done') : '' }}"></div>
                </div>
                <div class="ldb-pdot-line {{ $pn >= 3 ? 'ldb-pdot-done' : '' }}"></div>
                <div class="ldb-pdot-group">
                    <span class="ldb-pdot-label {{ $pn >= 3 ? ($pn == 3 ? 'ldb-pdot-active' : 'ldb-pdot-done') : '' }}">B</span>
                    <div class="ldb-pdot {{ $pn >= 3 ? ($pn == 3 ? 'ldb-pdot-pulse' : 'ldb-pdot-done') : '' }}"></div>
                </div>
            </div>
        </div>
    </div>
</a>
