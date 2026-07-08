<!-- External CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

<style>
    /* Fix Select2 z-index in nested modals */
    .select2-container--open {
        z-index: 9999999 !important;
    }

    /* ================================================================
     * PRODUCT CATALOG – Visual theme aligned with Warehouse module
     * ================================================================ */
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --info-gradient: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        --slate-muted: #64748b;
        --slate-dark: #1e293b;
        --accent-blue: #6366f1;
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    body {
        background-color: #f8fafc;
    }

    /* ---------- Hero Header ---------- */
    .product-header-section {
        background: var(--primary-gradient);
        color: white;
        padding: 2.5rem 0 5rem 0;
        margin-bottom: 0;
        border-radius: 0 0 30px 30px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .product-header-section::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -5%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.12) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .header-content {
        position: relative;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 1.75rem;
    }

    .header-actions {
        position: relative;
        z-index: 200;
    }

    .header-actions .dropdown-menu {
        z-index: 1050 !important;
    }

    .header-icon {
        width: 76px;
        height: 76px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.25rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: white;
    }

    .header-title {
        font-size: 2.2rem;
        font-weight: 900;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .header-subtitle {
        font-size: 0.85rem;
        font-weight: 700;
        margin: 0 0 4px 0;
        opacity: 0.65;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #94a3b8;
    }

    /* ---------- Metric cards (float up from header) ---------- */
    .stats-container {
        margin-top: -3.5rem;
        position: relative;
        z-index: 10;
        margin-bottom: 2rem;
    }

    .metric-card {
        background: white;
        border-radius: 24px;
        padding: 1.4rem 1.5rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid rgba(255, 255, 255, 0.8);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
    }

    .metric-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        border-color: var(--accent-blue);
    }

    .metric-header {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .metric-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: white;
    }

    .metric-info p {
        font-size: 0.72rem;
        color: var(--slate-muted);
        margin: 0;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-info h3 {
        font-size: 1.75rem;
        font-weight: 900;
        margin: 0;
        color: var(--slate-dark);
    }

    .bg-soft-blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    .bg-soft-emerald {
        background: #ecfdf5;
        color: #10b981;
    }

    .bg-soft-amber {
        background: #fffbeb;
        color: #f59e0b;
    }

    .bg-soft-purple {
        background: #f5f3ff;
        color: #8b5cf6;
    }

    .bg-soft-rose {
        background: #fff1f2;
        color: #f43f5e;
    }

    /* ---------- Content card ---------- */
    .content-card {
        background: white;
        border-radius: 28px;
        padding: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f1f5f9;
        margin-bottom: 2rem;
    }

    /* ---------- Controls (search bar area) ---------- */
    .form-control-premium {
        border-radius: 14px;
        padding: 0.75rem 1.25rem;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-weight: 500;
        transition: all 0.2s;
    }

    .form-control-premium:focus {
        background: white;
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.65rem 1rem;
        font-size: 0.875rem;
        color: var(--slate-dark);
        background: #f8fafc;
        transition: all 0.2s;
    }

    .form-control:focus,
    .form-select:focus {
        background: white;
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
    }

    .alphabet-filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding-top: 1rem;
        margin-top: 1rem;
        border-top: 1px solid #eef2f7;
    }

    .alphabet-filter-btn {
        min-width: 38px;
        height: 38px;
        padding: 0 0.75rem;
        border-radius: 12px;
        border: 1px solid #dbe4ee;
        background: #f8fafc;
        color: var(--slate-dark);
        font-size: 0.78rem;
        font-weight: 800;
        line-height: 1;
    }

    .alphabet-filter-btn:hover {
        background: #eef4ff;
        border-color: #93c5fd;
        color: #1d4ed8;
    }

    .alphabet-filter-btn.active {
        background: var(--primary-gradient);
        border-color: transparent;
        color: white;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.14);
    }

    /* ---------- Buttons ---------- */
    .btn {
        border-radius: 12px;
        padding: 0.6rem 1.25rem;
        font-weight: 700;
        font-size: 0.8rem;
        letter-spacing: 0.3px;
        transition: all 0.25s;
    }

    .btn-premium {
        border-radius: 14px;
        padding: 0.7rem 1.5rem;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 1px;
    }

    .btn-premium-primary {
        background: var(--primary-gradient);
        border: none;
        color: white;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .btn-premium-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        color: white;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    /* ---------- Premium table ---------- */
    .table-premium thead th {
        background: #f8fafc;
        border: none;
        color: var(--slate-muted);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        padding: 1.2rem 1rem;
    }

    .table thead th {
        background: #1a1c23;
        color: white;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 1rem 1rem;
        border: none;
        vertical-align: middle;
    }

    .table tbody td {
        padding: 1rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: var(--slate-dark);
        font-size: 0.9rem;
        font-weight: 500;
    }

    .table-hover tbody tr:hover {
        background-color: #f8fafc !important;
        cursor: pointer;
    }

    /* ---------- Status / jenis badges ---------- */
    .status-badge {
        font-size: 0.58rem;
        font-weight: 700;
        padding: 0.12rem 0.45rem;
        border-radius: 12px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    /* ---------- Feature Indicators (Price / BOM / Routing) ---------- */
    .setup-container {
        display: flex;
        gap: 8px;
        justify-content: center;
        align-items: center;
    }

    .feature-indicator {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #f8fafc;
        color: #94a3b8;
        border: 1.5px solid #e2e8f0;
        position: relative;
    }

    .feature-indicator i {
        font-size: 1rem;
        margin-bottom: -2px;
    }

    .feature-indicator .count-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #64748b;
        color: white;
        font-size: 0.6rem;
        padding: 2px 5px;
        border-radius: 6px;
        border: 2px solid white;
        min-width: 18px;
        font-weight: 800;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .indicator-price.has-data {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }

    .indicator-price.has-data .count-badge {
        background: #2563eb;
    }

    .indicator-bom.has-data {
        background: #fffbeb;
        color: #d97706;
        border-color: #fef3c7;
    }

    .indicator-bom.has-data .count-badge {
        background: #d97706;
    }

    .indicator-routing.has-data {
        background: #f5f3ff;
        color: #4f46e5;
        border-color: #ddd6fe;
    }

    .indicator-routing.has-data .count-badge {
        background: #4f46e5;
    }

    .feature-indicator:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.12);
        z-index: 10;
    }

    .feature-indicator.has-data:hover {
        border-color: currentColor;
    }

    .feature-indicator:not(.has-data) {
        opacity: 0.55;
        border-style: dashed;
    }

    /* ---------- Product name link hover ---------- */
    .hover-primary:hover {
        color: var(--accent-blue) !important;
        transition: color 0.2s ease;
    }

    /* ---------- Select2 ---------- */
    .select2-container {
        z-index: 999999 !important;
    }

    .select2-dropdown {
        z-index: 999999 !important;
    }

    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        background: #f8fafc !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        padding-left: 12px !important;
        color: var(--slate-dark) !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }

    .select2-search__field {
        height: auto !important;
        padding: 8px !important;
    }

    .select2-search--dropdown {
        display: block !important;
        padding: 4px !important;
    }

    /* ---------- Mini Step Tracker (Inside Modals) ---------- */
    .mini-sop-tracker {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        background: #f8fafc;
        padding: 1rem;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
    }

    .mini-step {
        display: flex;
        align-items: center;
        gap: 8px;
        opacity: 0.4;
        transition: all 0.3s;
    }

    .mini-step.active {
        opacity: 1;
    }

    .mini-step.active .step-num {
        background: #2563eb;
        color: white;
        border-color: #2563eb;
    }

    .mini-step.completed .step-num {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }

    .mini-step .step-num {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 900;
    }

    .mini-step .step-text {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .step-line {
        flex: 1;
        height: 2px;
        background: #e2e8f0;
        align-self: center;
        margin: 0 10px;
    }

    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            text-align: center;
        }

        .stats-container {
            margin-top: -2rem;
        }
    }

    /* ---------- SOP Progress Bar ---------- */
    .product-sop-progressive {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        display: flex;
        overflow: hidden;
        margin-bottom: 2rem;
        border: 1px solid #f1f5f9;
        height: 100px;
    }

    .sop-hero {
        background: #2563eb;
        color: white;
        padding: 0 2rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 25%;
        position: relative;
    }

    .sop-hero h4 {
        margin: 0;
        font-weight: 900;
        letter-spacing: 1px;
        font-size: 1.1rem;
    }

    .sop-hero p {
        margin: 4px 0 0 0;
        font-size: 0.75rem;
        opacity: 0.8;
    }

    .sop-step-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        text-align: center;
        padding: 0 10px;
        transition: all 0.3s;
        border-right: 1px solid #f1f5f9;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }

    .sop-step-item:last-child {
        border-right: none;
    }

    .sop-step-item:hover {
        background: #f8fafc;
    }

    .sop-number {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 0.8rem;
        margin-bottom: 8px;
        border: 1px solid #dbeafe;
    }

    .sop-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: #1e293b;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    /* ---------- Interactive Metric Cards ---------- */
    .metric-card.clickable {
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
    }

    .metric-card.clickable:hover {
        transform: translateY(-5px);
        background: #ffffff;
        box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.1);
        border-color: #e2e8f0;
    }

    .metric-card.clickable:active {
        transform: translateY(-2px);
    }

    .sop-sublabel {
        font-size: 0.65rem;
        color: #64748b;
        font-weight: 500;
    }

    .sop-arrow {
        position: absolute;
        right: -8px;
        top: 50%;
        transform: translateY(-50%);
        color: #cbd5e1;
        font-size: 1.2rem;
        z-index: 5;
    }

    /* ---------- Sortable Headers ---------- */
    .sortable {
        cursor: pointer;
        position: relative;
        transition: background-color 0.2s;
    }

    .sortable:hover {
        background-color: #f1f5f9 !important;
        color: var(--accent-blue) !important;
    }

    .sort-icon {
        margin-left: 5px;
        font-size: 0.8rem;
        opacity: 0.5;
    }

    .sortable.active {
        color: var(--accent-blue) !important;
    }

    .sortable.active .sort-icon {
        opacity: 1;
    }
</style>

