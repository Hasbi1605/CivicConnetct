@extends('layouts.feature')

@section('title', 'Policy Lab — Bank Solusi Kebijakan')

@section('content')
<div class="plab-page">
    {{-- Page Header --}}
    <div class="plab-header">
        <div>
            <h2 class="plab-header-title">Policy Lab: Bank Solusi Kebijakan</h2>
            <p class="plab-header-subtitle">Repositori publik risalah kebijakan mahasiswa untuk solusi nasional.</p>
        </div>
        <div class="plab-header-actions">
            @if(!Auth::user()->isAnonim())
            <a href="{{ route('lab-room.index') }}" class="plab-btn-outline">
                <span class="material-symbols-outlined" style="font-size:20px">meeting_room</span> Buka L.A.B Room
            </a>
            <button class="plab-btn-primary" onclick="document.getElementById('create-brief-modal').style.display='flex'">
                <span class="material-symbols-outlined" style="font-size:20px">add</span> Buat Risalah Baru
            </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter Bar --}}
    <div class="plab-filter-bar">
        <div class="plab-filter-tabs">
            <button class="plab-filter-tab active">Terbaru</button>
            <button class="plab-filter-tab">Terpopuler</button>
            <button class="plab-filter-tab">Disukai</button>
        </div>
        <div class="plab-filter-right">
            <span class="plab-filter-label">Kategori:</span>
            <select class="plab-filter-select">
                <option>Semua Kategori</option>
                <option>Ekonomi</option>
                <option>Hukum</option>
                <option>Lingkungan</option>
                <option>Kesehatan</option>
                <option>Pendidikan</option>
            </select>
        </div>
    </div>

    {{-- Published Briefs Grid --}}
    @if($publishedBriefs->count() > 0)
    <div class="plab-grid">
        @php
            $catColors = [
                'standar' => ['bg' => '#eff6ff', 'color' => '#0F4C81', 'border' => '#bfdbfe'],
                'data-driven' => ['bg' => '#f0fdf4', 'color' => '#10B981', 'border' => '#bbf7d0'],
                'quick-response' => ['bg' => '#fefce8', 'color' => '#F59E0B', 'border' => '#fef08a'],
            ];
        @endphp
        @foreach($publishedBriefs as $brief)
        @php $cc = $catColors[$brief->template] ?? $catColors['standar']; @endphp
        <a href="{{ route('policy-lab.show', $brief) }}" class="plab-card">
            <div class="plab-card-body">
                <div class="plab-card-top">
                    <span class="plab-card-badge" style="background:{{ $cc['bg'] }};color:{{ $cc['color'] }};border-color:{{ $cc['border'] }}">{{ $brief->templateLabel() }}</span>
                    <span class="plab-card-date">{{ $brief->created_at->isoFormat('D MMM Y') }}</span>
                </div>
                <h3 class="plab-card-title">{{ $brief->title }}</h3>
                <p class="plab-card-desc">{{ Str::limit($brief->summary, 140) }}</p>
                <div class="plab-card-author">
                    <img src="{{ $brief->author->avatar_url }}" alt="" class="plab-card-avatar">
                    <div class="plab-card-author-info">
                        <p class="plab-card-author-name">{{ $brief->author->name }}</p>
                        <p class="plab-card-author-univ">{{ $brief->author->universitas ?? '' }}</p>
                    </div>
                </div>
            </div>
            <div class="plab-card-footer">
                <div class="plab-card-stats">
                    <div class="plab-card-stat" title="Endorsement">
                        <span class="material-symbols-outlined" style="font-size:18px">verified</span>
                        <span class="plab-card-stat-bold">{{ $brief->endorsementCount() }} Endorse</span>
                    </div>
                    @if($brief->labRoom)
                    <div class="plab-card-stat" title="Dari L.A.B Room">
                        <span class="material-symbols-outlined" style="font-size:18px">science</span>
                        <span>LAB</span>
                    </div>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="plab-empty">
        <span class="material-symbols-outlined">description</span>
        <p>Belum ada naskah kebijakan terpublikasi. Jadilah yang pertama!</p>
    </div>
    @endif

    {{-- My Briefs Section --}}
    @if(!Auth::user()->isAnonim() && $myBriefs->count() > 0)
    <div class="plab-section-header">
        <h3 class="plab-section-title">
            <span class="material-symbols-outlined" style="font-size:20px">folder_open</span>
            Brief Saya
        </h3>
    </div>
    <div class="plab-my-list">
        @foreach($myBriefs as $brief)
        <div class="plab-my-item">
            <div class="plab-my-status plab-status-{{ $brief->status }}">{{ $brief->statusLabel() }}</div>
            <div class="plab-my-info">
                <h4>{{ $brief->title }}</h4>
                <p>Terakhir diedit: {{ $brief->updated_at->diffForHumans() }}</p>
            </div>
            <div class="plab-my-actions">
                @if($brief->isDraft() || $brief->isRejected())
                <a href="{{ route('policy-lab.edit', $brief) }}" class="plab-btn-outline" style="padding:6px 14px;font-size:13px">Edit</a>
                @endif
                @if($brief->isApproved())
                <a href="{{ route('policy-lab.show', $brief) }}" class="plab-btn-primary" style="padding:6px 14px;font-size:13px">Lihat</a>
                @endif
                @if($brief->isPending())
                <span class="plab-my-pending">Menunggu review agent...</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Create Policy Brief Modal --}}
