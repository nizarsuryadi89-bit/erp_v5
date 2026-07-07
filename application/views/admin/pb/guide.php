<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --accent-blue: #3498db;
        --accent-green: #27ae60;
        --accent-orange: #f39c12;
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
        border-color: var(--accent-blue);
    }

    .step-number {
        position: absolute;
        top: -20px;
        left: 40px;
        width: 50px;
        height: 50px;
        background: var(--accent-blue);
        color: white;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1.5rem;
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
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
        color: var(--accent-blue);
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
        color: var(--accent-blue);
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
        background: #f0f9ff;
        border-radius: 16px;
        padding: 1.25rem;
        border-left: 4px solid var(--accent-blue);
        margin-top: 1.5rem;
    }

    .tip-box h6 {
        color: #0369a1;
        font-weight: 800;
        margin-bottom: 5px;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }

    .tip-box p {
        margin: 0;
        font-size: 0.85rem;
        color: #0c4a6e;
    }

    .concept-card {
        background: #fff;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #e2e8f0;
    }
</style>

<div class="container-fluid px-0">
    <!-- HERO HEADER -->
    <div class="guide-header">
        <div class="container py-2 text-center">
            <h1 class="fw-900 mb-2">Panduan Pengambilan Barang (PB)</h1>
            <p class="opacity-75 lead fw-500">Standar Operasional Prosedur Serah Terima & Logistik WIP</p>
        </div>
    </div>

    <div class="container pb-5">

        <!-- SECTION 1: KONSEP DASAR -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="concept-card border-start border-5 border-primary">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="fw-900 text-dark mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>Filosofi
                                PB Hub: "Satu Pintu"</h3>
                            <p class="lead fw-600 text-muted mb-4">
                                Tim PB Hub memproses seluruh barang fisik yang sudah selesai diproduksi, baik order dari
                                cabang Anda sendiri maupun barang titipan cabang lain.
                            </p>
                            <div class="d-flex gap-4 flex-wrap">
                                <div class="d-flex align-items-center gap-3 bg-light px-4 py-3 rounded-4 border">
                                    <div class="bg-primary text-white rounded-circle p-2"><i class="fas fa-boxes"></i>
                                    </div>
                                    <div>
                                        <div class="small fw-bold text-muted text-uppercase">Tanggung Jawab</div>
                                        <div class="fw-900 text-dark">Barang Fisik di Lokasi</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3 bg-light px-4 py-3 rounded-4 border">
                                    <div class="bg-success text-white rounded-circle p-2"><i
                                            class="fas fa-check-circle"></i></div>
                                    <div>
                                        <div class="small fw-bold text-muted text-uppercase">Tujuan Akhir</div>
                                        <div class="fw-900 text-dark">Konsumen Menerima Barang</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center border-start d-none d-md-block">
                            <i class="fas fa-shipping-fast fa-4x text-primary opacity-25 mb-3"></i>
                            <div class="fw-bold text-muted small">
                                "Ceklis, Verifikasi, dan Serahkan."
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
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3 class="guide-title">Filter & Pilih Data</h3>
                    <div class="flow-badge">
                        <i class="fas fa-mouse-pointer"></i> Gunakan Fitur Ceklis
                    </div>
                    <p class="guide-text">
                        Gunakan filter <strong>"Asal Cabang"</strong> dan <strong>Rentang Tanggal</strong> untuk
                        mempersempit antrian pekerjaan Anda.
                    </p>
                    <ul class="action-list">
                        <li><i class="fas fa-check-circle"></i> Ceklis satu-per-satu SPK yang akan dibawa.</li>
                        <li><i class="fas fa-check-circle"></i> Gunakan <strong>Check All</strong> (di header tabel)
                            untuk memilih semua hasil filter.</li>
                    </ul>
                </div>
            </div>

            <!-- STEP 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="guide-step">
                    <div class="step-number">02</div>
                    <div class="guide-icon">
                        <i class="fas fa-search-plus"></i>
                    </div>
                    <h3 class="guide-title">Verifikasi (Opsional)</h3>
                    <div class="flow-badge">
                        <i class="fas fa-barcode"></i> Scan atau Klik Verifikasi
                    </div>
                    <p class="guide-text">
                        Untuk memastikan akurasi item, Anda dapat mengklik tombol <strong><i
                                class="fas fa-search-plus text-primary"></i> RINCIAN</strong> pada setiap baris SPK.
                    </p>
                    <ul class="action-list">
                        <li><i class="fas fa-check-circle"></i> Cek apakah item sudah lengkap diproduksi.</li>
                        <li><i class="fas fa-check-circle"></i> Jika ada kesalahan input qty oleh operator, laporkan ke
                            Supervisor.</li>
                    </ul>
                </div>
            </div>

            <!-- STEP 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="guide-step">
                    <div class="step-number">03</div>
                    <div class="guide-icon">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                    <h3 class="guide-title">Serahkan Massal</h3>
                    <div class="flow-badge">
                        <i class="fas fa-layer-group"></i> Tombol Serahkan Semua
                    </div>
                    <p class="guide-text">
                        Setelah dipastikan barang fisik siap, klik tombol besar <strong><i class="fas fa-handshake"></i>
                            SERAHKAN SEMUA</strong> di bagian bawah menu tab.
                    </p>
                    <ul class="action-list">
                        <li><i class="fas fa-check-circle"></i> Sistem akan meringkas jumlah SPK terpilih.</li>
                        <li><i class="fas fa-check-circle"></i> Isi keterangan penerima (Via & Catatan).</li>
                        <li><i class="fas fa-check-circle"></i> Lampirkan <strong>FOTO BUKTI</strong> penyerahan.</li>
                    </ul>
                </div>
            </div>

            <!-- PERHATIAN KHUSUS -->
            <div class="col-12">
                <div class="guide-step border-danger" style="background: #fff5f5;">
                    <div class="step-number bg-danger text-white"
                        style="box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);">!</div>
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        </div>
                        <div class="col-md-10">
                            <h3 class="guide-title text-danger">Peringatan: Status Pembayaran</h3>
                            <p class="guide-text text-dark">
                                Sistem akan memberikan <strong>PERINGATAN MERAH</strong> jika di antara data yang Anda
                                ceklis terdapat SPK yang <strong>BELUM LUNAS</strong>.
                            </p>
                            <ul class="action-list">
                                <li><i class="fas fa-info-circle text-danger"></i> Pastikan Anda sudah berkoordinasi
                                    dengan kasir/CS sebelum menyerahkan barang yang belum lunas.</li>
                                <li><i class="fas fa-info-circle text-danger"></i> Sistem tidak memblokir penyerahan,
                                    namun tanggung jawab verifikasi ada pada operator PB.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row mt-4">
            <div class="col-12 text-center">
                <button class="btn btn-outline-primary px-5 py-3 fw-900 rounded-pill shadow-sm"
                    onclick="window.print()">
                    <i class="fas fa-print me-2"></i>CETAK PANDUAN PENGOPERASIAN
                </button>
            </div>
        </div>
    </div>
</div>