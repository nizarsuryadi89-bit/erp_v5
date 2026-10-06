<?php
/**
 * TRANSACTION MONITOR COMPONENT
 * Purchases & Dispatches Live Feed
 */
?>

<div class="row g-4 h-100">
    <!-- 1. LIVE PURCHASES (STOCK IN) -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4 h-100 d-flex flex-column" style="border-radius: 28px; background: white;">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-shrink-0">
                <h5 class="m-0 fw-900 text-dark"><i class="fas fa-truck-loading text-success me-2"></i>Live Pembelian
                </h5>
                <span class="badge bg-soft-success text-success px-3 py-1 rounded-pill fw-bold"
                    style="font-size: 0.6rem;">BARANG MASUK</span>
            </div>

            <?php if (empty($latest_purchases)): ?>
                <div class="text-center py-5 opacity-50 flex-grow-1 d-flex flex-column justify-content-center">
                    <i class="fas fa-box-open fa-3x mb-3"></i>
                    <p class="small fw-bold">Belum ada pembelian hari ini.</p>
                </div>
            <?php else: ?>
                <div class="transaction-timeline flex-grow-1">
                    <?php foreach ($latest_purchases as $p): ?>
                        <div class="trx-item d-flex gap-3 mb-4">
                            <div class="trx-icon bg-soft-success text-success rounded-4 d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 48px; height: 48px;">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div class="trx-body border-bottom pb-3 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="fw-800 text-dark mb-0 fs-7">#
                                        <?= $p->no_faktur ?>
                                    </h6>
                                    <span class="text-muted" style="font-size: 0.65rem;">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        <?= date('d/m/y', strtotime($p->pembelian_tgl)) ?>
                                        <i class="far fa-clock ms-2 me-1"></i>
                                        <?= ($p->pembelian_waktu == '0000-00-00 00:00:00' || empty($p->pembelian_waktu)) ? '--:--' : date('H:i', strtotime($p->pembelian_waktu)) ?>
                                    </span>
                                </div>
                                <p class="text-muted mb-1" style="font-size: 0.75rem; font-weight: 600;">
                                    <i class="fas fa-store me-1"></i>
                                    <?= $p->supplier_nama ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-dark fw-bold" style="font-size: 0.65rem;">Rp
                                        <?= number_format($p->pembelian_total) ?>
                                    </span>
                                    <a href="javascript:void(0)" onclick="openModalDetail('<?= base_url('warehouse/cetakPembelian/' . $p->pembelian_id) ?>')"
                                        class="text-success fw-800 fs-8 text-decoration-none">Detail <i
                                            class="fas fa-print ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. LIVE DISPATCH (STOCK OUT TO PRODUCTION) -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4 h-100 d-flex flex-column" style="border-radius: 28px; background: white;">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-shrink-0">
                <h5 class="m-0 fw-900 text-dark"><i class="fas fa-paper-plane text-info me-2"></i>Live Dispatch WIP
                </h5>
                <span class="badge bg-soft-info text-info px-3 py-1 rounded-pill fw-bold"
                    style="font-size: 0.6rem;">BARANG KELUAR</span>
            </div>

            <?php if (empty($latest_dispatches)): ?>
                <div class="text-center py-5 opacity-50 flex-grow-1 d-flex flex-column justify-content-center">
                    <i class="fas fa-clipboard-check fa-3x mb-3"></i>
                    <p class="small fw-bold">Belum ada barang keluar ke produksi hari ini.</p>
                </div>
            <?php else: ?>
                <div class="transaction-timeline flex-grow-1">
                    <?php foreach ($latest_dispatches as $pg): ?>
                        <div class="trx-item d-flex gap-3 mb-4">
                            <div class="trx-icon bg-soft-info text-info rounded-4 d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 48px; height: 48px;">
                                <i class="fas fa-industry"></i>
                            </div>
                            <div class="trx-body border-bottom pb-3 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="fw-800 text-dark mb-0 fs-7">DSP-
                                        <?= str_pad($pg->pengambilan_id, 5, '0', STR_PAD_LEFT) ?>
                                    </h6>
                                    <span class="text-muted" style="font-size: 0.65rem;">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        <?= date('d/m/y', strtotime($pg->pengambilan_tgl)) ?>
                                        <i class="far fa-clock ms-2 me-1"></i>
                                        <?= ($pg->pengambilan_waktu == '0000-00-00 00:00:00' || empty($pg->pengambilan_waktu)) ? '--:--' : date('H:i', strtotime($pg->pengambilan_waktu)) ?>
                                    </span>
                                </div>
                                <p class="text-muted mb-1" style="font-size: 0.75rem; font-weight: 600;">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?= $pg->gudang_nama ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-soft-blue text-primary fw-bold" style="font-size: 0.65rem;">Ke Gg
                                        Produksi</span>
                                    <a href="javascript:void(0)" onclick="openModalDetail('<?= base_url('warehouse/cetakPengambilan/' . $pg->pengambilan_id) ?>')"
                                        class="text-info fw-800 fs-8 text-decoration-none">Detail <i
                                            class="fas fa-print ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .bg-soft-success {
        background: rgba(16, 185, 129, 0.1);
    }

    .bg-soft-info {
        background: rgba(6, 182, 212, 0.1);
    }

    .fs-7 {
        font-size: 0.85rem !important;
    }

    .fs-8 {
        font-size: 0.75rem !important;
    }

    .trx-item:last-child .trx-body {
        border-bottom: 0 !important;
    }

    .transaction-timeline {
        max-height: 500px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .transaction-timeline::-webkit-scrollbar {
        width: 4px;
    }

    .transaction-timeline::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
</style>

<script>
    if (typeof openModalDetail !== 'function') {
        function openModalDetail(url) {
            Swal.fire({
                html: '<iframe src="' + url + '" style="width:100%; height:75vh; border:none; border-radius: 10px;"></iframe>',
                width: '850px',
                padding: '0',
                showCloseButton: true,
                showConfirmButton: false,
                background: '#fff',
                customClass: {
                    popup: 'rounded-4 shadow-lg'
                }
            });
        }
    }
</script>