<!-- ====== HERO HEADER ====== -->
<div class="product-header-section">
    <div class="container-fluid px-5">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="header-text">
                        <p class="header-subtitle">Master Katalog Manufaktur</p>
                        <h1 class="header-title">Produk &amp; Setup</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-8 text-end header-actions d-flex justify-content-end align-items-center gap-2">
                <button class="btn btn-warning text-dark fw-bold rounded-pill px-4 border-0 shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#modalSopProduk">
                    <i class="fas fa-book-reader me-2"></i>SOP
                </button>
                <a href="<?= base_url('manufaktur/catalog') ?>"
                    class="btn btn-primary text-white fw-bold rounded-pill px-4 border-0 shadow-sm transition-all hover-scale">
                    <i class="bi bi-journal-text me-2"></i>KATALOG HARGA
                </a>
                <a href="<?= base_url('warehouse/materials_jasa') ?>"
                    class="btn btn-success text-white fw-bold rounded-pill px-4 border-0 shadow-sm transition-all hover-scale">
                    <i class="bi bi-journal-text me-2"></i>Bahan Baku
                </a>

                <button class="btn btn-warning text-dark fw-bold rounded-pill px-4 border-0 shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#modalBulkPriceUpdate">
                    <i class="fas fa-coins me-2"></i>ADJUST HARGA
                </button>
                <button class="btn btn-danger btn-premium rounded-pill px-4" data-bs-toggle="modal"
                    data-bs-target="#modalBulkSetup">
                    <i class="bi bi-layers-half me-2"></i>BULK SETUP
                </button>
                <button class="btn btn-outline-light btn-premium rounded-pill px-4" data-bs-toggle="modal"
                    data-bs-target="#modalInputData">
                    <i class="bi bi-plus-lg me-2"></i>PRODUK BARU
                </button>

            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-5">
    <!-- ====== METRIC CARDS ====== -->
    <div class="stats-container">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon bg-soft-blue"><i class="bi bi-box-seam"></i></div>
                        <div class="metric-info">
                            <p>Total Produk</p>
                            <h3><?= number_format($total_products) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon" style="background: #fef3c7; color: #b45309;"><i
                                class="bi bi-hourglass-split"></i></div>
                        <div class="metric-info">
                            <p>Produk Partial</p>
                            <h3><?= number_format($produk_partial ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon" style="background: #dcfce7; color: #15803d;"><i
                                class="bi bi-check2-all"></i></div>
                        <div class="metric-info">
                            <p>Produk Ready</p>
                            <h3><?= number_format($produk_complete ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon" style="background: #e0e7ff; color: #4338ca;"><i
                                class="bi bi-tags-fill"></i></div>
                        <div class="metric-info">
                            <p>Produk Siap Jual</p>
                            <h3><?= number_format($produk_harga_ready ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- ====== CONTENT CARD ====== -->
    <div class="content-card">
        <!-- Control bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2 flex-wrap" style="flex:1;">
                <div class="input-group" style="max-width:300px;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="searchInput"
                        placeholder="Cari Produk..." value="<?= $this->input->get('q') ?>">
                </div>

                <!-- Quick Master Access -->
                <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                    <button type="button" class="btn btn-white border-end" onclick="$('#modalkategori').modal('show')"
                        title="Konfigurasi Kategori (Step 0)">
                        <i class="bi bi-tags text-warning me-1"></i> <span class="small fw-700">Kategori</span>
                    </button>
                    <button type="button" class="btn btn-white border-end"
                        onclick="$('#modalsubkategori').modal('show')">
                        <i class="bi bi-layers text-success me-1"></i> <span class="small fw-700">Sub</span>
                    </button>
                    <a href="<?= base_url('warehouse/materials') ?>" class="btn btn-white">
                        <i class="bi bi-box text-primary me-1"></i> <span class="small fw-700">Bahan</span>
                    </a>
                </div>

                <div class="vr mx-2 opacity-10"></div>

                <select class="form-select" id="filterKategori" style="width:180px;">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategori as $kat): ?>
                        <option value="<?= $kat->kategori_id ?>" <?= $this->input->get('kategori') == $kat->kategori_id ? 'selected' : '' ?>>
                            <?= $kat->kategori_nama ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select class="form-select" id="filterStatus" style="width:140px;">
                    <option value="">Semua Status</option>
                    <option value="complete" <?= $this->input->get('status') == 'complete' ? 'selected' : '' ?>>Ready
                    </option>
                    <option value="partial" <?= $this->input->get('status') == 'partial' ? 'selected' : '' ?>>Partial
                    </option>
                    <option value="empty" <?= $this->input->get('status') == 'empty' ? 'selected' : '' ?>>Empty</option>
                    <option value="no_bom" <?= $this->input->get('status') == 'no_bom' ? 'selected' : '' ?>>Belum Punya BOM
                    </option>
                    <option value="no_routing" <?= $this->input->get('status') == 'no_routing' ? 'selected' : '' ?>>Belum
                        Punya Routing</option>
                </select>
                <select class="form-select" id="filterStartsWith" style="width:140px;">
                    <option value="">Semua Huruf</option>
                    <?php foreach (($alphabet_filters ?? []) as $letter): ?>
                        <option value="<?= $letter ?>" <?= $this->input->get('starts_with') == $letter ? 'selected' : '' ?>>
                            <?= $letter ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select class="form-select" id="filterSort" style="width:120px;">
                    <option value="asc" <?= $this->input->get('sort', TRUE) !== 'desc' ? 'selected' : '' ?>>A-Z</option>
                    <option value="desc" <?= $this->input->get('sort', TRUE) === 'desc' ? 'selected' : '' ?>>Z-A</option>
                </select>
                <select class="form-select" id="pageSize" style="width:110px;">
                    <?php foreach ([20, 50, 100, 200] as $pageSizeOption): ?>
                        <option value="<?= $pageSizeOption ?>" <?= (int) $per_page === $pageSizeOption ? 'selected' : '' ?>>
                            <?= $pageSizeOption ?>/hal
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-light border" onclick="resetFilters()" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
            <div class="text-end">
                <?php if ($total_products > 0): ?>
                    <span class="badge bg-light text-dark border px-3 py-2">
                        <?= $offset + 1 ?>–<?= min($offset + $per_page, $total_products) ?> / <?= $total_products ?> Produk
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="alphabet-filter-bar">
            <?php
            $current_starts_with = $this->input->get('starts_with');
            ?>
            <button type="button" class="btn alphabet-filter-btn <?= empty($current_starts_with) ? 'active' : '' ?>"
                data-starts-with="">Semua</button>
            <?php foreach (($alphabet_filters ?? []) as $letter): ?>
                <button type="button" class="btn alphabet-filter-btn <?= $current_starts_with == $letter ? 'active' : '' ?>"
                    data-starts-with="<?= $letter ?>"><?= $letter ?></button>
            <?php endforeach; ?>
        </div>
        <!-- Pagination Top -->
        <?php if ($total_pages > 1): ?>
            <?php
            $pagination_base_params = $current_filters ?? ['per_page' => $per_page];
            $buildPageUrl = function ($pageNumber) use ($pagination_base_params) {
                $params = $pagination_base_params;
                $params['page'] = $pageNumber;
                $params = array_filter($params, function ($value) {
                    return $value !== null && $value !== '';
                });
                return base_url('manufaktur?' . http_build_query($params));
            };
            ?>
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div class="text-muted small fw-500">
                    Menampilkan <span
                        class="fw-bold text-dark"><?= $offset + 1 ?>–<?= min($offset + $per_page, $total_products) ?></span>
                    dari <span class="fw-bold text-dark"><?= $total_products ?></span> produk
                </div>
                <nav aria-label="Product pagination top">
                    <ul class="pagination pagination-sm mb-0">
                        <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link border-0 rounded-circle mx-1 text-secondary"
                                    href="<?= $buildPageUrl($current_page - 1) ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link border-0 rounded-circle mx-1 text-secondary"
                                    href="<?= $buildPageUrl(1) ?>">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled"><span class="page-link border-0">…</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                <a class="page-link border-0 rounded-circle mx-1 <?= $i == $current_page ? 'shadow-sm' : 'text-secondary' ?>"
                                    href="<?= $buildPageUrl($i) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <li class="page-item disabled"><span class="page-link border-0">…</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link border-0 rounded-circle mx-1 text-secondary"
                                    href="<?= $buildPageUrl($total_pages) ?>"><?= $total_pages ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link border-0 rounded-circle mx-1 text-secondary"
                                    href="<?= $buildPageUrl($current_page + 1) ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover mb-0" id="productTable">
                <thead>
                    <?php
                    $current_orderby = $this->input->get('orderby') ?: 'product_nama';
                    $current_sort = $this->input->get('sort') ?: 'asc';
                    $next_sort = $current_sort == 'asc' ? 'desc' : 'asc';

                    function get_sort_icon($field, $current_orderby, $current_sort)
                    {
                        if ($current_orderby != $field)
                            return '<i class="bi bi-arrow-down-up sort-icon"></i>';
                        return $current_sort == 'asc' ? '<i class="bi bi-sort-alpha-down sort-icon"></i>' : '<i class="bi bi-sort-alpha-up-alt sort-icon"></i>';
                    }
                    ?>
                    <tr>
                        <th width="4%"
                            class="text-center sortable <?= $current_orderby == 'product_id' ? 'active' : '' ?>"
                            onclick="sortBy('product_id', '<?= $current_orderby == 'product_id' ? $next_sort : 'asc' ?>')">
                            ID <?= get_sort_icon('product_id', $current_orderby, $current_sort) ?>
                        </th>
                        <th width="22%" class="sortable <?= $current_orderby == 'product_nama' ? 'active' : '' ?>"
                            onclick="sortBy('product_nama', '<?= $current_orderby == 'product_nama' ? $next_sort : 'asc' ?>')">
                            Produk &amp; Status <?= get_sort_icon('product_nama', $current_orderby, $current_sort) ?>
                        </th>
                        <th width="18%">Klasifikasi</th>
                        <th width="16%">Spesifikasi</th>
                        <th width="12%">Metode Jual</th>
                        <th width="15%" class="text-center">Product Setup</th>
                        <?php if ($this->session->userdata('level') == '1'): ?>
                            <th width="8%" class="text-center">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $start_no = $offset + 1;
                    if (empty($tampilData)) {
                        echo '<tr><td colspan="10" class="text-center py-5 text-muted">Belum ada data produk tersedia</td></tr>';
                    } else {
                        foreach ($tampilData as $dt):
                            $jml_detail = $this->M_master->jml_detail($dt->product_id)->num_rows();
                            $jml_routing = $this->M_master->jml_routing($dt->product_id)->num_rows();
                            $jml_bom = $this->M_master->jml_bom($dt->product_id)->num_rows();
                            $is_complete = ($jml_detail > 0 && $jml_bom > 0 && $jml_routing > 0);
                            $status_str = $is_complete ? 'complete' : (($jml_detail > 0 || $jml_bom > 0 || $jml_routing > 0) ? 'incomplete' : 'empty');
                            ?>
                            <tr data-product="<?= strtolower($dt->product_nama) ?>"
                                data-kategori="<?= strtolower(isset($dt->kategori_nama) ? $dt->kategori_nama : 'N/A') ?>"
                                data-status="<?= $status_str ?>">
                                <td class="text-center text-muted fw-bold" style="font-size: 0.8rem;">
                                    <div class="mb-1"><?= $start_no++ ?></div>
                                    <?php
                                    // Ambil tanggal dari log adjust jika ada
                                    if (preg_match('/\[ADJUST HARGA (.*?): (.*?)\]/', $dt->product_deskripsi ?? '', $matches)):
                                        $last_date = $matches[1];
                                        ?>
                                        <div class="text-primary" style="font-size: 0.6rem; font-weight: 500;"
                                            title="Terakhir Update Harga">
                                            <i class="bi bi-clock-history me-1"></i><?= $last_date ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <a href="<?= base_url('manufaktur/detail/' . $dt->product_id) ?>"
                                            class="fw-bold text-slate-800 text-decoration-none hover-primary"
                                            style="font-size: 0.95rem;">
                                            <?= $dt->product_nama ?>
                                        </a>
                                        <?php
                                        // 1. Resolve setup status colors and text
                                        $setup_bg = '#fee2e2';
                                        $setup_color = '#b91c1c';
                                        $setup_text = 'EMPTY';
                                        if ($is_complete) {
                                            $setup_bg = '#dcfce7';
                                            $setup_color = '#15803d';
                                            $setup_text = 'READY';
                                        } elseif ($jml_detail > 0 || $jml_bom > 0 || $jml_routing > 0) {
                                            $setup_bg = '#fef3c7';
                                            $setup_color = '#b45309';
                                            $setup_text = 'PARTIAL';
                                        }

                                        // 2. Resolve metadata status colors and text
                                        $meta_stats = $this->db->query("
                                            SELECT 
                                                COUNT(*) as total_variasi,
                                                SUM(CASE WHEN metadata IS NOT NULL AND metadata != '' AND metadata != '[]' THEN 1 ELSE 0 END) as dengan_metadata
                                            FROM tbl_harga_product 
                                            WHERE product_id = ?
                                        ", [$dt->product_id])->row();

                                        $total_v = $meta_stats->total_variasi ?? 0;
                                        $with_m = $meta_stats->dengan_metadata ?? 0;

                                        $meta_bg = '#f1f5f9';
                                        $meta_color = '#64748b';
                                        $meta_text = 'NO PRICE';

                                        if ($total_v > 0) {
                                            if ($with_m == $total_v) {
                                                $meta_bg = '#e0f2fe';
                                                $meta_color = '#0369a1';
                                                $meta_text = 'VAR READY';
                                            } elseif ($with_m > 0) {
                                                $meta_bg = '#fffbeb';
                                                $meta_color = '#d97706';
                                                $meta_text = 'VAR ' . $with_m . '/' . $total_v;
                                            } else {
                                                $meta_bg = '#fef2f2';
                                                $meta_color = '#dc2626';
                                                $meta_text = 'VAR EMPTY';
                                            }
                                        }
                                        ?>
                                        <div class="d-inline-flex align-items-center rounded overflow-hidden border"
                                            style="font-size: 0.62rem; line-height: 1; font-weight: 800; border-color: rgba(0,0,0,0.08) !important; height: 20px;">
                                            <span
                                                style="background: <?= $setup_bg ?>; color: <?= $setup_color ?>; padding: 2px 7px; height: 100%; display: flex; align-items: center; letter-spacing: 0.3px;">
                                                <?= $setup_text ?>
                                            </span>
                                            <span
                                                style="background: <?= $meta_bg ?>; color: <?= $meta_color ?>; padding: 2px 7px; height: 100%; display: flex; align-items: center; border-left: 1px solid rgba(0,0,0,0.08); letter-spacing: 0.3px;">
                                                <?= $meta_text ?>
                                            </span>
                                        </div>

                                        <?php if (strpos($dt->product_deskripsi ?? '', '[ADJUST HARGA') !== false): ?>
                                            <span class="status-badge"
                                                style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;"
                                                title="Harga telah disesuaikan secara massal">
                                                <i class="fas fa-tag me-1" style="font-size: 0.6rem;"></i> PRICE ADJ
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($dt->min_price > 0 || $dt->calculated_hpp > 0): ?>
                                        <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                            <div
                                                style="font-size: 0.72rem; color: #64748b; display: flex; align-items: center; gap: 4px; background: #f1f5f9; padding: 2px 8px; border-radius: 4px;">
                                                <i class="bi bi-tag-fill" style="font-size: 0.65rem;"></i>
                                                <span style="font-weight: 700;">
                                                    <?php if ($dt->min_price > 0): ?>
                                                        Rp <?= number_format($dt->min_price, 0, ',', '.') ?> -
                                                        <?= number_format($dt->max_price, 0, ',', '.') ?>
                                                    <?php else: ?>
                                                        Price N/A
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                            <div
                                                style="font-size: 0.72rem; color: #ef4444; display: flex; align-items: center; gap: 4px; background: #fef2f2; padding: 2px 8px; border-radius: 4px; border: 1px solid #fee2e2;">
                                                <i class="bi bi-calculator-fill" style="font-size: 0.65rem;"></i>
                                                <span style="font-weight: 800;">HPP: Rp
                                                    <?= number_format($dt->calculated_hpp, 0, ',', '.') ?></span>
                                            </div>

                                            <?php
                                            if (preg_match('/\[ADJUST HARGA (.*?): (.*?)\]/', $dt->product_deskripsi ?? '', $matches)):
                                                $adj_val = $matches[2];
                                                $adj_color = (strpos($adj_val, '+') !== false) ? '#059669' : '#dc2626';
                                                ?>
                                                <div
                                                    style="font-size: 0.68rem; color: <?= $adj_color ?>; font-weight: 800; background: white; padding: 1px 6px; border: 1px solid <?= $adj_color ?>40; border-radius: 4px;">
                                                    <i
                                                        class="fas <?= (strpos($adj_val, '+') !== false) ? 'fa-arrow-up' : 'fa-arrow-down' ?> me-1"></i>
                                                    <?= $adj_val ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="fw-bold text-slate-900" style="font-size: 0.95rem;">
                                        <?= isset($dt->kategori_nama) ? $dt->kategori_nama : '-' ?>
                                    </div>
                                    <div class="text-slate-600 fw-medium" style="font-size: 0.85rem;">
                                        <?= isset($dt->subkategori_nama) ? $dt->subkategori_nama : '-' ?>
                                    </div>
                                </td>
                                <td>
                                    <!-- Jenis & Sisi Info -->
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <div
                                            style="font-size: 0.62rem; color: #64748b; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; display: flex; align-items: center;">
                                            <?= isset($dt->tipe_nama) ? $dt->tipe_nama : '-' ?>
                                        </div>
                                        <?php if (isset($dt->side_name) && $dt->side_name != '-'): ?>
                                            <div class="badge rounded-pill"
                                                style="background: #f0f7ff; color: #0284c7; border: 1px solid #e0f2fe; font-size: 0.65rem; padding: 0.15rem 0.5rem; font-weight: 800;">
                                                <i class="bi bi-layers-fill me-1 opacity-50"></i><?= $dt->side_name ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Satuan & Schema Spesifikasi -->
                                    <div class="fw-bold text-slate-700 mb-1" style="font-size: 0.85rem;">
                                        <i
                                            class="bi bi-box-seam me-1 text-muted"></i><?= isset($dt->satuan_nama) ? $dt->satuan_nama : '-' ?>
                                    </div>

                                    <?php
                                    $active_schema = !empty($dt->product_schema) ? $dt->product_schema :
                                        (!empty($dt->subkategori_schema) ? $dt->subkategori_schema :
                                            (!empty($dt->kategori_schema) ? $dt->kategori_schema : null));

                                    if ($active_schema) {
                                        $schema_arr = json_decode($active_schema, true);
                                        if (is_array($schema_arr)) {
                                            $schema_limit = 0;
                                            foreach ($schema_arr as $field) {
                                                if ($schema_limit >= 3)
                                                    break; // Limit up to 3 specs
                                                if (isset($field['type']) && in_array($field['type'], ['select', 'radio']) && !empty($field['options'])) {
                                                    $clean_options = array_filter(array_values($field['options']), function ($val) {
                                                        return trim($val) !== '' && stripos($val, 'Tidak Dipilih') === false && stripos($val, 'Pilih') === false;
                                                    });
                                                    if (!empty($clean_options)) {
                                                        $label = !empty($field['label']) ? $field['label'] : 'Pilihan';
                                                        $options_str = implode(', ', $clean_options);
                                                        echo '<div class="text-truncate" style="max-width: 150px; font-size: 0.72rem; line-height: 1.3;" title="' . htmlspecialchars($label . ': ' . $options_str) . '">';
                                                        echo '<span class="fw-bold text-slate-600">' . htmlspecialchars($label) . ':</span> <span class="text-muted">' . htmlspecialchars($options_str) . '</span>';
                                                        echo '</div>';
                                                        $schema_limit++;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    ?>

                                    <?php if (!empty($dt->product_deskripsi)): ?>
                                        <div class="text-muted mt-1 text-truncate" style="max-width: 150px; font-size: 0.75rem;"
                                            title="<?= htmlspecialchars($dt->product_deskripsi) ?>">
                                            <i
                                                class="bi bi-info-circle me-1 opacity-50"></i><?= htmlspecialchars($dt->product_deskripsi) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge rounded-pill"
                                        style="background: #eef2ff; color: #6366f1; border: 1px solid #e0e7ff; font-size: 0.75rem; padding: 0.4rem 0.8rem; font-weight: 800; letter-spacing: 0.3px;">
                                        <i
                                            class="bi bi-shop me-1 opacity-50"></i><?= isset($dt->metode_nama) ? $dt->metode_nama : '-' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="setup-container">


                                        <?php
                                        // Cek ketersediaan form builder dari hirarki
                                        $has_form_schema = !empty($dt->product_schema) || !empty($dt->subkategori_schema) || !empty($dt->kategori_schema);
                                        $schema_source = '';
                                        if (!empty($dt->product_schema)) {
                                            $schema_source = 'Kustom (Produk)';
                                        } elseif (!empty($dt->subkategori_schema)) {
                                            $schema_source = 'Bawaan (Sub Kategori)';
                                        } elseif (!empty($dt->kategori_schema)) {
                                            $schema_source = 'Bawaan (Kategori)';
                                        }
                                        $tooltip_text = "Config Form Builder" . ($schema_source ? " - " . $schema_source : " - KOSONG / BELUM DISET");
                                        ?>
                                        <!-- Step 0: Config Form Builder -->
                                        <a href="javascript:void(0)"
                                            onclick="openFormBuilderModal(<?= $dt->product_id ?>, '<?= htmlspecialchars(addslashes($dt->product_nama), ENT_QUOTES) ?>')"
                                            class="feature-indicator indicator-form <?= $has_form_schema ? 'has-data' : '' ?>"
                                            style="color: <?= $has_form_schema ? '#db2777' : '#9ca3af' ?>; border-color: <?= $has_form_schema ? '#fbcfe8' : '#e5e7eb' ?>; background: <?= $has_form_schema ? '#fdf2f8' : '#f9fafb' ?>;"
                                            data-bs-toggle="tooltip" title="<?= $tooltip_text ?>">
                                            <i
                                                class="bi <?= $has_form_schema ? 'bi-input-cursor-text' : 'bi-exclamation-circle' ?>"></i>
                                        </a>

                                        <i class="bi bi-arrow-right mx-1 opacity-25" style="font-size: 0.7rem;"></i>

                                        <!-- Step 2: BOM Config -->
                                        <a href="javascript:void(0)"
                                            onclick="openBomModal(<?= $dt->product_id ?>, '<?= $dt->product_nama ?>')"
                                            class="feature-indicator indicator-bom <?= $jml_bom > 0 ? 'has-data' : '' ?>"
                                            data-bs-toggle="tooltip"
                                            title="Step 2: Setup BOM / Resep Bahan (<?= $jml_bom ?> data)">
                                            <i class="bi bi-gear-wide"></i>
                                            <?php if ($jml_bom > 0): ?>
                                                <span class="count-badge"><?= $jml_bom ?></span>
                                            <?php endif; ?>
                                        </a>

                                        <i class="bi bi-arrow-right mx-1 opacity-25" style="font-size: 0.7rem;"></i>

                                        <!-- Step 3: Routing Config -->
                                        <a href="javascript:void(0)"
                                            onclick="openRoutingModal(<?= $dt->product_id ?>, '<?= $dt->product_nama ?>')"
                                            class="feature-indicator indicator-routing <?= $jml_routing > 0 ? 'has-data' : '' ?>"
                                            data-bs-toggle="tooltip"
                                            title="Step 3: Setup Alur Kerja / Routing (<?= $jml_routing ?> data)">
                                            <i class="bi bi-diagram-3"></i>
                                            <?php if ($jml_routing > 0): ?>
                                                <span class="count-badge"><?= $jml_routing ?></span>
                                            <?php endif; ?>
                                        </a>

                                        <i class="bi bi-arrow-right mx-1 opacity-25" style="font-size: 0.7rem;"></i>

                                        <!-- Step 4: Price Config -->
                                        <a href="javascript:void(0)"
                                            onclick="openPricingModal(<?= $dt->product_id ?>, '<?= addslashes($dt->product_nama) ?>')"
                                            class="feature-indicator indicator-price <?= $jml_detail > 0 ? 'has-data' : '' ?>"
                                            data-bs-toggle="tooltip"
                                            title="Step 4: Matriks Harga Jual (<?= $jml_detail ?> variasi)">
                                            <i class="bi bi-currency-dollar"></i>
                                            <?php if ($jml_detail > 0): ?>
                                                <span class="count-badge">
                                                    <?= $jml_detail ?>
                                                </span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </td>
                                <?php if ($this->session->userdata('level') == '1'): ?>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button type="button" class="btn btn-light btn-action text-primary"
                                                onclick="openEditProductModal(<?= $dt->product_id ?>)" data-bs-toggle="tooltip"
                                                title="Step 1: Identitas Produk (Edit)">
                                                <i class="bi bi-pencil-fill" style="font-size: 0.75rem;"></i>
                                            </button>
                                            <button class="btn btn-light btn-action text-info"
                                                onclick="cloneProduct(<?= $dt->product_id ?>, '<?= addslashes($dt->product_nama) ?>')"
                                                title="Duplicate">
                                                <i class="bi bi-copy" style="font-size: 0.75rem;"></i>
                                            </button>
                                            <button class="btn btn-light btn-action text-danger btn-delete-product"
                                                data-href="<?= base_url('manufaktur/deleteproduct/' . $dt->product_id) ?>"
                                                data-product-name="<?= htmlspecialchars($dt->product_nama) ?>" title="Delete">
                                                <i class="bi bi-trash-fill" style="font-size: 0.75rem;"></i>
                                            </button>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach;
                    } // end else ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <div class="text-muted small fw-500">
                    Menampilkan <span
                        class="fw-bold text-dark"><?= $offset + 1 ?>–<?= min($offset + $per_page, $total_products) ?></span>
                    dari <span class="fw-bold text-dark"><?= $total_products ?></span> produk
                </div>
                <nav aria-label="Product pagination">
                    <ul class="pagination pagination-sm mb-0">
                        <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link border-0 rounded-circle mx-1 text-secondary"
                                    href="<?= $buildPageUrl($current_page - 1) ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link border-0 rounded-circle mx-1 text-secondary"
                                    href="<?= $buildPageUrl(1) ?>">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled"><span class="page-link border-0">…</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                <a class="page-link border-0 rounded-circle mx-1 <?= $i == $current_page ? 'shadow-sm' : 'text-secondary' ?>"
                                    href="<?= $buildPageUrl($i) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <li class="page-item disabled"><span class="page-link border-0">…</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link border-0 rounded-circle mx-1 text-secondary"
                                    href="<?= $buildPageUrl($total_pages) ?>"><?= $total_pages ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link border-0 rounded-circle mx-1 text-secondary"
                                    href="<?= $buildPageUrl($current_page + 1) ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>

    </div><!-- /content-card -->
</div><!-- /container-fluid px-5 -->

<!-- Modal Form Builder -->
<div class="modal fade" id="modalFormBuilder" tabindex="-1" aria-labelledby="modalFormBuilderLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 95vw;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden; height: 90vh;">
            <div class="modal-header border-0 bg-light p-3 pb-2" style="border-radius: 24px 24px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="bg-pink bg-opacity-10 p-2 rounded-3 me-3" style="background: #fdf2f8; color: #db2777;">
                        <i class="bi bi-input-cursor-text fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-slate-900 mb-0" id="modalFormBuilderLabel">Konfigurasi Form
                            Builder</h5>
                        <p class="text-muted small mb-0">Setup form kustom untuk: <span class="fw-bold text-pink"
                                id="formBuilderProductNameHeader" style="color: #db2777;">-</span></p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 m-0 bg-light" style="overflow: hidden;">
                <iframe id="iframeFormBuilder" src=""
                    style="width: 100%; height: 100%; border: none; display: block;"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pricing -->
<div class="modal fade" id="modalPricing" tabindex="-1" aria-labelledby="modalPricingLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 bg-light p-4" style="border-radius: 24px 24px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-currency-dollar text-primary fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-slate-900" id="modalPricingLabel">Matriks Skala Harga</h5>
                        <p class="text-muted small mb-0">Atur strategi harga untuk: <span class="fw-bold text-primary"
                                id="pricingProductName">-</span></p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-0" id="pricingModalBody">
                <!-- Content loaded via AJAX from get_pricing_partial -->
            </div>
            <input type="hidden" id="pricingProductIdInput">
        </div>
    </div>
</div>

<!-- Modal Routing -->
<div class="modal fade" id="modalRouting" tabindex="-1" aria-labelledby="modalRoutingLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 bg-light p-4" style="border-radius: 24px 24px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-diagram-3 text-success fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-slate-900" id="modalRoutingLabel">Routing & Alur Kerja</h5>
                        <p class="text-muted small mb-0">Setup tenaga kerja untuk: <span class="fw-bold text-success"
                                id="routingProductNameHeader">-</span></p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <!-- Step Tracker -->
                <div class="mini-sop-tracker mb-4 mt-3">
                    <div class="mini-step completed">
                        <div class="step-num">0</div>
                        <div class="step-text">Config Form</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="mini-step completed">
                        <div class="step-num">1</div>
                        <div class="step-text">Identitas</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="mini-step completed">
                        <div class="step-num">2</div>
                        <div class="step-text">Resep BOM</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="mini-step active">
                        <div class="step-num">3</div>
                        <div class="step-text">Alur Kerja</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="mini-step">
                        <div class="step-num">4</div>
                        <div class="step-text">Matriks Harga</div>
                    </div>
                </div>

                <!-- Stats Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-list-task me-2 text-success"></i>Daftar Proses
                        Produksi</h6>
                    <div class="px-3 py-1 rounded-pill bg-success bg-opacity-10 text-success small fw-bold">
                        Total: <span id="routingTotalSteps">0</span> Langkah
                    </div>
                </div>

                <!-- Routing Data Table -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="bg-success p-1 rounded-2"
                                style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-list-ul text-white small"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-0">List Alur Produksi Aktif</h6>
                        </div>
                        <div class="table-responsive rounded-3 overflow-hidden border">
                            <table class="table table-hover mb-0">
                                <thead style="background: #f8fafc; border-bottom: 2px solid #059669;">
                                    <tr class="text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                        <th width="8%" class="py-3 px-4">Urutan</th>
                                        <th width="30%" class="py-3">Proses / Langkah Kerja</th>
                                        <th width="20%" class="py-3 text-center">Work Center</th>
                                        <th width="15%" class="py-3 text-center">Waktu (Min)</th>
                                        <th width="15%" class="py-3 text-end pe-4">Upah / Unit</th>
                                        <th width="12%" class="py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="routingTableBody" style="font-size: 0.85rem;">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Add Routing Form -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm"
                            style="background: #f0fdf4; border: 1px dashed #059669 !important; border-radius: 16px;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <i class="bi bi-plus-circle-fill text-success fs-5"></i>
                                    <h6 class="fw-bold text-dark mb-0">Tambah Step Baru ke Alur</h6>
                                </div>
                                <form id="addRoutingForm">
                                    <input type="hidden" id="routingProductIdInput" name="product_id">
                                    <div class="row g-3">
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold text-muted" style="font-size: 0.8rem;">SEQ.
                                                <i class="bi bi-info-circle ms-1" data-bs-toggle="tooltip"
                                                    title="Urutan angka (10, 20, dst)"></i></label>
                                            <input type="number" class="form-control form-control-premium"
                                                id="routing_urutan" name="routing_urutan" min="1" placeholder="10"
                                                required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold text-muted" style="font-size: 0.8rem;">NAMA
                                                PROSES <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-premium"
                                                id="routing_name" name="routing_name"
                                                placeholder="Contoh: Cetak / Laminasi" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold text-muted" style="font-size: 0.8rem;">DEPT
                                                / OPERATOR</label>
                                            <select class="form-select select2-operator" id="operator_id"
                                                name="operator_id" required></select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold text-muted" style="font-size: 0.8rem;">TIPE
                                                TAHAPAN</label>
                                            <select class="form-select" id="is_finishing" name="is_finishing" required>
                                                <option value="0">Cetak</option>
                                                <option value="1">Finishing</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label fw-bold text-muted" style="font-size: 0.8rem;"
                                                title="Estimasi Waktu (Menit)">WAKTU</label>
                                            <input type="number" class="form-control form-control-premium text-center"
                                                name="est_time" value="0">
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label fw-bold text-muted" style="font-size: 0.8rem;"
                                                title="Upah Tukang (Rp)">UPAH</label>
                                            <input type="number" class="form-control form-control-premium text-end"
                                                name="labor_cost" value="0">
                                        </div>
                                        <div class="col-md-9 pt-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-link-45deg fs-5 text-muted"></i>
                                                <select class="form-select select2-bom border-0 bg-transparent p-0"
                                                    id="bom_line_id" name="bom_line_id" required
                                                    style="font-size: 0.8rem; font-weight: 600;">
                                                    <option value="99999">-- Umum (Tanpa Varian Material Khusus) --
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pt-2 text-end">
                                            <button type="submit"
                                                class="btn btn-success w-100 py-2 rounded-pill shadow-sm">
                                                <i class="bi bi-plus-lg me-2"></i>TAMBAH STEP
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Routing -->
<div class="modal fade" id="modalEditRouting" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
            <div class="modal-header border-0 bg-light p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-diagram-3-fill text-warning fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-slate-900">Ubah Langkah Kerja</h5>
                        <p class="text-muted small mb-0">Sesuaikan urutan dan biaya operasional</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="editRoutingForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="routing_id" id="edit_routing_id">
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-primary">TERAPKAN KE VARIASI</label>
                        <select class="form-select shadow-none" name="harga_id" id="edit_harga_id_routing">
                            <option value="">-- Semua Variasi (Global) --</option>
                        </select>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">SEQUENCE</label>
                            <input type="number" name="routing_urutan" id="edit_routing_urutan"
                                class="form-control bg-light fw-bold" required>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-bold small text-muted">NAMA OPERASI / PROSES</label>
                            <input type="text" name="routing_name" id="edit_routing_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">WORK CENTER / DEPT</label>
                            <select name="operator_id" id="edit_operator_id" class="form-select" required></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">LINK MATERIAL BOM</label>
                            <select name="bom_line_id" id="edit_bom_line_id_routing" class="form-select"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">EST. WAKTU (MENIT)</label>
                            <input type="number" name="est_time" id="edit_est_time" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">BIAYA KERJA (RP)</label>
                            <input type="text" name="labor_cost" id="edit_labor_cost" class="form-control rupiah-input">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 text-end">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white rounded-pill px-4 fw-bold shadow-sm">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal BOM -->
<div class="modal fade" id="modalBom" tabindex="-1" aria-labelledby="modalBomLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 bg-light p-4" style="border-radius: 24px 24px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-gear-wide-connected text-warning fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-slate-900" id="modalBomLabel">BOM & Resep Bahan</h5>
                        <p class="text-muted small mb-0">Setup material untuk: <span class="fw-bold text-warning"
                                id="bomProductNameHeader">-</span></p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <!-- Step Tracker -->
                <div class="mini-sop-tracker mb-4 mt-3">
                    <div class="mini-step completed">
                        <div class="step-num">0</div>
                        <div class="step-text">Config Form</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="mini-step completed">
                        <div class="step-num">1</div>
                        <div class="step-text">Identitas</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="mini-step active">
                        <div class="step-num">2</div>
                        <div class="step-text">Resep BOM</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="mini-step">
                        <div class="step-num">3</div>
                        <div class="step-text">Alur Kerja</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="mini-step">
                        <div class="step-num">4</div>
                        <div class="step-text">Matriks Harga</div>
                    </div>
                </div>

                <!-- Stats Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-flask me-2 text-warning"></i>Daftar Bahan Baku /
                        Komponen</h6>
                </div>

                <!-- BOM Data Table -->
                <div class="table-responsive rounded-3 overflow-hidden border mb-4">
                    <table class="table table-hover mb-0" id="bomTable">
                        <thead style="background: #f8fafc; border-bottom: 2px solid #1e293b;">
                            <tr class="text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <th width="5%" class="py-3 px-4">No</th>
                                <th width="35%" class="py-3">Bahan Baku / Komponen (Varian)</th>
                                <th width="12%" class="py-3 text-center"
                                    title="Kebutuhan murni bahan per 1 unit produk">Kebutuhan Bersih</th>
                                <th width="10%" class="py-3 text-center"
                                    title="Persentase cadangan/toleransi kerusakan">Limbah %</th>
                                <th width="15%" class="py-3 text-center"
                                    title="Total bahan yang dibutuhkan setelah limbah">Total Kebutuhan</th>
                                <th width="15%" class="py-3">Dimensi (P x L)</th>
                                <th width="8%" class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bomTableBody" style="font-size: 0.85rem;">
                            <!-- Dynamic content -->
                        </tbody>
                    </table>
                </div>

                <!-- Add BOM Form -->
                <div class="card border-0 shadow-sm"
                    style="background: #f8fafc; border: 1px dashed #1e293b !important; border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <i class="bi bi-patch-plus-fill text-primary fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0">Tambah Bahan Baku ke Resep (BOM)</h6>
                        </div>
                        <form id="addBomForm">
                            <input type="hidden" id="bomProductIdInput" name="product_id">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-primary mb-1"
                                        style="font-size: 0.82rem;">TERAPKAN KE VARIASI</label>
                                    <select class="form-select shadow-none form-control-premium" id="bomVariasiSelect" name="harga_id">
                                        <option value="">-- Semua Variasi (Global) --</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="bahan_id" class="form-label fw-bold text-muted"
                                        style="font-size: 0.82rem;">MATERIAL / VARIAN <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2-bahan" id="bahan_id" name="bahan_id"
                                        required></select>
                                </div>
                                <div class="col-md-2">
                                    <label for="qty" class="form-label fw-bold text-muted" style="font-size: 0.82rem;"
                                        title="Kebutuhan bahan baku untuk membuat 1 pcs/unit produk jadi">KEBUTUHAN PER
                                        PCS <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control form-control-premium text-center" id="qty"
                                        name="qty" step="0.0001" min="0" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="scrap_factor" class="form-label fw-bold text-muted"
                                        style="font-size: 0.82rem;"
                                        title="Persentase toleransi bahan rusak/terbuang">TOLERANSI LIMBAH %</label>
                                    <input type="number" class="form-control form-control-premium text-center"
                                        id="scrap_factor" name="scrap_factor" value="0" min="0" max="100">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold text-muted" style="font-size: 0.82rem;"
                                        title="Metode perhitungan konsumsi bahan baku">TIPE KALKULASI</label>
                                    <select class="form-select form-control-premium text-center" name="calc_type"
                                        id="calc_type" required style="font-weight: 500;">
                                        <option value="fixed" selected>1. Fixed</option>
                                        <option value="area">2. Area (P x L)</option>
                                        <option value="length">3. Length (P)</option>
                                        <option value="weight">4. Weight</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-dark w-100 py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-check-circle me-2"></i>SIMPAN
                                    </button>
                                </div>
                                <div class="col-12 mt-3">
                                    <textarea class="form-control form-control-premium border-0" name="keterangan"
                                        rows="1" placeholder="Catatan internal untuk operator produksi (opsional)..."
                                        style="font-size: 0.8rem; background: rgba(255,255,255,0.5);"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Edit Product -->
<div class="modal fade" id="modalEditProduct" tabindex="-1" aria-labelledby="modalEditProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editProductForm" class="needs-validation" novalidate>
            <input type="hidden" name="product_id" id="edit_product_id">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalEditProductLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit Product: <span id="edit_product_name_title"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Kategori & Sub Kategori -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_id" id="edit_kategori_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k->kategori_id ?>"><?= $k->kategori_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sub Kategori <span class="text-danger">*</span></label>
                            <select name="subkategori_id" id="edit_subkategori_id" class="form-select subkategori"
                                required>
                                <option value="">-- Pilih Sub Kategori --</option>
                                <?php foreach ($subkategori as $k): ?>
                                    <option value="<?= $k->subkategori_id ?>"><?= $k->subkategori_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Nama Produk, Tipe, Satuan -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="product_nama" id="edit_product_nama" class="form-control" required
                                placeholder="Masukkan nama product">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Jenis <span class="text-danger">*</span></label>
                            <select name="tipe_id" id="edit_tipe_id" class="form-select" required>
                                <option value="">-- Pilih Jenis --</option>
                                <?php foreach ($tipe as $k): ?>
                                    <option value="<?= $k->tipe_id ?>"><?= $k->tipe_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Satuan <span class="text-danger">*</span></label>
                            <select name="satuan_id" id="edit_satuan_id" class="form-select" required>
                                <option value="">-- Pilih Satuan --</option>
                                <?php foreach ($satuan as $k): ?>
                                    <option value="<?= $k->satuan_id ?>"><?= $k->satuan_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Pilihan Sisi & Metode -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilihan Sisi</label>
                            <select name="side_id" id="edit_side_id" class="form-select">
                                <option value="">-- Pilihan Sisi --</option>
                                <?php foreach ($sisi as $k): ?>
                                    <option value="<?= $k->side_id ?>"><?= $k->side_name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilihan Metode Penjualan</label>
                            <select name="metode_id" id="edit_metode_id" class="form-select">
                                <option value="">-- Pilihan Metode Penjualan --</option>
                                <?php foreach ($metode as $k): ?>
                                    <option value="<?= $k->metode_id ?>"><?= $k->metode_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Product</label>
                        <textarea name="product_deskripsi" id="edit_product_deskripsi" class="form-control" rows="4"
                            placeholder="Masukkan deskripsi lengkap product..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Perbarui Produk
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Input Data -->
<div class="modal fade" id="modalInputData" tabindex="-1" aria-labelledby="modalInputDataLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="post" action="<?= base_url('manufaktur/actionAdd') ?>" class="needs-validation" novalidate>
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalInputDataLabel">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Product Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">


                    <!-- Kategori & Sub Kategori -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_id" id="kategori_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k->kategori_id ?>"><?= $k->kategori_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sub Kategori <span class="text-danger">*</span></label>
                            <select name="subkategori_id" class="form-select subkategori" required>
                                <option value="">-- Pilih Sub Kategori --</option>
                                <?php foreach ($subkategori as $k): ?>
                                    <option value="<?= $k->subkategori_id ?>"><?= $k->subkategori_nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Nama Produk, Tipe, Satuan -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="product_nama" class="form-control" required
                                placeholder="Masukkan nama product">

                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Jenis <span class="text-danger">*</span></label>
                            <select name="tipe_id" class="form-select" required>
                                <option value="">-- Pilih Jenis --</option>
                                <?php foreach ($tipe as $k): ?>
                                    <option value="<?= $k->tipe_id ?>"><?= $k->tipe_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Satuan <span class="text-danger">*</span></label>
                            <select name="satuan_id" class="form-select" required>
                                <option value="">-- Pilih Satuan --</option>
                                <?php foreach ($satuan as $k): ?>
                                    <option value="<?= $k->satuan_id ?>" <?= isset($getproduct['satuan_id']) && $k->satuan_id == $getproduct['satuan_id'] ? 'selected' : '' ?>>
                                        <?= $k->satuan_nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Bahan Baku -->


                    <!-- Pilihan Sisi -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilihan Sisi</label>
                            <select name="side_id" class="form-select">
                                <option value="">-- Pilihan Sisi --</option>
                                <?php foreach ($sisi as $k): ?>
                                    <option value="<?= $k->side_id ?>" <?= isset($getproduct['side_id']) && $k->side_id == $getproduct['side_id'] ? 'selected' : '' ?>>
                                        <?= $k->side_name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilihan Metode Penjualan</label>
                            <select name="metode_id" class="form-select">
                                <option value="">-- Pilihan Metode Penjualan --</option>
                                <?php foreach ($metode as $k): ?>
                                    <option value="<?= $k->metode_id ?>" <?= isset($getproduct['metode_id']) && $k->metode_id == $getproduct['metode_id'] ? 'selected' : '' ?>>
                                        <?= $k->metode_nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Product</label>
                        <textarea name="product_deskripsi" class="form-control product-description" rows="4"
                            placeholder="Masukkan deskripsi lengkap product..."><?= htmlspecialchars($getproduct['product_deskripsi'] ?? '') ?></textarea>
                        <div class="form-text">Deskripsi detail tentang product (spesifikasi, kegunaan, dll)
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Simpan Product
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Bulk Setup -->
<!-- Modal Bulk Price Update -->
<div class="modal fade" id="modalBulkPriceUpdate" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-coins me-2"></i>Bulk Price Adjuster
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning border-0 shadow-sm mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Perhatian:</strong> Fitur ini akan mengubah harga pada banyak produk sekaligus. Pastikan
                    filter sudah benar.
                </div>

                <form id="bulkPriceForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">1. Pilih Kategori Target</label>
                        <select class="form-select select2-bulk-price" name="category_id" id="bulk_price_category"
                            required>
                            <option value="all">-- Semua Kategori --</option>
                            <?php foreach ($kategori as $kat): ?>
                                <option value="<?= $kat->kategori_id ?>"><?= $kat->kategori_nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">2. Metode Penyesuaian</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <div
                                    class="form-check card p-3 border-light-subtle shadow-sm hover-scale transition-all cursor-pointer">
                                    <input class="form-check-input" type="radio" name="adj_method" id="method_pct"
                                        value="percent" checked>
                                    <label class="form-check-label fw-bold d-block" for="method_pct">
                                        <i class="fas fa-percent me-1 text-primary"></i> Persentase
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div
                                    class="form-check card p-3 border-light-subtle shadow-sm hover-scale transition-all cursor-pointer">
                                    <input class="form-check-input" type="radio" name="adj_method" id="method_flat"
                                        value="flat">
                                    <label class="form-check-label fw-bold d-block" for="method_flat">
                                        <i class="fas fa-money-bill-wave me-1 text-success"></i> Nominal Flat
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">3. Nilai Kenaikan (Gunakan tanda minus untuk turun)</label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white border-end-0" id="adj_symbol">%</span>
                            <input type="number" class="form-control border-start-0 ps-0 fw-bold text-primary"
                                name="adj_value" placeholder="Contoh: 10" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">4. Target Perubahan</label>
                        <div class="d-flex gap-3 p-3 bg-light rounded border border-light-subtle shadow-sm">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="targets[]" value="harga_1"
                                    id="target_jual" checked>
                                <label class="form-check-label fw-bold" for="target_jual">Harga Jual</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="targets[]" value="hpp"
                                    id="target_hpp">
                                <label class="form-check-label fw-bold" for="target_hpp">HPP (Modal)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-0 p-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"
                    onclick="submitBulkPriceUpdate()">
                    <i class="fas fa-save me-1"></i> Update Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBulkSetup" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-layers-half me-2"></i>Bulk Apply Template Manufaktur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Gunakan fitur ini untuk menerapkan konfigurasi <strong>BOM (Material)</strong> atau <strong>Routing
                        (Langkah Kerja)</strong> dari satu produk template ke banyak produk target sekaligus.
                </div>

                <form id="bulkSetupForm">
                    <div class="row g-4">
                        <!-- Step 1: Select Template -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold"><span class="badge bg-dark me-2">1</span>Pilih Produk
                                Template (Sumber)</label>
                            <select class="form-select select2-bulk-template" name="template_id" id="bulk_template_id"
                                required>
                                <option value="">-- Cari Produk Template --</option>
                            </select>
                            <div class="form-text">Data BOM & Routing dari produk ini akan disalin.</div>
                        </div>

                        <!-- Step 2: Select Targets -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold"><span class="badge bg-dark me-2">2</span>Pilih Target
                                Produk</label>
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="small text-muted fw-bold">FILTER SUBKATEGORI</label>
                                            <select class="form-select" id="bulk_target_subkategori">
                                                <option value="">-- Semua Subkategori --</option>
                                                <?php foreach ($subkategori as $sk): ?>
                                                    <option value="<?= $sk->subkategori_id ?>"><?= $sk->subkategori_nama ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="small text-muted fw-bold">PRODUK TARGET</label>
                                            <select class="form-select select2-bulk-targets" name="target_ids[]"
                                                id="bulk_target_ids" multiple required>
                                            </select>
                                            <div class="mt-2 d-flex gap-2">
                                                <button type="button" class="btn btn-xs btn-outline-primary"
                                                    onclick="bulkSelectAllTargets()">Pilih Semua di Subkategori
                                                    Ini</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary"
                                                    onclick="bulkClearTargets()">Hapus Semua</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Options -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold"><span class="badge bg-dark me-2">3</span>Opsi
                                Penyalinan</label>
                            <div class="d-flex gap-4 p-3 bg-light rounded shadow-sm border">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="options[]" value="bom"
                                        id="opt_bom" checked>
                                    <label class="form-check-label fw-bold" for="opt_bom">Salin BOM (Bahan Baku)</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="options[]" value="routing"
                                        id="opt_routing" checked>
                                    <label class="form-check-label fw-bold" for="opt_routing">Salin Routing (Langkah
                                        Kerja)</label>
                                </div>
                            </div>
                            <div class="mt-2 text-danger small">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                Perhatian: Data BOM/Routing yang sudah ada pada produk target akan
                                <strong>DIHAPUS</strong> dan diganti dengan data dari template.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-0 p-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm"
                    onclick="submitBulkSetup()">
                    <i class="bi bi-lightning-fill me-1"></i> Terapkan Template
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kategori -->
<div class="modal fade" id="modalkategori" tabindex="-1" aria-labelledby="modalkategori" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalkategori">
                    <i class="bi bi-tags me-2"></i>Data Kategori
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-warning">
                            <tr>
                                <th width="10%">No</th>
                                <th>Kategori</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($kategori as $kat): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong><?= $kat->kategori_nama ?></strong></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('pengaturan/kategori_form_builder/' . $kat->kategori_id) ?>"
                                            class="btn btn-xs btn-primary py-1 px-2">
                                            <i class="bi bi-pencil-square me-1"></i>BUILDER
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sub Kategori -->
<div class="modal fade" id="modalsubkategori" tabindex="-1" aria-labelledby="modalsubkategoriLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalsubkategoriLabel">
                    <i class="bi bi-tag me-2"></i>Data Sub Kategori
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formTambahSubkategori" method="post" class="mb-4">
                    <div class="row">
                        <div class="col-md-10">
                            <input type="text" name="subkategori_nama" class="form-control"
                                placeholder="Masukkan nama sub kategori baru" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-plus-circle me-1"></i>Tambah
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-success">
                            <tr>
                                <th width="10%">No</th>
                                <th>Sub Kategori</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($subkategori as $sub): ?>
                                <tr data-id="<?= $sub->subkategori_id ?>">
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <input type="text" class="form-control subkategori-nama"
                                            value="<?= $sub->subkategori_nama ?>">
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="<?= base_url('pengaturan/subkategori_form_builder/' . $sub->subkategori_id) ?>"
                                                class="btn btn-primary btn-sm px-2" title="Form Builder">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button type="button" class="btn btn-success btn-sm btn-edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm btn-delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let Toast;

    // --- Global Functions (Accessible from inline onclick) ---
    function sortBy(field, direction) {
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('orderby', field);
        currentUrl.searchParams.set('sort', direction);
        currentUrl.searchParams.delete('page');
        window.location.href = currentUrl.toString();
    }

    function resetFilters() {
        $('#searchInput').val('');
        $('#filterKategori').val('');
        $('#filterStatus').val('');
        $('#filterStartsWith').val('');
        $('#filterSort').val('asc');
        $('#filterMetode').val('');

        const currentUrl = new URL(window.location.href);
        const currentPerPage = currentUrl.searchParams.get('per_page') || '20';
        $('#pageSize').val(currentPerPage);

        // Reset to first page and clear filters
        currentUrl.searchParams.delete('page');
        currentUrl.searchParams.delete('q');
        currentUrl.searchParams.delete('kategori');
        currentUrl.searchParams.delete('status');
        currentUrl.searchParams.delete('starts_with');
        currentUrl.searchParams.delete('sort');
        currentUrl.searchParams.delete('orderby');
        currentUrl.searchParams.set('per_page', currentPerPage);

        window.location.href = currentUrl.toString();
    }

    function exportData() {
        const visibleRows = $('#productTable tbody tr:visible');
        if (visibleRows.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak ada data',
                text: 'Tidak ada data yang dapat diexport'
            });
            return;
        }

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "No,Nama Product,Kategori,Sub Kategori,Jenis,Sisi,Satuan,Metode Penjualan,Status Kelengkapan,BOM,Routing,Skala Harga\n";

        visibleRows.each(function (index) {
            const row = $(this);
            const no = index + 1;
            const nama = row.find('td:eq(1) a.fw-bold').text().trim().replace(/"/g, '""');
            const status = row.find('td:eq(1) .status-badge').text().trim();
            const kategori = row.find('td:eq(2) .fw-bold').text().trim().replace(/"/g, '""');
            const subkategori = row.find('td:eq(2) .text-slate-600').text().trim().replace(/"/g, '""');
            const jenis = row.find('td:eq(3) .d-flex.flex-wrap > div:first-child').text().trim();
            const sisi = row.find('td:eq(3) .badge').text().trim() || '-';
            const satuan = row.find('td:eq(3) .fw-bold.text-slate-700').text().trim();
            const metode = row.find('td:eq(4) .badge').text().trim();
            const bom = row.find('td:eq(5) .indicator-bom .count-badge').text().trim() || '0';
            const routing = row.find('td:eq(5) .indicator-routing .count-badge').text().trim() || '0';
            const skalaHarga = row.find('td:eq(5) .indicator-price .count-badge').text().trim() || '0';

            csvContent += `"${no}","${nama}","${kategori}","${subkategori}","${jenis}","${sisi}","${satuan}","${metode}","${status}","${bom}","${routing}","${skalaHarga}"\n`;
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `product_data_${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        Swal.fire({
            icon: 'success',
            title: 'Export Berhasil',
            text: `Data ${visibleRows.length} product berhasil diexport`,
            timer: 2000,
            showConfirmButton: false
        });
    }

    $(document).ready(function () {
        Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        // Enhanced Search and Filter functionality
        // Removed auto-search on keyup to prevent frequent reloads

        $('#searchInput').on('keypress', function (e) {
            if (e.which == 13) {
                filterTable();
            }
        });

        // Search icon click listener
        $('.bi-search').parent().css('cursor', 'pointer').on('click', function () {
            filterTable();
        });

        $('#filterKategori, #filterStatus, #filterStartsWith, #filterSort, #filterMetode').on('change', function () {
            filterTable();
        });

        $('.alphabet-filter-btn').on('click', function () {
            const selectedLetter = $(this).data('starts-with') ?? '';
            $('#filterStartsWith').val(selectedLetter);
            filterTable();
        });

        // Page size change
        $('#pageSize').on('change', function () {
            const pageSize = $(this).val();
            const currentUrl = new URL(window.location.href);

            // Update per_page parameter
            currentUrl.searchParams.set('per_page', pageSize);

            // Reset to first page when changing page size
            currentUrl.searchParams.delete('page');

            // Redirect to new URL
            window.location.href = currentUrl.toString();
        });

        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Set pageSize dropdown value based on current URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const currentPerPage = urlParams.get('per_page');
        if (currentPerPage) {
            $('#pageSize').val(currentPerPage);
        }

        // Add loading states
        $('.btn').on('click', function () {
            if (!$(this).hasClass('btn-export')) {
                $(this).addClass('loading');
                setTimeout(() => {
                    $(this).removeClass('loading');
                }, 1000);
            }
        });

        // Keyboard shortcuts
        $(document).on('keydown', function (e) {
            // Ctrl/Cmd + F for search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                $('#searchInput').focus();
            }
            // Ctrl/Cmd + E for export
            if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
                e.preventDefault();
                exportData();
            }
        });

        // Auto-refresh data every 5 minutes
        setInterval(function () {
            // Only refresh if user is not actively interacting
            if (!$('input:focus, select:focus').length) {
                // You can add AJAX refresh here if needed
            }
        }, 300000); // 5 minutes

        function filterTable() {
            const searchTerm = $('#searchInput').val();
            const kategoriFilter = $('#filterKategori').val();
            const statusFilter = $('#filterStatus').val();
            const startsWithFilter = $('#filterStartsWith').val();
            const sortFilter = $('#filterSort').val();
            const orderbyFilter = new URL(window.location.href).searchParams.get('orderby');

            const currentUrl = new URL(window.location.href);

            // Set Search Query
            if (searchTerm) currentUrl.searchParams.set('q', searchTerm);
            else currentUrl.searchParams.delete('q');

            // Set Kategori
            if (kategoriFilter) currentUrl.searchParams.set('kategori', kategoriFilter);
            else currentUrl.searchParams.delete('kategori');

            // Set Status
            if (statusFilter) currentUrl.searchParams.set('status', statusFilter);
            else currentUrl.searchParams.delete('status');

            // Set Starting Letter
            if (startsWithFilter) currentUrl.searchParams.set('starts_with', startsWithFilter);
            else currentUrl.searchParams.delete('starts_with');

            // Set Sort
            if (sortFilter && sortFilter !== 'asc') currentUrl.searchParams.set('sort', sortFilter);
            else currentUrl.searchParams.delete('sort');

            // Set OrderBy
            if (orderbyFilter) currentUrl.searchParams.set('orderby', orderbyFilter);

            // Reset to page 1 when searching
            currentUrl.searchParams.delete('page');

            window.location.href = currentUrl.toString();
        }

        function updateVisibleCount() {
            const visibleCount = $('#productTable tbody tr:visible').length;
            const totalCount = $('#productTable tbody tr').length;

            // Add or update count display
            let countDisplay = $('.filter-count');
            if (countDisplay.length === 0) {
                countDisplay = $('<div class="filter-count mt-2 text-muted small"></div>');
                $('.search-section').append(countDisplay);
            }

            if (visibleCount !== totalCount) {
                countDisplay.html(`<i class="bi bi-info-circle me-1"></i>Menampilkan ${visibleCount} dari ${totalCount} product`);
            } else {
                countDisplay.html('');
            }
        }

        // Form validation
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Subkategori AJAX functionality
        $('#formTambahSubkategori').on('submit', function (e) {
            e.preventDefault();
            const nama = $(this).find('input[name="subkategori_nama"]').val();

            $.ajax({
                url: "<?= base_url('product/add_subkategori_ajax') ?>",
                method: "POST",
                data: { subkategori_nama: nama },
                dataType: "json",
                success: function (res) {
                    if (res.status === 'success') {
                        const row = `
                        <tr data-id="${res.data.subkategori_id}">
                            <td class="text-center">-</td>
                            <td><input type="text" class="form-control subkategori-nama" value="${res.data.subkategori_nama}"></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-primary btn-sm btn-edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                        $('table tbody').append(row);
                        $('#formTambahSubkategori')[0].reset();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Sub Kategori berhasil ditambahkan',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal menambahkan sub kategori'
                        });
                    }
                }
            });
        });

        // Edit Subkategori
        $(document).on('click', '.btn-edit', function (e) {
            e.preventDefault();
            const row = $(this).closest('tr');
            const id = row.data('id');
            const nama = row.find('.subkategori-nama').val();

            $.ajax({
                url: "<?= base_url('product/update_subkategori_ajax') ?>",
                method: "POST",
                data: { subkategori_id: id, subkategori_nama: nama },
                dataType: "json",
                success: function (res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Sub Kategori berhasil diperbarui',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal memperbarui data'
                        });
                    }
                }
            });
        });

        // Delete Subkategori
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            const row = $(this).closest('tr');
            const id = row.data('id');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url('product/delete_subkategori_ajax') ?>",
                        method: "POST",
                        data: { subkategori_id: id },
                        dataType: "json",
                        success: function (res) {
                            if (res.status === 'success') {
                                row.remove();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dihapus!',
                                    text: 'Subkategori berhasil dihapus',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: res.message || 'Gagal menghapus data'
                                });
                            }
                        }
                    });
                }
            });
        });

        // Select2 initialization
        $('#modalInputData').on('shown.bs.modal', function () {
            var $select = $(this).find('.select2-bahan-ajax');
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $select.select2({
                placeholder: '-- Cari & Pilih Bahan Baku --',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalInputData'),
                ajax: {
                    url: '<?= base_url('product/search_bahan_json') ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { term: params.term };
                    },
                    processResults: function (data) {
                        return { results: data.results };
                    },
                    cache: true
                },
                minimumInputLength: 2
            });
        });
    });

    // BOM Modal Functions
    function openBomModal(productId, productName) {
        $('#bomProductId').text(productId);
        $('#bomProductName').text(productName);
        $('#bomProductNameHeader').text(productName);
        $('#bomProductIdInput').val(productId);



        // Load BOM data
        loadBomData(productId);

        // Initialize bahan select2
        initializeBahanSelect2();

        // Show modal
        $('#modalBom').modal('show');
    }



    function loadBomData(productId) {
        $.ajax({
            url: '<?= base_url('manufaktur/get_bom_data') ?>',
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    displayBomData(response.data);
                    
                    // Populate variations dropdown
                    let varSelect = $('#bomVariasiSelect');
                    varSelect.empty();
                    varSelect.append('<option value="">-- Semua Variasi (Global) --</option>');
                    if (response.variations && response.variations.length > 0) {
                        response.variations.forEach(function(v) {
                            varSelect.append(`<option value="${v.harga_id}">${v.display_name}</option>`);
                        });
                    }
                    
                } else {
                    $('#bomTableBody').html('<tr><td colspan="7" class="text-center text-muted">Tidak ada data BOM</td></tr>');
                }
            },
            error: function () {
                $('#bomTableBody').html('<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    }

    function displayBomData(bomData) {
        let html = '';
        if (bomData.length > 0) {
            bomData.forEach(function (item, index) {
                const netQty = parseFloat(item.quantity) || 0;
                const scrap = parseFloat(item.scrap_factor) || 0;
                const grossQty = netQty * (1 + (scrap / 100));

                const calcTypeMap = {
                    'fixed': '1. Fixed',
                    'area': '2. Area (P x L)',
                    'length': '3. Length (P)',
                    'weight': '4. Weight'
                };
                const calcType = item.calc_type || 'fixed';
                const calcTypeText = calcTypeMap[calcType] || calcTypeMap['fixed'];
                const variasiBadge = item.harga_id ? `<span class="badge bg-info-subtle text-info border border-info-subtle mt-1" style="font-size: 0.7rem;"><i class="bi bi-tag me-1"></i>${item.variasi_nama || 'Varian ' + item.harga_id}</span>` : `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle mt-1" style="font-size: 0.7rem;"><i class="bi bi-globe me-1"></i>Semua Variasi (Global)</span>`;

                html += `
                <tr id="bom-row-${item.bom_line_id}">
                    <td class="text-center fw-bold">${index + 1}</td>
                    <td>
                        <div class="fw-bold text-dark">${item.material_name || '-'}</div>
                        ${variasiBadge}
                        <div class="small text-muted mt-1" id="bom-ket-text-${item.bom_line_id}">${item.keterangan || ''}</div>
                    </td>
                    <td class="text-center fw-semibold text-primary">
                        <span id="bom-qty-text-${item.bom_line_id}">${+netQty.toFixed(4)}</span> ${item.satuan || ''}
                    </td>
                    <td class="text-center text-danger">
                        <span id="bom-scrap-text-${item.bom_line_id}">${scrap > 0 ? (+scrap.toFixed(4)) + '%' : '-'}</span>
                    </td>
                    <td class="text-center bg-light fw-bold text-dark" id="bom-gross-text-${item.bom_line_id}">${+grossQty.toFixed(4)}</td>
                    <td class="text-center text-secondary">
                        <span id="bom-dimensi-text-${item.bom_line_id}" class="badge bg-light text-dark border border-secondary">${calcTypeText}</span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" onclick="editBom(${JSON.stringify(item).replace(/"/g, '&quot;')})" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteBom(${item.bom_line_id})" title="Hapus">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            });
        } else {
            html = '<tr><td colspan="7" class="text-center text-muted">Tidak ada data BOM</td></tr>';
        }
        $('#bomTableBody').html(html);
    }

    function editBom(item) {
        const netQty = parseFloat(item.quantity) || 0;
        const scrap = parseFloat(item.scrap_factor) || 0;
        const calcType = item.calc_type || 'fixed';

        const rowHtml = `
            <td class="bg-soft-warning"></td>
            <td class="bg-soft-warning">
                <select class="form-select select2-edit-bahan" id="edit-bahan-${item.bom_line_id}">
                    <option value="${item.bahan_id}" selected>${item.material_name || '-'}</option>
                </select>
                <input type="text" class="form-control form-control-sm mt-1" id="edit-ket-${item.bom_line_id}" value="${item.keterangan || ''}" placeholder="Keterangan...">
            </td>
            <td class="bg-soft-warning">
                <div class="input-group input-group-sm">
                    <input type="number" step="0.0001" class="form-control text-center fw-bold" id="edit-qty-${item.bom_line_id}" value="${netQty}">
                    <span class="input-group-text" id="edit-satuan-label-${item.bom_line_id}">${item.satuan || ''}</span>
                </div>
            </td>
            <td class="bg-soft-warning">
                <div class="input-group input-group-sm">
                    <input type="number" step="0.1" class="form-control text-center" id="edit-scrap-${item.bom_line_id}" value="${scrap}">
                    <span class="input-group-text">%</span>
                </div>
            </td>
            <td class="bg-soft-warning text-center align-middle">
                <span class="badge bg-warning text-dark">REVISI</span>
            </td>
            <td class="bg-soft-warning">
                <select class="form-select form-select-sm text-center fw-bold" id="edit-calctype-${item.bom_line_id}">
                    <option value="fixed" ${calcType === 'fixed' ? 'selected' : ''}>1. Fixed</option>
                    <option value="area" ${calcType === 'area' ? 'selected' : ''}>2. Area</option>
                    <option value="length" ${calcType === 'length' ? 'selected' : ''}>3. Length</option>
                    <option value="weight" ${calcType === 'weight' ? 'selected' : ''}>4. Weight</option>
                </select>
            </td>
            <td class="bg-soft-warning text-center">
                <div class="btn-group">
                    <button class="btn btn-sm btn-success" onclick="saveEditBom(${item.bom_line_id})" title="Simpan">
                        <i class="bi bi-check-lg"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="loadBomData($('#bomProductIdInput').val())" title="Batal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </td>
        `;

        $(`#bom-row-${item.bom_line_id}`).html(rowHtml);

        // Initialize Select2 for the edit row
        $(`#edit-bahan-${item.bom_line_id}`).select2({
            dropdownParent: $('#modalBom'),
            width: '100%',
            ajax: {
                url: '<?= base_url('manufaktur/get_bahan_options') ?>',
                dataType: 'json',
                delay: 250,
                processResults: function (data) { return { results: data.results }; }
            }
        }).on('select2:select', function (e) {
            const data = e.params.data;
            if (data.satuan) {
                $(`#edit-satuan-label-${item.bom_line_id}`).text(data.satuan);
            }
        });
    }

    function saveEditBom(bomId) {
        const data = {
            bom_line_id: bomId,
            bahan_id: $(`#edit-bahan-${bomId}`).val(),
            qty: $(`#edit-qty-${bomId}`).val(),
            scrap_factor: $(`#edit-scrap-${bomId}`).val(),
            calc_type: $(`#edit-calctype-${bomId}`).val(),
            keterangan: $(`#edit-ket-${bomId}`).val()
        };

        $.ajax({
            url: '<?= base_url('manufaktur/update_bom') ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                    loadBomData($('#bomProductIdInput').val());
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    }



    function initializeBahanSelect2() {
        if ($('#bahan_id').hasClass('select2-hidden-accessible')) {
            $('#bahan_id').select2('destroy');
        }

        // Badge color map matching the warehouse badge system
        const jenisBadge = {
            'bahan_baku': { label: 'Bahan Baku', color: '#166534', bg: '#dcfce7' },
            'bahan_operasional': { label: 'Bahan Operasional', color: '#854d0e', bg: '#fef3c7' },
            'barang_jadi': { label: 'Barang Jadi', color: '#1e40af', bg: '#dbeafe' },
            'spare_part': { label: 'Spare Part', color: '#6d28d9', bg: '#ede9fe' },
            'operasional': { label: 'Operasional', color: '#374151', bg: '#f3f4f6' },
        };

        function formatBahanOption(item) {
            if (!item.id) return item.text;
            const j = jenisBadge[item.jenis_barang] || { label: item.jenis_barang, color: '#64748b', bg: '#f1f5f9' };
            return $(`
                <div class="d-flex justify-content-between align-items-center py-1">
                    <span class="fw-600 text-dark">${item.text.split(' [')[0]}</span>
                    <span style="font-size:0.75rem;font-weight:800;padding:2px 10px;border-radius:20px;
                        background:${j.bg};color:${j.color};text-transform:uppercase;letter-spacing:0.5px;">
                        ${j.label}
                    </span>
                </div>
            `);
        }

        $('#bahan_id').select2({
            placeholder: '-- Cari atau klik untuk pilih bahan --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modalBom'),
            templateResult: formatBahanOption,
            ajax: {
                url: '<?= base_url('manufaktur/get_bahan_options') ?>',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return { term: params.term || '' };
                },
                processResults: function (data) {
                    return { results: data.results || [] };
                },
                cache: true
            },
            minimumInputLength: 0
        });

        // When bahan is selected — auto-fill satuan from response (no second AJAX needed)
        $('#bahan_id').off('select2:select').on('select2:select', function (e) {
            const data = e.params.data;
            if (data.satuan) {
                // Show satuan info next to qty field
                let $satuanLabel = $('#bom_satuan_label');
                if (!$satuanLabel.length) {
                    $satuanLabel = $('<span id="bom_satuan_label" class="input-group-text fw-bold text-primary"></span>');
                    $('#qty').closest('.col-md-2').find('.input-group').length
                        ? $('#qty').closest('.input-group').append($satuanLabel)
                        : $('#qty').wrap('<div class="input-group"></div>').parent().append($satuanLabel);
                }
                $satuanLabel.text(data.satuan);
            }
        });

        $('#bahan_id').off('select2:clear').on('select2:clear', function () {
            $('#bom_satuan_label').text('');
        });
    }

    function getBahanUnit(bahanId) {
        $.ajax({
            url: '<?= base_url('manufaktur/get_bahan_unit') ?>',
            type: 'POST',
            data: { bahan_id: bahanId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#satuan').val(response.data.satuan);
                }
            }
        });
    }

    // Handle BOM form submission
    $('#addBomForm').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('manufaktur/add_bom') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Reload BOM data
                    loadBomData($('#bomProductIdInput').val());

                    // Reset form
                    $('#addBomForm')[0].reset();
                    $('#bahan_id').val(null).trigger('change');
                    $('#satuan').val('');

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem'
                });
            }
        });
    });

    // Handle Routing form submission
    $('#addRoutingForm').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);



        $.ajax({
            url: '<?= base_url('manufaktur/add_routing') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Reload routing data
                    loadRoutingData($('#routingProductIdInput').val());

                    // Reset form and Select2 dropdowns
                    $('#addRoutingForm')[0].reset();
                    $('#bom_line_id').val(null).trigger('change');
                    $('#operator_id').val(null).trigger('change');

                    // Re-initialize Select2 to ensure proper functionality
                    setTimeout(function () {
                        initializeMesinSelect2();
                    }, 100);
                }
            },
            error: function () {
                // Silent error handling
            }
        });
    });



    function deleteBom(bomId) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus BOM ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('manufaktur/delete_bom') ?>',
                    type: 'POST',
                    data: { bom_id: bomId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Reload BOM data
                            loadBomData($('#bomProductIdInput').val());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan sistem'
                        });
                    }
                });
            }
        });
    }

    // ==================== ROUTING FUNCTIONS ====================

    function openRoutingModal(productId, productName) {
        // Set product info
        $('#routingProductId').text(productId);
        $('#routingProductName').text(productName);
        $('#routingProductNameHeader').text(productName);
        $('#routingProductIdInput').val(productId);

        // Load routing data
        loadRoutingData(productId);

        // Initialize mesin select2
        initializeMesinSelect2();

        // Show modal
        $('#modalRouting').modal('show');
    }

    function loadRoutingData(productId) {
        $.ajax({
            url: '<?= base_url('manufaktur/get_routing_data') ?>',
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    window.currentRoutingVariations = response.variations || [];
                    displayRoutingData(response.data);
                    updateRoutingStats(response.data);
                } else {
                    window.currentRoutingVariations = [];
                    $('#routingTableBody').html('<tr><td colspan="6" class="text-center text-muted">Tidak ada data routing</td></tr>');
                    updateRoutingStats([]);
                }
            },
            error: function () {
                window.currentRoutingVariations = [];
                $('#routingTableBody').html('<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>');
                updateRoutingStats([]);
            }
        });
    }

    function displayRoutingData(routingData) {
        let html = '';

        if (routingData.length === 0) {
            html = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data routing</td></tr>';
        } else {
            routingData.forEach((item, index) => {
                const laborCost = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.labor_cost || 0);
                html += `
                <tr>
                    <td class="text-center fw-bold"><span class="badge bg-secondary rounded-pill px-3">${item.routing_urutan || (index + 1) * 10}</span></td>
                    <td>
                        <div class="fw-bold">${item.routing_name || '-'}</div>
                        <small class="text-muted d-block">${item.bom_material_name ? '<i class="bi bi-link-45deg"></i> ' + item.bom_material_name : ''}</small>
                    </td>
                    <td><span class="badge bg-light text-dark border"><i class="bi bi-person-workspace me-1"></i>${item.operator_name || '-'}</span></td>
                    <td class="text-center fw-semibold text-primary">${item.est_time || 0} Min</td>
                    <td class="text-end fw-bold text-success">${laborCost}</td>
                    <td class="text-center" style="white-space: nowrap;">
                        <button class="btn btn-sm btn-outline-warning me-1" onclick='openEditRoutingModalByItem(${JSON.stringify(item).replace(/'/g, "&#39;")})' title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteRouting(${item.routing_id})" title="Hapus">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </td>
                </tr>
            `;
            });
        }

        $('#routingTableBody').html(html);
    }

    function openEditRoutingModalByItem(item) {
        $('#edit_routing_id').val(item.routing_id);
        
        let varSelect = $('#edit_harga_id_routing');
        varSelect.empty();
        varSelect.append('<option value="">-- Semua Variasi (Global) --</option>');
        if (window.currentRoutingVariations && window.currentRoutingVariations.length > 0) {
            window.currentRoutingVariations.forEach(function(v) {
                varSelect.append(`<option value="${v.harga_id}">${v.display_name}</option>`);
            });
        }
        $('#edit_harga_id_routing').val(item.harga_id || '');
        
        $('#edit_routing_urutan').val(item.routing_urutan);
        $('#edit_routing_name').val(item.routing_name);
        $('#edit_est_time').val(item.est_time);
        $('#edit_labor_cost').val(new Intl.NumberFormat('id-ID').format(item.labor_cost || 0).replace(/,/g, '.'));

        // Load & Set Operator (Normal Select)
        if ($('#edit_operator_id').hasClass('select2-hidden-accessible')) {
            $('#edit_operator_id').select2('destroy');
        }
        
        // Disable temporarily while loading
        $('#edit_operator_id').prop('disabled', true);
        $.ajax({
            url: '<?= base_url('manufaktur/get_operator_options') ?>',
            dataType: 'json',
            data: { term: '' },
            success: function(res) {
                let $sel = $('#edit_operator_id');
                $sel.empty();
                $sel.append('<option value="">-- Pilih --</option>');
                let data = res.results || res || [];
                data.forEach(d => {
                    $sel.append(`<option value="${d.id}">${d.text}</option>`);
                });
                if (item.operator_id) {
                    $sel.val(item.operator_id);
                }
                $sel.prop('disabled', false);
            }
        });

        // Load & Set BOM Select (Normal Select)
        if ($('#edit_bom_line_id_routing').hasClass('select2-hidden-accessible')) {
            $('#edit_bom_line_id_routing').select2('destroy');
        }
        
        $('#edit_bom_line_id_routing').prop('disabled', true);
        $.ajax({
            url: '<?= base_url('manufaktur/get_bom_options_by_product/') ?>' + $('#routingProductIdInput').val(),
            dataType: 'json',
            data: { term: '' },
            success: function(res) {
                let $sel = $('#edit_bom_line_id_routing');
                $sel.empty();
                $sel.append('<option value="">-- Tanpa Material --</option>');
                let data = res.results || res || [];
                data.forEach(d => {
                    $sel.append(`<option value="${d.id}">${d.text}</option>`);
                });
                const bomId = item.bom_line_id || '99999';
                $sel.val(bomId);
                $sel.prop('disabled', false);
            }
        });

        $('#modalEditRouting').modal('show');
    }

    $(document).on('submit', '#editRoutingForm', function (e) {
        e.preventDefault();
        const data = $(this).serializeArray();
        const lb = data.find(i => i.name === 'labor_cost');
        if (lb) lb.value = lb.value.replace(/[^,\d]/g, '').replace(',', '.');
        $.post('<?= base_url('manufaktur/update_routing') ?>', data, () => {
            $('#modalEditRouting').modal('hide');
            loadRoutingData($('#routingProductIdInput').val());
        }, 'json');
    });

    function updateRoutingStats(routingData) {
        const totalSteps = routingData.length;

        $('#routingTotalSteps').text(totalSteps);

    }

    function initializeMesinSelect2() {
        // Destroy existing Select2 instances to prevent conflicts
        if ($('#bom_line_id').hasClass('select2-hidden-accessible')) {
            $('#bom_line_id').select2('destroy');
        }
        if ($('#operator_id').hasClass('select2-hidden-accessible')) {
            $('#operator_id').select2('destroy');
        }

        // Initialize BOM Select2
        $('#bom_line_id').select2({
            placeholder: '-- Pilih BOM --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modalRouting'),
            ajax: {
                url: '<?= base_url('manufaktur/get_bom_options') ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    const productId = $('#routingProductIdInput').val();
                    return {
                        term: params.term || '',
                        product_id: productId
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results || data || [],
                        pagination: {
                            more: false
                        }
                    };
                },
                error: function (xhr, status, error) {
                    // Error handling for BOM AJAX
                },
                cache: true
            },
            minimumInputLength: 0
        });

        // Initialize Operator Select2
        $('#operator_id').select2({
            placeholder: '-- Pilih Operator --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modalRouting'),
            minimumInputLength: 0, // Allow empty search to show all results
            ajax: {
                url: '<?= base_url('manufaktur/get_operator_options') ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { term: params.term || '' };
                },
                processResults: function (data) {
                    return {
                        results: data.results || data || [],
                        pagination: {
                            more: false
                        }
                    };
                },
                error: function (xhr, status, error) {
                    // Error handling for Operator AJAX
                },
                cache: true
            }
        });

        // Select2 initialization completed
    }

    function deleteRouting(routingId) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus routing ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('manufaktur/delete_routing') ?>',
                    type: 'POST',
                    data: { routing_id: routingId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Reload routing data
                            loadRoutingData($('#routingProductIdInput').val());
                        }
                    },
                    error: function () {
                        // Silent error handling
                    }
                });
            }
        });
    }


