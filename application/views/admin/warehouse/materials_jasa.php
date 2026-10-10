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
        z-index: 0;
        pointer-events: none;
    }

    .header-actions {
        position: relative;
        z-index: 10;
    }

    .header-content {
        position: relative;
        z-index: 10;
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
        margin-bottom: 2rem;
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

    .table-premium thead th {
        background: #f8fafc;
        border: none;
        color: var(--slate-muted);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 1px;
        padding: 1.25rem 1rem;
    }

    .product-badge {
        background: var(--success-gradient);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .operasional-badge {
        background: var(--info-gradient);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .jasa-badge {
        background: var(--warning-gradient);
        color: #1e293b;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .spare-badge {
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .ops-badge {
        background: linear-gradient(135deg, #64748b 0%, #334155 100%);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
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

    .bg-soft-info {
        background: #ecfeff;
        color: #06b6d4;
    }

    .bg-soft-purple {
        background: #f5f3ff;
        color: #8b5cf6;
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
</style>

<div class="warehouse-header-section">
    <div class="container-fluid px-5">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="fa fa-folder-open"></i>
                    </div>
                    <div class="header-text">
                        <p class="header-subtitle">Database Master Inventory</p>
                        <h1 class="header-title">Induk, Jasa & Umum</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-end header-actions d-flex justify-content-end align-items-center gap-2">
                <button id="btnToggleWorkflow" class="btn btn-outline-light btn-premium rounded-pill px-3">
                    <i class="fa fa-eye-slash me-1"></i>HIDE SOP
                </button>
                <button class="btn btn-outline-light btn-premium rounded-pill px-3" data-bs-toggle="modal"
                    data-bs-target="#modalSopBahan">
                    <i class="fas fa-book-open me-1 text-warning"></i>SOP / PANDUAN
                </button>
                <a href="<?= base_url('warehouse') ?>" class="btn btn-outline-light btn-premium rounded-pill px-3">
                    <i class="fa fa-th-large me-1"></i>DASHBOARD
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-5">
    <!-- Statistics Overview -->
    <div class="stats-container">
        <div class="row g-3">
            <div class="col-md-2">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon bg-soft-blue">
                            <i class="fa fa-boxes"></i>
                        </div>
                        <div class="metric-info">
                            <p>Total Item</p>
                            <h3 class="stat-value"><?= $total_rows ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon bg-soft-emerald">
                            <i class="fa fa-industry"></i>
                        </div>
                        <div class="metric-info">
                            <p>Bahan Baku</p>
                            <h3 class="stat-value"><?= $jumlah_bahan_baku ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon bg-soft-amber">
                            <i class="fa fa-flask"></i>
                        </div>
                        <div class="metric-info">
                            <p>Bahan Operasional</p>
                            <h3 class="stat-value"><?= $jumlah_bahan_operasional ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon bg-soft-blue">
                            <i class="fa fa-cube"></i>
                        </div>
                        <div class="metric-info">
                            <p>Barang Jadi</p>
                            <h3 class="stat-value"><?= $jumlah_barang_jadi ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon bg-soft-purple">
                            <i class="fa fa-tools"></i>
                        </div>
                        <div class="metric-info">
                            <p>Spare Part</p>
                            <h3 class="stat-value"><?= $jumlah_spare_part ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon"
                            style="background: linear-gradient(135deg, #64748b, #334155); color:white;">
                            <i class="fa fa-clipboard-list"></i>
                        </div>
                        <div class="metric-info">
                            <p>Operasional</p>
                            <h3 class="stat-value"><?= $jumlah_operasional ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6-STEP CORE WORKFLOW (SOP) -->
    <div id="workflowContainer">
        <?php $this->load->view('admin/warehouse/components/v_material_workflow', ['current_step' => 1]); ?>
    </div>

    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-premium btn-premium-primary" data-bs-toggle="modal"
                    data-bs-target="#modalInputData">
                    <i class="fa fa-plus me-2"></i>Tambah Bahan / Jasa
                </button>
                <button class="btn btn-premium btn-light border" onclick="exportToExcel()">
                    <i class="fa fa-download me-2"></i>Export
                </button>
            </div>

            <div class="d-flex align-items-center gap-2" style="width: 600px;">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 border-radius-14">
                        <i class="fa fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control form-control-premium border-start-0" id="searchInput"
                        placeholder="Cari bahan...">
                </div>

                <select class="form-select form-control-premium" id="filterJenis" style="width: 220px;">
                    <option value="">Semua Jenis</option>
                    <option value="bahan_baku">Bahan Baku</option>
                    <option value="bahan_operasional">Bahan Operasional</option>
                    <option value="barang_jadi">Barang Jadi</option>
                    <option value="spare_part">Spare Part</option>
                    <option value="operasional">Operasional</option>
                </select>
            </div>
        </div>

        <!-- Info bar: result count + per-page -->
        <div class="d-flex justify-content-between align-items-center mb-3" id="tableInfoBar">
            <div class="small fw-700 text-muted" id="resultCount">Memuat data...</div>
            <div class="d-flex align-items-center gap-2">
                <span class="small text-muted fw-600">Tampilkan</span>
                <select id="perPageSelect" class="form-select form-select-sm"
                    style="width: 75px; border-radius: 10px; font-weight: 700;">
                    <option value="10">10</option>
                    <option value="15" selected>15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="999">Semua</option>
                </select>
                <span class="small text-muted fw-600">per halaman</span>
            </div>
        </div>

        <!-- Material Table -->
        <div class="table-responsive">
            <table class="table table-premium" id="bahanTable">
                <thead>
                    <tr>
                        <th style="width: 36px;" class="text-center">
                            <input type="checkbox" id="checkAll" class="form-check-input"
                                style="width:16px;height:16px;cursor:pointer;">
                        </th>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th style="width: 250px;">Nama Bahan/Jasa</th>
                        <th style="width: 100px;" class="text-center">Jenis</th>
                        <th style="width: 120px;">Satuan Produksi</th>
                        <th style="width: 150px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($tampilData as $dt):
                        $jenis_map = [
                            'bahan_baku' => ['label' => 'Bahan Baku', 'class' => 'product-badge', 'slug' => 'bahan_baku'],
                            'bahan_operasional' => ['label' => 'Bahan Operasional', 'class' => 'jasa-badge', 'slug' => 'bahan_operasional'],
                            'barang_jadi' => ['label' => 'Barang Jadi', 'class' => 'operasional-badge', 'slug' => 'barang_jadi'],
                            'spare_part' => ['label' => 'Spare Part', 'class' => 'spare-badge', 'slug' => 'spare_part'],
                            'operasional' => ['label' => 'Operasional', 'class' => 'ops-badge', 'slug' => 'operasional'],
                        ];
                        $j = $jenis_map[$dt->jenis_barang] ?? ['label' => 'Lainnya', 'class' => 'jasa-badge', 'slug' => 'lainnya'];
                        $projas = '<span class="' . $j['class'] . '">' . $j['label'] . '</span>';
                        $jenis_slug = $j['slug'];
                        ?>
                        <tr data-bahan="<?= htmlspecialchars($dt->bahan_nama) ?>" data-jenis="<?= $jenis_slug ?>" data-id="<?= $dt->bahan_id ?>">
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input row-check" value="<?= $dt->bahan_id ?>"
                                    style="width:16px;height:16px;cursor:pointer;">
                            </td>
                            <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                            <td>
                                <div class="fw-900 text-dark mb-0"><?= $dt->bahan_nama ?></div>
                                <small class="text-muted fw-bold" style="font-size: 0.75rem;">ID: #<?= $dt->bahan_id ?></small>
                            </td>
                            <td class="text-center"><?= $projas ?></td>
                            <td>
                                <span class="badge-premium bg-light text-dark border">
                                    <i class="fas fa-ruler-combined me-1 opacity-50"></i><?= $dt->satuan_nama ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button"
                                        class="btn btn-light btn-sm rounded-12 border bg-white shadow-sm btn-edit"
                                        data-bs-toggle="modal" data-bs-target="#modalEditData"
                                        data-id="<?= $dt->bahan_id ?>" data-barcode="<?= $dt->barcode ?>"
                                        data-nama="<?= $dt->bahan_nama ?>" data-jenis_barang="<?= $dt->jenis_barang ?>"
                                        data-satuan_id="<?= $dt->satuan_id ?>" title="Edit Bahan">
                                        <i class="fa fa-edit text-primary"></i>
                                    </button>
                                    <a href="javascript:void(0);"
                                        class="btn btn-light btn-sm rounded-12 border bg-white shadow-sm btn-delete"
                                        data-url="<?= base_url('warehouse/hapus-bahan/' . $dt->bahan_id) ?>"
                                        title="Hapus Bahan">
                                        <i class="fa fa-trash text-danger"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="d-flex justify-content-center align-items-center gap-2 pt-4 mt-2 border-top"
            id="paginationControls"></div>
    </div>
</div>
</div>
</div>

<!-- ====== BULK ACTION TOOLBAR (floating, slides up when rows selected) ====== -->
<div id="bulkActionBar" style="
    position: fixed; bottom: -100px; left: 50%; transform: translateX(-50%);
    background: #1e293b; color: white; border-radius: 20px; padding: 1rem 2rem;
    display: flex; align-items: center; gap: 1.5rem; z-index: 9999;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3); transition: bottom 0.35s cubic-bezier(0.34,1.56,0.64,1);
    min-width: 420px;">
    <div class="d-flex align-items-center gap-2">
        <div
            style="width:36px;height:36px;background:rgba(99,102,241,0.25);border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-check-square text-indigo-300"></i>
        </div>
        <div>
            <div class="fw-900" style="font-size:0.85rem;" id="bulkCountLabel">0 item dipilih</div>
            <div class="small opacity-60">Pilih aksi massal di bawah</div>
        </div>
    </div>
    <div class="d-flex gap-2 ms-auto">
        <button class="btn btn-sm btn-warning fw-800 rounded-pill px-4" id="btnBulkSatuan" data-bs-toggle="modal"
            data-bs-target="#modalBulkSatuan">
            <i class="fas fa-ruler-combined me-2"></i>Ubah Satuan
        </button>
        <button class="btn btn-sm btn-primary fw-800 rounded-pill px-4" id="btnBulkJenis" data-bs-toggle="modal"
            data-bs-target="#modalBulkJenis">
            <i class="fas fa-tags me-2"></i>Ubah Jenis
        </button>
        <button class="btn btn-sm btn-outline-light fw-800 rounded-pill px-3" id="btnBulkCancel">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- ====== MODAL BULK UPDATE SATUAN ====== -->
<div class="modal fade" id="modalBulkSatuan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 28px; overflow: hidden;">
            <div class="modal-header border-0 p-4" style="background: var(--primary-gradient);">
                <h5 class="modal-title fw-900 text-white">
                    <i class="fas fa-ruler-combined me-2"></i>Ubah Satuan Massal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="alert alert-info d-flex align-items-center gap-3 rounded-3 border-0 mb-4"
                    style="background:#eff6ff;">
                    <i class="fas fa-info-circle fa-lg text-primary flex-shrink-0"></i>
                    <div class="small fw-700">
                        Satuan baru akan diterapkan ke <strong id="bulkCountInModal">0</strong> item yang dipilih.
                        Tindakan ini tidak dapat dibatalkan.
                    </div>
                </div>
                <label class="form-label fw-800 text-dark small text-uppercase mb-2">Pilih Satuan Baru</label>
                <select id="bulkSatuanSelect" class="form-select form-control-premium" required>
                    <option value="">-- Pilih Satuan --</option>
                    <?php foreach ($satuan as $s): ?>
                        <option value="<?= $s->satuan_id ?>">
                            <?= $s->satuan_nama ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="modal-footer border-0 p-4 bg-light d-flex gap-2">
                <button type="button" class="btn btn-light btn-premium rounded-pill px-4"
                    data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning btn-premium rounded-pill px-5 fw-900"
                    id="btnConfirmBulkSatuan">
                    <i class="fas fa-save me-2"></i>Terapkan Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ====== MODAL BULK UPDATE JENIS ====== -->
<div class="modal fade" id="modalBulkJenis" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 28px; overflow: hidden;">
            <div class="modal-header border-0 p-4" style="background: var(--primary-gradient);">
                <h5 class="modal-title fw-900 text-white">
                    <i class="fas fa-tags me-2"></i>Ubah Jenis Massal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="alert alert-info d-flex align-items-center gap-3 rounded-3 border-0 mb-4"
                    style="background:#eff6ff;">
                    <i class="fas fa-info-circle fa-lg text-primary flex-shrink-0"></i>
                    <div class="small fw-700">
                        Jenis baru akan diterapkan ke <strong class="bulkCountInModal">0</strong> item yang dipilih.
                        Tindakan ini tidak dapat dibatalkan.
                    </div>
                </div>
                <label class="form-label fw-800 text-dark small text-uppercase mb-2">Pilih Jenis Baru</label>
                <select id="bulkJenisSelect" class="form-select form-control-premium" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="bahan_baku">Bahan Baku</option>
                    <option value="bahan_operasional">Bahan Operasional</option>
                    <option value="barang_jadi">Barang Jadi</option>
                    <option value="spare_part">Spare Part</option>
                    <option value="operasional">Operasional</option>
                </select>
            </div>
            <div class="modal-footer border-0 p-4 bg-light d-flex gap-2">
                <button type="button" class="btn btn-light btn-premium rounded-pill px-4"
                    data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-premium rounded-pill px-5 fw-900"
                    id="btnConfirmBulkJenis">
                    <i class="fas fa-save me-2"></i>Terapkan Sekarang
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ================= MODAL INPUT DATA ================= -->
<div class="modal fade" id="modalInputData" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="post" action="<?= base_url('warehouse/actionAddBahanLegacy') ?>" class="needs-validation"
            novalidate>
            <div class="modal-content border-0 shadow-lg" style="border-radius: 28px; overflow: hidden;">
                <div class="modal-header border-0 p-4" style="background: var(--primary-gradient);">
                    <h5 class="modal-title fw-900 text-white">
                        <i class="fa fa-plus-circle me-2"></i>Tambah Bahan Produksi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-800 text-dark small text-uppercase">Nama Bahan / Jasa
                                Produksi</label>
                            <input name="bahan_nama" class="form-control form-control-premium" required
                                placeholder="Contoh: Art Paper 150gr">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-800 text-dark small text-uppercase">Jenis</label>
                            <select name="jenis_barang" class="form-select form-control-premium" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="bahan_baku">Bahan Baku</option>
                                <option value="bahan_operasional">Bahan Operasional</option>
                                <option value="barang_jadi">Barang Jadi</option>
                                <option value="spare_part">Spare Part</option>
                                <option value="operasional">Operasional</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-800 text-dark small text-uppercase">Satuan Produksi
                                (Terkecil)</label>
                            <select name="satuan_id" class="form-select form-control-premium" required>
                                <option value="">-- Pilih Satuan --</option>
                                <?php foreach ($satuan as $s): ?>
                                    <option value="<?= $s->satuan_id ?>"><?= $s->satuan_nama ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light">
                    <button type="button" class="btn btn-light btn-premium rounded-pill px-4"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-premium btn-premium-primary rounded-pill px-5">SIMPAN
                        DATA</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT DATA ================= -->
<div class="modal fade" id="modalEditData" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="post" action="<?= base_url('warehouse/actionUpdateBahanLegacy') ?>" class="needs-validation"
            novalidate>
            <div class="modal-content border-0 shadow-lg" style="border-radius: 28px; overflow: hidden;">
                <div class="modal-header border-0 p-4" style="background: var(--primary-gradient);">
                    <h5 class="modal-title fw-900 text-white">
                        <i class="fa fa-edit me-2"></i>Update Bahan Produksi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="bahan_id" id="edit_id">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-800 text-dark small text-uppercase">Nama Bahan Produksi</label>
                            <input type="text" name="bahan_nama" id="edit_nama"
                                class="form-control form-control-premium" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-800 text-dark small text-uppercase">Jenis</label>
                            <select name="jenis_barang" id="edit_jenis_barang" class="form-select form-control-premium"
                                required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="bahan_baku">Bahan Baku</option>
                                <option value="bahan_operasional">Bahan Operasional</option>
                                <option value="barang_jadi">Barang Jadi</option>
                                <option value="spare_part">Spare Part</option>
                                <option value="operasional">Operasional</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-800 text-dark small text-uppercase">Satuan Produksi</label>
                            <select name="satuan_id" id="edit_satuan_id" class="form-select form-control-premium"
                                required>
                                <option value="">-- Pilih Satuan --</option>
                                <?php foreach ($satuan as $s): ?>
                                    <option value="<?= $s->satuan_id ?>"><?= $s->satuan_nama ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light">
                    <button type="button" class="btn btn-light btn-premium rounded-pill px-4"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-premium btn-premium-primary rounded-pill px-5">UPDATE
                        DATA</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- ================= JAVASCRIPT SECTION ================= -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
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

        // Animate statistics on page load
        function animateStats() {
            const statValues = document.querySelectorAll('.stat-value');
            statValues.forEach(stat => {
                const finalValue = parseInt(stat.textContent);
                let currentValue = 0;
                const increment = finalValue / 50;

                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        currentValue = finalValue;
                        clearInterval(timer);
                    }
                    stat.textContent = Math.floor(currentValue);
                }, 30);
            });
        }

        // Initialize statistics animation
        animateStats();

        // --- SEARCH, FILTER & PAGINATION ---
        let currentPage = 1;

        function highlight(text, term) {
            if (!term) return text;
            const safeText = $('<div>').text(text).html(); // escape HTML
            const regex = new RegExp('(' + term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
            return safeText.replace(regex, '<mark style="background:#fef08a;padding:0 2px;border-radius:3px;">$1</mark>');
        }

        function getFilteredRows() {
            const searchTerm = $('#searchInput').val().toLowerCase().trim();
            const jenisFilter = $('#filterJenis').val();
            return $('#bahanTable tbody tr').filter(function () {
                const bahanName = ($(this).data('bahan') || '').toString().toLowerCase();
                const jenis = $(this).data('jenis') || '';
                const matchSearch = !searchTerm || bahanName.includes(searchTerm);
                const matchJenis = !jenisFilter || jenis === jenisFilter;
                return matchSearch && matchJenis;
            });
        }

        function renderPagination(totalFiltered) {
            const perPage = parseInt($('#perPageSelect').val());
            if (perPage >= 999 || totalFiltered === 0) {
                $('#paginationControls').empty();
                return;
            }
            const totalPages = Math.ceil(totalFiltered / perPage);
            if (totalPages <= 1) { $('#paginationControls').empty(); return; }

            let html = '';
            // Prev
            html += `<button class="btn btn-sm btn-light border rounded-pill px-3 fw-800 page-btn" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}><i class="fa fa-chevron-left"></i></button>`;

            // Page numbers (smart window)
            const window_size = 5;
            let startP = Math.max(1, currentPage - Math.floor(window_size / 2));
            let endP = Math.min(totalPages, startP + window_size - 1);
            if (endP - startP < window_size - 1) startP = Math.max(1, endP - window_size + 1);

            if (startP > 1) html += `<button class="btn btn-sm btn-light border rounded-pill px-3 fw-800 page-btn" data-page="1">1</button><span class="text-muted small fw-700">…</span>`;
            for (let p = startP; p <= endP; p++) {
                const active = p === currentPage ? 'text-white fw-900' : 'fw-800';
                const bg = p === currentPage ? 'background: #1e293b;' : '';
                html += `<button class="btn btn-sm border rounded-pill px-3 page-btn ${active}" style="${bg}" data-page="${p}">${p}</button>`;
            }
            if (endP < totalPages) html += `<span class="text-muted small fw-700">…</span><button class="btn btn-sm btn-light border rounded-pill px-3 fw-800 page-btn" data-page="${totalPages}">${totalPages}</button>`;

            // Next
            html += `<button class="btn btn-sm btn-light border rounded-pill px-3 fw-800 page-btn" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}><i class="fa fa-chevron-right"></i></button>`;

            $('#paginationControls').html(html);
        }

        function applyTable(resetPage = false) {
            if (resetPage) currentPage = 1;
            const searchTerm = $('#searchInput').val().toLowerCase().trim();
            const perPage = parseInt($('#perPageSelect').val());
            const allFiltered = getFilteredRows();
            const total = allFiltered.length;
            const totalAll = $('#bahanTable tbody tr').length;

            // Hide all rows first
            $('#bahanTable tbody tr').hide();

            // Slice for current page
            const start = (currentPage - 1) * (perPage >= 999 ? total : perPage);
            const end = perPage >= 999 ? total : start + perPage;
            let pageRows = allFiltered.slice(start, end);

            // Show & update numbering + highlight
            let rowNum = start + 1;
            pageRows.each(function () {
                $(this).show();
                $(this).find('td:eq(1)').text(rowNum++);
                const nameCell = $(this).find('td:eq(2) .fw-900');
                const originalName = $(this).data('bahan');
                nameCell.html(searchTerm ? highlight(originalName, searchTerm) : $('<div>').text(originalName).html());
            });

            // Update result count info
            if (total === 0) {
                $('#resultCount').html('<span class="text-danger"><i class="fas fa-times-circle me-1"></i>Tidak ada hasil ditemukan</span>');
            } else {
                const from = start + 1, to = Math.min(start + pageRows.length, total);
                $('#resultCount').html(
                    `<i class="fas fa-list me-1 text-primary opacity-75"></i>Menampilkan <strong>${from}–${to}</strong> dari <strong>${total}</strong> data` +
                    (total < totalAll ? ` <span class="text-muted">(difilter dari ${totalAll} total)</span>` : '')
                );
            }

            // Toggle no-result row
            if (total === 0) {
                if ($('#emptySearchRow').length === 0) {
                    $('#bahanTable tbody').append(`<tr id="emptySearchRow"><td colspan="5" class="text-center py-5"><div class="opacity-25 mb-2"><i class="fas fa-search fa-3x"></i></div><div class="fw-800 text-muted">Tidak ditemukan hasil pencarian</div><div class="small text-muted mt-1">Coba kata kunci atau filter yang berbeda</div></td></tr>`);
                }
                $('#emptySearchRow').show();
            } else {
                $('#emptySearchRow').remove();
            }

            $('#resetFilter').toggle(!!$('#searchInput').val() || !!$('#filterJenis').val());
            renderPagination(total);
        }

        // Event bindings
        $('#searchInput').on('input', function () { applyTable(true); });
        $('#filterJenis').on('change', function () {
            applyTable(true);
            const jenis = $(this).val();
            const map = { produksi: 'Cari bahan produksi...', umum: 'Cari barang umum/atk...', jasa: 'Cari jasa...' };
            $(this).closest('.content-card').find('#searchInput').attr('placeholder', map[jenis] || 'Cari bahan...');
        });
        $('#perPageSelect').on('change', function () { applyTable(true); });

        $(document).on('click', '.page-btn:not([disabled])', function () {
            currentPage = parseInt($(this).data('page'));
            // Reset select-all state when changing page
            $('#checkAll').prop('checked', false).prop('indeterminate', false);
            applyTable(false);
        });

        $('#resetFilter').on('click', function () {
            $('#searchInput').val('');
            $('#filterJenis').val('');
            applyTable(true);
        });

        // Auto-focus
        $('#searchInput').focus();

        // Init
        applyTable();

        // --- BULK ACTION LOGIC ---
        function getCheckedIds() {
            return $('.row-check:checked').map(function () { return $(this).val(); }).get();
        }

        function updateBulkBar() {
            const count = getCheckedIds().length;
            const bar = $('#bulkActionBar');
            $('#bulkCountLabel').text(count + ' item dipilih');
            $('.bulkCountInModal').text(count);
            if (count > 0) {
                bar.css('bottom', '28px');
            } else {
                bar.css('bottom', '-100px');
                $('#checkAll').prop('checked', false).prop('indeterminate', false);
            }
        }

        // Select All — only visible rows on current page
        $('#checkAll').on('change', function () {
            const checked = $(this).prop('checked');
            $('#bahanTable tbody tr:visible .row-check').prop('checked', checked);
            updateBulkBar();
        });

        // Individual checkbox
        $(document).on('change', '.row-check', function () {
            const total = $('#bahanTable tbody tr:visible .row-check').length;
            const checkedCount = $('#bahanTable tbody tr:visible .row-check:checked').length;
            $('#checkAll')
                .prop('checked', checkedCount === total && total > 0)
                .prop('indeterminate', checkedCount > 0 && checkedCount < total);
            updateBulkBar();
        });

        // Cancel / X button
        $('#btnBulkCancel').on('click', function () {
            $('.row-check, #checkAll').prop('checked', false).prop('indeterminate', false);
            updateBulkBar();
        });

        // Confirm bulk update satuan
        $('#btnConfirmBulkSatuan').on('click', function () {
            const ids = getCheckedIds();
            const satuanId = $('#bulkSatuanSelect').val();
            const satuanLabel = $('#bulkSatuanSelect option:selected').text();

            if (!satuanId) {
                Swal.fire({ icon: 'warning', title: 'Pilih satuan terlebih dahulu!', toast: true, position: 'top-end', timer: 2500, showConfirmButton: false });
                return;
            }

            $('#modalBulkSatuan').modal('hide');

            Swal.fire({
                title: 'Konfirmasi Perubahan Massal',
                html: `Satuan <strong>${satuanLabel}</strong> akan diterapkan ke <strong>${ids.length} item</strong>. Lanjutkan?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                confirmButtonText: 'Ya, Terapkan',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (!result.isConfirmed) return;

                const btn = $('#btnConfirmBulkSatuan');
                btn.prop('disabled', true);
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                $.ajax({
                    url: '<?= base_url('warehouse/bulkUpdateSatuan') ?>',
                    type: 'POST',
                    data: { ids: ids, satuan_id: satuanId },
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1800, showConfirmButton: false })
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                    },
                    complete: () => btn.prop('disabled', false)
                });
            });
        });

        // Confirm bulk update jenis
        $('#btnConfirmBulkJenis').on('click', function () {
            const ids = getCheckedIds();
            const jenisBarang = $('#bulkJenisSelect').val();
            const jenisLabel = $('#bulkJenisSelect option:selected').text();

            if (!jenisBarang) {
                Swal.fire({ icon: 'warning', title: 'Pilih jenis terlebih dahulu!', toast: true, position: 'top-end', timer: 2500, showConfirmButton: false });
                return;
            }

            $('#modalBulkJenis').modal('hide');

            Swal.fire({
                title: 'Konfirmasi Perubahan Massal',
                html: `Jenis <strong>${jenisLabel}</strong> akan diterapkan ke <strong>${ids.length} item</strong>. Lanjutkan?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'Ya, Terapkan',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (!result.isConfirmed) return;

                const btn = $('#btnConfirmBulkJenis');
                btn.prop('disabled', true);
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                $.ajax({
                    url: '<?= base_url('warehouse/bulkUpdateJenis') ?>',
                    type: 'POST',
                    data: { ids: ids, jenis_barang: jenisBarang },
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1800, showConfirmButton: false })
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                    },
                    complete: () => btn.prop('disabled', false)
                });
            });
        });


        // Initialize Select2 & Auto-fill Logic


        // --- VALIDASI & MODAL ---
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

        // Edit functionality
        $('.btn-edit').click(function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const jenis_barang = $(this).data('jenis_barang');
            const satuan_id = $(this).data('satuan_id');

            $('#edit_id').val(id);
            $('#edit_nama').val(nama);
            $('#edit_jenis_barang').val(jenis_barang).trigger('change');
            $('#edit_satuan_id').val(satuan_id).trigger('change');
        });


        // Delete functionality
        $('.btn-delete').click(function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            Swal.fire({
                title: 'Yakin ingin menghapus data ini?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

    // Enhanced Export to Excel function
    function exportToExcel() {
        const table = document.getElementById('bahanTable');
        const rows = Array.from(table.querySelectorAll('tr'));

        let csv = 'No,Nama Bahan/Jasa,Jenis,Satuan Produksi\n';

        rows.slice(1).forEach((row, index) => {
            const cells = Array.from(row.querySelectorAll('td'));
            const rowData = [
                index + 1,
                cells[1]?.querySelector('.fw-900')?.textContent?.trim() || '',
                cells[2]?.textContent?.trim() || '',
                cells[3]?.textContent?.trim() || ''
            ];
            csv += rowData.map(cell => `"${cell}"`).join(',') + '\n';
        });

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'data_bahan_produksi.csv';
        a.click();
        window.URL.revokeObjectURL(url);
    }

    // Enhanced Print function
    function printTable() {
        const printWindow = window.open('', '_blank');
        const table = document.getElementById('bahanTable');

        let html = `
        <html>
        <head>
            <title>Data Bahan Produksi</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #1a1c23; color: white; text-transform: uppercase; font-size: 12px; }
                h1 { color: #1a1c23; margin-bottom: 5px; }
                .material-info { margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid #eee; }
            </style>
        </head>
        <body>
            <h1>Data Bahan Produksi</h1>
            <p style="color: #666; margin-top: 0;">Langkah 1: Registrasi Induk Material</p>
            <div class="material-info">
                <p><strong>Total Bahan:</strong> ${document.querySelector('.stat-value').textContent}</p>
                <p><strong>Tanggal Cetak:</strong> ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</p>
            </div>
            ${table.outerHTML}
        </body>
        </html>
    `;

        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.print();
    }
</script><?php $this->load->view('admin/warehouse/components/v_modal_sop_bahan'); ?>