@extends('layouts.feature')

@section('title', 'Verifikasi Identitas Akademik')

@section('content')
<div class="kya-page">
    {{-- Page Header — matches plab-header pattern --}}
    <div class="kya-header">
        <div>
            <h2 class="kya-header-title">Verifikasi Identitas Akademik</h2>
            <p class="kya-header-subtitle">Know Your Academician (KYA) — Pastikan identitas Anda untuk mengakses semua fitur</p>
        </div>
        <div class="kya-header-actions">
            @if($user->isIdentityVerified())
            <span class="kya-status-badge kya-badge-approved"><span class="material-symbols-outlined" style="font-size:18px">verified</span> Terverifikasi</span>
            @elseif($user->isIdentityPending())
            <span class="kya-status-badge kya-badge-pending"><span class="material-symbols-outlined" style="font-size:18px">hourglass_top</span> Menunggu Review</span>
            @elseif($user->isIdentityRejected())
            <span class="kya-status-badge kya-badge-rejected"><span class="material-symbols-outlined" style="font-size:18px">error</span> Ditolak</span>
            @else
            <span class="kya-status-badge kya-badge-unsubmitted"><span class="material-symbols-outlined" style="font-size:18px">pending</span> Belum Diverifikasi</span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- State: APPROVED --}}
    {{-- ═══════════════════════════════════════════ --}}
    @if($user->isIdentityVerified())
    <div class="kya-content-card">
        <div class="kya-state-banner approved">
            <span class="material-symbols-outlined">check_circle</span>
            <div>
                <h3>Identitas Terverifikasi</h3>
                <p>Identitas akademik Anda telah diverifikasi. Anda memiliki akses penuh ke semua fitur CIVIC-Connect.</p>
            </div>
        </div>
        <div class="kya-detail-grid">
            <div class="kya-detail-item">
                <span class="kya-detail-label">Jenis Kartu</span>
                <span class="kya-detail-value">{{ $user->identity_card_label }}</span>
            </div>
            <div class="kya-detail-item">
                <span class="kya-detail-label">{{ $user->identity_card_type === 'ktd' ? 'NIDN' : 'NIM' }}</span>
                <span class="kya-detail-value">{{ $user->nim_nidn }}</span>
            </div>
            <div class="kya-detail-item">
                <span class="kya-detail-label">Diverifikasi Pada</span>
                <span class="kya-detail-value">{{ $user->identity_verified_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="kya-detail-item">
                <span class="kya-detail-label">Role</span>
                <span class="kya-detail-value">{{ $user->role_badge }}</span>
            </div>
        </div>
        <div class="kya-card-actions">
            <a href="{{ route('home') }}" class="kya-btn kya-btn-primary">
                <span class="material-symbols-outlined">home</span> Kembali ke Beranda
            </a>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- State: PENDING --}}
    {{-- ═══════════════════════════════════════════ --}}
    @elseif($user->isIdentityPending())
    <div class="kya-content-card">
        <div class="kya-state-banner pending">
            <span class="material-symbols-outlined">hourglass_top</span>
            <div>
                <h3>Menunggu Verifikasi</h3>
                <p>Dokumen Anda sedang ditinjau oleh CIVIC Agent. Proses ini biasanya memerlukan waktu 1×24 jam.</p>
            </div>
        </div>
        <div class="kya-detail-grid">
            <div class="kya-detail-item">
                <span class="kya-detail-label">Jenis Kartu</span>
                <span class="kya-detail-value">{{ $user->identity_card_label }}</span>
            </div>
            <div class="kya-detail-item">
                <span class="kya-detail-label">{{ $user->identity_card_type === 'ktd' ? 'NIDN' : 'NIM' }}</span>
                <span class="kya-detail-value">{{ $user->nim_nidn }}</span>
            </div>
        </div>
        <div class="kya-info-box">
            <span class="material-symbols-outlined">info</span>
            <p>Selama menunggu, Anda masih dapat menjelajahi konten di CIVIC-Connect dalam mode baca. Anda akan mendapat notifikasi begitu verifikasi selesai.</p>
        </div>
        <div class="kya-card-actions">
            <a href="{{ route('home') }}" class="kya-btn kya-btn-secondary">
                <span class="material-symbols-outlined">home</span> Jelajahi Platform
            </a>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- State: REJECTED — show reason + resubmit form --}}
    {{-- ═══════════════════════════════════════════ --}}
    @elseif($user->isIdentityRejected())
    <div class="kya-content-card">
        <div class="kya-state-banner rejected">
            <span class="material-symbols-outlined">cancel</span>
            <div>
                <h3>Verifikasi Ditolak</h3>
                <p>Mohon maaf, verifikasi identitas Anda ditolak oleh CIVIC Agent.</p>
            </div>
        </div>

        @if($user->identity_rejection_reason)
        <div class="kya-rejection-box">
            <strong>Alasan Penolakan:</strong>
            <p>{{ $user->identity_rejection_reason }}</p>
        </div>
        @endif
    </div>

    <div class="kya-content-card">
        <h3 class="kya-section-title">Upload Ulang Dokumen</h3>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin:0; padding-left:20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('identity.resubmit') }}" method="POST" enctype="multipart/form-data" class="kya-form">
            @csrf
            {{-- Card Type --}}
            <div class="kya-form-group">
                <label class="kya-form-label">Jenis Kartu Identitas <span class="kya-required">*</span></label>
                <div class="kya-radio-group">
                    <label class="kya-radio-option">
                        <input type="radio" name="identity_card_type" value="ktm" {{ old('identity_card_type', $user->identity_card_type) === 'ktm' ? 'checked' : '' }}>
                        <div class="kya-radio-card">
                            <span class="material-symbols-outlined">school</span>
                            <strong>KTM</strong>
                            <small>Kartu Tanda Mahasiswa</small>
                        </div>
                    </label>
                    <label class="kya-radio-option">
                        <input type="radio" name="identity_card_type" value="ktd" {{ old('identity_card_type', $user->identity_card_type) === 'ktd' ? 'checked' : '' }}>
                        <div class="kya-radio-card">
                            <span class="material-symbols-outlined">work</span>
                            <strong>KTD</strong>
                            <small>Kartu Tanda Dosen</small>
                        </div>
                    </label>
                </div>
            </div>

            {{-- NIM/NIDN --}}
            <div class="kya-form-group">
                <label for="nim_nidn" class="kya-form-label">NIM / NIDN <span class="kya-required">*</span></label>
                <input type="text" name="nim_nidn" id="nim_nidn" class="kya-input" value="{{ old('nim_nidn', $user->nim_nidn) }}" placeholder="Masukkan NIM atau NIDN Anda" required>
                <p class="kya-form-hint">Nomor Induk Mahasiswa (NIM) atau Nomor Induk Dosen Nasional (NIDN)</p>
            </div>

            {{-- Upload Photo --}}
            <div class="kya-form-group">
                <label class="kya-form-label">Foto Kartu Identitas <span class="kya-required">*</span></label>
                <div class="kya-upload-area" id="kya-upload-area">
                    <input type="file" name="identity_card_image" id="identity_card_image" accept="image/*" class="kya-upload-input" required>
                    <div class="kya-upload-placeholder" id="kya-upload-placeholder">
                        <span class="material-symbols-outlined">cloud_upload</span>
                        <p>Klik atau drag foto KTM/KTD di sini</p>
                        <small>JPG, PNG — Maks 5MB</small>
                    </div>
                    <img id="kya-upload-preview" class="kya-upload-preview" style="display:none;" alt="Preview">
                </div>
            </div>

            <button type="submit" class="kya-btn kya-btn-primary">
                <span class="material-symbols-outlined">upload</span> Kirim Ulang Dokumen
            </button>
        </form>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- State: UNSUBMITTED — fresh upload form --}}
    {{-- ═══════════════════════════════════════════ --}}
    @else
    {{-- Why Verify section --}}
    <div class="kya-content-card">
        <h3 class="kya-section-title">Mengapa Perlu Verifikasi?</h3>
        <div class="kya-why-grid">
            <div class="kya-why-item">
                <span class="material-symbols-outlined">shield</span>
                <strong>Cegah Buzzer</strong>
                <p>Mencegah infiltrasi akun palsu dan buzzer politik</p>
            </div>
            <div class="kya-why-item">
                <span class="material-symbols-outlined">verified</span>
                <strong>Kredibilitas</strong>
                <p>Menjamin setiap wacana berasal dari sivitas akademika</p>
            </div>
            <div class="kya-why-item">
                <span class="material-symbols-outlined">lock</span>
                <strong>Akses Fitur</strong>
                <p>Buka akses posting, voting, policy brief, & fact-check</p>
            </div>
        </div>
    </div>

    {{-- Upload Form --}}
    <div class="kya-content-card">
        <h3 class="kya-section-title">Upload Dokumen Identitas</h3>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin:0; padding-left:20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('identity.store') }}" method="POST" enctype="multipart/form-data" class="kya-form">
            @csrf

            {{-- Card Type --}}
            <div class="kya-form-group">
                <label class="kya-form-label">Jenis Kartu Identitas <span class="kya-required">*</span></label>
                <div class="kya-radio-group">
                    <label class="kya-radio-option">
                        <input type="radio" name="identity_card_type" value="ktm" {{ old('identity_card_type') === 'ktm' ? 'checked' : '' }}>
                        <div class="kya-radio-card">
                            <span class="material-symbols-outlined">school</span>
                            <strong>KTM</strong>
                            <small>Kartu Tanda Mahasiswa</small>
                        </div>
                    </label>
                    <label class="kya-radio-option">
                        <input type="radio" name="identity_card_type" value="ktd" {{ old('identity_card_type') === 'ktd' ? 'checked' : '' }}>
                        <div class="kya-radio-card">
                            <span class="material-symbols-outlined">work</span>
                            <strong>KTD</strong>
                            <small>Kartu Tanda Dosen</small>
                        </div>
                    </label>
                </div>
            </div>

            {{-- NIM/NIDN --}}
            <div class="kya-form-group">
                <label for="nim_nidn_new" class="kya-form-label">NIM / NIDN <span class="kya-required">*</span></label>
                <input type="text" name="nim_nidn" id="nim_nidn_new" class="kya-input" value="{{ old('nim_nidn') }}" placeholder="Masukkan NIM atau NIDN Anda" required>
                <p class="kya-form-hint">Nomor Induk Mahasiswa (NIM) atau Nomor Induk Dosen Nasional (NIDN)</p>
            </div>

            {{-- Upload Photo --}}
            <div class="kya-form-group">
                <label class="kya-form-label">Foto Kartu Identitas <span class="kya-required">*</span></label>
                <div class="kya-upload-area" id="kya-upload-area-new">
                    <input type="file" name="identity_card_image" id="identity_card_image_new" accept="image/*" class="kya-upload-input" required>
                    <div class="kya-upload-placeholder" id="kya-upload-placeholder-new">
                        <span class="material-symbols-outlined">cloud_upload</span>
                        <p>Klik atau drag foto KTM/KTD di sini</p>
                        <small>JPG, PNG — Maks 5MB. Pastikan foto jelas dan tidak terpotong.</small>
                    </div>
                    <img id="kya-upload-preview-new" class="kya-upload-preview" style="display:none;" alt="Preview">
                </div>
            </div>

            <div class="kya-info-box">
                <span class="material-symbols-outlined">privacy_tip</span>
                <p>Dokumen Anda disimpan secara aman dan hanya dapat diakses oleh CIVIC Agent untuk keperluan verifikasi.</p>
            </div>

            <button type="submit" class="kya-btn kya-btn-primary">
                <span class="material-symbols-outlined">send</span> Kirim untuk Verifikasi
            </button>
        </form>
    </div>
    @endif
</div>

@push('scripts')
<script>
// File upload preview
document.querySelectorAll('.kya-upload-input').forEach(input => {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const area = this.closest('.kya-upload-area');
        const preview = area.querySelector('.kya-upload-preview');
        const placeholder = area.querySelector('.kya-upload-placeholder');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                preview.src = ev.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });
});

// Drag and drop support
document.querySelectorAll('.kya-upload-area').forEach(area => {
    ['dragenter', 'dragover'].forEach(evt => {
        area.addEventListener(evt, e => { e.preventDefault(); area.classList.add('dragover'); });
    });
    ['dragleave', 'drop'].forEach(evt => {
        area.addEventListener(evt, e => { e.preventDefault(); area.classList.remove('dragover'); });
    });
    area.addEventListener('drop', e => {
        const input = area.querySelector('.kya-upload-input');
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
    });
});
</script>
@endpush
@endsection
