<div class="modal-overlay" id="report-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Laporkan Informasi</h2>
            <button class="modal-close" id="close-report">&times;</button>
        </div>
        <div class="modal-body">
            <p class="modal-desc">Gunakan fitur ini untuk melaporkan informasi yang perlu diverifikasi.</p>
            <form action="{{ route('report.submit') }}" method="POST" id="report-form">
                @csrf
                <div class="form-group">
                    <label>Deskripsi Klaim</label>
                    <textarea name="claim" id="report-claim" rows="3" placeholder="Jelaskan klaim atau berita..."></textarea>
                </div>
                <div class="form-group">
                    <label>Sumber (Opsional)</label>
                    <input type="text" name="source" id="report-link" placeholder="https://...">
                </div>
                <button type="submit" class="btn-primary btn-full" id="submit-report">Kirim Laporan</button>
            </form>
        </div>
    </div>
</div>
