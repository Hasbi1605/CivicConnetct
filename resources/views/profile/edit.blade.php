@extends('layouts.feature')

@section('title', $user->is_profile_complete ? 'Pengaturan Profil' : 'Lengkapi Profil')

@section('content')
<div class="se-wrapper se-wrapper-full">
    {{-- Edit Form (full width) --}}
    <div class="se-main-form">
        <div class="se-form-card">
            <div class="se-form-header">
                <h2 class="se-form-title">{{ $user->is_profile_complete ? 'Edit Profil' : 'Lengkapi Profil Anda' }}</h2>
                @if(!$user->is_profile_complete)
                    <p class="se-form-subtitle">Isi data berikut untuk mulai menggunakan CIVIC-Connect</p>
                @endif
            </div>

            @if(session('info'))
                <div class="alert alert-info" style="margin: 0 24px;">{{ session('info') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" style="margin: 0 24px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="se-form-body">
                    {{-- Avatar Upload --}}
                    <div class="se-field-section">
                        <h3 class="se-field-section-title">Foto Profil</h3>
                        <div class="se-avatar-upload">
                            <img src="{{ $user->avatar_url }}" alt="Avatar" class="se-avatar-preview" id="avatar-preview">
                            <div class="se-avatar-action">
                                <label for="avatar" class="se-btn-outline-sm">Pilih Foto</label>
                                <input type="file" name="avatar" id="avatar" accept="image/*" style="display: none;">
                                <p class="se-field-hint">JPG, PNG. Maks 2MB.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Name (readonly) --}}
                    <div class="se-field-group">
                        <label class="se-field-label">Nama Lengkap</label>
                        <input type="text" class="se-field-input se-field-readonly" value="{{ $user->name }}" readonly>
                        <p class="se-field-hint">Nama tidak bisa diubah di sini. Hubungi admin untuk perubahan nama resmi.</p>
                    </div>

                    {{-- Jurusan & Universitas --}}
                    <div class="se-field-row">
                        <div class="se-field-group">
                            <label for="jurusan" class="se-field-label">Jurusan / Program Studi <span class="se-required">*</span></label>
                            <input type="text" name="jurusan" id="jurusan" class="se-field-input" value="{{ old('jurusan', $user->jurusan) }}" placeholder="Contoh: Ilmu Politik" required>
                        </div>
                        <div class="se-field-group">
                            <label for="universitas" class="se-field-label">Universitas <span class="se-required">*</span></label>
                            <input type="text" name="universitas" id="universitas" class="se-field-input" value="{{ old('universitas', $user->universitas) }}" placeholder="Contoh: Universitas Indonesia" required>
                        </div>
                    </div>

                    {{-- Role (read-only — auto-assigned by KYA verification) --}}
                    <div class="se-field-group">
                        <label class="se-field-label">Peran</label>
                        <input type="text" class="se-field-input se-field-readonly" value="{{ $user->role_badge }}" readonly>
                        <p class="se-field-hint">
                            @if($user->isIdentityVerified())
                                Role ditentukan otomatis berdasarkan verifikasi identitas ({{ $user->identity_card_label }}).
                            @elseif($user->isAnonim())
                                Anda masuk sebagai pengunjung anonim.
                            @elseif($user->isAgent())
                                Anda adalah CIVIC Agent.
                            @else
                                Role akan ditentukan otomatis setelah <a href="{{ route('identity.verify') }}" style="color:#2563EB;text-decoration:underline;">verifikasi identitas</a>.
                            @endif
                        </p>
                    </div>

                    {{-- Identity Verification Status --}}
                    @if(!$user->isAnonim() && !$user->isAgent())
                    <div class="se-field-group">
                        <label class="se-field-label">Status Verifikasi Identitas</label>
                        <div class="se-verification-status se-status-{{ $user->identity_status ?? 'unsubmitted' }}">
                            @if($user->isIdentityVerified())
                                <span class="material-symbols-outlined">check_circle</span>
                                <span>Terverifikasi — {{ $user->identity_verified_at->format('d M Y') }}</span>
                            @elseif($user->isIdentityPending())
                                <span class="material-symbols-outlined">hourglass_top</span>
                                <span>Menunggu verifikasi CIVIC Agent</span>
                            @elseif($user->isIdentityRejected())
                                <span class="material-symbols-outlined">cancel</span>
                                <span>Ditolak — <a href="{{ route('identity.verify') }}" style="color:#2563EB;text-decoration:underline;">Upload ulang</a></span>
                            @else
                                <span class="material-symbols-outlined">info</span>
                                <span>Belum diverifikasi — <a href="{{ route('identity.verify') }}" style="color:#2563EB;text-decoration:underline;">Verifikasi sekarang</a></span>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Bio --}}
                    <div class="se-field-group">
                        <label for="bio" class="se-field-label">Bio / Deskripsi Singkat</label>
                        <textarea name="bio" id="bio" class="se-field-input se-field-textarea" rows="4" maxlength="1000" placeholder="Ceritakan sedikit tentang diri Anda...">{{ old('bio', $user->bio) }}</textarea>
                    </div>
                </div>

                {{-- Actions Footer --}}
                <div class="se-form-footer">
                    @if($user->is_profile_complete)
                        <a href="{{ route('profile') }}" class="se-btn-secondary">Batal</a>
                    @endif
                    <button type="submit" class="se-btn-primary">
                        {{ $user->is_profile_complete ? 'Simpan Perubahan' : 'Simpan & Mulai' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('avatar')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
            const sidebarPreview = document.getElementById('avatar-sidebar-preview');
            if (sidebarPreview) sidebarPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
@endsection
