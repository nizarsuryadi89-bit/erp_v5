<?php
// application/views/admin/warehouse/opname.php
?>

<!-- PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - WAREHOUSE STOCK OPNAME -->
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --slate-soft: #f8fafc;
        --slate-muted: #64748b;
        --slate-dark: #1e293b;
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        --accent-blue: #6366f1;
    }

    body {
        background-color: #f8fafc;
        /* font-family: 'Inter', system-ui, -apple-system, sans-serif; */
    }

    /* Standard Header */
    .warehouse-header-section {
        background: var(--primary-gradient);
        color: white;
        padding: 1.5rem 0 3.25rem 0; /* Compact padding */
        margin-bottom: 0;
        border-radius: 0 0 24px 24px; /* Tighter curves */
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
    }

    .warehouse-header-section::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -5%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }

    .header-content,
    .header-actions {
        position: relative;
        z-index: 10;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .header-icon {
        width: 52px; /* Compact icon container */
        height: 52px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .header-title {
        font-size: 1.75rem; /* Smaller premium title */
        font-weight: 900;
        margin: 0;
        letter-spacing: -0.5px;
        line-height: 1.2;
    }

    .header-subtitle {
        font-size: 0.85rem; /* Compact subtitle */
        font-weight: 700;
        margin: 0 0 2px 0;
        opacity: 0.7;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
    }

    /* Metric Cards */
    .stats-container {
        margin-top: -2rem; /* Compact alignment */
        position: relative;
        z-index: 10;
        margin-bottom: 1.5rem;
    }

    .metric-card {
        background: white;
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid rgba(255, 255, 255, 0.8);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        height: 100%;
    }

    .metric-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .metric-info p {
        font-size: 0.75rem;
        color: var(--slate-muted);
        margin: 0;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-info h3 {
        font-size: 1.8rem;
        font-weight: 900;
        margin: 0;
        color: var(--slate-dark);
    }

    /* Content Area */
    .content-card {
        background: white;
        border-radius: 28px;
        padding: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f8fafc;
        margin-bottom: 2rem;
    }

    .card-title-premium {
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--slate-dark);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* Form Premium */
    .form-control-premium {
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 14px;
        padding: 1rem 1.25rem;
        font-weight: 600;
        transition: all 0.25s;
    }

    .form-control-premium:focus {
        background: white;
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .form-label-premium {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--slate-muted);
        letter-spacing: 1px;
        margin-bottom: 0.75rem;
        display: block;
    }

    .btn-premium {
        border-radius: 10px; /* More compact curves */
        padding: 0.45rem 1rem; /* Sleeker padding */
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem; /* Smaller premium text */
        letter-spacing: 0.5px;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none !important;
    }

    .btn-premium-primary {
        background: var(--primary-gradient);
        border: none;
        color: white !important;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }

    /* Tables */
    .table-premium thead th {
        background: #f8fafc;
        border: none;
        color: var(--slate-muted);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        padding: 1.25rem 1.5rem;
    }

    .table-premium tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f8fafc;
        color: var(--slate-dark);
    }

    /* Badge Custom */
    .badge-premium {
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
    }

    .bg-soft-success {
        background: #ecfdf5;
        color: #10b981;
    }

    .bg-soft-blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    /* UX Enhancement: Scan Mode & Adjusters */
    .scan-mode-active #formOpname {
        transform: scale(1.02);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .barcode-scanner-wrapper {
        background: #1e293b;
        padding: 1rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border: 2px solid transparent;
        transition: all 0.3s;
    }

    .barcode-scanner-wrapper:focus-within {
        border-color: var(--accent-blue);
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.2);
    }

    .barcode-input-premium {
        background: transparent !important;
        border: none !important;
        color: white !important;
        font-size: 1.5rem !important;
        font-weight: 800 !important;
        letter-spacing: 2px;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .barcode-input-premium::placeholder {
        color: rgba(255, 255, 255, 0.3);
        letter-spacing: normal;
        font-size: 1.1rem;
        font-weight: 500;
    }

    .btn-adjust {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: white;
        color: var(--slate-dark);
        font-weight: 800;
        transition: all 0.2s;
        cursor: pointer;
    }

    .btn-adjust:hover {
        background: var(--slate-soft);
        border-color: var(--accent-blue);
        color: var(--accent-blue);
    }

    .btn-adjust:active {
        transform: scale(0.9);
    }

    .input-lg-premium {
        font-size: 2.5rem !important;
        font-weight: 900 !important;
        text-align: center !important;
        height: 80px !important;
        border-radius: 20px !important;
        color: var(--slate-dark) !important;
    }

    .scanner-glow {
        animation: glow 1.5s infinite;
    }

    @keyframes glow {
        0% {
            box-shadow: 0 0 5px rgba(99, 102, 241, 0.2);
        }

        50% {
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
        }

        100% {
            box-shadow: 0 0 5px rgba(99, 102, 241, 0.2);
        }
    }

    .scan-success-flash {
        animation: flashGreen 0.5s ease-out;
    }

    @keyframes flashGreen {
        0% {
            background-color: transparent;
        }

        50% {
            background-color: rgba(39, 174, 96, 0.2);
        }

        100% {
            background-color: transparent;
        }
    }

    /* Scanner Mode Premium Enhancements */
    .scan-mode-active .barcode-scanner-wrapper {
        border-color: var(--accent-blue);
        box-shadow: 0 0 30px rgba(99, 102, 241, 0.4);
        background: #0f172a;
        transform: translateY(-5px);
    }

    .scanner-status-badge {
        font-size: 0.65rem;
        font-weight: 900;
        padding: 4px 12px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .status-online {
        background: #22c55e;
        color: white;
    }

    .status-offline {
        background: #64748b;
        color: white;
    }

    #scanFeedbackOverlay {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        pointer-events: none;
    }

    .scan-toast {
        background: rgba(15, 23, 42, 0.9);
        backdrop-filter: blur(10px);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 16px;
        margin-bottom: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        border-left: 5px solid var(--accent-blue);
        display: flex;
        align-items: center;
        gap: 12px;
        transform: translateX(120%);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .scan-toast.show {
        transform: translateX(0);
    }

    .scan-toast.success {
        border-left-color: #22c55e;
    }

    .scan-toast.error {
        border-left-color: #ef4444;
    }

    .scanner-active-pulse {
        width: 12px;
        height: 12px;
        background: #22c55e;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        box-shadow: 0 0 0 rgba(34, 197, 94, 0.4);
        animation: pulse-green 2s infinite;
    }

    @keyframes pulse-green {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
        }

        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
        }
    }
</style>

<div class="warehouse-header-section">
    <div class="container-fluid px-5">
        <div class="row align-items-start">
            <div class="col-md-7">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="fa fa-clipboard-check"></i>
                    </div>
                    <div class="header-text">
                        <p class="header-subtitle">Tahap 5: Rekonsiliasi</p>
                        <h1 class="header-title">Stok Opname & Penyesuaian</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-end header-actions d-flex justify-content-end align-items-center gap-2">
                <button id="btnToggleWorkflow" class="btn btn-outline-light btn-premium rounded-pill px-3">
                    <i class="fa fa-eye-slash me-1"></i>SEMBUNYIKAN
                </button>
                <button class="btn btn-outline-light btn-premium rounded-pill px-3" onclick="openCekStokModal()">
                    <i class="fa fa-clipboard-list me-1"></i>CEK SALDO APLIKASI
                </button>
                <button class="btn btn-outline-light btn-premium rounded-pill px-3" data-bs-toggle="modal"
                    data-bs-target="#modalOpnameGuide">
                    <i class="fa fa-info-circle me-1"></i>BANTUAN/SOP
                </button>
                <a href="<?= base_url('warehouse') ?>" class="btn btn-outline-light btn-premium rounded-pill px-3">
                    <i class="fa fa-arrow-left me-1"></i>DASHBOARD
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-5">
    <!-- Stats Row -->
    <div class="stats-container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-icon" style="background: var(--info-gradient);">
                        <i class="fa fa-list-ol"></i>
                    </div>
                    <div class="metric-info">
                        <p>Total Input Opname</p>
                        <h3><?= number_format(count($recent_opname)) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-icon" style="background: var(--success-gradient);">
                        <i class="fa fa-calendar-alt"></i>
                    </div>
                    <div class="metric-info">
                        <p>Periode Aktif</p>
                        <h3><?= date('M Y') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-icon" style="background: var(--primary-gradient);">
                        <i class="fa fa-sync"></i>
                    </div>
                    <div class="metric-info">
                        <p>Status Auto-Sync</p>
                        <h3>AKTIF</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6-STEP CORE WORKFLOW (SOP) -->
    <div id="workflowContainer">
        <?php $this->load->view('admin/warehouse/components/v_material_workflow', ['current_step' => 3]); ?>
    </div>

    <!-- Scanner Mode Bar (Floating/Fixed top) -->
    <div id="scannerSection" class="mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="barcode-scanner-wrapper scanner-glow">
                    <div class="d-flex align-items-center gap-3 w-100">
                        <i class="fa fa-barcode fa-2x text-primary ms-2"></i>
                        <input type="text" id="barcodeInput" class="form-control barcode-input-premium"
                            placeholder="SCAN BARCODE DI SINI UNTUK MEMULAI..." autocomplete="off">
                        <div class="spinner-border spinner-border-sm text-primary d-none" id="barcodeLoader"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-inline-flex flex-column align-items-end gap-2">
                    <div class="d-inline-flex align-items-center gap-3 p-3 bg-white rounded-20 shadow-sm border">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="scanModeToggle" checked>
                            <label class="form-check-label fw-900 text-dark small" for="scanModeToggle">MODE
                                SCANNER</label>
                        </div>
                    </div>
                    <div id="scannerStatusIndicator" class="small fw-800 text-muted d-none">
                        <span class="scanner-active-pulse"></span> SCANNER SIAP INPUT
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scanner Feedback Overlay -->
    <div id="scanFeedbackOverlay"></div>

    <!-- Entry Box -->
    <div class="content-card" id="opnameEntryCard">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title-premium mb-0"><i class="fa fa-edit text-primary"></i> Input Hasil Opname</h5>
            <div id="syncIndicator" class="badge-premium bg-soft-success text-success d-none">
                <i class="fas fa-check-circle me-1"></i> BARCODE TERDETEKSI
            </div>
        </div>
        
        <div class="alert alert-primary bg-soft-blue border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3">
            <i class="fa fa-lightbulb fa-2x text-primary"></i>
            <div>
                <p class="mb-0 fw-bold" style="font-size: 0.85rem; color: #1e293b;">
                    <strong>Jangan takut jika terjadi selisih!</strong> Tujuan menu ini adalah untuk <span class="text-primary text-decoration-underline">memperbaiki data sistem</span> agar sama persis dengan kenyataan di rak fisik. Pastikan Anda menginput jumlah barang apa adanya di gudang.
                </p>
            </div>
        </div>

        <form action="<?= base_url('warehouse/opname') ?>" method="post" id="formOpname">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label-premium">1. Hubungkan Lokasi</label>
                        <select name="gudang_id" id="opname_gudang" class="form-select form-control-premium select2"
                            required>
                            <?php foreach ($warehouses as $w): ?>
                                <option value="<?= $w->gudang_id ?>"><?= $w->gudang_nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-premium">2. Pilih Material / Barang</label>
                        <select name="material_id" id="opname_material" class="form-select form-control-premium select2"
                            required>
                            <option value="">-- Pilih Material --</option>
                            <?php foreach ($materials as $m): ?>
                                <option value="<?= $m->material_id ?>">
                                    <?= $m->varian ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="p-4 bg-slate-soft rounded-24 border">
                        <label class="form-label-premium"><i class="fas fa-desktop me-2"></i>Stok Sistem Saat
                            Ini</label>
                        <div id="system-stock-breakdown" class="fw-900 text-dark" style="font-size: 1.5rem;">
                            ---
                        </div>
                        <input type="hidden" name="son_aplikasi" id="son_aplikasi_hidden">
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-premium text-primary">3. Masukkan Stok Fisik</label>

                            <!-- Quick Pad Adjuster -->
                            <div class="d-flex flex-column gap-3">
                                <div class="input-group">
                                    <button type="button" class="btn-adjust btn-minus" data-target="#input_whole"
                                        data-val="1"><i class="fa fa-minus"></i></button>
                                    <input type="number" step="1" id="input_whole"
                                        class="form-control form-control-premium input-lg-premium mx-2" placeholder="0">
                                    <button type="button" class="btn-adjust btn-plus" data-target="#input_whole"
                                        data-val="1"><i class="fa fa-plus"></i></button>
                                </div>
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-sm btn-light border fw-bold btn-quick-set"
                                        data-target="#input_whole" data-val="10">+10</button>
                                    <button type="button" class="btn btn-sm btn-light border fw-bold btn-quick-set"
                                        data-target="#input_whole" data-val="50">+50</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger fw-bold"
                                        onclick="$('#input_whole').val(0).trigger('input')">RESET</button>
                                </div>
                                <div class="text-center mt-n2">
                                    <span class="badge bg-primary rounded-pill px-3 label-whole">Satuan</span>
                                </div>
                            </div>

                            <div id="pieces-input-container" class="mt-4" style="display: none;">
                                <div class="d-flex flex-column gap-3">
                                    <div class="input-group">
                                        <button type="button" class="btn-adjust btn-minus" data-target="#input_pieces"
                                            data-val="1"><i class="fa fa-minus"></i></button>
                                        <input type="number" step="1" id="input_pieces"
                                            class="form-control form-control-premium input-lg-premium mx-2 border-info"
                                            placeholder="0">
                                        <button type="button" class="btn-adjust btn-plus" data-target="#input_pieces"
                                            data-val="1"><i class="fa fa-plus"></i></button>
                                    </div>
                                    <div class="text-center mt-n2">
                                        <span class="badge bg-info text-dark rounded-pill px-3 label-pieces">Satuan
                                            Kecil</span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="son_real" id="son_real_final_input">
                        </div>

                        <div class="col-md-6">
                            <div class="metric-card bg-slate-soft border-0 h-100 flex-column justify-content-between">
                                <div class="w-100 text-center">
                                    <label class="form-label-premium">Selisih Realtime</label>
                                    <div id="delta-display" class="fw-900" style="font-size: 3.5rem; color: #64748b;">
                                        0.000</div>
                                    <p id="delta-message" class="small fw-bold text-muted mb-1">Sesuai dengan sistem</p>
                                    <div class="alert alert-light border shadow-sm p-2 mt-2" style="font-size: 0.65rem; border-radius: 12px; text-align: left;">
                                        <i class="fa fa-info-circle text-primary me-1"></i>
                                        <strong>Rumus:</strong> (Stok Fisik) - (Stok Sistem).<br>
                                        Jika angka di atas <strong>minus (-)</strong>, berarti fisik Anda kurang dari sistem. Silakan isi kotak <strong>Masukkan Stok Fisik</strong> sesuai jumlah yang Anda hitung rak agar deviasi ini akurat.
                                    </div>
                                </div>

                                <div class="w-100">
                                    <label class="form-label-premium">4. Alasan / Catatan</label>
                                    <textarea name="catatan" class="form-control form-control-premium" rows="2"
                                        placeholder="Tuliskan alasan jika ada selisih..."></textarea>
                                </div>

                                <div class="w-100 mt-3">
                                    <button type="submit"
                                        class="btn btn-premium btn-premium-primary w-100 py-4 shadow-lg"
                                        style="font-size: 1.1rem;">
                                        <i class="fa fa-save fa-lg me-2"></i> SIMPAN HASIL REKONSILIASI
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-top d-flex align-items-center gap-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="sync_now" value="1" id="syncNowCheck" checked>
                    <label class="form-check-label fw-bold text-primary small" for="syncNowCheck">Otomatis Update Stok
                        Sistem</label>
                </div>
                <span class="text-muted small">| Hasil opname akan langsung mempengaruhi jumlah stok di database.</span>
            </div>
        </form>
    </div>

    <!-- history table -->
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="card-title-premium mb-1"><i class="fa fa-history text-secondary"></i> Log Aktivitas Jurnal
                    Opname</h5>
                <p class="text-muted small mb-0">Menampilkan urutan kronologis kejadian rekonsiliasi stok (Bukan saldo
                    akhir saat ini)</p>
            </div>
            <div class="btn-group">
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt me-1"></i> Segarkan Log
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-premium align-middle mb-0" id="tableOpname">
                <thead class="bg-light">
                    <tr>
                        <th width="180">WAKTU LOG</th>
                        <th>ITEM REKONSILIASI</th>
                        <th>TITIK LOKASI</th>
                        <th class="text-end">SALDO SISTEM</th>
                        <th class="text-end">HASIL FISIK</th>
                        <th class="text-center">DEVIASI (SELISIH)</th>
                        <th class="text-center">STATUS AUDIT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_opname as $o):
                        $selisih = $o->son_real - $o->son_aplikasi;
                        ?>
                        <tr>
                            <td>
                                <div class="fw-800 text-dark small"><?= date('d M Y', strtotime($o->son_tgl)) ?></div>
                                <div class="text-muted small font-monospace"><?= date('H:i:s', strtotime($o->son_tgl)) ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-800 text-dark"><?= $o->bahan_nama ?></div>
                                <div class="small text-muted"><?= $o->varian ?></div>
                            </td>
                            <td><span
                                    class="badge-premium bg-soft-blue text-primary"><?= $o->gudang_nama ?? 'Main' ?></span>
                            </td>
                            <td class="text-end text-muted fw-bold">
                                <?php
                                $konversi = (float)$o->konversi > 0 ? (float)$o->konversi : 1;
                                $app_total_pieces = round($o->son_aplikasi * $konversi);
                                $app_whole = floor($app_total_pieces / $konversi);
                                $app_remainder = $app_total_pieces % $konversi;
                                echo number_format($app_whole) . " " . $o->satuan_nama;
                                if ($app_remainder > 0 && $konversi > 1)
                                    echo "<br><small>+ " . number_format($app_remainder) . " " . $o->satuan_produksi . "</small>";
                                ?>
                            </td>
                            <td class="text-end fw-900 text-dark">
                                <?php
                                $real_total_pieces = round($o->son_real * $konversi);
                                $real_whole = floor($real_total_pieces / $konversi);
                                $real_remainder = $real_total_pieces % $konversi;
                                echo number_format($real_whole) . " " . $o->satuan_nama;
                                if ($real_remainder > 0 && $konversi > 1)
                                    echo "<br><small>+ " . number_format($real_remainder) . " " . $o->satuan_produksi . "</small>";
                                ?>
                            </td>
                            <td class="text-center">
                                <?php if (abs($selisih) < 0.0001): ?>
                                    <span class="text-success fw-900">0</span>
                                <?php else: ?>
                                    <span class="<?= $selisih > 0 ? 'text-warning' : 'text-danger' ?> fw-900">
                                        <?php
                                        $diff_total_pieces = round($selisih * $konversi);
                                        $diff_whole = ($selisih > 0) ? floor(abs($diff_total_pieces) / $konversi) : ceil($diff_total_pieces / $konversi);
                                        $diff_remainder = abs($diff_total_pieces) % $konversi;

                                        echo ($selisih > 0 ? '+' : '') . number_format($diff_whole) . " " . $o->satuan_nama;
                                        if ($diff_remainder > 0 && $konversi > 1)
                                            echo "<br><small>" . ($selisih > 0 ? '+' : '-') . number_format($diff_remainder) . " " . $o->satuan_produksi . "</small>";
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($o->verifikasi == 1): ?>
                                    <span class="badge-premium bg-soft-success text-success fw-800"><i
                                            class="fa fa-check-circle me-1"></i> TERVERIFIKASI & SINKRON</span>
                                <?php else: ?>
                                    <span class="badge-premium bg-light text-muted fw-800">MENUNGGU</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Init Select2
        $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });

        const table = $('#tableOpname').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "language": { "search": "Cari Riwayat:" }
        });

        let global_meta = { konversi: 1, satuan_gudang: 'Unit', satuan_produksi: 'Pcs' };

        // --- WORKFLOW TOGGLE ---
        $('#btnToggleWorkflow').on('click', function () {
            const container = $('#workflowContainer');
            const btn = $(this);
            if (container.is(':visible')) {
                container.slideUp(400);
                btn.html('<i class="fa fa-eye me-2"></i>TAMPILKAN SOP');
                localStorage.setItem('tgm_opname_sop_hidden', 'true');
            } else {
                container.slideDown(400);
                btn.html('<i class="fa fa-eye-slash me-2"></i>SEMBUNYIKAN SOP');
                localStorage.removeItem('tgm_opname_sop_hidden');
            }
        });

        // Restore state from localStorage
        if (localStorage.getItem('tgm_opname_sop_hidden') === 'true') {
            $('#workflowContainer').hide();
            $('#btnToggleWorkflow').html('<i class="fa fa-eye me-2"></i>TAMPILKAN SOP');
        }

        // --- BARCODE LOOKUP ---
        const scanSound = new Audio('https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3'); // Subtle beep

        function showScanFeedback(title, message, type = 'success') {
            const id = 'toast-' + Date.now();
            const icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-times-circle' : 'fa-info-circle');
            const html = `
                <div id="${id}" class="scan-toast ${type}">
                    <i class="fa ${icon} fa-lg"></i>
                    <div>
                        <div class="fw-900 small">${title.toUpperCase()}</div>
                        <div class="small opacity-75">${message}</div>
                    </div>
                </div>
            `;
            $('#scanFeedbackOverlay').append(html);
            setTimeout(() => $(`#${id}`).addClass('show'), 10);
            setTimeout(() => {
                $(`#${id}`).removeClass('show');
                setTimeout(() => $(`#${id}`).remove(), 300);
            }, 3000);
        }

        $('#barcodeInput').on('keypress', function (e) {
            if (e.which == 13) { // Enter key
                e.preventDefault();
                const barcode = $(this).val().trim();
                if (barcode) lookupBarcode(barcode);
                $(this).val('');
            }
        });

        function lookupBarcode(barcode) {
            $('#barcodeLoader').removeClass('d-none');
            $.ajax({
                url: '<?= base_url('warehouse/ajax_lookup_barcode') ?>',
                type: 'GET',
                data: { barcode: barcode },
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        try { scanSound.play().catch(e => { }); } catch (err) { }

                        const currentMaterialId = $('#opname_material').val();
                        if (currentMaterialId == res.data.material_id && $('#scanModeToggle').is(':checked')) {
                            // Same item scanned again -> increment
                            const currentWhole = parseFloat($('#input_whole').val()) || 0;
                            $('#input_whole').val(currentWhole + 1).trigger('input');
                            showScanFeedback("Berhasil", `+1 ${res.data.varian}`, 'success');
                        } else {
                            // New item scanned
                            $('#opname_material').val(res.data.material_id).trigger('change');

                            if ($('#scanModeToggle').is(':checked')) {
                                $('#input_whole').val(1).trigger('input');
                                showScanFeedback("Terpilih", res.data.varian, 'success');
                            } else {
                                showScanFeedback("Ditemukan", res.data.varian, 'info');
                            }
                        }

                        $('#syncIndicator').removeClass('d-none').addClass('scan-success-flash');
                        setTimeout(() => $('#syncIndicator').removeClass('scan-success-flash'), 500);

                        // Keep focus on barcode input in scan mode
                        if ($('#scanModeToggle').is(':checked')) {
                            $('#barcodeInput').focus().select();
                        } else {
                            $('#input_whole').focus().select();
                        }
                    } else {
                        showScanFeedback("Tidak Ditemukan", res.message, 'error');
                    }
                },
                complete: () => $('#barcodeLoader').addClass('d-none')
            });
        }

        // --- SCAN MODE TOGGLE ---
        $('#scanModeToggle').on('change', function () {
            if ($(this).is(':checked')) {
                $('body').addClass('scan-mode-active');
                $('#scannerStatusIndicator').removeClass('d-none');
                $('#barcodeInput').focus();

                // Alert if using scanner mode
                Swal.fire({
                    title: 'Mode Scanner Aktif',
                    text: 'Scanner akan otomatis menambah jumlah item. Tekan ENTER pada scanner tidak akan langsung menyimpan data.',
                    icon: 'info',
                    toast: true,
                    position: 'top-end',
                    timer: 5000,
                    showConfirmButton: false
                });
            } else {
                $('body').removeClass('scan-mode-active');
                $('#scannerStatusIndicator').addClass('d-none');
            }
        });

        // Prevent unintentional submission from Enter key in SCAN MODE
        $(document).on('keydown', 'input', function (e) {
            if (e.which == 13 && $('#scanModeToggle').is(':checked')) {
                if ($(this).attr('id') !== 'barcodeInput') {
                    // If Enter pressed on other input while in scan mode, 
                    // redirect focus back to barcode input instead of submitting
                    e.preventDefault();
                    $('#barcodeInput').focus();
                    return false;
                }
            }
        });

        // --- QUICK ADJUSTERS ---
        $('.btn-plus').on('click', function () {
            const target = $($(this).data('target'));
            const step = parseFloat($(this).data('val')) || 1;
            const current = parseFloat(target.val()) || 0;
            target.val(current + step).trigger('input');
        });

        $('.btn-minus').on('click', function () {
            const target = $($(this).data('target'));
            const step = parseFloat($(this).data('val')) || 1;
            const current = parseFloat(target.val()) || 0;
            if (current >= step) target.val(current - step).trigger('input');
        });

        $('.btn-quick-set').on('click', function () {
            const target = $($(this).data('target'));
            const val = parseFloat($(this).data('val')) || 0;
            const current = parseFloat(target.val()) || 0;
            target.val(current + val).trigger('input');
        });

        // Helper to format number in Indonesian style (decimal using comma, dot for thousand)
        function formatID(num, decimals = 3) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: decimals
            }).format(num);
        }

        // AJAX Fetch current stock
        function fetchCurrentStock() {
            const material_id = $('#opname_material').val();
            const gudang_id = $('#opname_gudang').val();

            if (material_id && gudang_id) {
                $.ajax({
                    url: '<?= base_url('warehouse/ajax_get_current_stock') ?>',
                    type: 'POST',
                    data: { material_id: material_id, gudang_id: gudang_id },
                    dataType: 'json',
                    success: function (res) {
                        const stok = parseFloat(res.current_stock) || 0;
                        global_meta = res;

                        // Update UI Labels
                        $('.label-whole').text(res.satuan_gudang);
                        $('.label-pieces').text(res.satuan_produksi);

                        // Show/Hide pieces input
                        if (res.konversi > 1) {
                            $('#pieces-input-container').fadeIn();
                        } else {
                            $('#pieces-input-container').hide();
                        }

                        // Breakdown current stock for display
                        const total_pieces = Math.round(stok * res.konversi);
                        const whole = Math.floor(total_pieces / res.konversi);
                        const remainder = total_pieces % res.konversi;

                        let breakdown = `<strong>${formatID(whole, 0)}</strong> ${res.satuan_gudang}`;
                        if (res.konversi > 1) breakdown += ` <strong>${formatID(remainder, 0)}</strong> ${res.satuan_produksi}`;

                        // Show clarified decimal with Indonesian comma
                        breakdown += `<div class='mt-1 text-muted fw-normal' style='font-size:0.8rem'>
                            Sama dengan: ${formatID(stok)} ${res.satuan_gudang} 
                            <span class="mx-1">|</span> 
                            Total: ${formatID(total_pieces, 0)} ${res.satuan_produksi}
                        </div>`;

                        $('#system-stock-breakdown').html(breakdown);
                        $('#son_aplikasi_hidden').val(stok);

                        calculateFinalQty();
                    }
                });
            }
        }

        // Auto-select from URL params
        const urlParams = new URLSearchParams(window.location.search);
        const mId = urlParams.get('material_id');
        const gId = urlParams.get('gudang_id') || '1'; // Default main

        if (mId) {
            $('#opname_material').val(mId).trigger('change.select2');
            $('#opname_gudang').val(gId).trigger('change.select2');
            fetchCurrentStock();
        }

        $('#opname_material, #opname_gudang').on('change', fetchCurrentStock);

        // Core Calculation Logic
        function calculateFinalQty() {
            const whole = parseFloat($('#input_whole').val()) || 0;
            const pieces = parseFloat($('#input_pieces').val()) || 0;
            const konversi = global_meta.konversi || 1;

            const finalQty = whole + (pieces / konversi);
            $('#son_real_final_input').val(finalQty);

            calculateDelta(finalQty);
        }

        // Live deviation calculation
        function calculateDelta(finalQty) {
            const app = parseFloat($('#son_aplikasi_hidden').val()) || 0;
            const delta = finalQty - app;
            const konversi = global_meta.konversi || 1;

            const display = $('#delta-display');
            const msg = $('#delta-message');

            const total_diff_pieces = Math.round(delta * konversi);

            // Change big number to show the main warehouse unit (Satuan Gudang)
            display.html((delta > 0 ? '+' : '') + formatID(delta) + `<span style="font-size: 1.25rem; margin-left: 8px; opacity: 0.7;">${global_meta.satuan_gudang || ''}</span>`);

            if (Math.abs(delta) < 0.0001) {
                display.css({ 'color': '#27ae60' });
                msg.text('Sesuai dengan sistem').removeClass('text-danger text-warning').addClass('text-success');
            } else {
                // Show breakdown for delta to avoid ambiguity
                const d_whole = (delta > 0) ? Math.floor(Math.abs(total_diff_pieces) / konversi) : -Math.floor(Math.abs(total_diff_pieces) / konversi);
                const d_remainder = Math.abs(total_diff_pieces) % konversi;

                let delta_text = (delta > 0 ? 'Kelebihan stok fisik: ' : 'Kekurangan stok fisik: ');
                
                if (konversi > 1) {
                    delta_text += `(${formatID(Math.abs(d_whole), 0)} ${global_meta.satuan_gudang || ''} ${formatID(d_remainder, 0)} ${global_meta.satuan_produksi || ''})`;
                }

                if (delta > 0) {
                    display.css({ 'color': '#f39c12' });
                    msg.text(delta_text).removeClass('text-danger text-success').addClass('text-warning');
                } else {
                    display.css({ 'color': '#e74c3c' });
                    msg.text(delta_text).removeClass('text-warning text-success').addClass('text-danger');
                }
            }
        }

        $('#input_whole, #input_pieces').on('input', calculateFinalQty);

        // Shortcut Keys
        $(document).on('keydown', function (e) {
            if (e.altKey && e.key === 's') {
                e.preventDefault();
                $('#barcodeInput').focus();
            }
        });

        // --- CEK SALDO APLIKASI MODAL ---
        window.openCekStokModal = function() {
            $('#modalCekStok').modal('show');
            loadCekStokData();
        };

        function loadCekStokData() {
            const gudang_id = $('#opname_gudang').val() || 1;
            $('#cekStokLoader').removeClass('d-none');
            $('#tableCekStokBody').html('');

            $.ajax({
                url: '<?= base_url('warehouse/ajax_get_all_stock_opname') ?>',
                type: 'POST',
                data: { gudang_id: gudang_id },
                dataType: 'json',
                success: function(res) {
                    if (res.success && res.data) {
                        // Urutkan nilai paling minus ke teratas
                        res.data.sort((a, b) => parseFloat(a.qty_stock) - parseFloat(b.qty_stock));

                        let html = '';
                        let rowNumber = 1;
                        res.data.forEach((item) => {
                            let qty = parseFloat(item.qty_stock);
                            
                            // Filter: jika stok nol persis, tidak perlu dieksekusi / disembunyikan
                            if (qty === 0) return;

                            let konversi = parseFloat(item.konversi) || 1;
                            let isMinus = qty < 0;
                            let isPlus = qty > 0;
                            
                            let total_pieces = Math.round(Math.abs(qty) * konversi);
                            let whole = Math.floor(total_pieces / konversi);
                            let remainder = total_pieces % konversi;

                            let displayQty = `<div class="fw-bold fs-5 ${isMinus ? 'text-danger' : 'text-success'}">`;
                            let prefix = isMinus ? '-' : (isPlus ? '+' : '');
                            
                            displayQty += prefix + formatID(whole, 0) + ` <small class="text-dark fs-7">${item.satuan_nama || 'Unit'}</small>`;
                            if (remainder > 0 && konversi > 1) {
                                displayQty += `<br><small class="${isMinus ? 'text-danger' : 'text-primary'}" style="font-size: 0.70rem;"> + ${formatID(remainder, 0)} ${item.satuan_produksi || 'Pcs'}</small>`;
                            }
                            displayQty += `</div>`;

                            html += `
                                <tr>
                                    <td class="text-center text-muted fw-bold">${rowNumber++}</td>
                                    <td>
                                        <div class="fw-800 text-dark">${item.bahan_nama}</div>
                                        <div class="small text-muted">${item.varian || '-'}</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark font-monospace border">${item.barcode || '-'}</span></td>
                                    <td class="text-end align-middle bg-light border-start border-end" style="min-width: 140px;">${displayQty}</td>
                                    <td class="text-center bg-white">
                                        <button class="btn btn-sm btn-light border btn-premium" onclick="selectForOpname(${item.material_id})" title="Opname Item Ini">
                                            <i class="fa fa-hand-pointer text-primary"></i> Hitung Fisik
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        if ($.fn.DataTable.isDataTable('#tableCekStok')) {
                            $('#tableCekStok').DataTable().destroy();
                        }
                        
                        $('#tableCekStokBody').html(html);
                        
                        $('#tableCekStok').DataTable({
                            pageLength: 15,
                            ordering: false, // Pertahankan urutan dari proses sort di atas
                            language: { search: "Cari Material:" }
                        });
                    }
                },
                complete: function() {
                    $('#cekStokLoader').addClass('d-none');
                }
            });
        }

        window.selectForOpname = function(material_id) {
            $('#modalCekStok').modal('hide');
            $('#opname_material').val(material_id).trigger('change.select2');
            if (!$('#scanModeToggle').is(':checked')) {
                setTimeout(() => $('#input_whole').focus().select(), 500);
            }
        };


        // Validation on submit
        $('#formOpname').on('submit', function (e) {
            const material = $('#opname_material').val();
            if (!material) {
                Swal.fire('Required', 'Silakan pilih material yang akan di-opname!', 'warning');
                return false;
            }
        });

        // Inisialisasi Mode Scanner jika aktif (default)
        if ($('#scanModeToggle').is(':checked')) {
            $('body').addClass('scan-mode-active');
            $('#scannerStatusIndicator').removeClass('d-none');
            $('#barcodeInput').focus();
        }
    });
</script>

<?php $this->load->view('admin/warehouse/components/v_modal_opname_guide'); ?>

<!-- Modal Cek Saldo Sistem -->
<div class="modal fade" id="modalCekStok" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header px-4 py-4" style="background: var(--primary-gradient); color: white;">
                <h5 class="modal-title fw-900"><i class="fas fa-clipboard-list me-2"></i>Daftar Pengecekan Saldo Sistem</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-slate-soft">
                <div class="alert alert-light border shadow-sm mb-4">
                    <i class="fa fa-info-circle text-primary me-2"></i> Daftar ini adalah <strong>Saldo Mentah</strong> yang dicatat oleh sistem pusat saat ini (real-time). Bawa perangkat Anda keliling gudang dan perhatikan jika fisik di rak berbeda dengan angka di bawah ini, atau langsung klik tombol <strong>Hitung Fisik</strong>.
                </div>
                
                <div class="text-center py-5 d-none" id="cekStokLoader">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 text-muted fw-bold">Memuat data stok realtime dari server...</p>
                </div>

                <div class="card border-0 shadow-sm p-3">
                    <div class="table-responsive">
                        <table class="table table-premium align-middle mb-0 w-100" id="tableCekStok">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50" class="text-center">NO</th>
                                    <th style="min-width: 300px;">MATERIAL / BARANG</th>
                                    <th>BARCODE</th>
                                    <th class="text-end">SALDO SISTEM</th>
                                    <th class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="tableCekStokBody">
                                <!-- Ajax Content -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>