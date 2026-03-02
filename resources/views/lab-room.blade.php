@extends('layouts.feature')

@section('title', 'Dasbor Kolaborasi L.A.B')

@section('content')
<div class="ldb-page">
    {{-- Page Header (card style — matches plab-header) --}}
    <div class="ldb-header">
        <div>
            <h2 class="ldb-title">Dasbor Kolaborasi L.A.B</h2>
            <p class="ldb-subtitle">Kelola proyek riset kolaboratif lintas universitas.</p>
        </div>
        <div class="ldb-header-actions">
            <a href="{{ route('policy-lab.index') }}" class="ldb-btn-outline">
                <span class="material-symbols-outlined" style="font-size:20px">policy</span> Buka Policy Lab
            </a>
            <button class="ldb-btn-create" onclick="document.getElementById('create-room-modal').style.display='flex'">
                <span class="material-symbols-outlined" style="font-size:20px">add_circle</span>
                Buat Ruang LAB Baru
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Filter Bar (matches plab-filter-bar) --}}
    <div class="ldb-filter-bar">
        <div class="ldb-filter-tabs">
            <button class="ldb-filter-btn active" onclick="showLabTab('all', this)">Semua Proyek</button>
            <button class="ldb-filter-btn" onclick="showLabTab('my', this)">Saya Host</button>
            <button class="ldb-filter-btn" onclick="showLabTab('joined', this)">Saya Ikuti</button>
            <button class="ldb-filter-btn" onclick="showLabTab('completed', this)">Selesai</button>
        </div>
        <div class="ldb-filter-right">
            <div class="ldb-search">
                <span class="material-symbols-outlined ldb-search-icon">search</span>
                <input type="text" class="ldb-search-input" placeholder="Cari topik..." id="ldb-search-field">
            </div>
        </div>
    </div>

    {{-- Grid: Main + Sidebar --}}
    <div class="ldb-grid">
        {{-- Main Column --}}
        <div class="ldb-main">
            {{-- All Active Rooms --}}
            <div class="ldb-tab-panel" id="lab-tab-all">
                @if($rooms->count() > 0)
                <div class="ldb-card-list">
                    @foreach($rooms as $room)
                        @include('components.lab-room-card', ['room' => $room])
                    @endforeach
                </div>
                @else
                <div class="ldb-empty">
                    <span class="material-symbols-outlined">science</span>
                    <p>Belum ada room aktif. Mulai kolaborasi dengan membuat room baru!</p>
                </div>
                @endif
            </div>

            {{-- My Rooms --}}
            <div class="ldb-tab-panel" id="lab-tab-my" style="display:none;">
                @if($myRooms->count() > 0)
                <div class="ldb-card-list">
                    @foreach($myRooms as $room)
                        @include('components.lab-room-card', ['room' => $room])
                    @endforeach
                </div>
                @else
                <div class="ldb-empty">
                    <span class="material-symbols-outlined">person</span>
                    <p>Anda belum membuat room. Klik "Buat Ruang LAB Baru" untuk memulai.</p>
                </div>
                @endif
            </div>

            {{-- Joined Rooms --}}
            <div class="ldb-tab-panel" id="lab-tab-joined" style="display:none;">
                @if($joinedRooms->count() > 0)
                <div class="ldb-card-list">
                    @foreach($joinedRooms as $room)
                        @include('components.lab-room-card', ['room' => $room])
                    @endforeach
                </div>
                @else
                <div class="ldb-empty">
                    <span class="material-symbols-outlined">group_add</span>
                    <p>Anda belum bergabung ke room manapun.</p>
                </div>
                @endif
            </div>

            {{-- Completed Rooms --}}
            <div class="ldb-tab-panel" id="lab-tab-completed" style="display:none;">
                @if($completedRooms->count() > 0)
                <div class="ldb-card-list">
                    @foreach($completedRooms as $room)
                        @include('components.lab-room-card', ['room' => $room])
                    @endforeach
                </div>
                @else
                <div class="ldb-empty">
                    <span class="material-symbols-outlined">check_circle</span>
                    <p>Belum ada room yang selesai.</p>
                </div>
                @endif
            </div>

            {{-- Archive --}}
            <button class="ldb-archive-btn">Lihat Proyek Arsip</button>
        </div>{{-- end ldb-main --}}

        {{-- Sidebar --}}
        <aside class="ldb-sidebar">
        {{-- National Collaboration Map --}}
        <div class="ldb-side-card">
            <div class="ldb-side-card-header">
                <h3>Peta Kolaborasi Nasional</h3>
                <span class="material-symbols-outlined" style="font-size:18px;color:#94a3b8">public</span>
            </div>
            <div class="ldb-map-area">
                <div class="ldb-map-bg"></div>
                <div class="ldb-map-dot" style="top:60%;left:30%"></div>
                <div class="ldb-map-dot" style="top:65%;left:45%"></div>
                <div class="ldb-map-dot" style="top:55%;left:65%"></div>
                <svg class="ldb-map-lines">
                    <line x1="30%" y1="60%" x2="45%" y2="65%" stroke="#0F4C81" stroke-width="1" stroke-dasharray="2 2" stroke-opacity="0.5"/>
                    <line x1="45%" y1="65%" x2="65%" y2="55%" stroke="#0F4C81" stroke-width="1" stroke-dasharray="2 2" stroke-opacity="0.5"/>
                </svg>
                <div class="ldb-map-badge">
                    <span class="ldb-map-badge-num">{{ $stats['total_participants'] }}</span> Partisipan Terhubung
                </div>
            </div>
            <div class="ldb-map-footer">
                <span>Top Kolaborator:</span>
                <span style="font-weight:500;color:#1a1d1f">{{ Auth::user()->universitas ?? 'UI, ITB, UGM' }}</span>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="ldb-side-card">
            <div class="ldb-side-card-header">
                <h3>Aktivitas Terbaru</h3>
                <a href="#" class="ldb-side-link">Lihat Semua</a>
            </div>
            <div class="ldb-activity-list">
                @foreach($rooms->take(3) as $room)
                <div class="ldb-activity-item">
                    <div class="ldb-activity-avatar">
                        <span style="font-weight:700;font-size:11px;color:#64748b">{{ strtoupper(substr($room->host->name, 0, 2)) }}</span>
                    </div>
                    <div class="ldb-activity-body">
                        <p class="ldb-activity-text">
                            <span style="font-weight:600">{{ $room->host->name }}</span> memperbarui proyek
                            <span style="font-weight:500;color:#0f4c81">{{ Str::limit($room->title, 30) }}</span>.
                        </p>
                        <p class="ldb-activity-time">{{ $room->updated_at->diffForHumans() }} • Tahap {{ $room->current_phase }}</p>
                    </div>
                </div>
                @endforeach
                @if($rooms->count() === 0)
                <p style="font-size:12px;color:#94a3b8;text-align:center;padding:16px">Belum ada aktivitas.</p>
                @endif
            </div>
        </div>

        {{-- Contribution Stats --}}
        <div class="ldb-stats-card">
            <div class="ldb-stats-glow"></div>
            <div class="ldb-stats-inner">
                <h3>Statistik Kontribusi Anda</h3>
                <p class="ldb-stats-period">Bulan ini</p>
                <div class="ldb-stats-grid">
                    <div>
                        <span class="ldb-stats-num">{{ $stats['active_rooms'] }}</span>
                        <span class="ldb-stats-label">PROYEK AKTIF</span>
                    </div>
                    <div>
                        <span class="ldb-stats-num">{{ $stats['completed_rooms'] }}</span>
                        <span class="ldb-stats-label">SELESAI</span>
                    </div>
                </div>
                <div class="ldb-stats-bottom">
                    <p>Anda masuk dalam <strong style="color:#fff">Top 10%</strong> kontributor aktif.</p>
                </div>
            </div>
        </div>
    </aside>
    </div>{{-- end ldb-grid --}}
