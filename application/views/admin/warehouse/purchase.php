<?php
// application/views/admin/warehouse/purchase.php
?>

<!-- PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - WAREHOUSE PURCHASE -->
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
        padding: 1.5rem 0 3.25rem 0;
        margin-bottom: 0;
        border-radius: 0 0 24px 24px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        position: relative;
        overflow: hidden;
    }

    .warehouse-header-section::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -5%;
        width: 280px;
        height: 280px;
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
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        flex-shrink: 0;
    }

    .header-title {
        font-size: 1.6rem;
        font-weight: 900;
        margin: 0;
        letter-spacing: -0.5px;
        color: white;
    }

    .header-subtitle {
        font-size: 0.7rem;
        font-weight: 600;
        margin: 0 0 2px 0;
        opacity: 0.7;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #94a3b8;
    }

    /* Metric Cards */
    .stats-container {
        margin-top: -2rem;
        position: relative;
        z-index: 10;
        margin-bottom: 2rem;
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
        border: 1px solid #f1f5f9;
        margin-bottom: 2rem;
    }

    .badge-rack {
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.65rem;
        background: #f0f9ff;
        color: #0369a1;
        border: 1px solid #e0f2fe;
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

    .btn-premium {
        border-radius: 50px;
        padding: 0.45rem 1.1rem;
        font-weight: 700;
        font-size: 0.72rem;
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
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-premium-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
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
        padding: 1.25rem 1rem;
    }

    .table-premium tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f8fafc;
        color: var(--slate-dark);
        font-size: 0.95rem;
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

    /* Sidebar Design */
    .history-card {
        background: #f8fafc;
        border-radius: 24px;
        border: 2px dashed #e2e8f0;
        padding: 1.5rem;
    }

    .history-item {
        background: white;
        border-radius: 18px;
        padding: 1rem;
        margin-bottom: 1rem;
        border: 1px solid #f1f5f9;
        transition: all 0.3s;
    }

    .history-item:hover {
        border-color: var(--accent-blue);
        transform: translateX(5px);
    }
</style>

<div class="warehouse-header-section">
    <div class="container-fluid px-5">
        <div class="row align-items-center">
            <div class="col-md-9">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="fa fa-shopping-basket"></i>
                    </div>
                    <div class="header-text">
                        <p class="header-subtitle">Phase 2: Execution</p>
                        <h1 class="header-title">Penerimaan & Pembelian Material</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-3 text-end header-actions d-flex justify-content-end align-items-center gap-2">
                <button id="btnToggleWorkflow" class="btn btn-outline-light btn-premium rounded-pill px-4">
                    <i class="fa fa-eye-slash me-2"></i>HIDE
                </button>
                <button class="btn btn-outline-light btn-premium rounded-pill px-4" data-bs-toggle="modal"
                    data-bs-target="#modalPurchaseGuide">
                    <i class="fa fa-info-circle me-2"></i>BANTUAN/SOP
                </button>
                <a href="<?= base_url('warehouse') ?>" class="btn btn-outline-light btn-premium rounded-pill px-4">
                    <i class="fa fa-arrow-left me-2"></i>DASHBOARD
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
                        <i class="fa fa-cubes"></i>
                    </div>
                    <div class="metric-info">
                        <p>Item di Keranjang</p>
                        <h3><?= count($temp_items) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-icon" style="background: var(--success-gradient);">
                        <i class="fa fa-wallet"></i>
                    </div>
                    <div class="metric-info">
                        <p>Estimasi Total</p>
                        <h3>Rp <?= number_format($cart_total, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-icon" style="background: var(--primary-gradient);">
                        <i class="fa fa-calendar-check"></i>
                    </div>
                    <div class="metric-info">
                        <p>Periode Transaksi</p>
                        <h3><?= date('M Y') ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6-STEP CORE WORKFLOW (SOP) -->
    <div id="workflowContainer">
        <?php $this->load->view('admin/warehouse/components/v_material_workflow', ['current_step' => 4]); ?>
    </div>

    <div class="row">
        <!-- Main Cart & Form -->
        <div class="col-lg-8">
            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title-premium mb-0"><i class="fa fa-shopping-basket text-primary"></i> Keranjang
                        Penerimaan</h5>
                    <button class="btn btn-premium btn-premium-primary" data-bs-toggle="modal"
                        data-bs-target="#modalAddItem">
                        <i class="fa fa-plus me-2"></i>Tambah Material
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-premium align-middle">
                        <thead>
                            <tr>
                                <th>DETAIL MATERIAL</th>
                                <th>PENEMPATAN RAK</th>
                                <th class="text-center">QUANTITY</th>
                                <th class="text-end">HARGA SATUAN</th>
                                <th class="text-end">SUBTOTAL</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($temp_items)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="opacity-25 mb-3"><i class="fa fa-shopping-cart fa-4x"></i></div>
                                        <h6 class="fw-900 text-muted">Keranjang masih kosong</h6>
                                        <p class="text-muted small">Mulai tambahkan material yang diterima dari supplier.
                                        </p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($temp_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-800 text-dark"><?= $item->bahan_nama ?></div>
                                            <div class="small text-muted font-monospace mt-1"><?= $item->varian ?></div>
                                        </td>
                                        <td>
                                            <?php if ($item->rak_id): ?>
                                                <span class="badge-rack"><i class="fas fa-th me-1"></i>
                                                    <?= $item->rak_kode ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small italic">Default Area</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-soft-blue text-primary py-2 px-3 fw-900 rounded-pill">
                                                <?= number_format($item->qty) ?>         <?= $item->satuan_nama ?>
                                            </span>
                                        </td>
                                        <td class="text-end text-muted small">Rp <?= number_format($item->harga, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-end fw-900 text-dark">Rp
                                            <?= number_format($item->qty * $item->harga, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('warehouse/purchase_delete_item/' . $item->temp_id) ?>"
                                                class="btn btn-sm btn-light border text-danger rounded-pill px-3">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Transaction Header -->
            <div class="content-card">
                <h5 class="card-title-premium"><i class="fa fa-file-invoice text-dark"></i> Finalisasi Transaksi</h5>
                <form action="<?= base_url('warehouse/purchase_finalize') ?>" method="post">
                    <input type="hidden" name="pembelian_total" value="<?= $cart_total ?>">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-premium">Pilih Supplier / vendor <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <select name="supplier_id" id="supplier_id_select"
                                    class="form-select form-control-premium" required>
                                    <option value="">-- Cari Supplier --</option>
                                    <!-- Debug: Total Suppliers = <?= count($suppliers) ?> -->
                                    <?php foreach ($suppliers as $s): ?>
                                        <option value="<?= $s->supplier_id ?>"><?= $s->supplier_nama ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-outline-primary rounded-end-4 px-3"
                                    data-bs-toggle="modal" data-bs-target="#modalAddSupplier"
                                    title="Tambah Supplier Baru">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-premium">Tanggal Masuk</label>
                            <input type="date" name="pembelian_tgl" class="form-control form-control-premium"
                                value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-premium">Nomor Faktur / Invoice</label>
                            <input type="text" name="no_faktur" class="form-control form-control-premium"
                                placeholder="Ex: INV-2024-001" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-premium">Metode Pembayaran</label>
                            <div class="d-flex gap-2">
                                <select name="metode_bayar" class="form-select form-control-premium" id="metode_bayar"
                                    required>
                                    <option value="Cash">Cash (Tunai)</option>
                                    <option value="Transfer">Transfer Bank</option>
                                    <option value="Hutang">Hutang (Termin/Kredit)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4" id="input_rekening_asal" style="display: none;">
                            <label class="form-label-premium">Rekening Asal (Dari)</label>
                            <select name="rekening_id" class="form-select form-control-premium" id="rekening_id_input">
                                <option value="">-- Pilih Rekening Asal --</option>
                                <?php foreach ($rekening as $r): ?>
                                    <option value="<?= $r->id ?>">
                                        <?= $r->nama_bank ?> -
                                        <?= $r->no_rekening ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                        <div class="col-md-4" id="input_rekening_tujuan" style="display: none;">
                            <label class="form-label-premium">Rekening Tujuan (Ke)</label>
                            <input type="text" name="rekening_tujuan" id="rekening_tujuan_input"
                                class="form-control form-control-premium" placeholder="Bank / No Rek / Nama">
                        </div>
                        <div class="col-md-4" id="input_jatuh_tempo" style="display: none;">
                            <label class="form-label-premium">Batas Jatuh Tempo</label>
                            <input type="date" name="jatuh_tempo"
                                class="form-control form-control-premium border-warning"
                                value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                        </div>
                        <div class="col-12 mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-premium btn-premium-primary w-100 py-3 font-lg"
                                <?= empty($temp_items) ? 'disabled' : '' ?>>
                                <i class="fa fa-check-double me-2"></i> SIMPAN & PROSES TRANSAKSI MASUK
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- History Sidebar -->
        <div class="col-lg-4">
            <h5 class="card-title-premium mb-4"><i class="fa fa-history text-secondary"></i> Riwayat Terakhir</h5>
            <div class="history-card">
                <?php if (empty($recent_purchases)): ?>
                    <div class="text-center py-5 text-muted opacity-50">
                        <i class="fa fa-folder-open fa-3x mb-2"></i>
                        <p class="small fw-bold">Belum ada riwayat</p>
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($recent_purchases, 0, 7) as $rp): ?>
                        <div class="history-item shadow-sm">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="badge bg-soft-blue text-primary font-monospace small px-2 py-1 rounded-pill">
                                    <?= $rp->no_faktur ?>
                                </div>
                                <span
                                    class="badge <?= $rp->metode_bayar == 'Cash' ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' ?> rounded-pill px-2">
                                    <?= strtoupper($rp->metode_bayar) ?>
                                </span>
                            </div>
                            <div class="fw-900 text-dark mb-1 h6"><?= $rp->supplier_nama ?></div>
                            <div class="d-flex justify-content-between align-items-end mt-2">
                                <div class="small text-muted fw-bold">
                                    <i class="fa fa-calendar-alt me-1"></i> <?= date('d M Y', strtotime($rp->pembelian_tgl)) ?>
                                </div>
                                <div class="fw-900 text-primary">Rp <?= number_format($rp->pembelian_total, 0, ',', '.') ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="d-grid mt-3">
                        <a href="<?= base_url('warehouse/laporan_pembelian') ?>"
                            class="btn btn-light btn-premium text-muted border-0">
                            Lihat Semua Riwayat <i class="fa fa-chevron-right ms-2 opacity-50"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Item -->
<div class="modal fade" id="modalAddItem" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('warehouse/purchase_add_item') ?>" method="post"
            class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header px-4 py-4" style="background: var(--primary-gradient); color: white;">
                <h5 class="modal-title fw-900"><i class="fa fa-cart-plus me-2"></i>Tambah Ke Keranjang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="mb-4">
                    <label class="form-label-premium">Pilih Material / Produk <span class="text-danger">*</span></label>
                    <select name="material_id" class="form-select form-control-premium select2-modal" required>
                        <option value="">-- Cari Material --</option>
                        <?php foreach ($materials as $m): ?>
                            <option value="<?= $m->material_id ?>">


                                <?= $m->varian ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label-premium">Penempatan Rak (Bin Location) <span
                            class="text-info small fw-bold ms-2">Gudang Utama</span></label>
                    <select name="rak_id" id="p_rak_id" class="form-select form-control-premium select2-modal">
                        <option value="">-- Pilih Rak (Opsional) --</option>
                        <!-- Dynamic Racks will be loaded via AJAX if needed, or pre-rendered -->
                        <?php
                        // Get main warehouse racks (id=1)
                        $main_racks = $this->db->get_where('tbl_rak', ['gudang_id' => 1, 'is_active' => 1])->result();
                        foreach ($main_racks as $r):
                            ?>
                            <option value="<?= $r->rak_id ?>"><?= $r->rak_kode ?> - <?= $r->rak_nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label-premium text-primary">Kuantitas Yang Diterima</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="input-group">
                                    <input type="number" step="1" id="p_input_whole"
                                        class="form-control form-control-premium border-primary" placeholder="0"
                                        required>
                                    <span class="input-group-text bg-light fw-800 p-label-whole">Unit</span>
                                </div>
                            </div>
                            <div class="col-6" id="p_pieces_container" style="display:none;">
                                <div class="input-group">
                                    <input type="number" step="1" id="p_input_pieces"
                                        class="form-control form-control-premium border-info" placeholder="0">
                                    <span class="input-group-text bg-light fw-800 p-label-pieces">Pcs</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="qty" id="p_qty_final">
                        <div id="p_calc_preview" class="small text-muted mt-2 fw-bold" style="display:none;">
                            Total Konversi: <span class="text-primary">0</span> <span class="p-label-whole">Unit</span>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <label class="form-label-premium">Harga Satuan (IDR) <small class="text-muted">(Per <span
                                    class="p-label-whole">Unit</span>)</small></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 fw-bold">Rp</span>
                            <input type="text" id="p_input_harga_display"
                                class="form-control form-control-premium border-start-0" placeholder="0" required>
                            <input type="hidden" name="harga" id="p_input_harga_raw">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-4 pt-0 border-0">
                <button type="button" class="btn btn-light btn-premium px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-premium btn-premium-primary px-4 fw-900">Tambahkan
                    Material</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Safe notification helper
    function showToast(icon, title) {
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({ icon: icon, title: title });
        } else {
            console.log('Swal not loaded yet:', title);
            alert(title);
        }
    }

    $(document).ready(function () {
        // Initialize Supplier Select2 (Direct by ID)
        /*
        $('#supplier_id_select').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih / Cari Supplier --',
            allowClear: true
        });
        */

        // Init other specialized Select2 for premium forms (excluding the one we just did)
        $('.select2').each(function () {
            $(this).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: $(this).data('placeholder') || 'Silakan Pilih'
            });
        });

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

        $('.select2-modal').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#modalAddItem')
        });

        $('#metode_bayar').on('change', function () {
            const val = $(this).val();
            // Reset visibility and requirements
            $('#input_jatuh_tempo, #input_rekening_asal, #input_rekening_tujuan').hide();
            $('#rekening_id_input, #rekening_tujuan_input').prop('required', false);

            if (val === 'Hutang') {
                $('#input_jatuh_tempo').slideDown();
            } else if (val === 'Transfer') {
                $('#input_rekening_asal, #input_rekening_tujuan').slideDown();
                $('#rekening_id_input, #rekening_tujuan_input').prop('required', true);
            }
        });

        let p_global_meta = { konversi: 1, satuan_gudang: 'Unit', satuan_produksi: 'Pcs' };

        $('select[name="material_id"]').on('change', function () {
            const mId = $(this).val();
            if (mId) {
                $.ajax({
                    url: '<?= base_url('warehouse/ajax_get_current_stock') ?>',
                    type: 'POST',
                    data: { material_id: mId, gudang_id: 1 }, // Default Main
                    dataType: 'json',
                    success: function (res) {
                        p_global_meta = res;
                        $('.p-label-whole').text(res.satuan_gudang);
                        $('.p-label-pieces').text(res.satuan_produksi);

                        if (res.konversi > 1) {
                            $('#p_pieces_container').fadeIn();
                            $('#p_calc_preview').fadeIn();
                        } else {
                            $('#p_pieces_container').hide();
                            $('#p_calc_preview').hide();
                        }
                        calculatePurchaseQty();
                    }
                });
            }
        });

        function calculatePurchaseQty() {
            const whole = parseFloat($('#p_input_whole').val()) || 0;
            const pieces = parseFloat($('#p_input_pieces').val()) || 0;
            const konversi = p_global_meta.konversi || 1;

            const finalQty = whole + (pieces / konversi);
            $('#p_qty_final').val(finalQty);
            $('#p_calc_preview span.text-primary').text(finalQty.toFixed(3));
        }

        $('#p_input_whole, #p_input_pieces').on('input', calculatePurchaseQty);

        // --- RUPIAH FORMATTER FOR PRICE ---
        $('#p_input_harga_display').on('input', function () {
            let val = $(this).val().replace(/[^0-9]/g, '');
            $('#p_input_harga_raw').val(val);

            if (val !== "") {
                $(this).val(new Intl.NumberFormat('id-ID').format(val));
            } else {
                $(this).val('');
            }
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
                $('select[name="material_id"]').val(autoMaterialId).trigger('change');
            }, 500);
        }

        // --- QUICK ADD SUPPLIER LOGIC ---
        function generateSupplierCode() {
            $.get('<?= base_url("warehouse/supplier_generate_code") ?>', function (res) {
                if (res.status === 'success') {
                    $('#q_supplier_kode').val(res.code);
                }
            }, 'json');
        }

        $('#modalAddSupplier').on('show.bs.modal', function () {
            generateSupplierCode();
        });

        $('#btnGenSupCode').on('click', function () {
            generateSupplierCode();
        });

        $('#formQuickAddSupplier').on('submit', function (e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');

            // Disable & Blur to prevent double click and focus issues
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Menyimpan...').blur();

            const formData = $form.serialize();

            $.ajax({
                url: '<?= base_url("warehouse/supplier_add") ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        showToast('success', res.pesan);

                        // Select the newly added supplier
                        const newSupName = $form.find('input[name="supplier_nama"]').val();
                        const newSupKode = $('#q_supplier_kode').val();

                        $.get('<?= base_url("warehouse/supplier_get_json") ?>', function (resp) {
                            if (resp.status === 'success') {
                                let newId = null;
                                resp.data.forEach(function (sup) {
                                    if (sup.supplier_nama === newSupName && sup.supplier_kode === newSupKode) {
                                        newId = sup.supplier_id;
                                    }
                                });

                                if (newId) {
                                    // Append new option to standard select
                                    var newOption = new Option(newSupName, newId, true, true);
                                    $('#supplier_id_select').append(newOption).trigger('change');
                                }
                            }
                        }, 'json');

                        $('#modalAddSupplier').modal('hide');
                        $form[0].reset();
                    } else {
                        Swal.fire('Error', res.pesan, 'error');
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="fa fa-save me-2"></i>Simpan Supplier');
                }
            });
        });
    });
