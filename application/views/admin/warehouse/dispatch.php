<?php
// application/views/admin/warehouse/dispatch.php
?>

<!-- PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - WAREHOUSE DISPATCH/DISTRIBUTION -->
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

    /* Visual Bridge Flow */
    .transfer-bridge {
        background: white;
        border-radius: 28px;
        padding: 2.5rem;
        display: flex;
        align-items: center;
        gap: 2.5rem;
        margin-top: -2rem; /* Compact alignment */
        margin-bottom: 2.5rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid rgba(255, 255, 255, 0.8);
        position: relative;
        z-index: 20;
    }

    .bridge-node {
        flex: 1;
        padding: 1.75rem;
        border-radius: 22px;
        text-align: center;
        position: relative;
        transition: all 0.3s;
    }

    .node-source {
        background: #f8fafc;
        border: 2px solid #f1f5f9;
    }

    .node-dest {
        background: #f0fdf4;
        border: 2px solid #dcfce7;
    }

    .node-label {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--slate-muted);
        letter-spacing: 1.5px;
        margin-bottom: 0.75rem;
        display: block;
    }

    .node-name {
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--slate-dark);
    }

    .bridge-arrow {
        width: 50px;
        height: 50px;
        background: var(--primary-gradient);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        z-index: 10;
        border: 4px solid white;
        flex-shrink: 0;
    }

    .bridge-control-card {
        background: #f0fdf4;
        border: 2px solid #dcfce7;
        border-radius: 22px;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex: 2;
    }

    .bridge-input-group {
        text-align: left;
    }

    .bridge-input-group label {
        font-size: 0.65rem;
        font-weight: 800;
        color: var(--success-gradient);
        text-transform: uppercase;
        letter-spacing: 1px;
        display: block;
        margin-bottom: 4px;
    }

    .btn-execute {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 0.75rem 1.5rem;
        font-weight: 900;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-execute:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
    }

    .btn-execute:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Content Area */
    .content-card {
        background: white;
        border-radius: 28px;
        padding: 2rem;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f8fafc;
        margin-bottom: 2rem;
        height: 100%;
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
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
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

    /* DRAG & DROP UI */
    .catalog-container {
        max-height: 600px;
        overflow-y: auto;
        padding-right: 10px;
    }

    .catalog-card {
        background: white;
        border-radius: 18px;
        padding: 1.25rem;
        border: 2px solid #f1f5f9;
        margin-bottom: 1rem;
        cursor: grab;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        user-select: none;
    }

    .catalog-card:hover {
        border-color: var(--accent-blue);
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.1);
    }

    .catalog-card:active {
        cursor: grabbing;
        transform: scale(0.95);
    }

    .catalog-card.dragging {
        opacity: 0.5;
        border: 2px dashed var(--accent-blue);
        background: #f0f7ff;
    }

    .drop-zone-highlight {
        background: #eef2ff !important;
        border: 3px dashed var(--accent-blue) !important;
        border-radius: 28px;
    }

    .stock-badge-catalog {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 0.65rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 50px;
        background: #f1f5f9;
        color: var(--slate-muted);
    }

    .catalog-search-wrapper {
        position: sticky;
        top: 0;
        z-index: 5;
        background: white;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid #f1f5f9;
    }

    /* SCANNER MODE STYLES */
    .scanner-mode-bar {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 15px;
        max-width: 500px;
        transition: all 0.3s;
    }

    body.scan-mode-active .scanner-mode-bar {
        background: rgba(34, 197, 94, 0.15);
        border-color: rgba(34, 197, 94, 0.4);
        box-shadow: 0 0 20px rgba(34, 197, 94, 0.2);
    }

    .scanner-input-wrapper {
        position: relative;
        flex-grow: 1;
    }

    .scanner-input-wrapper i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.5);
    }

    .scanner-input {
        background: rgba(0, 0, 0, 0.2) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: white !important;
        border-radius: 10px !important;
        padding-left: 35px !important;
        height: 40px !important;
        font-family: monospace;
        letter-spacing: 1px;
    }

    .scanner-input:focus {
        background: rgba(0, 0, 0, 0.4) !important;
        border-color: #22c55e !important;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.3) !important;
    }

    .scanner-pulse {
        width: 10px;
        height: 10px;
        background: #22c55e;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 rgba(34, 197, 94, 0.4);
        animation: pulse-green-mini 2s infinite;
    }

    @keyframes pulse-green-mini {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 6px rgba(34, 197, 94, 0);
        }

        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
        }
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
        border-left: 5px solid #6366f1;
        display: flex;
        align-items: center;
        gap: 12px;
        transform: translateX(120%);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        pointer-events: auto;
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

    .scan-toast.info {
        border-left-color: #3b82f6;
    }

    @keyframes rowHighlight {
        0% {
            background: rgba(34, 197, 94, 0);
        }

        50% {
            background: rgba(34, 197, 94, 0.2);
        }

        100% {
            background: rgba(34, 197, 94, 0);
        }
    }

    .row-highlight-blink {
        animation: rowHighlight 1s ease-in-out infinite;
        border-left: 4px solid #22c55e !important;
    }

    .loading-input {
        opacity: 0.5;
        pointer-events: none;
        background: #f8fafc url('data:image/svg+xml;charset=utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><circle cx="50" cy="50" fill="none" stroke="%236366f1" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138" transform="rotate(77.3482 50 50)"><animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"/></circle></svg>') no-repeat center right 5px;
        background-size: 15px;
    }
