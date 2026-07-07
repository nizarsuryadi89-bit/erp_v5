<!-- Page Header -->
<div class="custom-page-header">
    <h5><i class="bi bi-headset me-2 text-warning"></i>Pusat Bantuan</h5>
    <p>Ada kendala operasional? Kami siap membantu</p>
</div>

<style>
    .help-card {
        background: #fff;
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        text-decoration: none;
        transition: all 0.2s;
        cursor: pointer;
    }

    .help-card:active {
        transform: scale(0.98);
        background: #f8fafc;
    }

    .help-icon {
        width: 55px;
        height: 55px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .help-content {
        flex: 1;
        min-width: 0;
    }

    .help-title {
        font-weight: 700;
        color: #1e293b;
        font-size: 1.05rem;
        margin-bottom: 0.2rem;
        display: block;
    }

    .help-desc {
        font-size: 0.8rem;
        color: #64748b;
        line-height: 1.4;
        display: block;
    }

    .help-chevron {
        color: #cbd5e1;
        font-size: 1.2rem;
    }

    /* Colors */
    .bg-it {
        background: #eff6ff;
        color: #3b82f6;
    }

    .bg-hr {
        background: #fdf4ff;
        color: #d946ef;
    }

    .bg-ga {
        background: #fffbeb;
        color: #f59e0b;
    }

    .bg-app {
        background: #f0fdfa;
        color: #0d9488;
    }
</style>

<!-- Main Content -->
<div class="content-container">

    <div class="px-2 mb-4">
        <h6 class="fw-bold mb-2">Pilih Kategori Bantuan</h6>
        <p class="text-muted small">Hubungi divisi terkait agar kendala Anda lebih cepat terselesaikan.</p>
    </div>

    <!-- Help Categories -->

    <!-- 1. Sistem & Aplikasi -->
    <div class="help-card" onclick="openWADesk('Sistem ERP', '6281234567890')">
        <div class="help-icon bg-app">
            <i class="bi bi-robot"></i>
        </div>
        <div class="help-content">
            <span class="help-title">Bantuan Sistem ERP</span>
            <span class="help-desc">Masalah teknis, error aplikasi, lupa password, atau fiturnya rusak.</span>
        </div>
        <i class="bi bi-chevron-right help-chevron"></i>
    </div>

    <!-- 2. Human Resources (HR) -->
    <div class="help-card" onclick="openWADesk('HR & Personalia', '6281234567891')">
        <div class="help-icon bg-hr">
            <i class="bi bi-people"></i>
        </div>
        <div class="help-content">
            <span class="help-title">Personalia / HR</span>
            <span class="help-desc">Pertanyaan soal gaji, absen error, sisa cuti, atau kontrak kerja.</span>
        </div>
        <i class="bi bi-chevron-right help-chevron"></i>
    </div>

    <!-- 3. General Affair (GA) -->
    <div class="help-card" onclick="openWADesk('General Affair', '6281234567892')">
        <div class="help-icon bg-ga">
            <i class="bi bi-buildings"></i>
        </div>
        <div class="help-content">
            <span class="help-title">Fasilitas / GA</span>
            <span class="help-desc">Lapor AC mati, komputer rusak, stok alat tulis habis, dll.</span>
        </div>
        <i class="bi bi-chevron-right help-chevron"></i>
    </div>

    <!-- 4. IT Support -->
    <div class="help-card" onclick="openWADesk('IT Support', '6281234567893')">
        <div class="help-icon bg-it">
            <i class="bi bi-pc-display"></i>
        </div>
        <div class="help-content">
            <span class="help-title">Hardware & Jaringan</span>
            <span class="help-desc">Internet mati, setting printer, install ulang software, atau virus.</span>
        </div>
        <i class="bi bi-chevron-right help-chevron"></i>
    </div>

    <!-- Emergency Section -->
    <div class="mt-5 text-center px-4">
        <hr class="opacity-25 border-secondary">
        <p class="text-muted fw-bold small mb-2 mt-4">Kondisi Darurat?</p>
        <p class="text-muted small" style="font-size: 0.75rem;">Jika ada keadaan darurat di lingkungan kerja yang
            mengancam nyawa atau properti perusahaan, segera tekan tombol darurat di bawah ini.</p>
        <button onclick="darurat()" class="btn btn-danger rounded-pill px-4 py-2 mt-2 shadow-sm fw-bold">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> PANGGILAN DARURAT
        </button>
    </div>

</div>

<script>
    function openWADesk(kategori, nomorWA) {
        Swal.fire({
            title: `Hubungi ${kategori}?`,
            text: "Anda akan dialihkan ke WhatsApp untuk melaporkan kendala kepada pihak terkait.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: '<i class="bi bi-whatsapp me-1"></i> Buka WhatsApp',
            cancelButtonText: 'Batal',
            borderRadius: '20px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Auto generate message template
                let currentHour = new Date().getHours();
                let greeting = currentHour < 12 ? 'Pagi' : currentHour < 15 ? 'Siang' : currentHour < 18 ? 'Sore' : 'Malam';
                let userName = "<?= $this->session->userdata('username') ?>";

                let text = `Halo, Selamat ${greeting}.%0A%0ASaya ${userName} ingin meminta bantuan terkait [${kategori}].%0ADetail Kendala: %0A...`;
                window.open(`https://wa.me/${nomorWA}?text=${text}`, '_blank');
            }
        });
    }

    function darurat() {
        Swal.fire({
            title: 'Panggilan Darurat',
            html: `
                <div class="text-start mt-3">
                    <a href="tel:112" class="btn btn-outline-danger w-100 mb-3 text-start fw-bold p-3 rounded-4">
                        <i class="bi bi-telephone-fill me-2"></i> Layanan Darurat (112)
                    </a>
                    <a href="tel:113" class="btn btn-outline-danger w-100 mb-3 text-start fw-bold p-3 rounded-4">
                        <i class="bi bi-fire me-2"></i> Pemadam Kebakaran (113)
                    </a>
                    <a href="tel:119" class="btn btn-outline-danger w-100 text-start fw-bold p-3 rounded-4">
                        <i class="bi bi-heart-pulse-fill me-2"></i> Ambulans (119)
                    </a>
                </div>
            `,
            showConfirmButton: false,
            showCloseButton: true,
            padding: '1.5rem',
            borderRadius: '25px',
        });
    }
</script>