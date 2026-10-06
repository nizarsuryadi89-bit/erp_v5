<?php
/**
 * PREMIUM UNIFIED LIVE MONITOR COMPONENT
 * Warehouse Control Center v1.0
 */
?>

<!-- Tab Navigation for Monitor -->
<ul class="nav nav-pills mb-4 bg-light p-2 rounded-4" id="monitorTab" role="tablist">
    <li class="nav-item flex-fill" role="presentation">
        <button class="nav-link active fw-800 rounded-pill w-100 py-2 border-0" id="activity-tab" data-bs-toggle="pill"
            data-bs-target="#tab-activity" type="button" role="tab">
            <i class="fas fa-satellite-dish me-2"></i>LIVE FEED
        </button>
    </li>
    <li class="nav-item flex-fill" role="presentation">
        <button class="nav-link fw-800 rounded-pill w-100 py-2 border-0 position-relative" id="requests-tab"
            data-bs-toggle="pill" data-bs-target="#tab-requests" type="button" role="tab">
            <i class="fas fa-tasks me-2"></i>ANTRIAN
            <?php if (count($pending_requests) > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                    style="font-size: 0.6rem;">
                    <?= count($pending_requests) ?>
                </span>
            <?php endif; ?>
        </button>
    </li>
    <li class="nav-item flex-fill" role="presentation">
        <button class="nav-link fw-800 rounded-pill w-100 py-2 border-0" id="stock-tab" data-bs-toggle="pill"
            data-bs-target="#tab-stock" type="button" role="tab">
            <i class="fas fa-boxes me-2"></i>STOK KRITIS
        </button>
    </li>
</ul>

<div class="tab-content flex-grow-1 d-flex flex-column" id="monitorTabContent">

    <!-- 1. LIVE OPERATOR FEED -->
    <div class="tab-pane fade show active flex-grow-1 d-flex flex-column" id="tab-activity" role="tabpanel" style="min-height: 0;">
        <?php if (empty($operator_logs)): ?>
            <div class="text-center py-5 opacity-50">
                <i class="fas fa-running fa-3x mb-3"></i>
                <p class="small fw-bold">Belum ada aktivitas operator tercatat.</p>
            </div>
        <?php else: ?>
            <div class="activity-feed flex-grow-1" style="overflow-y: auto;">
                <?php foreach ($operator_logs as $log): ?>
                    <div class="activity-log-item d-flex gap-3 mb-4">
                        <div class="activity-avatar bg-soft-blue text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="activity-body border-bottom pb-3 flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-800 text-dark mb-0 fs-7">
                                    <?= htmlspecialchars($log->nama_lengkap ?: $log->username) ?>
                                </h6>
                                <span class="text-muted" style="font-size: 0.65rem;">
                                    <i class="far fa-clock me-1"></i>
                                    <?= date('H:i', strtotime($log->timestamp)) ?>
                                </span>
                            </div>
                            <div class="activity-action text-muted" style="font-size: 0.75rem;">
                                <?php
                                $action_labels = [
                                    'input_pembelian' => '<span class="badge bg-soft-success text-success py-1">PEMBELIAN</span> Menyelesaikan input stok masuk',
                                    'input_item_keranjang' => 'Menambahkan material ke keranjang',
                                    'dispatch_material' => '<span class="badge bg-soft-danger text-danger py-1">PENGIRIMAN</span> Mengeluarkan barang untuk produksi',
                                    'stok_opname' => '<span class="badge bg-soft-warning text-warning py-1">OPNAME</span> Melakukan audit stok fisik',
                                    'stock_adjustment' => '<span class="badge bg-soft-blue text-primary py-1">ADJUST</span> Menyesuaikan saldo stok',
                                    'add_master_material' => 'Menambah master data material baru',
                                ];
                                echo $action_labels[$log->action] ?? '<span class="badge bg-light text-dark py-1">' . strtoupper($log->action) . '</span> Melakukan tindakan pada sistem';
                                ?>
                            </div>
                            <?php if ($log->reference_id): ?>
                                <div class="mt-1">
                                    <span class="badge bg-light text-muted fw-normal" style="font-size: 0.6rem;">Ref ID: #
                                        <?= $log->reference_id ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- 2. PENDING REQUESTS -->
    <div class="tab-pane fade" id="tab-requests" role="tabpanel">
        <?php if (empty($pending_requests)): ?>
            <div class="text-center py-5 opacity-50">
                <i class="fas fa-check-double fa-3x mb-3"></i>
                <p class="small fw-bold">Semua pengajuan sudah diproses.</p>
            </div>
        <?php else: ?>
            <div class="pending-list">
                <?php foreach ($pending_requests as $req): ?>
                    <a href="<?= base_url('warehouse/dispatch_form/' . $req->pengajuan_id) ?>"
                        class="text-decoration-none border rounded-4 p-3 d-block mb-3 bg-white hover-shadow transition">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-soft-warning text-warning rounded-pill fw-800 px-3">PENDING REQUEST</span>
                            <span class="text-muted fw-bold" style="font-size: 0.65rem;">
                                <?= date('d/m/Y', strtotime($req->pengajuan_tgl)) ?>
                            </span>
                        </div>
                        <h6 class="text-dark fw-900 mb-1 fs-7"><i class="fas fa-file-invoice me-2 text-primary"></i>RQ-
                            <?= str_pad($req->pengajuan_id, 5, '0', STR_PAD_LEFT) ?>
                        </h6>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="bg-light rounded-circle p-1"
                                style="width: 24px; height: 24px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-user text-muted" style="font-size: 0.7rem;"></i>
                            </div>
                            <span class="text-muted small fw-bold">
                                <?= $req->nama_lengkap ?: 'Produksi' ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted font-monospace bg-light px-2 rounded">
                                <?= $req->gudang_nama ?>
                            </span>
                            <span class="text-primary fw-800 fs-7">Proses <i class="fas fa-chevron-right ms-1"></i></span>
                        </div>
                    </a>
                <?php endforeach; ?>
                <div class="text-center mt-3">
                    <a href="<?= base_url('warehouse/requests') ?>"
                        class="btn btn-link btn-sm fw-800 text-decoration-none">Lihat Semua Antrian</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- 3. CRITICAL STOCK -->
    <div class="tab-pane fade" id="tab-stock" role="tabpanel">
        <?php if (empty($low_stock_materials)): ?>
            <div class="text-center py-5 opacity-50 text-success">
                <i class="fas fa-check-circle fa-3x mb-3"></i>
                <p class="small fw-bold">Semua stok aman.</p>
            </div>
        <?php else: ?>
            <?php foreach (array_slice($low_stock_materials, 0, 5) as $item): ?>
                <div class="d-flex align-items-center gap-3 p-3 bg-white border rounded-4 mb-2">
                    <div class="bg-soft-danger text-danger rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 40px; height: 40px;">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <h6 class="fw-800 text-dark mb-0 text-truncate fs-7">
                            <?= htmlspecialchars($item->bahan_nama) ?>
                        </h6>
                        <p class="small text-muted mb-0">
                            <?= htmlspecialchars($item->varian) ?>
                        </p>
                    </div>
                    <div class="text-end">
                        <h6 class="fw-900 text-danger mb-0 fs-7">
                            <?= number_format($item->qty_stok) ?>
                        </h6>
                        <p class="text-muted fw-bold" style="font-size: 0.6rem;">
                            <?= strtoupper($item->satuan_nama) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="text-center mt-3">
                <a href="<?= base_url('warehouse/inventory') ?>"
                    class="btn btn-premium btn-premium-primary w-100 py-2 border-0">
                    <i class="fas fa-shopping-cart me-2"></i>BUAT RE-ORDER
                </a>
            </div>
        <?php endif; ?>
    </div>

</div>

<style>
    .nav-pills .nav-link {
        color: var(--slate-muted);
        font-size: 0.75rem;
        transition: all 0.3s;
    }

    .nav-pills .nav-link.active {
        background: white !important;
        color: var(--accent-blue) !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .hover-shadow:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
        transform: translateY(-2px);
    }

    .bg-soft-blue {
        background: rgba(99, 102, 241, 0.1);
    }

    .bg-soft-success {
        background: rgba(16, 185, 129, 0.1);
    }

    .bg-soft-danger {
        background: rgba(244, 63, 94, 0.1);
    }

    .bg-soft-warning {
        background: rgba(245, 158, 11, 0.1);
    }

    .fs-7 {
        font-size: 0.85rem !important;
    }

    .transition {
        transition: all 0.2s ease-in-out;
    }
</style>