</style>

<div style="height: 35px;"></div>
<div class="container-fluid px-5 mb-4">
    <div class="d-flex align-items-center justify-content-between bg-white p-3 rounded-4 shadow-sm border">
        <div class="d-flex align-items-center gap-3">
            <div style="width:40px; height:40px; background:#eff6ff; color:#3b82f6; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">
                <i class="fa fa-barcode"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-dark">Mode Cepat (Scanner)</h6>
                <p class="mb-0 small text-muted">Scan barcode untuk memilih material & input Qty</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" id="scanModeToggle" checked>
                <label class="form-check-label fw-bold small text-dark" style="cursor:pointer;" for="scanModeToggle">AKTIF</label>
            </div>
            <div class="position-relative" style="width: 250px;">
                <i class="fa fa-barcode position-absolute" style="left:12px; top:50%; transform:translateY(-50%); color:#94a3b8;"></i>
                <input type="text" id="barcodeInput" class="form-control form-control-sm bg-light border-0 ps-4" style="border-radius:8px; height:36px;" placeholder="Scan barcode di sini...">
            </div>
            <div id="scannerStatusIndicator" class="small fw-800 text-success d-none">
                <span class="scanner-pulse"></span> READY
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-5">
    <?php $this->load->view('admin/warehouse/components/legacy_drafts', ['legacy_drafts' => $legacy_drafts ?? [], 'draft_kind' => 'dispatch']); ?>
    <!-- 6-STEP CORE WORKFLOW (SOP) -->
    <div id="workflowContainer">
        <?php $this->load->view('admin/warehouse/components/sop_stepper', ['active_step' => 4]); ?>
    </div>

    <!-- Visual Bridge & Control Center -->
    <form action="<?= base_url('warehouse/dispatch_finalize') ?>" method="post" id="formDispatch">
        <div class="transfer-bridge">
            <div class="bridge-node node-source" style="flex: 1;">
                <span class="node-label">Persediaan Sumber</span>
                <div class="node-name" style="font-size: 1rem;"><i class="fa fa-warehouse me-2 text-primary"></i>Gudang
                    Utama</div>
            </div>

            <div class="bridge-arrow"><i class="fa fa-arrow-right"></i></div>

            <div class="bridge-control-card">
                <div class="bridge-input-group" style="flex: 1;">
                    <label>Tanggal Dispatch</label>
                    <input type="date" class="form-control form-control-premium border-0 py-1" name="pengambilan_tgl"
                        required value="<?= date('Y-m-d') ?>" style="background: transparent; font-size: 0.9rem;">
                </div>

                <div class="bridge-input-group" style="flex: 2;">
                    <label>Gudang Tujuan <span class="text-danger">*</span></label>
                    <select class="form-select form-control-premium border-0 py-1 select2" name="gudang_id" required
                        style="background: transparent; font-size: 0.9rem;">
                        <option value="">-- Pilih Lokasi Target --</option>
                        <?php foreach ($warehouses as $w):
                            if ($w->gudang_id == 1)
                                continue; ?>
                            <option value="<?= $w->gudang_id ?>"><?= $w->gudang_nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="ms-auto">
                    <button type="submit" class="btn-execute" <?= empty($temp_items) ? 'disabled' : '' ?>>
                        <i class="fa fa-paper-plane"></i> <span>EXECUTE</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="row g-4 mb-5">
        <!-- Catalog Inventory (Source) -->
        <div class="col-lg-4">
            <div class="content-card">
                <div class="catalog-search-wrapper">
                    <h5 class="card-title-premium mb-3"><i class="fa fa-folder-open text-primary"></i> Catalog Sumber
                    </h5>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fa fa-search text-muted"></i></span>
                        <input type="text" id="catalogSearch" class="form-control form-control-premium border-0"
                            placeholder="Cari material...">
                    </div>
                </div>

                <div class="catalog-container" id="sourceCatalog">
                    <?php foreach ($materials_ready as $m): ?>
                        <?php 
                            // Menghitung stok dalam satuan gudang (Misal: 1000 lembar / 500 = 2 Rim)
                            $stok_gudang = ($m->konversi > 0) ? ($m->jumlah_stok / $m->konversi) : $m->jumlah_stok;
                        ?>
                        <div class="catalog-card draggit" draggable="true" data-id="<?= $m->material_id ?>"
                            data-nama="<?= htmlspecialchars($m->bahan_nama) ?>"
                            data-varian="<?= htmlspecialchars($m->varian) ?>" 
                            data-stock="<?= $stok_gudang ?>"
                            data-bahan="<?= $m->bahan_id ?>">
                            
                            <div class="stock-badge-catalog">STOK:
                                <?php echo number_format($stok_gudang, 0); ?> 
                                <?= $m->satuan_nama ?>
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-soft-blue p-2 rounded-3 text-primary">
                                    <i class="fas fa-box fa-lg"></i>
                                </div>
                                <div>
                                    <div class="fw-900 text-dark small text-uppercase"><?= $m->bahan_nama ?></div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge bg-soft-info text-info px-2 py-1"
                                            style="font-size: 0.6rem; letter-spacing: 0.5px;">VARIAN:
                                            <?= $m->varian ?></span>
                                        <span class="badge bg-soft-success text-success px-2 py-1"
                                            style="font-size: 0.6rem; letter-spacing: 0.5px;">SATUAN:
                                            <?= $m->satuan_gudang ?>
                                        </span>
                                        <span class="text-muted font-monospace"
                                            style="font-size: 0.6rem;">[<?= $m->barcode ?>]</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-800 text-dark mb-3 small"><i class="fa fa-history me-1 opacity-50"></i> Recent
                        Dispatches</h6>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($recent_dispatches, 0, 5) as $rd): ?>
                            <div class="list-group-item px-0 border-0 bg-transparent py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="small fw-800 text-dark"><?= $rd->gudang_nama ?></div>
                                    <div class="small text-muted font-monospace" style="font-size: 0.65rem;">
                                        <?= date('d/m/y', strtotime($rd->pengambilan_tgl)) ?>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="small text-muted italic" style="font-size: 0.65rem;">
                                        <?= $rd->item_count ?> types &bull; <?= number_format($rd->qty_total) ?> units
                                    </div>
                                    <a href="<?= base_url('warehouse/print_dispatch/' . $rd->pengambilan_id) ?>"
                                        target="_blank" class="text-primary small fw-bold text-decoration-none">
                                        <i class="fa fa-print me-1"></i>PRINT
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Picklist Table (Drop Zone) -->
        <div class="col-lg-8">
            <div id="transferDropZone" class="content-card h-100 transition-all">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title-premium mb-0"><i class="fa fa-clipboard-list text-primary"></i> Picklist
                        Transfer Hub</h5>
                    <div class="small text-muted fw-bold"><i class="fas fa-mouse-pointer me-1"></i> DRAG MATERIAL KE
                        SINI</div>
                </div>

                <div class="table-responsive">
                    <table class="table table-premium align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="60">NO</th>
                                <th>DETAIL MATERIAL</th>
                                <th class="text-center">QTY TRANSFER</th>
                                <th class="text-center">SATUAN</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($temp_items)): ?>
                                <tr id="emptyPicklistState">
                                    <td colspan="5" class="text-center py-5">
                                        <div class="opacity-25 mb-3"><i class="fa fa-hand-rock fa-4x"></i></div>
                                        <h6 class="fw-900 text-muted">Belum ada barang dipilih</h6>
                                        <p class="text-muted small">Tarik (drag) material dari kiri untuk memulai
                                            pemindahan.</p>
                                    </td>
                                </tr>
                            <?php else:
                                $no = 1;
                                foreach ($temp_items as $dt): ?>
                                    <tr id="temp-row-<?= $dt->material_id ?>" class="picklist-row">
                                        <td class="text-muted fw-bold"><?= $no++ ?></td>
                                        <td>
                                            <div class="fw-800 text-dark"><?= $dt->bahan_nama ?></div>
                                            <div class="small text-muted mt-1 font-monospace"><?= $dt->varian ?></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="input-group input-group-sm mx-auto" style="width: 120px;">
                                                <input type="number" step="any"
                                                    class="form-control form-control-sm text-center fw-900 update-qty-inline"
                                                    data-id="<?= $dt->temp_id ?>" id="qty-input-<?= $dt->material_id ?>"
                                                    value="<?= $dt->qty ?>"
                                                    style="border-radius: 8px 0 0 8px; border: 1px solid #e2e8f0;">
                                                <span class="input-group-text bg-light text-muted small fw-bold"
                                                    style="border-radius: 0 8px 8px 0; border: 1px solid #e2e8f0; border-left: 0; font-size: 0.6rem;"><?= $dt->satuan_nama ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center fw-800 text-secondary small uppercase">
                                            <span class="badge bg-light text-dark px-2"><?= $dt->satuan_nama ?></span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('warehouse/dispatch_delete_item/' . $dt->temp_id) ?>"
                                                class="btn btn-sm btn-light border text-danger rounded-pill px-3">
                                                <i class="fa fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center pb-5 opacity-50">
        <div class="d-flex justify-content-center align-items-center gap-2 small fw-bold text-muted">
            <i class="fa fa-shield-alt text-primary"></i>
            Automated Stock-Check System Active
        </div>
    </div>