</script>

<link href="<?= base_url('assets/select2/css/select2.min.css') ?>" rel="stylesheet" />
<script src="<?= base_url('assets/select2/js/select2.full.min.js') ?>"></script>
<script>
    $(document).ready(function () {
        // Select2 AJAX untuk bahan baku produksi
        $('.select2-bahan-ajax').select2({
            placeholder: "-- Cari & Pilih Bahan Baku --",
            allowClear: true,
            width: '100%',
            ajax: {
                url: '<?= base_url('product/search_bahan_json') ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { term: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        // Initialize Select2 when routing modal is shown
        $('#modalRouting').on('shown.bs.modal', function () {
            setTimeout(function () {
                initializeMesinSelect2();
            }, 100);
        });
    });
</script>

<script>
    $(document).ready(function () {
        // Pastikan Select2 sudah dimuat
        if (typeof $.fn.select2 === 'undefined') {
            return;
        }

        // Initialize Select2 dengan delay untuk memastikan DOM sudah siap
        setTimeout(function () {
            $('#bahan_id').select2({
                placeholder: "-- Cari & Pilih Bahan Baku --",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalInputData'), // Pastikan dropdown muncul di dalam modal
                ajax: {
                    url: '<?= base_url("product/getBahanAjax") ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data || [],
                            pagination: {
                                more: false
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });

            // Saat bahan dipilih, isi product_nama dan bahan_deskripsi otomatis
            $('#bahan_id').on('select2:select', function (e) {
                const namaBahan = e.params.data.text;
                const bahanDeskripsi = e.params.data.deskripsi || '';

                // Isi nama produk otomatis jika kosong
                if (!$('input[name="product_nama"]').val()) {
                    $('input[name="product_nama"]').val(namaBahan);
                }

                // Isi deskripsi bahan
                $('input[name="bahan_deskripsi"]').val(bahanDeskripsi);
            });

            // Jika dibersihkan, kosongkan field terkait
            $('#bahan_id').on('select2:clear', function () {
                $('input[name="bahan_deskripsi"]').val('');
            });
        }, 100);
    });
</script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- Script untuk memastikan Select2 berfungsi di modal -->
<script>
    $(document).ready(function () {
        // Re-initialize Select2 saat modal dibuka
        $('#modalInputData').on('shown.bs.modal', function () {
            setTimeout(function () {
                if ($('#bahan_id').length && typeof $.fn.select2 !== 'undefined') {
                    // Destroy existing Select2 jika ada
                    if ($('#bahan_id').hasClass('select2-hidden-accessible')) {
                        $('#bahan_id').select2('destroy');
                    }

                    // Re-initialize Select2
                    $('#bahan_id').select2({
                        placeholder: "-- Cari & Pilih Bahan Baku --",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#modalInputData'),
                        ajax: {
                            url: '<?= base_url("product/getBahanAjax") ?>',
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    search: params.term,
                                    page: params.page || 1
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data || [],
                                    pagination: {
                                        more: false
                                    }
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 1
                    });

                    // Event handler untuk select bahan
                    $('#bahan_id').off('select2:select select2:clear').on('select2:select', function (e) {
                        const namaBahan = e.params.data.text;
                        const bahanDeskripsi = e.params.data.deskripsi || '';

                        // Isi nama produk otomatis jika kosong
                        if (!$('input[name="product_nama"]').val()) {
                            $('input[name="product_nama"]').val(namaBahan);
                        }

                        // Isi deskripsi bahan
                        $('input[name="bahan_deskripsi"]').val(bahanDeskripsi);
                    });

                    $('#bahan_id').on('select2:clear', function () {
                        $('input[name="bahan_deskripsi"]').val('');
                    });
                }
            }, 200);
        });
    });
</script>

<script>
    $(document).ready(function () {
        // Handle product delete confirmation
        $('.btn-delete-product').click(function (e) {
            e.preventDefault();

            const href = $(this).data('href');
            const productName = $(this).data('product-name');

            Swal.fire({
                title: 'Konfirmasi Hapus Produk',
                html: `
                <div class="text-start">
                    <p class="mb-3">Anda yakin ingin menghapus produk ini?</p>
                    <div class="alert alert-warning">
                        <strong>Produk:</strong> ${productName}
                    </div>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Peringatan:</strong> Semua data terkait akan dihapus:
                        <ul class="mb-0 mt-2">
                            <li>Data harga produk</li>
                            <li>Bill of Materials (BOM)</li>
                            <li>Data routing produksi</li>
                            <li>Data transaksi terkait</li>
                        </ul>
                    </div>
                </div>
            `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Produk!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Menghapus Produk...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Redirect to delete URL
                    window.location.href = href;
                }
            });
        });
    });

    function openEditProductModal(productId) {
        Swal.fire({
            title: 'Memuat...',
            text: 'Sedang mengambil data produk',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: "<?= site_url('manufaktur/get_product_detail/') ?>" + productId,
            method: "GET",
            dataType: "json",
            success: function (res) {
                Swal.close();
                if (res.status === 'success') {
                    const d = res.data;
                    $('#edit_product_id').val(d.product_id);
                    $('#edit_product_nama').val(d.product_nama);
                    $('#edit_product_name_title').text(d.product_nama);
                    $('#edit_kategori_id').val(d.kategori_id);
                    $('#edit_tipe_id').val(d.tipe_id);
                    $('#edit_satuan_id').val(d.satuan_id);
                    $('#edit_side_id').val(d.side_id);
                    $('#edit_metode_id').val(d.detail_id);
                    $('#edit_product_deskripsi').val(d.product_deskripsi);

                    // Load subkategori
                    loadSubkategori(d.kategori_id, '#edit_subkategori_id', d.subkategori_id);

                    $('#modalEditProduct').modal('show');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function () {
                Swal.close();
                Swal.fire('Error', 'Gagal menghubungi server', 'error');
            }
        });
    }

    function loadSubkategori(kategoriId, targetSelector, selectedId = '') {
        if (!kategoriId) {
            $(targetSelector).html('<option value="">-- Pilih Sub Kategori --</option>');
            return;
        }

        $(targetSelector).prop('disabled', true).html('<option value="">Memuat...</option>');

        $.ajax({
            url: "<?= site_url('manufaktur/get_subkategori') ?>",
            method: "POST",
            data: { id: kategoriId },
            dataType: "json",
            success: function (data) {
                let options = '<option value="">-- Pilih Sub Kategori --</option>';
                if (data && Array.isArray(data)) {
                    data.forEach(function (item) {
                        const isSelected = (item.subkategori_id == selectedId) ? 'selected' : '';
                        options += `<option value="${item.subkategori_id}" ${isSelected}>${item.subkategori_nama}</option>`;
                    });
                }
                $(targetSelector).prop('disabled', false).html(options);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error: ", error);
                $(targetSelector).prop('disabled', false).html('<option value="">-- Gagal memuat data --</option>');
            }
        });
    }

    $('#edit_kategori_id').on('change', function () {
        loadSubkategori($(this).val(), '#edit_subkategori_id');
    });

    $('#kategori_id').on('change', function () {
        loadSubkategori($(this).val(), '.subkategori');
    });

    $('#editProductForm').on('submit', function (e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            this.classList.add('was-validated');
            return;
        }

        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: "<?= site_url('manufaktur/update_product_ajax') ?>",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function (res) {
                submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Perbarui Produk');
                if (res.status === 'success') {
                    $('#modalEditProduct').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function () {
                submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Perbarui Produk');
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            }
        });
    });
</script>

<script>
    function cloneProduct(productId, productName) {
        Swal.fire({
            title: 'Clone Master Data',
            html: `
            <div class="text-start">
                <p>Duplikasi BOM, Routing, dan Skala Harga dari:</p>
                <div class="alert alert-info py-2"><strong>${productName}</strong></div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Produk Baru</label>
                    <input type="text" id="new_product_name" class="form-control" value="${productName} - Copy">
                </div>
            </div>
        `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            confirmButtonText: '<i class="bi bi-files me-2"></i>Mulai Duplikasi',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const newName = Swal.getPopup().querySelector('#new_product_name').value;
                if (!newName) {
                    Swal.showValidationMessage(`Nama produk baru tidak boleh kosong`);
                }
                return { newName: newName };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sedang Menyalin...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: '<?= base_url("manufaktur/clone_product") ?>',
                    type: 'POST',
                    data: {
                        source_id: productId,
                        new_name: result.value.newName
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Produk berhasil diduplikasi.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Duplikasi',
                                text: response.message || 'Terjadi kesalahan sistem'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Koneksi server terputus'
                        });
                    }
                });
            }
        });
    }
</script>

<!-- Script untuk modal routing -->
<script>
    $(document).ready(function () {
        // Event handler untuk modal routing
        $('#modalRouting').on('shown.bs.modal', function () {
            // Pastikan product ID sudah diset
            const productId = $('#routingProductIdInput').val();

            // Initialize Select2 dengan delay
            setTimeout(function () {
                if (typeof $.fn.select2 !== 'undefined') {
                    initializeMesinSelect2();
                }

                // Initialize Bootstrap Tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }, 300);
        });

        // Event handler untuk form routing
        $('#addRoutingForm').on('submit', function (e) {
            e.preventDefault();

            const formData = {
                product_id: $('#routingProductIdInput').val(),
                routing_urutan: $('#routing_urutan').val(),
                routing_name: $('#routing_name').val(),
                bom_line_id: $('#bom_line_id').val(),
                operator_id: $('#operator_id').val(),
                est_time: $('input[name="est_time"]').val(),
                labor_cost: $('input[name="labor_cost"]').val()
            };

            // Validasi
            if (!formData.routing_urutan || !formData.routing_name || !formData.bom_line_id || !formData.operator_id) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Tidak Lengkap',
                    text: 'Mohon isi semua field yang bertanda bintang (*)'
                });
                return;
            }

            // Submit form
            $.ajax({
                url: '<?= base_url("manufaktur/add_routing") ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Reset form
                        $('#addRoutingForm')[0].reset();

                        // Reload routing data
                        loadRoutingData(formData.product_id);

                        // Clear Select2
                        $('#bom_line_id').val(null).trigger('change');
                        $('#operator_id').val(null).trigger('change');
                    }
                },
                error: function (xhr, status, error) {
                    // Silent error handling
                }
            });
        });

        // ==================== BULK SETUP FUNCTIONS ====================
        function initializeBulkSelect2() {
            // Destroy existing to prevent conflicts
            if ($('#bulk_template_id').hasClass('select2-hidden-accessible')) {
                $('#bulk_template_id').select2('destroy');
            }
            if ($('#bulk_target_ids').hasClass('select2-hidden-accessible')) {
                $('#bulk_target_ids').select2('destroy');
            }

            // Template Search
            $('#bulk_template_id').select2({
                placeholder: '-- Cari Produk --',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalBulkSetup'),
                ajax: {
                    url: '<?= base_url("manufaktur/search_product") ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { term: params.term };
                    },
                    processResults: function (data) {
                        return { results: data.results || [] };
                    }
                },
                minimumInputLength: 0,
                minimumResultsForSearch: 0
            });

            // Targets Search
            $('#bulk_target_ids').select2({
                placeholder: '-- Pilih Produk Target --',
                width: '100%',
                allowClear: true,
                dropdownParent: $('#modalBulkSetup'),
                closeOnSelect: false,
                minimumResultsForSearch: 0
            });
        }

        $('#bulk_target_subkategori').on('change', function () {
            const subId = $(this).val();
            if (!subId) return;

            $.get('<?= base_url("manufaktur/get_products_by_subkategori") ?>', { subkategori_id: subId }, function (data) {
                let options = '';
                data.forEach(item => {
                    options += `<option value="${item.id}">${item.text}</option>`;
                });
                $('#bulk_target_ids').html(options).trigger('change');
            }, 'json');
        });

        // Event handler untuk modal bulk setup
        $('#modalBulkSetup').on('shown.bs.modal', function () {
            initializeBulkSelect2();
        });

        // Auto-focus Select2 search field when opened
        $(document).on('select2:open', function (e) {
            const searchField = document.querySelector('.select2-search__field');
            if (searchField) {
                searchField.focus();
            }
        });

        // Export submit function to global for onclick
        window.submitBulkSetup = function () {
            const form = $('#bulkSetupForm');
            const templateId = $('#bulk_template_id').val();
            const targetIds = $('#bulk_target_ids').val();

            if (!templateId || !targetIds || targetIds.length === 0) {
                Swal.fire('Peringatan', 'Silakan pilih Template dan Minimal 1 Produk Target', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Bulk Setup',
                text: "Data BOM/Routing pada produk target akan digantikan. Lanjutkan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Terapkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.post('<?= base_url("manufaktur/bulk_apply_template") ?>', form.serialize(), function (res) {
                        if (res.success) {
                            Swal.fire('Berhasil', res.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    }, 'json');
                }
            });
        };

        window.bulkSelectAllTargets = function () {
            $('#bulk_target_ids option').prop('selected', true).trigger('change');
        };

        window.bulkClearTargets = function () {
            $('#bulk_target_ids').val(null).trigger('change');
        };

        // ==================== BULK PRICE UPDATE ====================
        $(document).ready(function () {
            $('.select2-bulk-price').select2({
                dropdownParent: $('#modalBulkPriceUpdate'),
                width: '100%'
            });

            $('input[name="adj_method"]').on('change', function () {
                const method = $(this).val();
                $('#adj_symbol').text(method === 'percent' ? '%' : 'Rp');
            });
        });

        window.submitBulkPriceUpdate = function () {
            const form = $('#bulkPriceForm');
            const data = form.serialize();
            const adjValue = form.find('input[name="adj_value"]').val();
            const targets = form.find('input[name="targets[]"]:checked').length;

            if (!adjValue) {
                return Swal.fire('Error', 'Harap masukkan nilai kenaikan!', 'warning');
            }

            if (targets === 0) {
                return Swal.fire('Error', 'Harap pilih target perubahan (Harga Jual/HPP)!', 'warning');
            }

            Swal.fire({
                title: 'Konfirmasi Perubahan',
                text: "Apakah Anda yakin ingin memperbarui harga untuk kategori terpilih? Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Update Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading() }
                    });

                    $.ajax({
                        url: "<?= base_url('manufaktur/bulk_price_adjustment_ajax') ?>",
                        type: "POST",
                        data: data,
                        dataType: "json",
                        success: function (res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: res.message,
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Terjadi kesalahan sistem saat menghubungi server', 'error');
                        }
                    });
                }
            });
        };

        // ==================== PRICING FUNCTIONS ====================
        window.lastCalculatedHPP = 0;

        window.openPricingModal = function (productId, productName) {
            $('#pricingProductName').text(productName);
            $('#pricingProductIdInput').val(productId);

            // Initial Loading State
            $('#pricingModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Memuat matriks harga...</p></div>');
            $('#modalPricing').modal('show');

            // Load Calculate HPP from backend for the "Magic Button"
            $.get('<?= base_url('manufaktur/get_calculated_hpp_ajax/') ?>' + productId, function (res) {
                if (res.success) {
                    window.lastCalculatedHPP = res.hpp;
                }
            }, 'json');

            // Load HTML Partial
            $.get('<?= base_url('manufaktur/get_pricing_partial/') ?>' + productId, function (html) {
                $('#pricingModalBody').html(html);

                // Override Form Action for AJAX inside modal
                $('#formSavePrices').on('submit', function (e) {
                    e.preventDefault();
                    savePricingMatrix($(this));
                });
            });
        }

        window.savePricingMatrix = function (form) {
            const btn = form.find('button[type="submit"]');
            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Menyimpan...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function (res) {
                    btn.prop('disabled', false).html(originalHtml);
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function () {
                    btn.prop('disabled', false).html(originalHtml);
                    Swal.fire('Error', 'Terjadi kesalahan sistem saat menyimpan harga', 'error');
                }
            });
        }

        // ==================== FORM BUILDER MODAL ====================
        window.openFormBuilderModal = function (productId, productName) {
            $('#formBuilderProductNameHeader').text(productName);
            $('#iframeFormBuilder').attr('src', '<?= base_url('pengaturan/product_form_builder/') ?>' + productId + '/modal');
            $('#modalFormBuilder').modal('show');
        }

        $('#modalFormBuilder').on('hidden.bs.modal', function () {
            $('#iframeFormBuilder').attr('src', '');
            // Optional: Reload window to update badge color if user saved schema
            // window.location.reload(); 
        });

    }
    );
</script>

<?php $this->load->view('admin/product/components/v_modal_sop_produk'); ?>