</div>{{-- end ldb-page --}}

{{-- Create Room Modal --}}
<div class="modal-overlay" id="create-room-modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Buat Ruang L.A.B Baru</h3>
            <button class="modal-close" onclick="document.getElementById('create-room-modal').style.display='none'">&times;</button>
        </div>
        <form action="{{ route('lab-room.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Judul Room <span style="color:#cc1016">*</span></label>
                    <input type="text" name="title" class="form-input" placeholder="Contoh: Dampak Ekonomi Hilirisasi Nikel" required maxlength="200">
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-input form-textarea" rows="3" placeholder="Jelaskan topik dan tujuan riset kolaboratif..." maxlength="1000"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori <span style="color:#cc1016">*</span></label>
                    <select name="category" class="form-input" required>
                        <option value="fact-check">Fact-Check</option>
                        <option value="kebijakan">Kebijakan Publik</option>
                        <option value="sosial">Isu Sosial</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Target Output</label>
                    <select name="target" class="form-input">
                        <option value="Policy Brief">Policy Brief</option>
                        <option value="Fact-Check">Fact-Check</option>
                        <option value="Video Edukasi">Video Edukasi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="is_private" value="1">
                        Room Privat (hanya bisa diakses dengan undangan)
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="document.getElementById('create-room-modal').style.display='none'">Batal</button>
                <button type="submit" class="btn-primary">Buat Room</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showLabTab(tab, btn) {
    document.querySelectorAll('.ldb-tab-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.ldb-filter-btn').forEach(t => t.classList.remove('active'));
    document.getElementById('lab-tab-' + tab).style.display = 'block';
    btn.classList.add('active');
}

document.getElementById('create-room-modal')?.addEventListener('click', (e) => {
    if (e.target.id === 'create-room-modal') e.target.style.display = 'none';
});

// Simple search filter
document.getElementById('ldb-search-field')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.ldb-project-card').forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
