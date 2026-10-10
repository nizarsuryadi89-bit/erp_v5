<?php
// application/views/admin/warehouse/order.php
?>

<!-- PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - WAREHOUSE ORDERING -->
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
        color: white;
        flex-shrink: 0;
    }

    .header-title {
        font-size: 1.6rem;
        font-weight: 900;
        margin: 0;
        letter-spacing: -0.5px;
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
        margin-bottom: 3rem;
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

    .btn-premium {
        border-radius: 50px;
        padding: 0.45rem 1.1rem;
        font-weight: 700;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        transition: all 0.3s;
        text-decoration: none !important;
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

    /* Badges */
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

    .bg-soft-blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    .bg-soft-success {
        background: #ecfdf5;
        color: #10b981;
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
</style>

<div class="warehouse-header-section">
    <div class="container-fluid px-5">
        <div class="row align-items-center">
            <div class="col-md-9">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <div class="header-text">
                        <p class="header-subtitle">Phase 1: Planning</p>
                        <h1 class="header-title">Pemesanan & Rencana Belanja</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-3 text-end header-actions d-flex justify-content-end align-items-center gap-2">
                <button class="btn btn-outline-light btn-premium rounded-pill px-4" data-bs-toggle="modal"
                    data-bs-target="#modalOrderGuide">
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
    <!-- Stats Overview -->
    <div class="stats-container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon" style="background: var(--danger-gradient);">
                            <i class="fa fa-exclamation-circle"></i>
                        </div>
                        <div class="metric-info">
                            <p>Stok Habis</p>
                            <h3>
                                <?php
                                $outOfStock = array_filter($recommendations, function ($item) {
                                    return $item->qty <= 0;
                                });
                                echo count($outOfStock);
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon" style="background: var(--warning-gradient);">
                            <i class="fa fa-layer-group"></i>
                        </div>
                        <div class="metric-info">
                            <p>Stok Minim</p>
                            <h3>
                                <?php
                                $lowStock = array_filter($recommendations, function ($item) {
                                    return $item->qty > 0;
                                });
                                echo count($lowStock);
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon" style="background: var(--info-gradient);">
                            <i class="fa fa-clipboard-list"></i>
                        </div>
                        <div class="metric-info">
                            <p>Total Rekomendasi</p>
                            <h3><?= count($recommendations) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-900 text-dark mb-0">Rekomendasi Belanja Material</h4>
            <div class="d-flex gap-2">
                <button class="btn btn-light btn-premium" onclick="location.reload()">
                    <i class="fa fa-sync-alt me-2"></i>Refresh
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-premium" id="tableOrdering">
                <thead>
                    <tr>
                        <th>MATERIAL & VARIAN</th>
                        <th>STOK SAAT INI</th>
                        <th>STOK MINIMAL</th>
                        <th>STATUS</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recommendations)): ?>
                        <?php foreach ($recommendations as $item): ?>
                            <tr>
                                <td>
                                    <div class="fw-800 text-dark"><?= htmlspecialchars($item->bahan_nama) ?></div>
                                    <div class="small text-muted mt-1">
                                        <span
                                            class="badge bg-light text-dark font-monospace border"><?= $item->barcode ?></span>
                                        &bull; <?= htmlspecialchars($item->varian) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-900"><?= number_format($item->qty) ?></span>
                                    <span class="text-muted small"><?= $item->satuan_nama ?></span>
                                </td>
                                <td>
                                    <span class="fw-800"><?= number_format($item->stok_min) ?></span>
                                    <span class="text-muted small"><?= $item->satuan_nama ?></span>
                                </td>
                                <td>
                                    <?php if ($item->qty <= 0): ?>
                                        <span class="badge-premium bg-soft-danger status-pill">
                                            <div class="status-dot" style="background: #f43f5e;"></div>
                                            HABIS TOTAL
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-premium bg-soft-warning status-pill">
                                            <div class="status-dot" style="background: #f59e0b;"></div>
                                            LIMIT KRITIS
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('warehouse/purchase?material_id=' . $item->material_id) ?>"
                                        class="btn btn-premium btn-premium-primary">
                                        <i class="fa fa-cart-plus me-2"></i>Buat Pembelian
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="opacity-25 mb-3">
                                    <i class="fa fa-check-circle fa-4x text-success"></i>
                                </div>
                                <h5 class="fw-900 text-muted">Stok Masih Aman</h5>
                                <p class="text-muted mb-0">Belum ada material yang memerlukan pemesanan segera.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#tableOrdering').DataTable({
            "pageLength": 10,
            "ordering": true,
            "language": {
                "search": "Cari Rekomendasi:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(disaring dari _MAX_ total data)"
            }
        });
    });
</script>

<?php $this->load->view('admin/warehouse/components/v_modal_order_guide'); ?>