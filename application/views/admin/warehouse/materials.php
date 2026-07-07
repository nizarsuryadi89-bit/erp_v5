<?php
// FILE: application/views/admin/warehouse/materials.php
// Warehouse Master Materials View
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Master Bahan & Material -
        <?= $perusahaan ?>
    </title>

    <!-- PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - MATERIALS MASTER -->
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

        /* Executive Hero Header */
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
            color: white;
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
            border: 1px solid #f1f5f9;
        }

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
            border-bottom: 1px solid #f1f5f9;
            color: var(--slate-dark);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .table-premium tbody tr:hover {
            background-color: #fcfdfe;
        }

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

        /* Badges */
        .badge-premium {
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .bg-soft-blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .bg-soft-info {
            background: #ecfeff;
            color: #06b6d4;
        }

        .bg-soft-emerald {
            background: #ecfdf5;
            color: #10b981;
        }

        .bg-soft-amber {
            background: #fffbeb;
            color: #f59e0b;
        }

        /* Scanner Mode Premium Enhancements */
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
            width: 10px;
            height: 10px;
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

        .scanning-focus {
            background-color: #fef9c3 !important;
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.2) !important;
        }
    </style>
</head>

<body>

    <div class="warehouse-header-section">
        <div class="container-fluid px-5">
            <div class="row align-items-start">
                <div class="col-md-6">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fa fa-database"></i>
                        </div>
                        <div class="header-text">
                            <p class="header-subtitle">Database Master</p>
                            <h1 class="header-title">Master Bahan & Material</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-end header-actions d-flex justify-content-end align-items-center gap-2">
                    <button id="btnBulkPrintBarcode"
                        class="btn btn-info btn-premium rounded-pill px-3 shadow-sm text-white">
                        <i class="fas fa-print me-1"></i>BARCODE
                    </button>
                    <button id="btnToggleWorkflow" class="btn btn-outline-light btn-premium rounded-pill px-3">
                        <i class="fa fa-eye-slash me-1"></i>Hide
                    </button>
                    <button id="btnBulkGenerateBarcode" class="btn btn-warning btn-premium rounded-pill px-3 shadow-sm">
                        <i class="fas fa-magic me-1"></i>GENERATE
                    </button>
                    <button class="btn btn-outline-light btn-premium rounded-pill px-3" data-bs-toggle="modal"
                        data-bs-target="#modalMaterialsGuide">
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
        <!-- Stats Overview -->
        <div class="stats-container">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-header">
                            <div class="metric-icon bg-soft-blue">
                                <i class="fa fa-boxes"></i>
                            </div>
                            <div class="metric-info">
                                <p>Bahan Baku</p>
                                <h3 id="statBahanProduksi">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-header">
                            <div class="metric-icon bg-soft-info">
                                <i class="fa fa-tools"></i>
                            </div>
                            <div class="metric-info">
                                <p>Operasional</p>
                                <h3 id="statBahanOperasional">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-header">
                            <div class="metric-icon bg-soft-emerald">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <div class="metric-info">
                                <p>Barang Jadi</p>
                                <h3 id="statBarangJadi">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-header">
                            <div class="metric-icon bg-soft-amber">
                                <i class="fa fa-database"></i>
                            </div>
                            <div class="metric-info">
                                <p>Total Master</p>
                                <h3 id="statTotalBahan">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6-STEP CORE WORKFLOW (SOP) -->
        <div id="workflowContainer">
            <?php $this->load->view('admin/warehouse/components/v_material_workflow', ['current_step' => 2]); ?>
        </div>

        <!-- Main Content -->

        <div class="content-card mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center flex-grow-1" style="max-width: 800px;">
                    <div class="input-group me-3" style="width: 300px;">
                        <span class="input-group-text bg-transparent border-end-0 border-radius-14">
                            <i class="fa fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control form-control-premium border-start-0" id="searchInput"
                            placeholder="Cari bahan...">
                    </div>

                    <select class="form-select form-control-premium" id="filterJenis" style="width: 200px;">
                        <option value="">Semua Jenis</option>
                        <option value="bahan_baku">Bahan Baku Produksi</option>
                        <option value="bahan_operasional">Barang Operasional</option>
                        <option value="barang_jadi">Barang Jadi</option>
                    </select>
                </div>

                <div class="d-flex align-items-center">
                    <button class="btn btn-light btn-premium" onclick="refreshTable()">
                        <i class="fa fa-sync-alt me-2"></i>Refresh
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-premium" id="bahanBakuTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">NO</th>
                            <th>Nama Induk Item</th>
                            <th>Kategori</th>
                            <th>Unit Dasar (Step 1)</th>
                            <th class="text-center">Jml Varian</th>
                            <th class="text-center" style="width: 250px;">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="small text-muted">Menampilkan <span id="statShowingRange"
                            class="fw-bold text-dark">0-0</span> dari <span id="statTotalBahanBottom"
                            class="fw-bold text-dark">0</span> master bahan.</div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted fw-bold">Tampilkan:</span>
                        <select id="bahanPerPage" class="form-select form-select-sm"
                            style="width: 80px; border-radius: 10px;">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="999">Semua</option>
                        </select>
                    </div>
                </div>
                <div id="bahanPaginationControls" class="pagination-controls d-flex gap-1"></div>
            </div>
        </div>
    </div>


    <!-- Modal Tambah Material (Variants) -->
    <div class="modal fade" id="modalTambahMaterial" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-soft-indigo">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fa fa-cubes me-2 text-primary"></i>Kelola Varian: <span id="materialBahanNama"
                            class="fw-900 border-bottom border-dark border-2"></span>
                        <button type="button" class="btn btn-sm btn-light btn-copy-premium ms-2" id="btnCopyMaterialName" title="Salin Nama Bahan">
                            <i class="fa fa-copy text-muted"></i>
                        </button>
                    </h5>
                    <div class="ms-auto me-3">
                        <span class="badge bg-white text-dark border fw-bold">
                            Satuan Produksi: <span id="baseUnitLabel" class="text-primary">-</span>
                        </span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <!-- Scanner Control Bar -->
                    <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="modalScanModeToggle" checked>
                                <label class="form-check-label fw-900 text-dark small" for="modalScanModeToggle">MODE
                                    SCANNER (AUTO-SAVE & FOCUS)</label>
                            </div>
                            <div id="modalScannerIndicator" class="small fw-800 text-success">
                                <span class="scanner-active-pulse"></span> SIAP PINDAI
                            </div>
                        </div>
                        <div class="text-muted small fw-bold">
                            <i class="fa fa-info-circle me-1"></i> Arahkan kursor ke kolom barcode dan tekan Enter/Scan
                            untuk simpan otomatis.
                        </div>
                    </div>

                    <div id="scanFeedbackOverlay"></div>

                    <div class="row g-0 flex-grow-1" style="height: calc(100vh - 120px);">
                        <div class="col-lg-9 border-end h-100">
                            <div class="p-4 bg-light h-100 d-flex flex-column">
                                <h6
                                    class="fw-bold text-secondary mb-3 text-uppercase small d-flex justify-content-between align-items-center">
                                    Daftar Varian Aktif
                                    <button class="btn btn-sm btn-outline-dark btn-premium" id="btnPrintVariantBulk">
                                        <i class="fa fa-print me-1"></i> Cetak Terpilih
                                    </button>

                                </h6>
                                <div class="table-responsive flex-grow-1" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                                    <table class="table table-hover align-middle mb-0" id="modalMaterialTable">
                                        <thead class="sticky-top shadow-sm" style="background: #f8fafc; z-index: 2;">
                                            <tr>
                                                <th width="5%"><input type="checkbox" class="form-check-input"
                                                        id="checkAllVariants"></th>
                                                <th>Varian</th>
                                                <th>Barcode</th>
                                                <th>Satuan</th>
                                                <th>Konversi</th>
                                                <th>Status</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white"></tbody>
                                    </table>
                                </div>
                                <div id="variantPagination"
                                    class="mt-3 d-flex justify-content-between align-items-center">
                                    <div class="small fw-bold text-muted" id="variantCountInfo">Menampilkan 0 varian</div>
                                    <div class="pagination-controls d-flex gap-1" id="variantPaginationControls"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 h-100 overflow-auto bg-white">
                            <form id="formTambahMaterial" class="p-4 needs-validation" novalidate>
                                <h6 class="fw-bold text-primary mb-4 text-uppercase small"><i
                                        class="fa fa-plus-circle me-2"></i>Tambah Varian Baru</h6>
                                <input type="hidden" name="bahan_id" id="material_bahan_id">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-secondary text-uppercase">Nama Varian
                                        <span class="text-danger">*</span></label>
                                    <input name="varian" type="text" class="form-control form-control-premium" required
                                        placeholder="Contoh: Merah, XL, Box 12...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-secondary text-uppercase">Barcode
                                        (Opsional)</label>
                                    <div class="input-group">
                                        <input name="barcode" type="text"
                                            class="form-control form-control-premium font-monospace"
                                            placeholder="Scan/Input Kode...">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-7">
                                        <label class="form-label fw-bold small text-secondary text-uppercase">Satuan
                                            Stok/Gudang
                                            <span class="text-danger">*</span></label>
                                        <select name="satuan_gudang_id" class="form-select form-control-premium"
                                            required>
                                            <option value="">-- Pilih --</option>
                                            <?php foreach ($satuan as $s): ?>
                                                <option value="<?= $s->satuan_id ?>">
                                                    <?= $s->satuan_nama ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        <label class="form-label fw-bold small text-secondary text-uppercase">Isi
                                            (Konversi)
                                            <span class="text-danger">*</span></label>
                                        <input name="konversi" type="number" class="form-control form-control-premium"
                                            required value="1.00" min="0" step="0.01">
                                    </div>
                                    <div class="col-12 mt-1">
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            <i class="fa fa-info-circle me-1"></i>
                                            1 <span class="label-varian-unit">Varian</span> = <span
                                                class="label-konversi-value">...</span> <span
                                                class="label-base-unit">Unit Produksi</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-secondary text-uppercase">Status</label>
                                    <select name="status" class="form-select form-control-premium">
                                        <option value="1">Aktif</option>
                                        <option value="0">Nonaktif</option>
                                    </select>
                                </div>
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-premium btn-premium-primary"
                                        id="btnSubmitMaterial">
                                        <i class="fa fa-plus me-1"></i> Tambahkan Varian
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Material -->
    <div class="modal fade" id="modalEditMaterial" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form id="formEditMaterial" class="needs-validation" novalidate>
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Edit Material</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="material_id" id="edit_material_id">
                        <input type="hidden" name="bahan_id" id="edit_material_bahan_id"> <!-- required for refresh -->

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary text-uppercase">Varian</label>
                            <input name="varian" id="edit_varian" type="text" class="form-control form-control-premium"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary text-uppercase">Barcode</label>
                            <input name="barcode" id="edit_material_barcode" type="text"
                                class="form-control form-control-premium font-monospace">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-8">
                                <label class="form-label fw-bold small text-secondary text-uppercase">Satuan</label>
                                <select name="satuan_gudang_id" id="edit_satuan_gudang_id"
                                    class="form-select form-control-premium" required>
                                    <?php foreach ($satuan as $s): ?>
                                        <option value="<?= $s->satuan_id ?>">
                                            <?= $s->satuan_nama ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-bold small text-secondary text-uppercase">Konversi</label>
                                <input name="konversi" id="edit_konversi" type="number"
                                    class="form-control form-control-premium" required min="0" step="0.01">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary text-uppercase">Status</label>
                            <select name="status" id="edit_status" class="form-select form-control-premium">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light btn-premium px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning btn-premium px-4 fw-bold shadow-sm"
                            id="btnSubmitEditMaterial">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Update Barcode -->
    <div class="modal fade" id="modalUpdateBarcode" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Update Barcode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <form id="formUpdateBarcode">
                        <input type="hidden" id="update_barcode_bahan_id">
                        <div class="mb-4">
                            <div class="p-4 bg-white rounded-3 mb-3 border d-flex flex-column align-items-center justify-content-center"
                                style="min-height: 150px;" id="barcodePreviewArea">
                                <svg id="barcodeRender"></svg>
                                <div id="noBarcodeText" class="text-muted small">Belum ada barcode</div>
                            </div>
                            <!-- Tools for Barcode Image -->
                            <div class="d-flex justify-content-center gap-2 mb-3" id="barcodeTools"
                                style="display:none;">
                                <button type="button" class="btn btn-sm btn-outline-dark btn-premium"
                                    id="btnDownloadBarcode">
                                    <i class="fa fa-download me-1"></i> Download PNG
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-dark btn-premium"
                                    id="btnPrintSingleBarcode">
                                    <i class="fa fa-print me-1"></i> Print
                                </button>
                            </div>
                        </div>
                        <div class="input-group mb-4">
                            <input type="text"
                                class="form-control form-control-premium text-center fw-900 font-monospace"
                                id="update_barcode_value" placeholder="SCAN OR TYPE BARCODE"
                                style="font-size: 1.25rem;">
                            <button class="btn btn-outline-secondary btn-premium border-start-0" type="button"
                                id="btnGenerateBarcode" title="Generate Random">
                                <i class="fa fa-magic"></i>
                            </button>
                        </div>
                        <div class="d-grid">
                            <button type="button" class="btn btn-premium btn-premium-primary py-3"
                                id="btnSubmitUpdateBarcode">
                                <i class="fa fa-save me-2"></i>Simpan Barcode
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        $(document).ready(function () {
            const controllerUrl = '<?= base_url('warehouse') ?>';

            let bahanData = [];
            let bahanCurrentPage = 1;

            function updateStats() {
                // Calculation on raw data
                let bahanBaku = bahanData.filter(i => i.jenis_barang === 'bahan_baku').length;
                let bahanOperasional = bahanData.filter(i => i.jenis_barang === 'bahan_operasional').length;
                let barangJadi = bahanData.filter(i => i.jenis_barang === 'barang_jadi').length;

                $('#statBahanProduksi').text(bahanBaku);
                $('#statBahanOperasional').text(bahanOperasional);
                $('#statBarangJadi').text(barangJadi);
                $('#statTotalBahan, #statTotalBahanBottom').text(bahanData.length);
            }

            function refreshTable() {
                $('#bahanBakuTable tbody').html('<tr><td colspan="7" class="text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i></td></tr>');

                $.getJSON(`${controllerUrl}/get_bahan_baku_json`, function (res) {
                    if (res.status === 'success') {
                        bahanData = res.data;
                        bahanCurrentPage = 1;
                        updateStats();
                        renderBahanTable();
                    }
                });
            }

            function renderBahanTable() {
                const tbody = $('#bahanBakuTable tbody').empty();
                const search = $('#searchInput').val().toLowerCase();
                const jenis = $('#filterJenis').val();
                const perPage = parseInt($('#bahanPerPage').val()) || 25;

                // Filtering
                let filtered = bahanData.filter(item => {
                    const matchesSearch = !search || item.bahan_nama.toLowerCase().includes(search) || item.bahan_id.toString().includes(search);
                    const matchesJenis = !jenis || item.jenis_barang === jenis;
                    return matchesSearch && matchesJenis;
                });

                const total = filtered.length;
                if (total === 0) {
                    tbody.append('<tr><td colspan="7" class="text-center py-5 text-muted">Data tidak ditemukan.</td></tr>');
                    $('#statShowingRange').text('0-0');
                    $('#bahanPaginationControls').empty();
                    return;
                }

                // Pagination
                const totalPages = Math.ceil(total / perPage);
                if (bahanCurrentPage > totalPages) bahanCurrentPage = totalPages;
                if (bahanCurrentPage < 1) bahanCurrentPage = 1;

                const start = (bahanCurrentPage - 1) * perPage;
                const end = Math.min(start + perPage, total);
                const pageData = filtered.slice(start, end);

                pageData.forEach((item, i) => {
                    tbody.append(`
                        <tr data-kode="${item.jenis_barang}" data-status="${item.active}">
                            <td class="text-center fw-bold text-muted">${start + i + 1}</td>
                            <td>
                                <div class="fw-900 text-dark">${item.bahan_nama}</div>
                                <small class="text-muted fw-bold" style="font-size: 0.65rem;">ID: #${item.bahan_id}</small>
                            </td>
                            <td><span class="badge badge-premium ${getJenisClass(item.jenis_barang)}">${getJenisLabel(item.jenis_barang)}</span></td>
                            <td class="fw-bold text-secondary small">${item.satuan_nama || '-'}</td>
                            <td class="text-center">
                                <span class="badge bg-soft-primary text-primary fw-bold" style="font-size: 0.85rem;">${item.jml_varian || 0} Varian</span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary btn-premium px-4 btn-manage-variants" data-id="${item.bahan_id}" data-nama="${item.bahan_nama}">
                                    <i class="fa fa-cubes me-1"></i> ATUR VARIAN & KONVERSI
                                </button>
                            </td>
                        </tr>
                    `);
                });

                // Update info
                $('#statShowingRange').text(`${start + 1}-${end}`);
                $('#statTotalBahanBottom').text(total);

                // Render controls
                renderBahanPaginationControls(totalPages);
            }

            function renderBahanPaginationControls(totalPages) {
                const container = $('#bahanPaginationControls').empty();
                if (totalPages <= 1) return;

                const maxVisible = 5;
                let startPage = Math.max(1, bahanCurrentPage - 2);
                let endPage = Math.min(totalPages, startPage + maxVisible - 1);

                if (endPage - startPage < maxVisible - 1) {
                    startPage = Math.max(1, endPage - maxVisible + 1);
                }

                // Prev
                container.append(`<button class="btn btn-sm btn-outline-light text-dark page-bahan-btn" data-page="${bahanCurrentPage - 1}" ${bahanCurrentPage === 1 ? 'disabled' : ''}><i class="fa fa-chevron-left"></i></button>`);

                if (startPage > 1) {
                    container.append(`<button class="btn btn-sm btn-outline-light text-dark page-bahan-btn" data-page="1">1</button>`);
                    if (startPage > 2) container.append(`<span class="px-2 text-muted">...</span>`);
                }

                for (let i = startPage; i <= endPage; i++) {
                    const active = i === bahanCurrentPage ? 'btn-primary text-white' : 'btn-outline-light text-dark';
                    container.append(`<button class="btn btn-sm ${active} page-bahan-btn" data-page="${i}">${i}</button>`);
                }

                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) container.append(`<span class="px-2 text-muted">...</span>`);
                    container.append(`<button class="btn btn-sm btn-outline-light text-dark page-bahan-btn" data-page="${totalPages}">${totalPages}</button>`);
                }

                // Next
                container.append(`<button class="btn btn-sm btn-outline-light text-dark page-bahan-btn" data-page="${bahanCurrentPage + 1}" ${bahanCurrentPage === totalPages ? 'disabled' : ''}><i class="fa fa-chevron-right"></i></button>`);
            }

            $(document).on('click', '.page-bahan-btn:not([disabled])', function () {
                bahanCurrentPage = parseInt($(this).data('page'));
                renderBahanTable();
                $('html, body').animate({ scrollTop: $('#bahanBakuTable').offset().top - 100 }, 200);
            });

            $('#bahanPerPage').on('change', function () {
                bahanCurrentPage = 1;
                renderBahanTable();
            });

            function getJenisClass(j) {
                if (j === 'bahan_baku') return 'bg-soft-blue text-primary';
                if (j === 'bahan_operasional') return 'bg-soft-info text-info';
                if (j === 'barang_jadi') return 'bg-soft-emerald text-emerald';
                return 'bg-secondary';
            }

            function getJenisLabel(j) {
                return j.replace('_', ' ').toUpperCase();
            }

            // --- Handlers ---
            $('#searchInput, #filterJenis').on('keyup change', function () {
                bahanCurrentPage = 1;
                renderBahanTable();
            });


            // Copy Material Name Logic
            $('#btnCopyMaterialName').on('click', function () {
                const textToCopy = $('#materialBahanNama').text();
                if (textToCopy) {
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Nama Produk Berhasil Disalin!',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        
                        // Icon Feedback Animation
                        const icon = $(this).find('i');
                        icon.removeClass('fa-copy text-muted').addClass('fa-check text-success');
                        setTimeout(() => {
                            icon.removeClass('fa-check text-success').addClass('fa-copy text-muted');
                        }, 1000);
                    }).catch(err => {
                        console.error('Failed to copy: ', err);
                        // Fallback for older browsers
                        const temp = $("<input>");
                        $("body").append(temp);
                        temp.val(textToCopy).select();
                        document.execCommand("copy");
                        temp.remove();
                        Swal.fire({
                            icon: 'success',
                            title: 'Nama Produk Berhasil Disalin!',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    });
                }
            });

            // Varian / Material Logic
            $(document).on('click', '.btn-manage-variants', function () {
                let id = $(this).data('id');
                let nama = $(this).data('nama');
                $('#materialBahanNama').text(nama);
                $('#material_bahan_id').val(id);
                loadVariants(id);
                $('#modalTambahMaterial').modal('show');
            });

            let variantData = [];
            let variantCurrentPage = 1;
            const variantPerPage = 5;

            function loadVariants(bahan_id) {
                $('#modalMaterialTable tbody').html('<tr><td colspan="7" class="text-center"><i class="fa fa-spinner fa-spin"></i></td></tr>');
                $.getJSON(`${controllerUrl}/get_material_by_bahan_json?bahan_id=${bahan_id}`, function (res) {
                    if (res.status === 'success') {
                        variantData = res.data;
                        variantCurrentPage = 1;

                        // Update Base Unit Context
                        const parent = res.parent;
                        const baseUnitName = parent ? parent.satuan_produksi : '-';
                        $('#baseUnitLabel').text(baseUnitName);
                        $('.label-base-unit').text(baseUnitName);
                        updateConversionHelper($('#modalTambahMaterial'));

                        renderPaginatedVariants();
                    }
                });
            }

            function renderPaginatedVariants() {
                const tbody = $('#modalMaterialTable tbody').empty();
                const total = variantData.length;

                if (total === 0) {
                    tbody.append('<tr><td colspan="7" class="text-center text-muted py-4">Belum ada varian.</td></tr>');
                    $('#variantPagination').hide();
                    return;
                }

                $('#variantPagination').show();
                
                // Hapus pagination, tampilkan semua varian di satu tabel scrollable
                const pageData = variantData;

                pageData.forEach((m, i) => {
                    tbody.append(`
                        <tr>
                            <td><input type="checkbox" class="form-check-input check-variant" value="${m.material_id}"></td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control form-control-sm quick-varian-input fw-bold text-dark" 
                                        value="${m.varian}" 
                                        data-id="${m.material_id}" 
                                        data-old="${m.varian}"
                                        placeholder="Nama Varian"
                                        style="min-width: 150px; border-color: transparent; background-color: transparent;">
                                    <button class="btn btn-outline-primary btn-save-varian" type="button" style="display:none;">
                                        <i class="fa fa-save"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa fa-barcode text-muted"></i></span>
                                    <input type="text" class="form-control form-control-sm quick-barcode-input font-monospace fw-bold" 
                                        value="${m.barcode || ''}" 
                                        data-id="${m.material_id}" 
                                        data-old="${m.barcode || ''}"
                                        placeholder="Scan here..."
                                        style="min-width: 140px;">
                                    <button class="btn btn-outline-success btn-save-barcode" type="button" style="display:none;">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </div>
                            </td>
                            <td>${m.satuan_nama}</td>
                            <td class="fw-bold text-primary">${m.konversi}</td>
                            <td><span class="badge ${m.status == 1 ? 'bg-success' : 'bg-secondary'}">${m.status == 1 ? 'Aktif' : 'Nonaktif'}</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning btn-edit-variant" data-id="${m.material_id}" data-varian="${m.varian}" data-barcode="${m.barcode}" data-satuan="${m.satuan_gudang_id}" data-konversi="${m.konversi}" data-status="${m.status}" data-bahan="${$('#material_bahan_id').val()}"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger btn-delete-variant" data-id="${m.material_id}" data-bahan="${$('#material_bahan_id').val()}"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    `);
                });

                // Update Info
                $('#variantCountInfo').text(`Total ${total} varian`);
                $('#variantPaginationControls').empty(); // Sembunyikan kontrol karena sudah scrollable
            }

            function renderVariantPaginationControls(totalPages) {
                const container = $('#variantPaginationControls').empty();
                if (totalPages <= 1) return;

                // Prev
                container.append(`<button class="btn btn-xs btn-outline-secondary page-variant-btn" data-page="${variantCurrentPage - 1}" ${variantCurrentPage === 1 ? 'disabled' : ''}><i class="fa fa-chevron-left"></i></button>`);

                // Pages
                for (let i = 1; i <= totalPages; i++) {
                    const active = i === variantCurrentPage ? 'btn-primary text-white' : 'btn-outline-secondary';
                    container.append(`<button class="btn btn-xs ${active} page-variant-btn" data-page="${i}">${i}</button>`);
                }

                // Next
                container.append(`<button class="btn btn-xs btn-outline-secondary page-variant-btn" data-page="${variantCurrentPage + 1}" ${variantCurrentPage === totalPages ? 'disabled' : ''}><i class="fa fa-chevron-right"></i></button>`);
            }

            $(document).on('click', '.page-variant-btn:not([disabled])', function () {
                variantCurrentPage = parseInt($(this).data('page'));
                $('#checkAllVariants').prop('checked', false);
                renderPaginatedVariants();
            });

            function updateConversionHelper(modal) {
                const unitName = modal.find('select[name="satuan_gudang_id"] option:selected').text().trim();
                const konversi = modal.find('input[name="konversi"]').val();
                modal.find('.label-varian-unit').text(unitName === '-- Pilih --' ? 'Varian' : unitName);
                modal.find('.label-konversi-value').text(konversi || '0');
            }

            $(document).on('change keyup', '#modalTambahMaterial select[name="satuan_gudang_id"], #modalTambahMaterial input[name="konversi"]', function () {
                updateConversionHelper($('#modalTambahMaterial'));
            });


            $('#formTambahMaterial').submit(function (e) {
                e.preventDefault();
                $.post(`${controllerUrl}/actionAddMaterial`, $(this).serialize(), function (res) {
                    if (res.status === 'success') {
                        loadVariants($('#material_bahan_id').val());
                        $('#formTambahMaterial')[0].reset();
                        Swal.fire({ icon: 'success', title: 'Berhasil', toast: true, position: 'top-end', timer: 3000 });
                    } else { Swal.fire('Error', res.pesan, 'error'); }
                }, 'json');
            });

            $(document).on('click', '.btn-edit-variant', function () {
                $('#edit_material_id').val($(this).data('id'));
                $('#edit_material_bahan_id').val($(this).data('bahan'));
                $('#edit_varian').val($(this).data('varian'));
                $('#edit_material_barcode').val($(this).data('barcode'));
                $('#edit_satuan_gudang_id').val($(this).data('satuan'));
                $('#edit_konversi').val($(this).data('konversi'));
                $('#edit_status').val($(this).data('status'));
                $('#modalEditMaterial').modal('show');
            });

            $('#formEditMaterial').submit(function (e) {
                e.preventDefault();
                $.post(`${controllerUrl}/actionUpdateMaterial`, $(this).serialize(), function (res) {
                    if (res.status === 'success') {
                        $('#modalEditMaterial').modal('hide');
                        loadVariants($('#edit_material_bahan_id').val());
                    } else { Swal.fire('Error', res.pesan, 'error'); }
                }, 'json');
            });

            $(document).on('click', '.btn-delete-variant', function () {
                let id = $(this).data('id');
                let bid = $(this).data('bahan');
                Swal.fire({ title: 'Hapus Varian?', icon: 'warning', showCancelButton: true }).then(r => {
                    if (r.isConfirmed) {
                        $.post(`${controllerUrl}/actionDeleteMaterial`, { material_id: id }, function (res) {
                            if (res.status === 'success') loadVariants(bid);
                            else Swal.fire('Error', res.pesan, 'error');
                        }, 'json');
                    }
                });
            });

            // Barcode
            $(document).on('click', '.btn-update-barcode-trigger', function () {
                $('#update_barcode_bahan_id').val($(this).data('id'));
                $('#update_barcode_value').val($(this).data('barcode'));
                $('#barcodePreview').html($(this).data('barcode') ? `<div class="fw-bold fs-4 font-monospace">${$(this).data('barcode')}</div>` : '<small class="text-muted">No Barcode</small>');
                $('#modalUpdateBarcode').modal('show');
            });

            $('#btnGenerateBarcode').click(function () {
                $('#update_barcode_value').val('BM' + Math.floor(100000 + Math.random() * 900000));
            });

            $('#btnSubmitUpdateBarcode').click(function () {
                let data = { bahan_id: $('#update_barcode_bahan_id').val(), barcode: $('#update_barcode_value').val() };
                $.ajax({
                    url: `${controllerUrl}/actionUpdateBarcode`,
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function (res) {
                        if (res.status === 'success') {
                            $('#modalUpdateBarcode').modal('hide');
                            refreshTable();
                        } else { Swal.fire('Error', res.pesan, 'error'); }
                    }
                });
            });

            // Quick Barcode Update Logic
            const scanSound = new Audio('https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3');

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

            $(document).on('focus', '.quick-barcode-input', function () {
                $(this).select();
                if ($('#modalScanModeToggle').is(':checked')) {
                    $(this).closest('tr').addClass('table-warning');
                    $(this).addClass('scanning-focus');
                }
            });

            $(document).on('blur', '.quick-barcode-input', function () {
                $(this).closest('tr').removeClass('table-warning');
                $(this).removeClass('scanning-focus');
            });

            $(document).on('keyup change', '.quick-barcode-input', function (e) {
                let input = $(this);
                let btn = input.closest('.input-group').find('.btn-save-barcode');

                if (input.val() !== input.data('old')) {
                    btn.fadeIn();
                    input.addClass('border-warning');
                } else {
                    btn.fadeOut();
                    input.removeClass('border-warning');
                }

                if (e.which === 13) {
                    e.preventDefault();
                    saveQuickBarcode(input);
                }
            });

            $(document).on('click', '.btn-save-barcode', function () {
                let input = $(this).closest('.input-group').find('.quick-barcode-input');
                saveQuickBarcode(input);
            });

            function saveQuickBarcode(input) {
                let material_id = input.data('id');
                let new_barcode = input.val();
                let btn = input.closest('.input-group').find('.btn-save-barcode');
                let varianName = input.closest('tr').find('td:eq(1)').text();

                if (!new_barcode) {
                    showScanFeedback("Peringatan", "Barcode tidak boleh kosong", "error");
                    return;
                }

                btn.html('<i class="fa fa-spinner fa-spin"></i>');

                $.post(`${controllerUrl}/actionUpdateMaterialBarcodeOnly`, {
                    material_id: material_id,
                    barcode: new_barcode
                }, function (res) {
                    if (res.status === 'success') {
                        try { scanSound.play().catch(e => { }); } catch (err) { }

                        input.data('old', new_barcode);
                        input.removeClass('border-warning scanning-focus').addClass('border-success text-success');
                        btn.removeClass('btn-outline-success').addClass('btn-success').html('<i class="fa fa-check"></i>');

                        showScanFeedback("Berhasil", `Barcode disimpan: ${new_barcode} (${varianName})`, 'success');

                        setTimeout(() => {
                            input.removeClass('border-success text-success');
                            btn.fadeOut(function () {
                                btn.html('<i class="fa fa-check"></i>').removeClass('btn-success').addClass('btn-outline-success');
                            });
                        }, 1500);

                        // Continuous Scanning Logic
                        if ($('#modalScanModeToggle').is(':checked')) {
                            let nextInput = input.closest('tr').next('tr').find('.quick-barcode-input');
                            if (nextInput.length > 0) {
                                nextInput.focus();
                            } else {
                                // If end of table
                                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Semua barcode di halaman ini sudah terisi', showConfirmButton: false, timer: 3000 });
                            }
                        }
                    } else {
                        showScanFeedback("Gagal", res.pesan, "error");
                        btn.html('<i class="fa fa-save"></i>');
                    }
                }, 'json');
            }

            $(document).on('focus', '.quick-varian-input', function () {
                $(this).select();
                $(this).closest('tr').addClass('table-primary');
            });

            $(document).on('blur', '.quick-varian-input', function () {
                $(this).closest('tr').removeClass('table-primary');
            });

            $(document).on('keyup change', '.quick-varian-input', function (e) {
                let input = $(this);
                let btn = input.closest('.input-group').find('.btn-save-varian');

                if (input.val() !== input.data('old')) {
                    btn.fadeIn();
                    input.addClass('border-warning');
                } else {
                    btn.fadeOut();
                    input.removeClass('border-warning');
                }

                if (e.which === 13) {
                    e.preventDefault();
                    saveQuickVarian(input);
                }
            });

            $(document).on('click', '.btn-save-varian', function () {
                let input = $(this).closest('.input-group').find('.quick-varian-input');
                saveQuickVarian(input);
            });

            function saveQuickVarian(input) {
                let material_id = input.data('id');
                let new_varian = input.val();
                let btn = input.closest('.input-group').find('.btn-save-varian');

                if (!new_varian) {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Nama varian tidak boleh kosong', showConfirmButton: false, timer: 3000 });
                    return;
                }

                btn.html('<i class="fa fa-spinner fa-spin"></i>');

                $.post(`${controllerUrl}/actionUpdateMaterialVarianOnly`, {
                    material_id: material_id,
                    varian: new_varian
                }, function (res) {
                    if (res.status === 'success') {
                        input.data('old', new_varian);
                        input.removeClass('border-warning').addClass('border-success text-success');
                        btn.removeClass('btn-outline-primary').addClass('btn-success').html('<i class="fa fa-check"></i>');

                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: `Nama varian disimpan: ${new_varian}`, showConfirmButton: false, timer: 2000 });

                        setTimeout(() => {
                            input.removeClass('border-success text-success');
                            btn.fadeOut(function () {
                                btn.html('<i class="fa fa-save"></i>').removeClass('btn-success').addClass('btn-outline-primary');
                            });
                        }, 1500);

                    } else {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: res.pesan, showConfirmButton: false, timer: 3000 });
                        btn.html('<i class="fa fa-save"></i>');
                    }
                }, 'json');
            }

            // Barcode Management with Image Generation
            function renderBarcode(code) {
                if (code) {
                    $('#noBarcodeText').hide();
                    $('#barcodeRender').show();
                    $('#barcodeTools').show();

                    try {
                        JsBarcode("#barcodeRender", code, {
                            format: "CODE128",
                            lineColor: "#000",
                            width: 2,
                            height: 60,
                            displayValue: true,
                            fontSize: 14,
                            margin: 10
                        });
                        return true;
                    } catch (e) {
                        $('#barcodeRender').hide();
                        $('#noBarcodeText').text('Invalid Barcode Format').show();
                        $('#barcodeTools').hide();
                        return false;
                    }
                } else {
                    $('#barcodeRender').hide();
                    $('#noBarcodeText').text('Belum ada barcode').show();
                    $('#barcodeTools').hide();
                    return false;
                }
            }

            $(document).on('click', '.btn-update-barcode-trigger', function () {
                let code = $(this).data('barcode');
                $('#update_barcode_bahan_id').val($(this).data('id'));
                $('#update_barcode_value').val(code);
                renderBarcode(code);
                $('#modalUpdateBarcode').modal('show');
            });

            $('#update_barcode_value').on('input keyup', function () {
                renderBarcode($(this).val());
            });

            $('#btnGenerateBarcode').click(function () {
                let code = 'BM' + Math.floor(10000000 + Math.random() * 90000000);
                $('#update_barcode_value').val(code);
                renderBarcode(code);
            });

            $('#btnDownloadBarcode').click(function () {
                const svg = document.getElementById('barcodeRender');
                const serializer = new XMLSerializer();
                const source = serializer.serializeToString(svg);

                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();

                // SVG to Base64
                img.src = 'data:image/svg+xml;base64,' + btoa(source);
                img.onload = function () {
                    canvas.width = img.width + 40; // Add padding
                    canvas.height = img.height + 40;

                    // White Background
                    ctx.fillStyle = "white";
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(img, 20, 20); // Draw image centered

                    // Download
                    const a = document.createElement('a');
                    a.download = `Barcode-${$('#update_barcode_value').val()}.png`;
                    a.href = canvas.toDataURL('image/png');
                    a.click();
                };
            });

            $('#btnPrintSingleBarcode').click(function () {
                const svg = document.querySelector('#barcodeRender').outerHTML;
                const win = window.open('', '_blank');
                win.document.write(`
                    <html>
                        <head><title>Print Barcode</title></head>
                        <body style="display:flex; justify-content:center; align-items:center; height:100vh;">
                            ${svg}
                            <script>
                                window.onload = function() { window.print(); window.close(); }
                            <\/script>
                        </body>
                    </html>
                `);
                win.document.close();
            });

            $('#btnSubmitUpdateBarcode').click(function () {
                let data = { bahan_id: $('#update_barcode_bahan_id').val(), barcode: $('#update_barcode_value').val() };
                $.ajax({
                    url: `${controllerUrl}/actionUpdateBarcode`,
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function (res) {
                        if (res.status === 'success') {
                            $('#modalUpdateBarcode').modal('hide');
                            refreshTable();
                        } else { Swal.fire('Error', res.pesan, 'error'); }
                    }
                });
            });

            $('#checkAll').change(function () { $('.check-item').prop('checked', $(this).prop('checked')); });


            // Variant Bulk Print Logic
            $(document).on('change', '#checkAllVariants', function () {
                $('.check-variant').prop('checked', $(this).prop('checked'));
            });

            $(document).on('click', '#btnPrintVariantBulk', function () {
                const selected = [];
                $('.check-variant:checked').each(function () {
                    selected.push($(this).val());
                });

                if (selected.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih varian yang akan dicetak barcodenya.'
                    });
                    return;
                }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${controllerUrl}/cetak_barcode_bulk`;
                form.target = '_blank';

                const inputIds = document.createElement('input');
                inputIds.type = 'hidden';
                inputIds.name = 'ids';
                inputIds.value = selected.join(',');

                const inputType = document.createElement('input');
                inputType.type = 'hidden';
                inputType.name = 'type';
                inputType.value = 'variant';

                form.appendChild(inputIds);
                form.appendChild(inputType);
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            });


            function saveQuickBarcodeBahan(input) {
                let bahan_id = input.data('id');
                let new_barcode = input.val();
                let btn = input.closest('.input-group').find('.btn-save-barcode-bahan');

                if (!new_barcode) return;

                btn.html('<i class="fa fa-spinner fa-spin"></i>');

                // Reuse existing endpoint actionUpdateBarcode which expects {bahan_id, barcode}
                let data = { bahan_id: bahan_id, barcode: new_barcode };

                $.ajax({
                    url: `${controllerUrl}/actionUpdateBarcode`,
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function (res) {
                        if (res.status === 'success') {
                            // Success Feedback
                            input.data('old', new_barcode);
                            input.removeClass('border-warning').addClass('border-success text-success');
                            btn.removeClass('btn-outline-success').addClass('btn-success').html('<i class="fa fa-check"></i>');

                            setTimeout(() => {
                                input.removeClass('border-success text-success');
                                btn.fadeOut(function () {
                                    btn.html('<i class="fa fa-check"></i>').removeClass('btn-success').addClass('btn-outline-success');
                                });
                            }, 2000);

                            // Focus next input (Continuous Scanning)
                            let nextInput = input.closest('tr').next('tr').find('.quick-barcode-input-bahan');
                            if (nextInput.length > 0) nextInput.focus();
                            else input.select();

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.pesan,
                                toast: true, position: 'top-end', timer: 3000
                            });
                            btn.html('<i class="fa fa-save"></i>');
                        }
                    }
                });
            }

            // Inisialisasi Mode Scanner di Modal
            $(document).on('change', '#modalScanModeToggle', function () {
                if ($(this).is(':checked')) {
                    $('#modalScannerIndicator').removeClass('d-none');
                    $('.quick-barcode-input:first').focus();
                } else {
                    $('#modalScannerIndicator').addClass('d-none');
                }
            });

            refreshTable();

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

            // Bulk Generate Barcodes
            $('#btnBulkGenerateBarcode').on('click', function () {
                Swal.fire({
                    title: 'Generate Barcode Massal?',
                    text: "Sistem akan membuatkan barcode unik untuk semua barang & varian yang barcodenya masih kosong. Proses ini tidak dapat dibatalkan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    confirmButtonText: 'Ya, Generate Sekarang',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Harap tunggu sejenak',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        $.ajax({
                            url: '<?= base_url('warehouse/bulkGenerateBarcodes') ?>',
                            type: 'POST',
                            dataType: 'json',
                            success: function (res) {
                                if (res.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: res.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Gagal', res.message, 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                            }
                        });
                    }
                });
            });

            // Bulk Print Barcodes
            $('#btnBulkPrintBarcode').on('click', function () {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${controllerUrl}/cetak_barcode_bulk`;
                form.target = '_blank';

                const inputIds = document.createElement('input');
                inputIds.type = 'hidden';
                inputIds.name = 'ids';
                inputIds.value = 'all';

                const inputType = document.createElement('input');
                inputType.type = 'hidden';
                inputType.name = 'type';
                inputType.value = 'variant';

                form.appendChild(inputIds);
                form.appendChild(inputType);
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            });
        });
    </script>

    <?php $this->load->view('admin/warehouse/components/v_modal_materials_guide'); ?>
</body>

</html>