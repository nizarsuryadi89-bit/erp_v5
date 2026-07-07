<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #7b0909 0%, #d63031 100%);
        --accent-red: #d63031;
        --accent-blue: #3498db;
        --accent-green: #27ae60;
        --slate-soft: #f8fafc;
        --slate-dark: #1e293b;
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .guide-header {
        background: var(--primary-gradient);
        color: white;
        padding: 3rem 0;
        margin-bottom: 2.5rem;
        border-radius: 0 0 30px 30px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .guide-step {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f1f5f9;
        position: relative;
        transition: transform 0.3s ease;
        height: 100%;
    }

    .guide-step:hover {
        transform: translateY(-5px);
        border-color: var(--accent-red);
    }

    .step-number {
        position: absolute;
        top: -20px;
        left: 40px;
        width: 50px;
        height: 50px;
        background: var(--accent-red);
        color: white;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1.5rem;
        box-shadow: 0 5px 15px rgba(214, 48, 49, 0.4);
    }

    .guide-icon {
        width: 60px;
        height: 60px;
        background: var(--slate-soft);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--accent-red);
        margin-bottom: 1.5rem;
    }

    .guide-title {
        font-weight: 900;
        color: var(--slate-dark);
        margin-bottom: 1rem;
        font-size: 1.25rem;
        line-height: 1.4;
    }

    .guide-text {
        color: #64748b;
        line-height: 1.6;
        font-size: 0.95rem;
    }

    .flow-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: var(--slate-soft);
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--slate-dark);
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
    }

    .flow-badge i {
        color: var(--accent-red);
    }

    .action-list {
        list-style: none;
        padding: 0;
        margin: 1.5rem 0 0 0;
    }

    .action-list li {
        padding: 8px 0;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
    }

    .action-list li i {
        margin-top: 4px;
        color: var(--accent-green);
    }

    .tip-box {
        background: #fff5f5;
        border-radius: 16px;
        padding: 1.25rem;
        border-left: 4px solid var(--accent-red);
        margin-top: 1.5rem;
    }

    .tip-box h6 {
        color: #c53030;
        font-weight: 800;
        margin-bottom: 5px;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }

    .tip-box p {
        margin: 0;
        font-size: 0.85rem;
        color: #9b2c2c;
    }

    .concept-card {
        background: #fff;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #e2e8f0;
    }

    .step-img-placeholder {
        background: #f1f5f9;
        border-radius: 12px;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-style: italic;
        margin-bottom: 1rem;
        border: 2px dashed #e2e8f0;
    }
</style>