</div>

<!-- Modal Add Item -->
<div class="modal fade" id="modalAddItem" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('warehouse/dispatch_add_item') ?>" method="post"
            class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header px-4 py-4" style="background: var(--primary-gradient); color: white;">
                <h5 class="modal-title fw-900"><i class="fa fa-layer-group me-2"></i>Staging Material</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="mb-4">
                    <label class="form-label-premium">Pilih Material (Stok Ready)</label>
                    <select name="material_id" id="select_material"
                        class="form-select form-control-premium select2-modal" required>
                        <option value="">-- Cari Nama Material --</option>
                        <?php foreach ($materials_ready as $m): ?>
                            <?php 
                                $stok_gudang = ($m->konversi > 0) ? ($m->jumlah_stok / $m->konversi) : $m->jumlah_stok;
                            ?>
                            <option value="<?= $m->material_id ?>" data-bahan="<?= $m->bahan_id ?>"
                                data-max="<?= $stok_gudang ?>">
                                <?= $m->bahan_nama ?> [<?= $m->varian ?>] &bull; Stok:
                                <?= number_format($stok_gudang, 2) ?>     <?= $m->satuan_nama ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="bahan_id" id="post_bahan_id">
                </div>

                <div class="mb-4">
                    <label class="form-label-premium text-primary">Kuantitas Transfer</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="input-group">
                                <input type="number" step="1" id="d_input_whole"
                                    class="form-control form-control-premium border-primary" placeholder="0" required>
                                <span class="input-group-text bg-light fw-800 d-label-whole">Unit</span>
                            </div>
                        </div>
                        <div class="col-6" id="d_pieces_container" style="display:none;">
                            <div class="input-group">
                                <input type="number" step="1" id="d_input_pieces"
                                    class="form-control form-control-premium border-info" placeholder="0">
                                <span class="input-group-text bg-light fw-800 d-label-pieces">Pcs</span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="qty" id="d_qty_final">
                    <div id="d_calc_preview" class="small text-muted mt-2 fw-bold" style="display:none;">
                        Total Transfer: <span class="text-primary">0</span> <span class="d-label-whole">Unit</span>
                        <span class="ms-2 text-danger" id="d_max_warning" style="display:none;">(Melebihi Stok!)</span>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label-premium">Internal Notes / Remarks</label>
                    <textarea name="keterangan" class="form-control form-control-premium" rows="2"
                        placeholder="Tulis catatan jika diperlukan..."></textarea>
                </div>
            </div>
            <div class="modal-footer p-4 pt-0 border-0">
                <button type="button" class="btn btn-light btn-premium px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-premium btn-premium-primary px-4 fw-900">Stage Material</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Init Select2
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

        $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
        $('.select2-modal').select2({ theme: 'bootstrap-5', dropdownParent: $('#modalAddItem'), width: '100%' });

        // CATALOG SEARCH
        $('#catalogSearch').on('keyup', function () {
            let val = $(this).val().toLowerCase();
            $('.catalog-card').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
            });
        });

        // DRAG AND DROP LOGIC
        const draggables = document.querySelectorAll('.draggit');
        const dropZone = document.getElementById('transferDropZone');

        draggables.forEach(draggable => {
            draggable.addEventListener('dragstart', () => {
                draggable.classList.add('dragging');
            });

            draggable.addEventListener('dragend', () => {
                draggable.classList.remove('dragging');
            });
        });

        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('drop-zone-highlight');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('drop-zone-highlight');
        });

        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('drop-zone-highlight');

            const dragging = document.querySelector('.dragging');
            if (dragging) {
                const materialId = dragging.getAttribute('data-id');
                const bahanId = dragging.getAttribute('data-bahan');
                const materialName = dragging.getAttribute('data-nama');

                // QUICK ADD AJAX
                $.ajax({
                    url: '<?= base_url("warehouse/ajax_dispatch_add_item") ?>',
                    type: 'POST',
                    data: {
                        material_id: materialId,
                        bahan_id: bahanId,
                        qty: 1
                    },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status == 'success') {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 1000,
                                timerProgressBar: true
                            });

                            Toast.fire({
                                icon: 'success',
                                title: materialName + ' +1'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Stok Limit', res.message, 'warning');
                        }
                    }
                });
            }
        });

        let d_global_meta = { konversi: 1, satuan_gudang: 'Unit', satuan_produksi: 'Pcs', max: 0 };

        $('#select_material').on('change', function () {
            const opt = $(this).find(':selected');
            const mId = opt.val();
            if (mId) {
                $('#post_bahan_id').val(opt.data('bahan'));
                const maxStok = parseFloat(opt.data('max')) || 0;

                $.ajax({
                    url: '<?= base_url('warehouse/ajax_get_current_stock') ?>',
                    type: 'POST',
                    data: { material_id: mId, gudang_id: 1 }, // Sources from Main
                    dataType: 'json',
                    success: function (res) {
                        d_global_meta = res;
                        d_global_meta.max = maxStok;

                        $('.d-label-whole').text(res.satuan_gudang);
                        $('.d-label-pieces').text(res.satuan_produksi);

                        if (res.konversi > 1) {
                            $('#d_pieces_container').fadeIn();
                        } else {
                            $('#d_pieces_container').hide();
                        }
                        $('#d_calc_preview').fadeIn();
                        calculateDispatchQty();
                    }
                });
            } else {
                $('#post_bahan_id').val('');
                $('#d_calc_preview').hide();
            }
        });

        function calculateDispatchQty() {
            const whole = parseFloat($('#d_input_whole').val()) || 0;
            const pieces = parseFloat($('#d_input_pieces').val()) || 0;
            const konversi = d_global_meta.konversi || 1;

            const finalQty = whole + (pieces / konversi);
            $('#d_qty_final').val(finalQty);
            $('#d_calc_preview span.text-primary').text(finalQty.toFixed(3));

            if (finalQty > d_global_meta.max) {
                $('#d_max_warning').fadeIn();
                $('#d_input_whole').addClass('is-invalid');
            } else {
                $('#d_max_warning').hide();
                $('#d_input_whole').removeClass('is-invalid');
            }
        }

        $('#d_input_whole, #d_input_pieces').on('input', calculateDispatchQty);

        // Confirmation on launch
        $('#formDispatch').on('submit', function (e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Launch Distribution?',
                text: "Confirm dispatching staged materials to target location.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1a1c23',
                confirmButtonText: 'Yes, Launch Transmission',
                cancelButtonText: 'Review First'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });

        // Auto-Open Modal from Smart Scanner / URL
        const urlParams = new URLSearchParams(window.location.search);
        const autoMaterialId = urlParams.get('material_id');
        if (autoMaterialId) {
            // Clean URL
            window.history.replaceState({}, document.title, window.location.pathname);

            // Initialize modal
            const modalEl = document.getElementById('modalAddItem');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            // Set value and trigger change for Select2
            setTimeout(() => {
                $('#select_material').val(autoMaterialId).trigger('change');
            }, 500);
        }

        // --- BARCODE SCANNER LOGIC ---
        const scanSound = document.getElementById('scanSound');

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

        // Initialize Scan Mode
        if ($('#scanModeToggle').is(':checked')) {
            $('body').addClass('scan-mode-active');
            $('#scannerStatusIndicator').removeClass('d-none');
            $('#barcodeInput').focus();
        }

        $('#scanModeToggle').on('change', function () {
            if ($(this).is(':checked')) {
                $('body').addClass('scan-mode-active');
                $('#scannerStatusIndicator').removeClass('d-none');
                $('#barcodeInput').focus();

                Swal.fire({
                    title: 'Mode Scanner Aktif',
                    text: 'Scan barcode akan otomatis menambahkan 1 item ke picklist.',
                    icon: 'info',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                $('body').removeClass('scan-mode-active');
                $('#scannerStatusIndicator').addClass('d-none');
            }
        });

        $('#barcodeInput').on('keypress', function (e) {
            if (e.which == 13) {
                e.preventDefault(); // STOP FORM SUBMISSION
                let barcode = $(this).val();
                if (barcode && !window.isScanning) {
                    processBarcodeScan(barcode);
                }
                $(this).val('').focus();
            }
        });

        window.isScanning = false;

        function processBarcodeScan(barcode) {
            if (window.isScanning) return;
            window.isScanning = true;
            $('#barcodeInput').prop('disabled', true);

            $.ajax({
                url: '<?= base_url('warehouse/ajax_lookup_barcode') ?>',
                type: 'GET',
                data: { barcode: barcode },
                dataType: 'json',
                success: function (res) {
                    if (res.success && res.data && res.data.material_id) {
                        try { scanSound.play().catch(e => { }); } catch (err) { }

                        // Always call add item to handle increment logic in backend
                        showScanFeedback("Memperbarui...", res.data.item_name, 'info');
                        $.ajax({
                            url: '<?= base_url("warehouse/ajax_dispatch_add_item") ?>',
                            type: 'POST',
                            data: {
                                material_id: res.data.material_id,
                                bahan_id: res.data.bahan_id || 0,
                                qty: 1
                            },
                            dataType: 'json',
                            success: function (addRes) {
                                if (addRes.status == 'success') {
                                    showScanFeedback("Berhasil", res.data.item_name, 'success');
                                    location.reload();
                                } else {
                                    // Use SWEETALERT for errors to be UNMISSABLE
                                    Swal.fire({
                                        title: 'Gagal Scan',
                                        text: addRes.message,
                                        icon: 'error',
                                        confirmButtonText: 'Ok, Mengerti'
                                    }).then(() => {
                                        window.isScanning = false;
                                        $('#barcodeInput').prop('disabled', false).val('').focus();
                                    });
                                }
                            },
                            error: function () {
                                Swal.fire('Error', 'Gagal memproses ke server', 'error');
                                window.isScanning = false;
                                $('#barcodeInput').prop('disabled', false).focus();
                            }
                        });
                    } else {
                        showScanFeedback("Tidak Ditemukan", "Barcode tidak valid", 'error');
                        window.isScanning = false;
                        $('#barcodeInput').prop('disabled', false).focus();
                    }
                },
                error: function () {
                    showScanFeedback("Error", "Gagal mencari barcode", 'error');
                    window.isScanning = false;
                    $('#barcodeInput').prop('disabled', false).focus();
                }
            });
        }

        // --- INLINE QTY UPDATE ---
        $(document).on('change', '.update-qty-inline', function () {
            const id = $(this).data('id');
            const qty = $(this).val();
            const $input = $(this);

            $input.addClass('loading-input');
            $.ajax({
                url: '<?= base_url("warehouse/ajax_dispatch_update_qty") ?>',
                type: 'POST',
                data: { temp_id: id, qty: qty },
                dataType: 'json',
                success: function (res) {
                    $input.removeClass('loading-input');
                    if (res.status == 'success') {
                        $input.addClass('border-success');
                        setTimeout(() => $input.removeClass('border-success'), 1000);

                        // Focus back to scanner after manual entry if scanner mode is on
                        if ($('#scanModeToggle').is(':checked')) {
                            setTimeout(() => $('#barcodeInput').focus(), 500);
                        }
                    } else {
                        Swal.fire('Gagal', res.message, 'error').then(() => {
                            location.reload();
                        });
                    }
                }
            });
        });

        // Keep focus on barcode input if mode active
        $(document).on('click', function () {
            if ($('#scanModeToggle').is(':checked')) {
                // Only auto-focus if not clicking another input
                setTimeout(() => {
                    if (!$(document.activeElement).is('input, select, textarea, button')) {
                        $('#barcodeInput').focus();
                    }
                }, 100);
            }
        });

    });
</script>

<?php if ($this->session->flashdata('last_dispatch_id')): ?>
    <script>
        window.addEventListener('load', function () {
            console.log('Dispatch ID detected: <?= $this->session->flashdata("last_dispatch_id") ?>');
            Swal.fire({
                title: 'Pengiriman Berhasil!',
                text: 'Material telah dipindahkan. Apakah Anda ingin mencetak Surat Jalan sekarang?',
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#1a1c23',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa fa-print me-2"></i>Cetak Surat Jalan',
                cancelButtonText: 'Tutup',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open('<?= base_url("warehouse/print_dispatch/" . $this->session->flashdata('last_dispatch_id')) ?>', '_blank');
                }
            });
        });
    </script>
<?php endif; ?>

<?php $this->load->view('admin/warehouse/components/v_modal_dispatch_guide'); ?>

<!-- FEEDBACK ASSETS -->
<div id="scanFeedbackOverlay"></div>
<audio id="scanSound" src="<?= base_url('assets/sounds/beep.mp3') ?>" preload="auto"></audio>