</script>

<?php $this->load->view('admin/warehouse/components/v_modal_purchase_guide'); ?>

<!-- MODAL QUICK ADD SUPPLIER -->
<div class="modal fade" id="modalAddSupplier" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formQuickAddSupplier" action="javascript:void(0)" method="POST"
            class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header px-4 py-4" style="background: var(--primary-gradient); color: white;">
                <h5 class="modal-title fw-900"><i class="fa fa-truck me-2"></i>Tambah Supplier Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-premium">Kode Supplier</label>
                        <div class="input-group">
                            <input type="text" name="supplier_kode" id="q_supplier_kode"
                                class="form-control form-control-premium" readonly required>
                            <button type="button" id="btnGenSupCode" class="btn btn-outline-primary rounded-end-3"><i
                                    class="fa fa-sync"></i></button>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label-premium">Nama Perusahaan / Supplier <span
                                class="text-danger">*</span></label>
                        <input type="text" name="supplier_nama" class="form-control form-control-premium"
                            placeholder="Nama CV/PT/Personal" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label-premium">Alamat Lengkap</label>
                        <textarea name="supplier_alamat" class="form-control form-control-premium" rows="2"
                            placeholder="Jln. ..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-premium">Kota / Provinsi</label>
                        <input type="text" name="kota_provinsi" class="form-control form-control-premium"
                            placeholder="Ex: Jakarta Selatan">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-premium">No HP / WhatsApp</label>
                        <input type="text" name="supplier_nohp" class="form-control form-control-premium"
                            placeholder="08...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-premium">Email</label>
                        <input type="email" name="supplier_email" class="form-control form-control-premium"
                            placeholder="vendor@example.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-premium">Termin Pembayaran (Hari)</label>
                        <input type="number" name="termin" class="form-control form-control-premium" value="30">
                    </div>
                </div>
            </div>
            <div class="modal-footer p-4 pt-0 border-0">
                <button type="button" class="btn btn-light btn-premium px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-premium btn-premium-primary px-4 fw-900">
                    <i class="fa fa-save me-2"></i>Simpan Supplier
                </button>
            </div>
        </form>
    </div>
</div>