<div class="modal-overlay" id="create-brief-modal" style="display:none;">
    <div class="modal-content" style="max-width:640px;">
        <div class="modal-header">
            <h3>Buat Risalah Kebijakan Baru</h3>
            <button class="modal-close" onclick="document.getElementById('create-brief-modal').style.display='none'">&times;</button>
        </div>
        <form action="{{ route('policy-lab.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Template <span style="color:#cc1016">*</span></label>
                    <div class="pb-template-select">
                        <label class="pb-template-option">
                            <input type="radio" name="template_type" value="standar" checked>
                            <div class="pb-template-option-inner">
                                <span class="material-symbols-outlined">description</span>
                                <span>Standar</span>
                            </div>
                        </label>
                        <label class="pb-template-option">
                            <input type="radio" name="template_type" value="data-driven">
                            <div class="pb-template-option-inner">
                                <span class="material-symbols-outlined">bar_chart</span>
                                <span>Data-Driven</span>
                            </div>
                        </label>
                        <label class="pb-template-option">
                            <input type="radio" name="template_type" value="quick-response">
                            <div class="pb-template-option-inner">
                                <span class="material-symbols-outlined">bolt</span>
                                <span>Quick Response</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Judul Policy Brief <span style="color:#cc1016">*</span></label>
                    <input type="text" name="title" class="form-input" placeholder="Judul naskah kebijakan..." required maxlength="200">
                </div>

                <div class="form-group">
                    <label class="form-label">Ringkasan Eksekutif <span style="color:#cc1016">*</span></label>
                    <textarea name="summary" class="form-input form-textarea" rows="3" required placeholder="Ringkasan singkat isi policy brief (1-2 paragraf)..." maxlength="2000"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Ringkasan Masalah <span style="color:#cc1016">*</span></label>
                    <textarea name="problem" class="form-input form-textarea" rows="4" required placeholder="Deskripsikan permasalahan yang diangkat..." maxlength="5000"></textarea>
                    <p class="form-hint">Jelaskan masalah secara objektif dengan data dan fakta pendukung.</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Analisis & Data <span style="color:#cc1016">*</span></label>
                    <textarea name="analysis" class="form-input form-textarea" rows="4" required placeholder="Analisis mendalam berbasis data..." maxlength="5000"></textarea>
                    <p class="form-hint">Sajikan analisis kritis dari berbagai sudut pandang.</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Rekomendasi Kebijakan <span style="color:#cc1016">*</span></label>
                    <textarea name="recommendation" class="form-input form-textarea" rows="4" required placeholder="Rekomendasi kebijakan yang konkret dan implementatif..." maxlength="5000"></textarea>
                    <p class="form-hint">Berikan rekomendasi yang spesifik, terukur, dan dapat dilaksanakan.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="document.getElementById('create-brief-modal').style.display='none'">Batal</button>
                <button type="submit" name="action" value="draft" class="btn-outline">
                    <span class="material-symbols-outlined" style="font-size:18px">save</span> Simpan Draft
                </button>
                <button type="submit" name="action" value="submit" class="btn-primary" onclick="return confirm('Submit ke review Agent? Pastikan isi sudah lengkap.')">
                    <span class="material-symbols-outlined" style="font-size:18px">send</span> Submit ke Review Agent
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('create-brief-modal')?.addEventListener('click', (e) => {
    if (e.target.id === 'create-brief-modal') e.target.style.display = 'none';
});
</script>
@endpush
@endsection
