@extends('layouts.fullwidth', ['backUrl' => route('hoax-buster'), 'backLabel' => 'Kembali ke Hoax Buster'])

@section('title', 'Pusat Penumpas Hoaks')

@section('content')
<div class="hb-layout">
    {{-- Left: Verification Queue --}}
    <aside class="hb-queue">
        <div class="hb-queue-header">
            <h1 class="hb-queue-title">Antrean Verifikasi</h1>
            <p class="hb-queue-subtitle">Tinjau klaim yang dilaporkan untuk menjaga integritas epistemik.</p>
            <div class="hb-filter-tabs">
                <button class="hb-filter-tab active">Prioritas Tinggi</button>
                <button class="hb-filter-tab">Terbaru</button>
                <button class="hb-filter-tab">Kontroversial</button>
            </div>
        </div>
        <div class="hb-queue-list">
            {{-- Claim 1 - Selected/Active --}}
            <div class="hb-claim-card hb-claim-active">
                <div class="hb-claim-top">
                    <span class="hb-claim-status hb-status-review">Perlu Tinjauan</span>
                    <span class="hb-claim-id">ID: #HB-2026-892</span>
                </div>
                <h3 class="hb-claim-text hb-claim-text-active">"Universitas Indonesia menciptakan perangkat energi gratis dari limbah sungai Jakarta."</h3>
                <div class="hb-claim-bottom">
                    <div class="hb-claim-reports">
                        <span class="material-symbols-outlined">flag</span>
                        <span class="hb-claim-reports-text text-alert">Dilaporkan oleh 45 pengguna</span>
                    </div>
                    <span class="hb-claim-time">2 jam lalu</span>
                </div>
            </div>

            {{-- Claim 2 --}}
            <div class="hb-claim-card">
                <div class="hb-claim-top">
                    <span class="hb-claim-status hb-status-review">Perlu Tinjauan</span>
                    <span class="hb-claim-id">ID: #HB-2026-891</span>
                </div>
                <h3 class="hb-claim-text">"RUU baru mengusulkan pengawasan AI wajib di seluruh universitas negeri pada tahun 2026."</h3>
                <div class="hb-claim-bottom">
                    <div class="hb-claim-reports">
                        <span class="material-symbols-outlined">flag</span>
                        <span>Dilaporkan oleh 12 pengguna</span>
                    </div>
                    <span class="hb-claim-time">5 jam lalu</span>
                </div>
            </div>

            {{-- Claim 3 --}}
            <div class="hb-claim-card">
                <div class="hb-claim-top">
                    <span class="hb-claim-status hb-status-consensus">Menunggu Konsensus</span>
                    <span class="hb-claim-id">ID: #HB-2026-888</span>
                </div>
                <h3 class="hb-claim-text">"Grafik menunjukkan peningkatan 400% angka putus sekolah terkait kelelahan pembelajaran daring."</h3>
                <div class="hb-claim-bottom">
                    <div class="hb-claim-reports">
                        <span class="material-symbols-outlined">how_to_vote</span>
                        <span>89 suara masuk</span>
                    </div>
                    <span class="hb-claim-time">1 hari lalu</span>
                </div>
            </div>

            {{-- Claim 4 --}}
            <div class="hb-claim-card">
                <div class="hb-claim-top">
                    <span class="hb-claim-status hb-status-review">Perlu Tinjauan</span>
                    <span class="hb-claim-id">ID: #HB-2026-885</span>
                </div>
                <h3 class="hb-claim-text">"Kementerian Pendidikan mengonfirmasi penghapusan kurikulum sejarah untuk jurusan STEM."</h3>
                <div class="hb-claim-bottom">
                    <div class="hb-claim-reports">
                        <span class="material-symbols-outlined">flag</span>
                        <span>Dilaporkan oleh 8 pengguna</span>
                    </div>
                    <span class="hb-claim-time">1 hari lalu</span>
                </div>
            </div>
        </div>
    </aside>

    {{-- Right: Detail View --}}
    <main class="hb-detail">
        {{-- Detail Header --}}
        <div class="hb-detail-header">
            <div>
                <div class="hb-detail-badges">
                    <span class="hb-detail-status-badge">
                        <span class="material-symbols-outlined">warning</span>
                        KLAIM DI BAWAH TINJAUAN
                    </span>
                    <span class="hb-detail-case-id">KASUS #HB-2026-892</span>
                </div>
                <h1 class="hb-detail-title">"Universitas Indonesia menciptakan perangkat energi gratis dari limbah sungai Jakarta."</h1>
            </div>
            <div class="hb-detail-timer">
                <div class="hb-timer-label">Sisa Waktu</div>
                <div class="hb-timer-value">14j : 22m</div>
            </div>
        </div>

        {{-- Detail Content --}}
        <div class="hb-detail-content">
            <div class="hb-detail-inner">
                {{-- Evidence + Consensus Grid --}}
                <div class="hb-evidence-grid">
                    {{-- Evidence Dashboard --}}
                    <div class="hb-evidence-card">
                        <div class="hb-evidence-header">
                            <h4 class="hb-section-label">Dasbor Bukti</h4>
                            <a href="#" class="hb-view-source">
                                Lihat Sumber
                                <span class="material-symbols-outlined">open_in_new</span>
                            </a>
                        </div>
                        <div class="hb-source-post">
                            <div class="hb-source-author">
                                <div class="hb-source-avatar"></div>
                                <div class="hb-source-name">@ViralNewsID</div>
                                <div class="hb-source-platform">• Twitter/X</div>
                            </div>
                            <p class="hb-source-text">BREAKING: Mahasiswa UI telah memecahkan krisis energi! Sebuah perangkat baru mengubah sampah sungai Ciliwung menjadi listrik tanpa batas. Mengapa media mainstream diam? #KaryaAnakBangsa #EnergiGratis</p>
                            <div class="hb-source-image">
                                <span class="material-symbols-outlined">image</span>
                            </div>
                        </div>
                        <div class="hb-source-stats">
                            <span>12.4rb Bagikan</span>
                            <span>8.2rb Suka</span>
                        </div>
                    </div>

                    {{-- Community Consensus --}}
                    <div class="hb-consensus-card">
                        <h4 class="hb-section-label">Konsensus Komunitas</h4>
                        <div class="hb-consensus-inner">
                            <div class="hb-consensus-header">
                                <div>
                                    <div class="hb-consensus-pct">82%</div>
                                    <div class="hb-consensus-label">Cenderung ke HOAKS</div>
                                </div>
                                <div class="hb-consensus-votes">
                                    <div class="hb-consensus-total">142 Suara</div>
                                    <div class="hb-consensus-pending">58 Menunggu Tinjauan</div>
                                </div>
                            </div>
                            <div class="hb-consensus-bar">
                                <div class="hb-bar-hoax" style="width: 82%"></div>
                                <div class="hb-bar-misleading" style="width: 12%"></div>
                                <div class="hb-bar-valid" style="width: 6%"></div>
                            </div>
                            <div class="hb-consensus-legend">
                                <div class="hb-legend-item">
                                    <div class="hb-legend-dot hb-dot-red"></div>
                                    <span>Hoaks (82%)</span>
                                </div>
                                <div class="hb-legend-item">
                                    <div class="hb-legend-dot hb-dot-amber"></div>
                                    <span>Menyesatkan (12%)</span>
                                </div>
                                <div class="hb-legend-item">
                                    <div class="hb-legend-dot hb-dot-green"></div>
                                    <span>Valid (6%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hb-divider"></div>

                {{-- Submit Verdict --}}
                <div class="hb-verdict-section">
                    <h2 class="hb-verdict-title">Kirim Putusan Anda</h2>
                    <p class="hb-verdict-subtitle">Pilih klasifikasi. Bukti wajib disertakan untuk semua putusan negatif.</p>

                    <div class="hb-verdict-options">
                        <label class="hb-verdict-option">
                            <input type="radio" name="verdict" value="valid" class="hb-verdict-radio">
                            <div class="hb-verdict-box hb-verdict-valid">
                                <div class="hb-verdict-icon hb-icon-valid">
                                    <span class="material-symbols-outlined">check_circle</span>
                                </div>
                                <h3>Valid Secara Ilmiah</h3>
                                <p>Didukung oleh data yang dapat direproduksi dan tinjauan sejawat.</p>
                                <div class="hb-verdict-check">
                                    <span class="material-symbols-outlined">check_circle</span>
                                </div>
                            </div>
                        </label>
                        <label class="hb-verdict-option">
                            <input type="radio" name="verdict" value="misleading" class="hb-verdict-radio">
                            <div class="hb-verdict-box hb-verdict-misleading">
                                <div class="hb-verdict-icon hb-icon-misleading">
                                    <span class="material-symbols-outlined">warning</span>
                                </div>
                                <h3>Menyesatkan</h3>
                                <p>Konteks hilang, data yang dipilih secara ceri, atau kesalahan korelasi.</p>
                                <div class="hb-verdict-check">
                                    <span class="material-symbols-outlined">check_circle</span>
                                </div>
                            </div>
                        </label>
                        <label class="hb-verdict-option">
                            <input type="radio" name="verdict" value="hoax" class="hb-verdict-radio" checked>
                            <div class="hb-verdict-box hb-verdict-hoax">
                                <div class="hb-verdict-icon hb-icon-hoax">
                                    <span class="material-symbols-outlined">block</span>
                                </div>
                                <h3>Rekayasa / Hoaks</h3>
                                <p>Secara faktual salah, media yang dimanipulasi, atau pseudosains.</p>
                                <div class="hb-verdict-check">
                                    <span class="material-symbols-outlined">check_circle</span>
                                </div>
                            </div>
                        </label>
                    </div>

                    {{-- Evidence Input --}}
                    <div class="hb-evidence-input">
                        <label class="hb-evidence-label">Bukti Pembanding <span class="text-alert">*</span></label>
                        <div class="hb-evidence-row">
                            <div class="hb-evidence-field">
                                <span class="material-symbols-outlined hb-field-icon">link</span>
                                <input type="text" placeholder="Tempel DOI, URL Jurnal, atau Tautan Pemeriksaan Fakta..." value="https://doi.org/10.1038/s41560-021-00898-3" class="hb-evidence-url">
                            </div>
                            <button class="hb-verify-btn">Verifikasi Tautan</button>
                        </div>
                        <div class="hb-verified-source">
                            <div class="hb-source-doc-icon">
                                <span class="material-symbols-outlined">description</span>
                            </div>
                            <div class="hb-source-doc-info">
                                <div class="hb-source-doc-journal">Nature Energy (2021)</div>
                                <div class="hb-source-doc-title">"Thermodynamic constraints on microbial fuel cells using wastewater substrates"</div>
                                <div class="hb-source-doc-meta">Smith, J. dkk. • Sesuai konteks klaim • <span class="hb-verified-tag">Sumber Terverifikasi</span></div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Actions --}}
                    <div class="hb-verdict-footer">
                        <div class="hb-civic-points">
                            <span class="material-symbols-outlined">info</span>
                            <span>Voting menambahkan <strong>15 Poin Sipil</strong> setelah konsensus.</span>
                        </div>
                        <div class="hb-verdict-actions">
                            <button class="hb-btn-skip">Lewati Kasus</button>
                            <button class="hb-btn-submit">
                                <span class="material-symbols-outlined">gavel</span>
                                Kirim Putusan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter tabs
    document.querySelectorAll('.hb-filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.hb-filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Claim card selection
    document.querySelectorAll('.hb-claim-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.hb-claim-card').forEach(c => c.classList.remove('hb-claim-active'));
            this.classList.add('hb-claim-active');
        });
    });

    // Verdict option visual feedback
    document.querySelectorAll('.hb-verdict-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.hb-verdict-box').forEach(box => {
                box.classList.remove('selected');
            });
            this.closest('.hb-verdict-option').querySelector('.hb-verdict-box').classList.add('selected');
        });
        // Initialize checked state
        if (radio.checked) {
            radio.closest('.hb-verdict-option').querySelector('.hb-verdict-box').classList.add('selected');
        }
    });
});
</script>
@endpush
