// CIVIC-Connect Laravel - JavaScript

document.addEventListener("DOMContentLoaded", () => {
    initReportModal();
    initLeaderboard();
    initRoomActions();
    initPostActions();
});

// ==================== REPORT MODAL ====================
function initReportModal() {
    const reportBtn = document.getElementById("report-btn");
    const reportModal = document.getElementById("report-modal");
    const closeReport = document.getElementById("close-report");
    const submitReport = document.getElementById("submit-report");

    reportBtn?.addEventListener("click", () => {
        reportModal?.classList.add("show");
    });

    closeReport?.addEventListener("click", () => {
        reportModal?.classList.remove("show");
    });

    reportModal?.addEventListener("click", (e) => {
        if (e.target === reportModal) {
            reportModal.classList.remove("show");
        }
    });

    // Handle form submission via AJAX
    submitReport?.addEventListener("click", (e) => {
        e.preventDefault();
        const claim = document.getElementById("report-claim")?.value;
        if (!claim?.trim()) {
            showToast("Harap isi deskripsi klaim");
            return;
        }

        reportModal?.classList.remove("show");
        showToast("Laporan berhasil dikirim ke tim verifikasi", "success");

        // Reset form
        document.getElementById("report-claim").value = "";
        document.getElementById("report-link").value = "";
    });
}

// ==================== LEADERBOARD TABS ====================
function initLeaderboard() {
    document.querySelectorAll(".tab-btn").forEach((btn) => {
        btn.addEventListener("click", () => {
            const parent = btn.closest(".sidebar-widget");

            // Update tabs
            parent
                .querySelectorAll(".tab-btn")
                .forEach((t) => t.classList.remove("active"));
            btn.classList.add("active");

            // Show list
            parent
                .querySelectorAll(".leaderboard-list")
                .forEach((l) => l.classList.add("hidden"));
            const target = document.getElementById(`tab-${btn.dataset.tab}`);
            if (target) target.classList.remove("hidden");
        });
    });
}

// ==================== ROOM ACTIONS ====================
function initRoomActions() {
    const createRoomBtn = document.getElementById("create-room-btn");

    createRoomBtn?.addEventListener("click", () => {
        showToast("Fitur pembuatan room akan segera tersedia", "success");
    });

    document.querySelectorAll(".room-card .btn-outline").forEach((btn) => {
        btn.addEventListener("click", () => {
            const card = btn.closest(".room-card");
            if (card.classList.contains("private")) {
                showToast("Permintaan akses dikirim ke host");
            } else {
                showToast("Bergabung ke room...", "success");
            }
        });
    });
}

// ==================== POST ACTIONS ====================
function initPostActions() {
    document.querySelectorAll(".post-action-btn").forEach((btn) => {
        btn.addEventListener("click", () => {
            btn.classList.toggle("active");
            if (
                btn.textContent.includes("Valid") &&
                btn.classList.contains("active")
            ) {
                showToast("Anda memvalidasi informasi ini", "success");
            }
        });
    });

    document.querySelector(".post-input-btn")?.addEventListener("click", () => {
        showToast("Editor postingan akan tersedia segera");
    });

    // Vote buttons
    document.querySelectorAll(".btn-vote").forEach((btn) => {
        btn.addEventListener("click", () => {
            const isHoax = btn.classList.contains("hoax");
            showToast(
                isHoax ? "Vote HOAKS tercatat" : "Vote FAKTA tercatat",
                "success",
            );
        });
    });
}

// ==================== TOAST ====================
function showToast(message, type = "info") {
    const existing = document.querySelector(".toast");
    if (existing) existing.remove();

    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = "0";
        toast.style.transition = "opacity 0.3s";
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
