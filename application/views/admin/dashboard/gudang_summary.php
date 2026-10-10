<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    :root {
        --wh-primary: #0f172a;
        --wh-secondary: #334155;
        --wh-accent: #0d9488;
        /* Teal */
        --wh-accent-glow: rgba(13, 148, 136, 0.15);
        --wh-danger: #e11d48;
        --wh-warning: #f59e0b;
        --wh-success: #10b981;
        --wh-bg: #f8fafc;
        --wh-card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .wh-dashboard {
        background: var(--wh-bg);
        min-height: 100vh;
        padding-bottom: 3rem;
    }

    /* Premium Metric Cards */
    .metric-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: var(--wh-card-shadow);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .metric-card .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .bg-soft-teal {
        background: #f0fdfa;
        color: #0d9488;
    }

    .bg-soft-rose {
        background: #fff1f2;
        color: #e11d48;
    }

    .bg-soft-amber {
        background: #fffbeb;
        color: #f59e0b;
    }

    .bg-soft-slate {
        background: #f1f5f9;
        color: #475569;
    }

    /* Action Grid */
    .action-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        text-align: center;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
        text-decoration: none !important;
        display: block;
    }

    .action-card:hover {
        background: var(--wh-accent);
        border-color: var(--wh-accent);
        transform: translateY(-3px);
    }

    .action-card i {
        font-size: 1.5rem;
        color: var(--wh-accent);
        margin-bottom: 0.5rem;
        display: block;
    }

    .action-card:hover i,
    .action-card:hover span {
        color: white;
    }

    .action-card span {
        font-weight: 700;
        color: var(--wh-secondary);
        font-size: 0.85rem;
    }

    /* List Containers */
    .wh-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: var(--wh-card-shadow);
        height: 100%;
    }

    .wh-card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .wh-card-title {
        font-weight: 800;
        color: var(--wh-primary);
        font-size: 1.1rem;
        margin: 0;
    }

    /* Log Stream */
    .log-item {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.2s;
    }

    .log-item:hover {
        background: #f8fafc;
    }

    .log-type {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 12px;
    }

    .type-in {
        background: var(--wh-success);
        box-shadow: 0 0 8px var(--wh-success);
    }

    .type-out {
        background: var(--wh-danger);
        box-shadow: 0 0 8px var(--wh-danger);
    }

    .type-mutation {
        background: var(--wh-accent);
        box-shadow: 0 0 8px var(--wh-accent);
    }

    .type-adj {
        background: var(--wh-warning);
        box-shadow: 0 0 8px var(--wh-warning);
    }
</style>