<div class="container-fluid px-0">
    <!-- HERO HEADER -->
    <div class="guide-header">
        <div class="container py-2 text-center">
            <h1 class="fw-900 mb-2">Panduan Revisi & Cetak Ulang (Reprint)</h1>
            <p class="opacity-75 lead fw-500">Standar Operasional Prosedur Pengembalian Status & Manajemen Waste
                Material</p>
        </div>
    </div>

    <div class="container pb-5">

        <!-- SECTION 1: KONSEP DASAR -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="concept-card border-start border-5 border-danger">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="fw-900 text-dark mb-3"><i class="fas fa-coins text-warning me-2"></i>Filosofi
                                Revisi: "Bahan adalah Aset"</h3>
                            <p class="lead fw-600 text-muted mb-4">
                                Setiap lembar rijek yang Anda buang dan cetak ulang wajib dicatat agar <strong>Stok
                                    Gudang</strong> tetap sinkron dengan fisik.
                            </p>
                            <div class="d-flex gap-4 flex-wrap">
                                <div class="d-flex align-items-center gap-3 bg-light px-4 py-3 rounded-4 border">
                                    <div class="bg-danger text-white rounded-circle p-2"><i
                                            class="fas fa-trash-alt"></i></div>
                                    <div>
                                        <div class="small fw-bold text-muted text-uppercase">Input Rijek</div>
                                        <div class="fw-900 text-dark">Potong Stok Otomatis</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3 bg-light px-4 py-3 rounded-4 border">
                                    <div class="bg-primary text-white rounded-circle p-2"><i class="fas fa-history"></i>
                                    </div>
                                    <div>
                                        <div class="small fw-bold text-muted text-uppercase">Audit Trail</div>
                                        <div class="fw-900 text-dark">Tercatat di History Item</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center border-start d-none d-md-block">
                            <i class="fas fa-undo-alt fa-4x text-danger opacity-25 mb-3"></i>
                            <div class="fw-bold text-muted small">
                                "Satu lembar rijek yang tidak dicatat,<br>adalah selisih stok di akhir bulan."
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- STEP 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="guide-step">
                    <div class="step-number">01</div>
                    <div class="guide-icon">
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                    <h3 class="guide-title">Aktifkan Tombol Revisi</h3>
                    <div class="flow-badge">
                        <i class="fas fa-lock-open"></i> Hanya untuk status "DONE"
                    </div>
                    <p class="guide-text">
                        Tombol <strong>"Revisi"</strong> (Warna Merah) hanya akan muncul pada item yang sudah berstatus
                        selesai sepenuhnya.
                    </p>
                    <ul class="action-list">
                        <li><i class="fas fa-check-circle"></i> Cari item di Control Center.</li>
                        <li><i class="fas fa-check-circle"></i> Klik tombol "Revisi" di kolom aksi.</li>
                    </ul>
                </div>
            </div>

            <!-- STEP 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="guide-step">
                    <div class="step-number">02</div>
                    <div class="guide-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3 class="guide-title">Input Quantity Rijek</h3>
                    <div class="flow-badge">
                        <i class="fas fa-vial"></i> Manajemen Waste
                    </div>
                    <p class="guide-text">
                        Masukkan berapa lembar bahan yang <strong>RUSAK</strong> dan harus dibuang untuk proses cetak
                        ulang ini.
                    </p>
                    <ul class="action-list">
                        <li><i class="fas fa-check-circle"></i> <strong>Isi Angka (>0)</strong>: Jika ada bahan yang
                            dibuang (reprint).</li>
                        <li><i class="fas fa-check-circle"></i> <strong>Isi 0</strong>: Jika hanya ingin Undo tanpa
                            membuang bahan baru.</li>
                    </ul>
                    <div class="tip-box">
                        <h6>💡 Info Stok</h6>
                        <p>Sistem akan menghitung pemotongan stok berdasarkan BOM bahan yang terhubung pada step
                            terakhir tersebut.</p>
                    </div>
                </div>
            </div>

            <!-- STEP 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="guide-step">
                    <div class="step-number">03</div>
                    <div class="guide-icon">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h3 class="guide-title">Cek Audit Trail</h3>
                    <div class="flow-badge">
                        <i class="fas fa-clipboard-list"></i> Transparansi Data
                    </div>
                    <p class="guide-text">
                        Setelah diproses, sistem akan otomatis mencatat alasan dan waktu revisi pada kolom
                        <strong>Catatan Item</strong>.
                    </p>
                    <ul class="action-list">
                        <li><i class="fas fa-check-circle"></i> Status kembali ke "PROSES".</li>
                        <li><i class="fas fa-check-circle"></i> Catatan revisi muncul otomatis.</li>
                        <li><i class="fas fa-check-circle"></i> Log stok tercatat di Gudang.</li>
                    </ul>
                </div>
            </div>

            <!-- SUMMARY BOX -->
            <div class="col-12">
                <div class="guide-step border-primary" style="background: #f0f7ff;">
                    <div class="step-number bg-primary text-white"
                        style="box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);">🎯</div>
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <i class="fas fa-check-double fa-3x text-primary mb-3"></i>
                        </div>
                        <div class="col-md-10">
                            <h3 class="guide-title text-dark">Ringkasan Aturan Main</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="bg-white p-3 rounded-3 border">
                                        <div class="fw-bold text-danger mb-1"><i class="fas fa-times-circle me-1"></i>
                                            JANGAN REVISI JIKA:</div>
                                        <p class="small text-muted mb-0">Hanya untuk memperbaiki salah ketik nama
                                            (Gunakan edit info produk saja).</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-white p-3 rounded-3 border">
                                        <div class="fw-bold text-success mb-1"><i class="fas fa-check-circle me-1"></i>
                                            WAJIB REVISI JIKA:</div>
                                        <p class="small text-muted mb-0">Hasil cetak rusak, salah laminasi, atau rijek
                                            fisik yang butuh bahan baru.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row mt-4">
            <div class="col-12 text-center">
                <button class="btn btn-outline-danger px-5 py-3 fw-900 rounded-pill shadow-sm" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>CETAK PANDUAN REVISI
                </button>
            </div>
        </div>
    </div>
</div>