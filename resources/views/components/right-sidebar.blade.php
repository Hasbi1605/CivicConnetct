<aside class="stitch-right-sidebar">
    {{-- Antrean Pantau Hoaks --}}
    <div class="stitch-card stitch-hoax-queue">
        <div class="stitch-hoax-queue-header">
            <h3>
                <span class="material-symbols-outlined text-alert">gpp_maybe</span>
                Antrean Pantau Hoaks
            </h3>
            <span class="stitch-live-badge">LIVE</span>
        </div>
        <div class="stitch-hoax-queue-list">
            @forelse($sidebarHoaxClaims as $claim)
                @php
                    $approvedCount = $claim->approved_verdicts_count;
                    $needed = max(0, 10 - $approvedCount);
                    $progress = min(100, ($approvedCount / 10) * 100);
                    $isUrgent = $progress < 40;
                @endphp
                <a href="{{ route('hoax-buster.show', $claim) }}" class="stitch-hoax-item" style="text-decoration:none;color:inherit;display:block;">
                    <div class="stitch-hoax-item-top">
                        <span class="material-symbols-outlined {{ $isUrgent ? 'text-alert' : 'text-warning-icon' }}">{{ $isUrgent ? 'report' : 'warning' }}</span>
                        <p>{{ Str::limit($claim->title, 80) }}</p>
                    </div>
                    <div class="stitch-hoax-item-meta">
                        <span class="stitch-hoax-id">KASUS #HB-{{ $claim->id }}</span>
                        <span class="stitch-hoax-needed {{ $isUrgent ? 'text-alert' : 'text-warning-icon' }}">{{ $needed }} verifikasi dibutuhkan</span>
                    </div>
                    <div class="stitch-hoax-progress">
                        <div class="stitch-hoax-progress-bar {{ $isUrgent ? 'alert' : 'warning' }}" style="width: {{ $progress }}%"></div>
                    </div>
                </a>
            @empty
                <div style="padding: 12px 0; text-align: center; color: #94a3b8; font-size: 13px;">
                    Tidak ada klaim yang perlu diverifikasi saat ini.
                </div>
            @endforelse
        </div>
        <div class="stitch-hoax-queue-footer">
            <a href="{{ route('hoax-buster') }}">Lihat Pusat Penumpas Hoaks &rarr;</a>
        </div>
    </div>

    {{-- Wacana Tren --}}
    <div class="stitch-card stitch-trending">
        <h3 class="stitch-trending-title">
            <span class="material-symbols-outlined">trending_up</span>
            Wacana Tren
        </h3>
        <ul class="stitch-trending-list">
            @forelse($sidebarTrending as $trending)
                <li>
                    <a href="#">
                        <span class="stitch-trending-category">{{ ucfirst($trending->category === 'fact-check' ? 'Cek Fakta' : $trending->category) }} &bull; {{ $trending->votes_count }} suara</span>
                        <strong>{{ Str::limit($trending->body, 50) }}</strong>
                    </a>
                </li>
            @empty
                <li style="color: #94a3b8; font-size: 13px; padding: 8px 0;">Belum ada wacana tren.</li>
            @endforelse
        </ul>
    </div>

    {{-- Footer --}}
    <div class="stitch-sidebar-footer">
        <p>&copy; {{ date('Y') }} CIVIC-Connect. Inisiatif untuk integritas akademik.</p>
        <div class="stitch-footer-links">
            <a href="#">Privasi</a>
            <span>&bull;</span>
            <a href="#">Ketentuan</a>
            <span>&bull;</span>
            <a href="#">Metodologi</a>
        </div>
    </div>
</aside>