<div class="wh-dashboard app-content mt-n4">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row align-items-center mb-5 pt-3">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="badge bg-soft-teal px-3 py-2 rounded-pill fw-bold">W-LOGISTICS v2.0</span>
                    <span class="text-muted small fw-bold"><i class="fas fa-clock me-1"></i>
                        <?= date('H:i') ?> WIB
                    </span>
                </div>
                <h3 class="fw-900 text-dark mb-1">Pusat Kendali Inventaris</h3>
                <p class="text-muted fw-500 mb-0">Monitor pergerakan stok dan ketersediaan bahan baku hari ini.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-inline-flex flex-column align-items-end">
                    <div class="p-2 bg-white rounded-12 shadow-sm border px-4 h-100 d-flex align-items-center">
                        <i class="fas fa-calendar-alt text-teal me-2"></i>
                        <span class="fw-900 text-dark small text-uppercase">
                            <?= date('l, d M Y') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Row -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="metric-card">
                    <div class="icon-box bg-soft-teal"><i class="fas fa-boxes"></i></div>
                    <div class="text-muted small fw-bold text-uppercase mb-1">Total Item Stok</div>
                    <h3 class="fw-900 mb-0">
                        <?= number_format($total_items) ?> <span class="fs-6 text-muted fw-600">Varian</span>
                    </h3>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="metric-card">
                    <div class="icon-box bg-soft-rose"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="text-muted small fw-bold text-uppercase mb-1">Stok Kritis</div>
                    <h3 class="fw-900 mb-0 text-danger">
                        <?= number_format($moves_today['critical_count']) ?> <span class="fs-6 text-muted fw-600">Butuh
                            Order</span>
                    </h3>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="metric-card">
                    <div class="icon-box bg-soft-teal"><i class="fas fa-arrow-down"></i></div>
                    <div class="text-muted small fw-bold text-uppercase mb-1">Barang Masuk (Hari Ini)</div>
                    <h3 class="fw-900 mb-0">
                        <?= number_format($moves_today['in']) ?> <span class="fs-6 text-muted fw-600">Trx</span>
                    </h3>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="metric-card">
                    <div class="icon-box bg-soft-amber"><i class="fas fa-arrow-up"></i></div>
                    <div class="text-muted small fw-bold text-uppercase mb-1">Barang Keluar (Hari Ini)</div>
                    <h3 class="fw-900 mb-0">
                        <?= number_format($moves_today['out']) ?> <span class="fs-6 text-muted fw-600">Trx</span>
                    </h3>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- Critical Stock List -->
            <div class="col-lg-7">
                <div class="wh-card">
                    <div class="wh-card-header">
                        <h5 class="wh-card-title"><i class="fas fa-layer-group text-danger me-2"></i>Daftar Stok Kritis
                        </h5>
                        <a href="<?= site_url('warehouse/bahan') ?>"
                            class="btn btn-sm btn-light fw-bold text-teal rounded-pill">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted small text-uppercase">
                                    <th class="ps-4">Material / Varian</th>
                                    <th class="text-center">Min Stok</th>
                                    <th class="text-center">Sisa Stok</th>
                                    <th class="text-end pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($low_stock)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">Semua stok aman terjaga.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($low_stock as $ls): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">
                                                    <?= $ls->varian ?>
                                                </div>
                                                <div class="small text-muted">
                                                    <?= $ls->kode_bahan ?>
                                                </div>
                                            </td>
                                            <td class="text-center fw-bold">
                                                <?= number_format($ls->stok_min, 0) ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-900 text-danger">
                                                    <?= number_format($ls->current_qty, 0) ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="badge bg-soft-rose rounded-pill px-3">Restock ASAP</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Logistics Action Center -->
            <div class="col-lg-5">
                <div class="wh-card">
                    <div class="wh-card-header">
                        <h5 class="wh-card-title"><i class="fas fa-bolt text-warning me-2"></i>Aksi Cepat Logistik</h5>
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="<?= site_url('warehouse/belanja_bahan') ?>" class="action-card">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Input Pembelian</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= site_url('warehouse/mutasi') ?>" class="action-card">
                                    <i class="fas fa-exchange-alt"></i>
                                    <span>Mutasi Barang</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= site_url('warehouse/stok_opname') ?>" class="action-card">
                                    <i class="fas fa-clipboard-check"></i>
                                    <span>Stok Opname</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= site_url('warehouse/bahan') ?>" class="action-card">
                                    <i class="fas fa-list-ul"></i>
                                    <span>Daftar Material</span>
                                </a>
                            </div>
                        </div>

                        <!-- System Alerts -->
                        <div class="mt-4 p-3 rounded-12 bg-light border border-dashed border-primary">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-presence bg-soft-teal p-2 rounded-12">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small">Integrasi Sistem Produksi</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Stok akan otomatis terpotong
                                        saat pesanan diproses oleh operator.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Logs Stream -->
        <div class="row">
            <div class="col-12">
                <div class="wh-card">
                    <div class="wh-card-header">
                        <h5 class="wh-card-title"><i class="fas fa-stream text-teal me-2"></i>Log Aktivitas Gudang
                            Terbaru</h5>
                        <span class="badge bg-soft-teal rounded-pill px-3">
                            <?= count($recent_logs) ?> Log Terakhir
                        </span>
                    </div>
                    <div class="log-stream">
                        <?php if (empty($recent_logs)): ?>
                            <div class="text-center py-5 text-muted">Belum ada aktivitas tercatat hari ini.</div>
                        <?php else: ?>
                            <?php foreach ($recent_logs as $log):
                                $dot_class = 'type-mutation';
                                if ($log->trx_type == 'Pembelian')
                                    $dot_class = 'type-in';
                                if ($log->trx_type == 'Pengambilan')
                                    $dot_class = 'type-out';
                                if ($log->trx_type == 'Opname' || $log->trx_type == 'Adjustment')
                                    $dot_class = 'type-adj';
                                ?>
                                <div class="log-item d-flex align-items-center">
                                    <div class="log-type <?= $dot_class ?>"></div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="fw-900 text-dark me-2">
                                                    <?= $log->varian ?>
                                                </span>
                                                <span
                                                    class="badge <?= $log->qty_in > 0 ? 'bg-soft-emerald' : 'bg-soft-rose' ?> rounded-pill px-2">
                                                    <?= $log->qty_in > 0 ? '+' . $log->qty_in : '-' . $log->qty_out ?>
                                                </span>
                                            </div>
                                            <div class="small text-muted fw-bold">
                                                <?= date('H:i', strtotime($log->created_at)) ?>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-1">
                                            <div class="small text-muted italic">
                                                <i class="fas fa-tag me-1 small"></i>
                                                <?= $log->trx_type ?>:
                                                <?= $log->catatan ?: '-' ?>
                                            </div>
                                            <div class="small text-dark fw-bold">
                                                <i class="fas fa-user-edit me-1 small"></i>
                                                <?= $log->username ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="wh-card-footer p-3 text-center border-top">
                        <a href="<?= site_url('warehouse/history') ?>"
                            class="small fw-bold text-teal text-decoration-none">LIHAT RIWAYAT LENGKAP</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>