<?php
// application/views/admin/warehouse/stock.php
?>

<!-- PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - WAREHOUSE STOCK -->
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

    /* Header Section */
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
        color: white;
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
        margin-top: -2rem; /* Aligned with compact header bottom */
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

    /* Category Pills */
    .category_pills {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .category-btn {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.6rem 1.25rem;
        font-weight: 800;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--slate-muted);
        transition: all 0.3s;
        text-decoration: none !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
    }

    .category-btn.active {
        background: var(--accent-blue);
        color: white;
        border-color: var(--accent-blue);
        box-shadow: 0 10px 15px rgba(99, 102, 241, 0.2);
    }

    .category-btn:hover:not(.active) {
        border-color: var(--accent-blue);
        color: var(--accent-blue);
        transform: translateY(-2px);
    }

    /* Content Area */
    .content-card {
        background: white;
        border-radius: 28px;
        padding: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f1f5f9;
        margin-bottom: 3rem;
    }

    /* Dual-Mode Search */
    .dual-search-wrapper {
        display: flex;
        flex-direction: column;
        min-width: 320px;
    }

    .search-mode-tabs {
        display: flex;
        gap: 4px;
    }

    .search-tab-btn {
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
        border-radius: 20px;
        padding: 3px 12px;
        font-size: 0.7rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        letter-spacing: 0.3px;
    }

    .search-tab-btn.active {
        background: var(--accent-blue, #6366f1);
        color: white;
        border-color: var(--accent-blue, #6366f1);
    }

    .search-tab-btn:hover:not(.active) {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #3b82f6;
    }

    .search-input-group {
        width: 100%;
    }

    /* Barcode mode */
    .search-input-group.mode-barcode .search-input-icon {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
    }
    .search-input-group.mode-barcode .unified-search-input {
        border-color: #1e293b;
        background: #f0f7ff;
    }
    .search-input-group.mode-barcode .unified-search-input:focus {
        box-shadow: 0 0 0 3px rgba(30, 41, 59, 0.15);
        border-color: #1e293b;
    }

    /* Text mode */
    .search-input-group.mode-text .search-input-icon {
        background: var(--accent-blue, #6366f1);
        color: white;
        border-color: var(--accent-blue, #6366f1);
    }
    .search-input-group.mode-text .unified-search-input {
        border-color: var(--accent-blue, #6366f1);
        background: #f5f3ff;
    }
    .search-input-group.mode-text .unified-search-input:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        border-color: var(--accent-blue, #6366f1);
    }

    .unified-search-input {
        transition: all 0.25s;
        font-size: 0.85rem;
    }

    .search-clear-btn {
        border-left: none;
        color: #94a3b8;
        font-size: 0.8rem;
        padding: 0 10px;
        transition: color 0.2s;
    }
    .search-clear-btn:hover { color: #f43f5e; }

    /* Hide default DataTable search box */
    div.dataTables_filter { display: none !important; }

    /* Tables */
    .table-premium thead th {
        background: #f8fafc;
        border: none;
        color: var(--slate-muted);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        padding: 1.25rem 1rem;
    }

    .table-premium tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f8fafc;
        color: var(--slate-dark);
        font-size: 0.95rem;
    }

    /* Custom UI Components */
    .form-control-premium {
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 14px;
        padding: 0.75rem 1rem;
        font-weight: 600;
        transition: all 0.25s;
    }

    .form-control-premium:focus {
        background: white;
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .badge-premium {
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .bg-soft-danger {
        background: #fff1f2;
        color: #f43f5e;
    }

    .bg-soft-warning {
        background: #fffbeb;
        color: #f59e0b;
    }

    .bg-soft-success {
        background: #ecfdf5;
        color: #10b981;
    }

    .bg-soft-blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .btn-premium {
        border-radius: 10px; /* More compact curves */
        padding: 0.45rem 1rem; /* Sleeker padding */
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem; /* Smaller premium text */
        letter-spacing: 0.5px;
        transition: all 0.3s;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    /* Fixed DataTable Tidy Styling */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 25px;
        color: var(--slate-muted);
        font-weight: 700;
        font-size: 0.85rem;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        padding: 6px 15px;
        margin-left: 10px;
        background: #f8fafc;
        transition: all 0.3s;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--accent-blue);
        outline: none;
        background: white;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .dataTables_wrapper .dataTables_length select {
        border: 2px solid #f1f5f9;
        border-radius: 10px;
        padding: 4px 10px;
        margin: 0 8px;
        background: #f8fafc;
    }

    /* Warehouse Command Center Styles */
    .warehouse-pills {
        display: flex;
        gap: 0.75rem; /* Compact gap */
        margin-bottom: 1.5rem;
        overflow-x: auto;
        padding: 0.25rem;
    }

    .warehouse-pill {
        background: white;
        border: 2px solid #f1f5f9;
        border-radius: 16px; /* Sleeker curves */
        padding: 0.65rem 1.25rem; /* Compact padding */
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        flex: 1;
        min-width: 170px;
        box-shadow: var(--premium-shadow);
    }

    .warehouse-pill.active {
        background: var(--primary-gradient);
        border-color: transparent;
        color: white;
        transform: translateY(-3px); /* Smaller lift */
    }

    .warehouse-pill:hover:not(.active) {
        border-color: var(--accent-blue);
        transform: translateY(-2px);
    }

    .pill-icon {
        width: 36px; /* Smaller icon container */
        height: 36px;
        border-radius: 10px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: var(--slate-dark);
        transition: all 0.3s;
    }

    .warehouse-pill.active .pill-icon {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .pill-text h6 {
        margin: 0;
        font-weight: 900;
        font-size: 0.85rem; /* Tighter typography */
        line-height: 1.2;
    }

    .pill-text p {
        margin: 0;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        opacity: 0.7;
        line-height: 1.2;
    }

    /* Pulse Annotations */
    .pulse-danger {
        animation: pulse-red 2s infinite;
    }

    @keyframes pulse-red {
        0% {
            box-shadow: 0 0 0 0 rgba(247, 37, 133, 0.7);
        }

        70% {
            box-shadow: 0 0 0 15px rgba(247, 37, 133, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(247, 37, 133, 0);
        }
    }

    .pulse-warning {
        animation: pulse-amber 2s infinite;
    }

    @keyframes pulse-amber {
        0% {
            box-shadow: 0 0 0 0 rgba(241, 196, 15, 0.7);
        }

        70% {
            box-shadow: 0 0 0 15px rgba(241, 196, 15, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(241, 196, 15, 0);
        }
    }

    .operator-badge {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 0.3rem 0.8rem; /* Reduced padding */
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: white;
        font-weight: 700;
        font-size: 0.72rem; /* Reduced from 0.85rem */
        margin-top: 0.45rem; /* Reduced from 1rem */
    }

    .operator-status-dot {
        width: 10px;
        height: 10px;
        background: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 10px #10b981;
    }
    /* Inventory Detail Modal Premium */
    .detail-stock-card {
        background: var(--primary-gradient);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        text-align: center;
        margin-bottom: 2rem;
    }
    .detail-stock-value {
        font-size: 5rem;
        font-weight: 900;
        line-height: 1;
        margin: 1rem 0;
        text-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    .detail-unit-label {
        font-size: 1.2rem;
        font-weight: 700;
        text-transform: uppercase;
        opacity: 0.8;
    }
    .history-timeline {
        border-left: 2px solid #e2e8f0;
        padding-left: 1.5rem;
        position: relative;
    }
    .history-item {
        margin-bottom: 1.5rem;
        position: relative;
    }
    .history-item::before {
        content: '';
        position: absolute;
        left: -1.95rem;
        top: 0.25rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: white;
        border: 2px solid var(--accent-blue);
    }
</style>

<div class="warehouse-header-section">
    <div class="container-fluid px-5">
        <div class="row align-items-start">
            <div class="col-md-6">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="fa fa-boxes"></i>
                    </div>
                    <div class="header-text">
                        <p class="header-subtitle"><?= $title ?></p>
                        <h1 class="header-title">Live Stock Monitoring</h1>
                        <div class="operator-badge text-uppercase">
                            <div class="operator-status-dot"></div>
                            Operator: <?= $this->session->userdata('nama') ?: 'Administrator' ?>
                        </div>
                        <div class="mt-2 text-white border-0 py-1.5 px-3 d-inline-block rounded-3"
                            style="max-width: 500px; font-size: 0.7rem; background: rgba(255, 255, 255, 0.08); border-left: 3px solid #6366f1 !important; line-height: 1.3;">
                            <span class="text-info fw-bold"><i class="fa fa-info-circle me-1"></i> INFO:</span>
                            <span class="text-white opacity-75">Sesuaikan data fisik di <b>Langkah 3: Stok Opname</b>.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-end header-actions d-flex justify-content-end align-items-center gap-2">

                <button id="btnToggleWorkflow" class="btn btn-outline-light btn-premium rounded-pill px-3">
                    <i class="fa fa-eye-slash me-1"></i>HIDE
                </button>
                <button class="btn btn-outline-light btn-premium rounded-pill px-3" data-bs-toggle="modal"
                    data-bs-target="#modalStockGuide">
                    <i class="fa fa-info-circle me-1"></i>SOP
                </button>
                <a href="<?= base_url('warehouse/dispatch') ?>"
                    class="btn btn-warning btn-premium rounded-pill px-3 shadow-sm fw-900 border border-white border-2 text-dark">
                    <i class="fa fa-bolt me-1"></i>DISPATCH
                </a>
                <a href="<?= base_url('warehouse') ?>" class="btn btn-outline-light btn-premium rounded-pill px-3">
                    <i class="fa fa-th-large me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-5">
    <!-- Warehouse Command Selector -->
    <div class="stats-container" style="margin-top: -2.5rem; margin-bottom: 2rem;">
        <div class="warehouse-pills">
            <?php foreach ($warehouses as $w): ?>
                <a href="<?= site_url('warehouse/stock?gudang_id=' . $w->gudang_id) ?>"
                    class="warehouse-pill <?= $selected_gudang == $w->gudang_id ? 'active' : '' ?>">
                    <div class="pill-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div class="pill-text">
                        <p>Lokasi</p>
                        <h6><?= strtoupper($w->gudang_nama) ?></h6>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 6-STEP CORE WORKFLOW (SOP) -->
    <div id="workflowContainer">
        <?php $this->load->view('admin/warehouse/components/v_material_workflow', ['current_step' => 6]); ?>
    </div>

    <!-- 7. POPULAR & FAST-MOVING MATERIALS -->
    <?php if (!empty($popular_materials)): ?>
        <div class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                <div>
                    <h5 class="fw-900 text-dark mb-0"><i class="fa fa-fire text-danger me-2"></i>Bahan Baku Terpopuler (Fast-Moving)</h5>
                    <p class="text-muted small mb-0">Material paling sering terpakai untuk gudang ini dalam 30 hari terakhir.</p>
                </div>
                <span class="badge bg-soft-blue text-primary px-3 py-2 fw-800 rounded-pill" style="font-size: 0.7rem;">
                    <i class="fa fa-chart-line me-1"></i> LOG ANALYSIS
                </span>
            </div>
            <div class="row g-3">
                <?php 
                $ranks = [
                    1 => ['bg' => 'linear-gradient(135deg, #ffd700 0%, #ffa500 100%)', 'text' => 'text-dark', 'label' => '#1 MOST POPULAR'],
                    2 => ['bg' => 'linear-gradient(135deg, #c0c0c0 0%, #a9a9a9 100%)', 'text' => 'text-white', 'label' => '#2 POPULAR'],
                    3 => ['bg' => 'linear-gradient(135deg, #cd7f32 0%, #8b5a2b 100%)', 'text' => 'text-white', 'label' => '#3 POPULAR'],
                    4 => ['bg' => 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)', 'text' => 'text-white', 'label' => '#4 POPULAR']
                ];
                $no = 1;
                foreach ($popular_materials as $pm): 
                    $rank = $ranks[$no] ?? ['bg' => 'var(--primary-gradient)', 'text' => 'text-white', 'label' => '#' . $no . ' POPULAR'];
                    $no++;
                    
                    // Sisa stok calculation
                    $konversi = (float)($pm->konversi ?: 1);
                    $qty_base = (float)$pm->qty_base;
                    $stok_akhir = $qty_base / $konversi;
                    $a_abs = abs($stok_akhir);
                    $a_whole = floor($a_abs);
                    $a_small = round(($a_abs - $a_whole) * $konversi);
                    if ($a_small >= $konversi) {
                        $a_whole++;
                        $a_small = 0;
                    }
                    
                    // Stock safety calculation for progress bar
                    $stok_min = (float)($pm->stok_min ?: 0);
                    $safety_pct = 100;
                    if ($stok_min > 0) {
                        $safety_pct = ($stok_akhir / $stok_min) * 100;
                        if ($safety_pct > 100) $safety_pct = 100;
                        if ($safety_pct < 0) $safety_pct = 0;
                    }
                    
                    $is_low = ($stok_akhir <= $stok_min);
                    $is_empty = ($stok_akhir <= 0);
                    
                    // Color for progress bar and icons
                    if ($is_empty) {
                        $theme_color = '#f43f5e'; // Red
                        $theme_gradient = 'var(--danger-gradient)';
                        $status_label = 'HABIS';
                    } elseif ($is_low) {
                        $theme_color = '#f59e0b'; // Amber
                        $theme_gradient = 'var(--warning-gradient)';
                        $status_label = 'LIMIT';
                    } else {
                        $theme_color = '#10b981'; // Green
                        $theme_gradient = 'var(--success-gradient)';
                        $status_label = 'AMAN';
                    }
                ?>
                <div class="col-md-3">
                    <div class="popular-material-card shadow-sm border" 
                         style="background: white; border-radius: 24px; padding: 1.25rem; height: 100%; transition: all 0.3s; position: relative; overflow: hidden; border-color: #f1f5f9 !important;">
                        <!-- Rank tag -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge fw-900 px-3 py-1.5 rounded-pill text-uppercase <?= $rank['text'] ?>" 
                                  style="background: <?= $rank['bg'] ?>; font-size: 0.6rem; letter-spacing: 0.5px;">
                                <?= $rank['label'] ?>
                            </span>
                            <span class="badge rounded-pill fw-800 text-uppercase d-flex align-items-center gap-1"
                                  style="background: <?= $is_low ? '#fffbeb' : '#ecfdf5' ?>; color: <?= $is_low ? '#d97706' : '#10b981' ?>; font-size: 0.65rem; padding: 4px 10px;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: <?= $theme_color ?>;"></span>
                                <?= $status_label ?>
                            </span>
                        </div>
                        
                        <!-- Material Name -->
                        <div class="mb-2">
                            <h6 class="fw-900 text-dark mb-0 text-truncate" title="<?= htmlspecialchars($pm->varian) ?>">
                                <?= htmlspecialchars($pm->varian) ?>
                            </h6>
                            <p class="text-muted small mb-0 text-truncate" style="font-size: 0.7rem; font-weight: 700;">
                                <?= htmlspecialchars($pm->bahan_nama) ?>
                            </p>
                        </div>
                        
                        <!-- Stats grid -->
                        <div class="row g-2 py-2 mb-3 bg-light rounded-4 text-center">
                            <div class="col-6 border-end">
                                <span class="text-muted d-block" style="font-size: 0.6rem; font-weight: 800; text-transform: uppercase;">Frekuensi</span>
                                <span class="fw-900 text-primary" style="font-size: 1.1rem;"><?= number_format($pm->frekuensi) ?> <small class="fw-bold text-muted small" style="font-size: 0.65rem;">Kali</small></span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.6rem; font-weight: 800; text-transform: uppercase;">Total Pakai</span>
                                <span class="fw-900 text-dark text-truncate d-block px-1" style="font-size: 0.95rem;">
                                    <?php 
                                    // Out month display calculation
                                    $out_month = (float)$pm->total_qty_keluar;
                                    $o_abs = abs($out_month);
                                    $o_whole = floor($o_abs);
                                    $o_small = round(($o_abs - $o_whole) * $konversi);
                                    if ($o_small >= $konversi) {
                                        $o_whole++;
                                        $o_small = 0;
                                    }
                                    
                                    echo number_format($o_whole) . ' ' . $pm->satuan_gudang;
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Stock Status -->
                        <div>
                            <div class="d-flex justify-content-between text-muted small mb-1" style="font-size: 0.7rem; font-weight: 700;">
                                <span>SISA STOK</span>
                                <span class="fw-900 text-dark">
                                    <?= number_format($a_whole, 0, ',', '.') ?> <?= $pm->satuan_gudang ?>
                                    <?php if ($konversi > 1 && $a_small > 0): ?>
                                        <?= $stok_akhir <= -0.01 ? '-' : '+' ?> <?= number_format($a_small, 0, ',', '.') ?> <?= $pm->satuan_produksi ?: 'Lbr' ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <!-- Sleek visual progress bar -->
                            <div class="progress" style="height: 6px; border-radius: 10px; background: #e2e8f0;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                                     style="width: <?= $safety_pct ?>%; background: <?= $theme_gradient ?>; border-radius: 10px;" 
                                     aria-valuenow="<?= $safety_pct ?>" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <style>
            .popular-material-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
                border-color: var(--accent-blue) !important;
            }
        </style>
    <?php endif; ?>

    <!-- Category Selector -->
    <div class="category_pills">
        <?php
        $cats = [
            '' => ['label' => 'Semua Material', 'icon' => 'fa-th-large'],
            'bahan_baku' => ['label' => 'Bahan Baku', 'icon' => 'fa-scroll'],
            'bahan_operasional' => ['label' => 'Ops. Produksi', 'icon' => 'fa-tools'],
            'barang_jadi' => ['label' => 'Barang Jadi', 'icon' => 'fa-box-open'],
            'spare_part' => ['label' => 'Spare Parts', 'icon' => 'fa-cog'],
            'operasional' => ['label' => 'Lainnya', 'icon' => 'fa-folder']
        ];

        foreach ($cats as $slug => $c):
            ?>
            <button type="button" class="category-btn category-filter-btn <?= $slug == '' ? 'active' : '' ?>"
                data-filter="<?= $slug ?>">
                <i class="fas <?= $c['icon'] ?> me-2"></i><?= $c['label'] ?>
            </button>
        <?php endforeach; ?>
    </div>
    <!-- <div class="row g-4">
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-icon" style="background: var(--info-gradient);">
                    <i class="fa fa-layer-group"></i>
                </div>
                <div class="metric-info">
                    <p>Total Item</p>
                    <h3><?= is_array($stock_data) || is_object($stock_data) ? count($stock_data) : 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-icon" style="background: var(--warning-gradient);">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <div class="metric-info">
                    <p>Stok Minim</p>
                    <h3>
                        <?php
                        $minimCount = (is_array($stock_data) || is_object($stock_data)) ? array_filter((array) $stock_data, function ($s) {
                            return isset($s->qty_stock) && $s->qty_stock > 0 && isset($s->stok_min) && $s->qty_stock <= $s->stok_min;
                        }) : [];
                        echo count($minimCount);
                        ?>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <?php
            $emptyCount = (is_array($stock_data) || is_object($stock_data)) ? array_filter((array) $stock_data, function ($s) {
                return isset($s->qty_stock) && $s->qty_stock <= 0;
            }) : [];
            ?>
            <div class="metric-card <?= (is_array($emptyCount) && count($emptyCount) > 0) ? 'pulse-danger' : '' ?>">
                <div class="metric-icon" style="background: var(--danger-gradient);">
                    <i class="fa fa-times-circle"></i>
                </div>
                <div class="metric-info">
                    <p>Stok Habis</p>
                    <h3 class="<?= (is_array($emptyCount) && count($emptyCount) > 0) ? 'text-danger' : '' ?>">
                        <?= is_array($emptyCount) ? count($emptyCount) : 0; ?>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-icon" style="background: var(--success-gradient);">
                    <i class="fa fa-check-double"></i>
                </div>
                <div class="metric-info">
                    <p>Stok Aman</p>
                    <h3>
                        <?php
                        $safeCount = (is_array($stock_data) || is_object($stock_data)) ? array_filter((array) $stock_data, function ($s) {
                            return isset($s->qty_stock) && isset($s->stok_min) && $s->qty_stock > $s->stok_min;
                        }) : [];
                        echo count($safeCount);
                        ?>
                    </h3>
                </div>
            </div>
        </div>
    </div> -->
</div>

<!-- Main Content -->
<div class="row g-4 px-4">
    <div class="col-xl-12">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-900 text-dark mb-0">Rincian Inventaris</h4>
                    <p class="text-muted small mb-0">Memantau aliran stok di <strong
                            id="total_items_count"><?= count($stock_data) ?></strong>
                        material terdaftar.</p>
                </div>
                <div class="header-actions d-flex gap-2 align-items-center flex-wrap">
                    <!-- Dual-Mode Search Bar -->
                    <div class="dual-search-wrapper">
                        <!-- Mode Toggle Tabs -->
                        <div class="search-mode-tabs mb-1 d-flex gap-1">
                            <button class="search-tab-btn active" id="tabBarcode" onclick="switchSearchMode('barcode')">
                                <i class="fa fa-barcode me-1"></i>Barcode
                            </button>
                            <button class="search-tab-btn" id="tabText" onclick="switchSearchMode('text')">
                                <i class="fa fa-search me-1"></i>Teks
                            </button>
                        </div>
                        <!-- Input -->
                        <div class="input-group search-input-group shadow-sm">
                            <span class="input-group-text search-input-icon" id="searchModeIcon">
                                <i class="fa fa-barcode"></i>
                            </span>
                            <input type="text" id="unifiedSearchInput"
                                class="form-control fw-bold unified-search-input"
                                placeholder="SCAN BARCODE DI SINI..."
                                autocomplete="off" autofocus>
                            <button class="btn btn-outline-secondary search-clear-btn" id="btnClearSearch"
                                type="button" style="display:none;" title="Hapus Pencarian">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <button onclick="resetStokGudang()" class="btn btn-danger btn-premium shadow-sm border border-danger text-white">
                        <i class="fa fa-trash me-1"></i>RESET STOK & LOG
                    </button>
                    <button class="btn btn-light btn-premium shadow-sm border" onclick="location.reload()">
                        <i class="fa fa-sync-alt me-1 text-primary"></i>REFRESH
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-premium align-middle" id="tableStock">
                    <thead>
                        <tr>
                            <th style="width: 50px;">NO</th>
                            <th>MATERIAL / BARANG</th>
                            <!-- <th>BARCODE</th> -->
                            <th class="d-none">JENIS</th>
                            <th>LOKASI / RAK</th>
                            <th class="text-end text-muted" style="font-size: 0.75rem;">STOK AWAL <span
                                    class="fw-normal">(+ OPNAME)</span></th>
                            <th class="text-end"><?= ($stock_data[0]->in_label ?? 'MASUK') ?> <span
                                    class="fw-normal text-muted">(BLN INI)</span></th>
                            <th class="text-center"><?= ($stock_data[0]->out_label ?? 'KELUAR') ?> <span
                                    class="fw-normal text-muted">(BLN INI)</span></th>
                            <th class="text-end">SALDO SAAT INI</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($stock_data as $s): ?>
                            <tr>
                                <td class="text-muted fw-bold"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-primary mt-1"
                                        style="font-size: 0.9rem; letter-spacing: 0.5px; opacity: 0.9;">
                                        <?= htmlspecialchars($s->varian) ?>
                                    </div>
                                    <div class="fw-800 text-dark" style="font-size: 0.7rem;">
                                        <?= htmlspecialchars($s->bahan_nama) ?>
                                    </div>
                                    <!-- <div class="mt-1">
                                        <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.65rem; padding: 2px 6px;"><?= $s->barcode ?></span>
                                    </div> -->
                                </td>
                                <!-- <td>
                                    <span class="badge bg-dark text-white border font-monospace"
                                        style="font-size: 0.8rem; letter-spacing: 1px; padding: 4px 8px;"><?= $s->barcode ?></span>
                                </td> -->
                                <td class="d-none"><?= htmlspecialchars($s->jenis_barang) ?></td>
                                <td>
                                    <?php if (!empty($s->rack_codes)): ?>
                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-soft-blue text-primary p-2 rounded-3">
                                                    <i class="fas fa-th-large"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-800 text-dark small"><?= htmlspecialchars($s->rack_codes, ENT_QUOTES, 'UTF-8') ?></div>
                                                    <div class="text-muted" style="font-size: 0.65rem;"><?= htmlspecialchars($s->rack_names, ENT_QUOTES, 'UTF-8') ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-light btn-sm rounded-pill px-3 fw-800 border"
                                                onclick="openMoveModal(<?= htmlspecialchars(json_encode($s)) ?>)"
                                                title="Pindah Rak">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-primary btn-sm rounded-pill w-100 fw-800 shadow-sm"
                                            onclick="openMoveModal(<?= htmlspecialchars(json_encode($s)) ?>)">
                                            <i class="fas fa-map-marker-alt me-1"></i> ATUR RAK
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php
                                    $konversi = (float) ($s->konversi ?: 1);
                                    $in_val = (float) ($s->total_in_all_month ?? 0);
                                    $out_val = (float) ($s->total_out_all_month ?? 0);
                                    $stok_akhir = (float) $s->qty_stock;

                                    // Gunakan total ALL in/out agar perhitungan saldo awal akurat
                                    // dan tidak menjadi minus akibat adanya Opname/Revert
                                    $awal_bulan = $stok_akhir - $in_val + $out_val;

                                    $ab_abs = abs($awal_bulan);
                                    $ab_whole = floor($ab_abs);
                                    $ab_small = round(($ab_abs - $ab_whole) * $konversi);
                                    if ($ab_small >= $konversi) {
                                        $ab_whole++;
                                        $ab_small = 0;
                                    }
                                    ?>
                                    <div class="fw-800 text-muted" style="font-size: 0.85rem">
                                        <?= ($awal_bulan <= -0.01 ? '-' : '') . number_format($ab_whole, 0, ',', '.') ?> <span
                                            class="small opacity-75"><?= $s->satuan_nama ?></span>
                                    </div>
                                    <?php if ($konversi > 1 && $ab_small > 0): ?>
                                        <div class="text-muted fw-bold" style="font-size: 0.65rem; margin-top: -2px;">
                                            <?= $awal_bulan <= -0.01 ? '-' : '+' ?> <?= number_format($ab_small, 0, ',', '.') ?>         <?= $s->satuan_produksi ?: 'Pcs' ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php
                                    $in_month = (float) ($s->total_in_month ?? 0);
                                    $out_month = (float) ($s->total_out_month ?? 0);

                                    $f_abs = abs($in_month);
                                    $f_whole = floor($f_abs);
                                    $f_small = round(($f_abs - $f_whole) * $konversi);
                                    if ($f_small >= $konversi) {
                                        $f_whole++;
                                        $f_small = 0;
                                    }
                                    ?>
                                    <div class="fw-800 text-dark"
                                        <?php if ($s->in_label == 'Dispatch'): ?>
                                            onclick="showDispatchDetail(<?= $s->material_id ?>, '<?= htmlspecialchars($s->varian ?? $s->bahan_nama) ?>')"
                                            style="cursor: pointer;" title="Klik untuk melihat detail histori dispatch"
                                        <?php elseif ($s->in_label == 'Pembelian'): ?>
                                            onclick="showPembelianDetail(<?= $s->material_id ?>, '<?= htmlspecialchars($s->varian ?? $s->bahan_nama) ?>')"
                                            style="cursor: pointer;" title="Klik untuk melihat detail histori pembelian"
                                        <?php endif; ?>>
                                        <?= ($in_month < 0 && $f_whole > 0 ? '-' : '') . number_format($f_whole, 0, ',', '.') ?> <span
                                            class="small opacity-75"><?= $s->satuan_nama ?></span>
                                    </div>
                                    <?php if ($konversi > 1 && $f_small > 0): ?>
                                        <div class="text-primary fw-bold" style="font-size: 0.65rem; margin-top: -2px;">
                                            <?= $in_month < 0 ? '-' : '+' ?> <?= number_format($f_small, 0, ',', '.') ?>         <?= $s->satuan_produksi ?: 'Lembar' ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="text-muted mt-1" style="font-size: 0.6rem; font-weight: 800;">
                                        TOTAL <?= strtoupper($s->in_label ?? '') ?> (MTD)
                                    </div>
                                </td>

                                <td class="text-center">
                                    <?php 
                                    $out_month = (float) ($s->total_out_month ?? 0);
                                    $p_abs = abs($out_month);
                                    $p_whole = floor($p_abs);
                                    $p_small = round(($p_abs - $p_whole) * $konversi);
                                    if ($p_small >= $konversi) {
                                        $p_whole++;
                                        $p_small = 0;
                                    }
                                    $total_prod_base = round($p_abs * $konversi);
                                    ?>
                                    <?php if ($p_whole > 0 || $p_small > 0 || $total_prod_base > 0): ?>
                                        <div
                                            <?php if ($s->out_label == 'Produksi'): ?>
                                                onclick="showProduksiDetail(<?= $s->material_id ?>, '<?= htmlspecialchars($s->varian ?? $s->bahan_nama) ?>')"
                                                style="cursor: pointer;" title="Klik untuk melihat detail histori produksi"
                                            <?php elseif ($s->out_label == 'Dispatch'): ?>
                                                onclick="showDispatchDetail(<?= $s->material_id ?>, '<?= htmlspecialchars($s->varian ?? $s->bahan_nama) ?>')"
                                                style="cursor: pointer;" title="Klik untuk melihat detail histori dispatch"
                                            <?php endif; ?>
                                            class="badge-premium <?= $s->out_label == 'Dispatch' ? 'bg-soft-blue text-primary' : 'bg-soft-danger text-danger' ?> px-3 py-1 d-inline-block">
                                            <i
                                                class="fas <?= $s->out_label == 'Dispatch' ? 'fa-truck' : 'fa-industry' ?> me-1"></i>
                                            <?php if ($konversi > 1): ?>
                                                <?php if ($p_whole > 0): ?>
                                                    <?= ($out_month < 0 ? '-' : '') . number_format($p_whole, 0, ',', '.') ?> <small><?= $s->satuan_nama ?></small>
                                                    <?php if ($p_small > 0): ?>
                                                        <span class="ms-1 border-start ps-1">
                                                            <?= number_format($p_small, 0, ',', '.') ?> <small><?= $s->satuan_produksi ?: 'Pcs' ?></small>
                                                        </span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?= ($out_month < 0 ? '-' : '') . number_format($p_small > 0 ? $p_small : $total_prod_base, 0, ',', '.') ?> <small><?= $s->satuan_produksi ?: 'Pcs' ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?= ($out_month < 0 ? '-' : '') . number_format($p_whole, 0, ',', '.') ?> <small><?= $s->satuan_nama ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-muted mt-1" style="font-size: 0.6rem; font-weight: 800;">TOTAL
                                            <?= strtoupper($s->out_label) ?> (MTD)
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted opacity-50 fw-bold">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php
                                    $stok_akhir = (float) $s->qty_stock;
                                    $a_abs = abs($stok_akhir);
                                    $a_whole = floor($a_abs);
                                    $a_small = round(($a_abs - $a_whole) * $konversi);
                                    if ($a_small >= $konversi) {
                                        $a_whole++;
                                        $a_small = 0;
                                    }
                                    ?>
                                    <div class="fw-900 fs-5 mb-0 <?= $stok_akhir <= 0 ? 'text-danger' : 'text-success' ?>"
                                        style="line-height: 1.2;">
                                        <?= ($stok_akhir <= -0.01 && $a_whole > 0 ? '-' : '') . number_format($a_whole, 0, ',', '.') ?> <span
                                            class="fs-7 text-uppercase opacity-75"><?= $s->satuan_nama ?></span>
                                    </div>
                                    <?php if ($konversi > 1 && $a_small > 0): ?>
                                        <div class="fw-bold <?= $stok_akhir <= 0 ? 'text-danger' : 'text-primary' ?>"
                                            style="font-size: 0.75rem; margin-top: -2px;">
                                            <?= $stok_akhir <= -0.01 ? '-' : '+' ?> <?= number_format($a_small, 0, ',', '.') ?> <span
                                                class="small opacity-75"><?= $s->satuan_produksi ?: 'Pcs' ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php
                                    $in_val_all = (float) ($s->total_in_all_month ?? 0);
                                    $in_val_vis = (float) ($s->total_in_month ?? 0);
                                    $adj_in = $in_val_all - $in_val_vis;
                                    
                                    $out_val_all = (float) ($s->total_out_all_month ?? 0);
                                    $out_val_vis = (float) ($s->total_out_month ?? 0);
                                    $adj_out = $out_val_all - $out_val_vis;
                                    
                                    $net_adj = $adj_in - $adj_out;
                                    if (abs($net_adj) >= 0.01):
                                    ?>
                                    <div class="mt-1 opacity-75" style="font-size: 0.65rem; color: #858796;" title="Ada Penyesuaian/Lainnya di luar operasional harian">
                                        (Adj: <?= $net_adj > 0 ? '+' : '' ?><?= number_format($net_adj, 2) ?> <?= $s->satuan_nama ?>)
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $base_qty = round((float)$s->qty_stock * $konversi);
                                    $min_qty = round((float)($s->stok_min ?? 5) * $konversi);
                                    if ($base_qty <= 0): 
                                    ?>
                                        <span class="badge-premium bg-soft-danger status-pill">
                                            <div class="status-dot" style="background: #f43f5e;"></div> HABIS
                                        </span>
                                    <?php elseif ($base_qty <= $min_qty): ?>
                                        <span class="badge-premium bg-soft-warning status-pill">
                                            <div class="status-dot" style="background: #f59e0b;"></div> LIMIT
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-premium bg-soft-success status-pill">
                                            <div class="status-dot" style="background: #10b981;"></div> AMAN
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                        <button class="btn btn-white btn-sm border-0"
                                        onclick='openAdjustModal(<?= htmlspecialchars(json_encode($s)) ?>)' title="Edit Stok Manual">
                                        <i class="bi bi-pencil-square text-warning"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Production Consumption Bottom Section -->
        <div class="col-12 mt-4" id="consumption-monitor-container">
            <div class="card border-0 shadow-sm p-4 text-center"
                style="border-radius: 28px; background: white; min-height: 150px;">
                <div class="py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted fw-800">MENUNGGU DATA AKTIVITAS DISPATCH...</p>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Move/Set Rack -->
<div class="modal fade" id="modalMoveRack" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header px-4 py-4" style="background: var(--primary-gradient); color: white;">
                <h5 class="modal-title fw-900"><i class="fas fa-map-marker-alt me-2"></i>Atur Penempatan Rak</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formMoveRack">
                <div class="modal-body p-4 bg-white">
                    <div class="p-3 bg-light rounded-4 mb-4 border">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-white p-3 rounded-4 shadow-sm">
                                <i class="fas fa-boxes fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h6 class="fw-900 text-dark mb-1" id="move_material_name">-</h6>
                                <p class="text-muted small mb-0" id="move_material_variant">-</p>
                                <div class="mt-2 badge-premium bg-soft-blue text-primary d-inline-block">
                                    Stok: <span id="move_qty_display">0</span> <span id="move_unit_display">Pcs</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="material_id" id="move_material_id">
                    <input type="hidden" name="gudang_id" id="move_gudang_id">
                    <input type="hidden" name="rak_asal" id="move_rak_asal">

                    <div class="mb-4">
                        <label class="form-label-premium">Lokasi Rak Baru</label>
                        <select name="rak_tujuan" id="move_rak_tujuan" class="form-select form-control-premium"
                            required>
                            <option value="">-- Pilih Rak Tujuan --</option>
                            <?php
                            // Fetch all racks for the selected warehouse
                            $all_racks = $this->db->get_where('tbl_rak', ['gudang_id' => $selected_gudang, 'is_active' => 1])->result();
                            foreach ($all_racks as $r):
                                ?>
                                <option value="<?= $r->rak_id ?>"><?= $r->rak_kode ?> - <?= $r->rak_nama ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted mt-2 d-block">Pilih rak tempat barang ini disimpan secara
                            fisik.</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label-premium">Catatan Mutasi</label>
                        <textarea name="catatan" class="form-control form-control-premium" rows="2"
                            placeholder="Ex: Penataan stok baru, pindah koridor..."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-light btn-premium px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-premium btn-premium-primary px-4 fw-900">SIMPAN
                        LOKASI</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Manual Stock Adjustment with PIN Verification -->
<div class="modal fade" id="modalAdjustStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header px-4 py-4 bg-dark text-white">
                <h5 class="modal-title fw-900"><i class="fas fa-edit me-2"></i>Penyesuaian Stok Real</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAdjustStock">
                <div class="modal-body p-4 bg-white">
                    <div class="alert alert-warning border-0 rounded-4 mb-4">
                        <div class="d-flex gap-3">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Kebijakan Perubahan Stok</h6>
                                <p class="small mb-0">Fitur ini hanya digunakan jika terjadi kesalahan input fatal.
                                    Seluruh perubahan akan dicatat ke dalam log audit sistem.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-4 mb-4 border">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-white p-3 rounded-4 shadow-sm text-dark">
                                <i class="fas fa-box-open fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="fw-900 text-dark mb-1" id="adj_material_name">-</h6>
                                <p class="text-muted small mb-0" id="adj_material_variant">-</p>
                                <div class="mt-2 text-danger fw-bold small">
                                    Stok Sistem: <span id="adj_qty_system">0</span> <span
                                        id="adj_unit_display">Pcs</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="material_id" id="adj_material_id">
                    <input type="hidden" name="gudang_id" id="adj_gudang_id">

                    <div class="row g-3">
                        <div class="col-8">
                            <label class="form-label-premium">Stok Real Terbaru</label>
                            <div class="input-group">
                                <input type="number" step="0.01" id="adj_input_gudang" class="form-control form-control-premium text-center fw-bold" placeholder="0" required>
                                <span class="input-group-text bg-light text-dark fw-bold" id="adj_lbl_gudang" style="border-right: 0;">Unit</span>
                                
                                <input type="number" step="0.01" id="adj_input_produksi" class="form-control form-control-premium text-center fw-bold" placeholder="0" style="display:none; border-left: 1px solid #dee2e6;">
                                <span class="input-group-text bg-light text-dark fw-bold" id="adj_lbl_produksi" style="display:none;">Pcs</span>
                            </div>
                            <small class="text-muted mt-1 d-block">Masukkan angka stok fisik yang terlihat di gudang.</small>
                            <input type="hidden" name="qty_aktual" id="adj_qty_aktual">
                            <input type="hidden" id="adj_konversi_val" value="1">
                        </div>
                        <div class="col-4">
                            <label class="form-label-premium" title="Gunakan PIN Produksi Anda (dari Data Karyawan)">PIN
                                OTORISASI</label>
                            <input type="password" name="owner_pin"
                                class="form-control form-control-premium text-center fw-bold" maxlength="6"
                                placeholder="****" required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label-premium">Alasan / Catatan Penyesuaian</label>
                        <textarea name="catatan" class="form-control form-control-premium" rows="2"
                            placeholder="Contoh: Salah input nota pembelian, Rusak belum dilaporkan, dsb..."
                            required></textarea>
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-light btn-premium px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-premium btn-dark px-4 fw-900 border-0">OTORISASI &
                        SIMPAN</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Detail Inventory (Auto-Popup on Scan) -->
<div class="modal fade" id="modalInventoryDetail" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h1 class="fw-900 text-primary mb-0" id="det_material_variant" style="font-size: 2.5rem; letter-spacing: -1.5px; line-height: 1.1;">Varian Produk</h1>
                    <div class="text-dark fw-bold opacity-75 mt-1" id="det_material_name" style="font-size: 1.2rem; letter-spacing: 0.5px;">Nama Material</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="detail-stock-card shadow-lg">
                            <span class="badge bg-white text-dark rounded-pill px-3 py-1 fw-bold mb-2">SALDO SAAT INI</span>
                            <div class="detail-stock-value" id="det_qty_large">0</div>
                            <div class="detail-unit-label" id="det_unit_name">SATUAN</div>
                            <div class="mt-4 pt-3 border-top border-white border-opacity-25 d-flex justify-content-center gap-4">
                                <div class="text-center">
                                    <div class="small opacity-75 fw-bold">KODE BARCODE</div>
                                    <div class="fw-900 font-monospace" id="det_barcode_label">-</div>
                                </div>
                                <div class="text-center">
                                    <div class="small opacity-75 fw-bold">LOKASI RAK</div>
                                    <div class="fw-900 text-uppercase" id="det_rak_label">-</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-0 bg-light rounded-4 p-3 mb-4">
                             <div class="fw-900 text-dark small mb-2"><i class="fa fa-info-circle me-1"></i> CATATAN TERAKHIR</div>
                             <p class="text-muted small mb-0 fst-italic" id="det_last_note">-</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-7">
                        <h5 class="fw-900 text-dark mb-4 px-2"><i class="fa fa-history me-2 text-primary"></i>RIWAYAT AKTIVITAS TERAKHIR</h5>
                        <div class="ps-3">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle" style="font-size: 0.85rem;">
                                    <thead class="text-muted fw-bold border-bottom">
                                        <tr>
                                            <th>TANGGAL</th>
                                            <th>TIPE</th>
                                            <th class="text-end">JUMLAH</th>
                                            <th>USER</th>
                                        </tr>
                                    </thead>
                                    <tbody id="det_history_body">
                                        <!-- Data via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light btn-premium rounded-pill px-5 border" data-bs-dismiss="modal">TUTUP</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openAdjustModal(data) {
        // Reset form first before setting values
        $('#formAdjustStock')[0].reset();

        $('#adj_material_id').val(data.material_id);
        $('#adj_gudang_id').val(data.gudang_id);

        $('#adj_material_name').text(data.bahan_nama);
        $('#adj_material_variant').text(data.varian);
        $('#adj_qty_system').text(parseFloat(data.qty_stock).toLocaleString());
        $('#adj_unit_display').text(data.satuan_nama);

        $('#adj_qty_aktual').val(parseFloat(data.qty_stock));
        
        let konv = parseFloat(data.konversi) || 1;
        $('#adj_konversi_val').val(konv);
        
        let qty_stock = parseFloat(data.qty_stock) || 0;
        let s_abs = Math.abs(qty_stock);
        let s_whole = Math.floor(s_abs);
        let s_small = Math.round((s_abs - s_whole) * konv);
        if (s_small >= konv) { s_whole++; s_small = 0; }
        if (qty_stock < 0) s_whole = -s_whole;

        $('#adj_lbl_gudang').text(data.satuan_nama);
        $('#adj_input_gudang').val(s_whole);

        if (konv > 1) {
            $('#adj_input_produksi').show().val(s_small);
            $('#adj_lbl_produksi').show().text(data.satuan_produksi || 'Pcs');
        } else {
            $('#adj_input_produksi').hide().val(0);
            $('#adj_lbl_produksi').hide();
        }

        $('#modalAdjustStock').modal('show');
    }
    
    // Calculate final qty before submit or when changed
    $('#adj_input_gudang, #adj_input_produksi').on('input', function() {
        let konv = parseFloat($('#adj_konversi_val').val()) || 1;
        let g_val = parseFloat($('#adj_input_gudang').val()) || 0;
        let p_val = parseFloat($('#adj_input_produksi').val()) || 0;
        
        let total = g_val + (p_val / konv);
        $('#adj_qty_aktual').val(total);
    });

    function openMoveModal(data) {
        $('#move_material_id').val(data.material_id);
        $('#move_gudang_id').val(data.gudang_id);
        $('#move_rak_asal').val(data.rak_id); // Source rack (can be null)

        $('#move_material_name').text(data.bahan_nama);
        $('#move_material_variant').text(data.varian);
        $('#move_qty_display').text(parseFloat(data.qty_stock).toLocaleString());
        $('#move_unit_display').text(data.satuan_nama);

        // Reset and show
        $('#formMoveRack')[0].reset();
        $('#modalMoveRack').modal('show');
    }

    function refreshConsumptionMonitor() {
        console.log("Refreshing Consumption Monitor...");
        const gudang_id = '<?= $selected_gudang ?>';
        $('#consumption-monitor-container').load('<?= base_url('warehouse/ajax_production_monitor/') ?>' + gudang_id);
    }

    $(document).ready(function () {
        // --- WORKFLOW TOGGLE ---
        $('#btnToggleWorkflow').on('click', function () {
            const container = $('#workflowContainer');
            const btn = $(this);
            if (container.is(':visible')) {
                container.slideUp(400);
                btn.html('<i class="fa fa-eye me-2"></i>SHOW SOP');
                localStorage.setItem('tgm_warehouse_sop_hidden', 'true');
            } else {
                container.slideDown(400);
                btn.html('<i class="fa fa-eye-slash me-2"></i>HIDE SOP');
                localStorage.removeItem('tgm_warehouse_sop_hidden');
            }
        });

        // Restore state from localStorage
        if (localStorage.getItem('tgm_warehouse_sop_hidden') === 'true') {
            $('#workflowContainer').hide();
            $('#btnToggleWorkflow').html('<i class="fa fa-eye me-2"></i>SHOW SOP');
        }

        // Initial Refresh
        refreshConsumptionMonitor();

        // Refresh every 20 seconds
        setInterval(refreshConsumptionMonitor, 20000);

        var stockTable = $('#tableStock').DataTable({
            "pageLength": 25,
            "ordering": true,
            "order": [], // Preserves original database sorting order (popularity first)
            "language": {
                "search": "Cari Inventaris:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            }
        });

        // Filter functionality without reload
        $('.category-filter-btn').on('click', function () {
            $('.category-filter-btn').removeClass('active');
            $(this).addClass('active');

            var filterValue = $(this).data('filter');

            if (filterValue === '') {
                stockTable.column(3).search('').draw(); // Column index 3 is JENIS (d-none)
            } else {
                stockTable.column(3).search('^' + filterValue + '$', true, false).draw();
            }

            // Update item count
            $('#total_items_count').text(stockTable.rows({ filter: 'applied' }).count());
        });

        $('#formAdjustStock').on('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Otorisasi Perubahan?',
                text: "Tindakan ini akan mengubah saldo stok di sistem secara langsung dan dicatat di log audit.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1e293b',
                confirmButtonText: 'Ya, Otorisasi',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('warehouse/actionAdjustmentStok') ?>',
                        type: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('Berhasil', res.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Terjadi kesalahan sistem atau PIN salah.', 'error');
                        }
                    });
                }
            });
        });

        $('#formMoveRack').on('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Simpan Lokasi?',
                text: "Memperbarui koordinat penyimpanan material di sistem.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1a1c23',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('warehouse/ajax_update_material_rack') ?>',
                        type: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function (res) {
                            if (res.status == 'success') {
                                Swal.fire('Berhasil', res.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        }
                    });
                }
            });
        });
        // ========= DUAL-MODE SEARCH =========
        var currentSearchMode = 'barcode'; // 'barcode' | 'text'

        // Init visual state
        $('.search-input-group').addClass('mode-barcode');

        window.switchSearchMode = function(mode) {
            currentSearchMode = mode;
            var $input = $('#unifiedSearchInput');
            var $group = $('.search-input-group');
            var $icon  = $('#searchModeIcon i');

            // Update tabs
            $('#tabBarcode, #tabText').removeClass('active');
            if (mode === 'barcode') {
                $('#tabBarcode').addClass('active');
                $group.removeClass('mode-text').addClass('mode-barcode');
                $icon.attr('class', 'fa fa-barcode');
                $input.attr('placeholder', 'SCAN BARCODE DI SINI...');
                // Clear any text filter
                stockTable.search('').draw();
            } else {
                $('#tabText').addClass('active');
                $group.removeClass('mode-barcode').addClass('mode-text');
                $icon.attr('class', 'fa fa-search');
                $input.attr('placeholder', 'Cari nama / varian / jenis...');
            }

            // Reset input & clear btn
            $input.val('').focus();
            $('#btnClearSearch').hide();
            $('#total_items_count').text(stockTable.rows({ filter: 'applied' }).count());
        };

        // Clear button
        $('#btnClearSearch').on('click', function() {
            $('#unifiedSearchInput').val('').focus();
            $(this).hide();
            if (currentSearchMode === 'text') {
                stockTable.search('').draw();
                $('#total_items_count').text(stockTable.rows({ filter: 'applied' }).count());
            }
        });

        // Input handler — dual mode
        $('#unifiedSearchInput').on('input', function() {
            var val = $(this).val();
            $('#btnClearSearch').toggle(val.length > 0);

            if (currentSearchMode === 'text') {
                // Live filter DataTable
                stockTable.search(val).draw();
                $('#total_items_count').text(stockTable.rows({ filter: 'applied' }).count());
            }
        });

        // BARCODE: press Enter to lookup
        $('#unifiedSearchInput').on('keypress', function(e) {
            if (e.which == 13 && currentSearchMode === 'barcode') {
                e.preventDefault();
                var barcode = $(this).val().trim();
                if (!barcode) return;

                $.ajax({
                    url: '<?= base_url('warehouse/ajax_get_inventory_detail') ?>',
                    type: 'POST',
                    data: {
                        barcode: barcode,
                        warehouse_id: '<?= $selected_gudang ?>'
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            var mat = res.material;
                            var history = res.history;

                            // Populate Modal
                            $('#det_material_name').text(mat.bahan_nama);
                            $('#det_material_variant').text(mat.varian);
                            $('#det_barcode_label').text(mat.barcode);
                            $('#det_rak_label').text(mat.rak_kode || 'BELUM DIATUR');

                            var konversi = parseFloat(mat.konversi || 1);
                            var base_qty = parseFloat(mat.qty);
                            var main_qty = Math.floor(base_qty / konversi);
                            var small_qty = Math.round(base_qty % konversi);

                            $('#det_qty_large').text(main_qty.toLocaleString());
                            $('#det_unit_name').text(mat.satuan_nama + (small_qty > 0 ? ' + ' + small_qty + ' ' + (mat.satuan_produksi || 'Lbr') : ''));

                            var h_html = '';
                            if (history && history.length > 0) {
                                history.forEach(function(h) {
                                    var type_badge = '';
                                    if (h.trx_type == 'Pembelian') type_badge = 'bg-soft-success text-success';
                                    else if (h.trx_type == 'Mutation' || h.trx_type == 'Dispatch') type_badge = 'bg-soft-blue text-primary';
                                    else if (h.trx_type == 'Opname') type_badge = 'bg-soft-warning text-warning';
                                    else type_badge = 'bg-light text-muted';

                                    var qty_display = (parseFloat(h.qty_in) > 0 ? '+' : '-') + (parseFloat(h.qty_in) || parseFloat(h.qty_out)).toLocaleString();

                                    h_html += `
                                        <tr class="border-bottom border-light">
                                            <td><span class="text-muted fw-bold">${h.created_at}</span></td>
                                            <td><span class="badge ${type_badge} rounded-pill px-3">${h.trx_type}</span></td>
                                            <td class="text-end fw-900">${qty_display}</td>
                                            <td class="small">${h.user_nama || 'System'}</td>
                                        </tr>
                                    `;
                                });
                                $('#det_last_note').text(history[0].catatan || '-');
                            } else {
                                h_html = '<tr><td colspan="4" class="text-center text-muted p-4">Belum ada riwayat transaksi</td></tr>';
                                $('#det_last_note').text('-');
                            }
                            $('#det_history_body').html(h_html);

                            // Highlight row in table
                            stockTable.search(barcode).draw();

                            // Show Modal
                            $('#modalInventoryDetail').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Barcode Tidak Dikenali',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    }
                });
                $(this).select();
            }
        });

        // Autofocus back to search after table draw (barcode mode)
        stockTable.on('draw', function() {
            if (currentSearchMode === 'barcode') {
                $('#unifiedSearchInput').focus();
            }
        });

        // Autofocus after modal close
        $('#modalInventoryDetail').on('hidden.bs.modal', function () {
            $('#unifiedSearchInput').focus().select();
            // Clear table highlight after modal close
            if (currentSearchMode === 'barcode') {
                stockTable.search('').draw();
                $('#unifiedSearchInput').val('');
                $('#btnClearSearch').hide();
            }
        });
        // ========= END DUAL-MODE SEARCH =========
    });

    function resetStokGudang() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Ini akan mereset semua stok dan log penggunaan pada gudang ini menjadi 0!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('warehouse/reset_stock_gudang/' . $selected_gudang) ?>";
            }
        });
    }

    function showProduksiDetail(materialId, materialName) {
        $('#modalDetailProduksiName').text(materialName);
        $('#modalDetailProduksiBody').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2 text-muted">Memuat data riwayat...</div>
            </div>
        `);
        
        var myModal = new bootstrap.Modal(document.getElementById('modalDetailProduksi'));
        myModal.show();
        
        $.ajax({
            url: '<?= site_url("wip/ajax_detail_konsumsi/") ?>' + materialId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#modalDetailProduksiBody').html(response.html);
                } else {
                    $('#modalDetailProduksiBody').html('<div class="alert alert-danger">Gagal memuat data.</div>');
                }
            },
            error: function() {
                $('#modalDetailProduksiBody').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat riwayat.</div>');
            }
        });
    }

    function showDispatchDetail(materialId, materialName) {
        $('#modalDetailDispatchName').text(materialName);
        $('#modalDetailDispatchBody').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2 text-muted">Memuat data riwayat...</div>
            </div>
        `);
        
        var myModal = new bootstrap.Modal(document.getElementById('modalDetailDispatch'));
        myModal.show();
        
        // Pass gudang_id parameter to scope dispatch to current warehouse
        let gudangId = <?= json_encode($selected_gudang) ?>;

        $.ajax({
            url: '<?= site_url("warehouse/ajax_detail_dispatch/") ?>' + materialId,
            type: 'GET',
            data: { gudang_id: gudangId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#modalDetailDispatchBody').html(response.html);
                } else {
                    $('#modalDetailDispatchBody').html('<div class="alert alert-danger">Gagal memuat data.</div>');
                }
            },
            error: function() {
                $('#modalDetailDispatchBody').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat riwayat dispatch.</div>');
            }
        });
    }

    function showPembelianDetail(materialId, materialName) {
        $('#modalDetailPembelianName').text(materialName);
        $('#modalDetailPembelianBody').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2 text-muted">Memuat data riwayat...</div>
            </div>
        `);
        
        var myModal = new bootstrap.Modal(document.getElementById('modalDetailPembelian'));
        myModal.show();
        
        let gudangId = <?= json_encode($selected_gudang) ?>;

        $.ajax({
            url: '<?= site_url("warehouse/ajax_detail_pembelian/") ?>' + materialId,
            type: 'GET',
            data: { gudang_id: gudangId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#modalDetailPembelianBody').html(response.html);
                } else {
                    $('#modalDetailPembelianBody').html('<div class="alert alert-danger">Gagal memuat data.</div>');
                }
            },
            error: function() {
                $('#modalDetailPembelianBody').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat riwayat pembelian.</div>');
            }
        });
    }
</script>

<!-- Modal Detail Pembelian -->
<div class="modal fade" id="modalDetailPembelian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-shopping-cart me-2 text-primary"></i> Detail Pembelian: <span id="modalDetailPembelianName" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-white" id="modalDetailPembelianBody">
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary fw-bold rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Dispatch -->
<div class="modal fade" id="modalDetailDispatch" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-truck-loading me-2 text-primary"></i> Detail Dispatch: <span id="modalDetailDispatchName" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-white" id="modalDetailDispatchBody">
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary fw-bold rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Produksi -->
<div class="modal fade" id="modalDetailProduksi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-history me-2 text-primary"></i> Detail Produksi: <span id="modalDetailProduksiName" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-white" id="modalDetailProduksiBody">
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary fw-bold rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('admin/warehouse/components/v_modal_stock_guide'); ?>
</content>