<!-- Papan Sortir Pengiriman Marketplace - Digital Manifest -->
<style>
    :root {
        --board-bg: #f8fafc;
        --col-width: 290px; /* Reduced from 380px */
        --accent-tokopedia: #42b549;
        --accent-shopee: #ee4d2d;
        --accent-tiktok: #000000;
        --accent-lazada: #000083;
        --accent-other: #64748b;
    }

    .sorting-board-container {
        display: flex;
        overflow-x: auto;
        padding: 0.75rem;
        gap: 0.75rem; /* Tighter gap */
        height: calc(100vh - 110px);
        background: var(--board-bg);
        scrollbar-width: thin;
    }

    .sorting-column {
        flex: 0 0 var(--col-width);
        background: #f1f5f9;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        max-height: 100%;
        border: 1px solid rgba(0,0,0,0.1);
    }

    .column-header {
        padding: 8px 12px;
        border-radius: 10px 10px 0 0;
        color: white;
        text-align: center;
    }

    .m-tokopedia { background: var(--accent-tokopedia); }
    .m-shopee { background: var(--accent-shopee); }
    .m-tiktok { background: var(--accent-tiktok); }
    .m-lazada { background: var(--accent-lazada); }
    .m-other { background: var(--accent-other); }

    .batch-title-mini {
        background: #1e293b;
        color: #fff;
        font-size: 9px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 3px;
        margin-bottom: 6px;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .item-card {
        background: white;
        border-radius: 6px;
        padding: 6px 10px;
        margin-bottom: 5px;
        border-left: 3px solid #cbd5e1;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        transition: all 0.15s ease;
    }

    .item-card:hover { border-left-width: 6px; background: #fafafa; }

    .resi-text {
        font-weight: 800;
        color: #1e293b;
        font-size: 11px;
    }

    .spk-badge {
        font-size: 9px;
        color: #94a3b8;
        font-weight: 700;
    }

    .product-list { margin: 3px 0; padding: 0; }

    .product-item {
        font-size: 11px;
        line-height: 1.25;
        font-weight: 700;
        color: #334155;
        display: flex;
        gap: 5px;
        margin-bottom: 1px;
    }

    .qty-num { color: #3b82f6; font-weight: 900; }

    .card-footer {
        font-size: 9px;
        font-weight: 700;
        color: #94a3b8;
        display: flex;
        justify-content: space-between;
        border-top: 1px dashed #e2e8f0;
        padding-top: 3px;
        margin-top: 3px;
        text-transform: uppercase;
    }

    .empty-state { padding: 4rem 1rem; text-align: center; color: #94a3b8; }
</style>

<div class="app-content">
    <div class="d-flex justify-content-between align-items-center mb-0 px-4 pt-3">
        <h5 class="fw-900 text-dark mb-0"><i class="fas fa-th-large me-2"></i> PAPAN SORTIR PENGIRIMAN</h5>
        <div class="d-flex align-items-center gap-2">
            <?php if ($this->session->userdata('level') == 1): ?>
                <a href="<?= site_url('transaksi/clean_marketplace_transactions') ?>" 
                   class="btn btn-xs btn-outline-danger fw-bold rounded-pill px-3"
                   onclick="return confirm('Hapus semua transaksi Marketplace hari ini? Tindakan ini tidak dapat dibatalkan!')">
                   <i class="fas fa-trash-alt me-1"></i> CLEAN DATA
                </a>
            <?php endif; ?>
            <span class="badge bg-white text-dark shadow-sm px-3 py-2 fw-800 border small">
                <i class="far fa-calendar-alt me-2 text-primary"></i> <?= date('d F Y', strtotime($today)) ?>
            </span>
            <button onclick="location.reload()" class="btn btn-xs btn-dark fw-bold rounded-pill px-3">
                <i class="fas fa-sync-alt me-1"></i> REFRESH
            </button>
        </div>
    </div>

    <?php if (empty($board)): ?>
        <div class="empty-state">
            <i class="fas fa-shipping-fast fa-4x opacity-10 mb-3"></i>
            <h5 class="fw-bold text-muted">Belum ada pesanan untuk disortir hari ini.</h5>
        </div>
    <?php else: ?>
        <div class="sorting-board-container">
            <?php foreach ($board as $key => $col): ?>
                <?php 
                    $m_class = 'm-other';
                    if (stripos($col['market'], 'TOKOPEDIA') !== false) $m_class = 'm-tokopedia';
                    elseif (stripos($col['market'], 'SHOPEE') !== false) $m_class = 'm-shopee';
                    elseif (stripos($col['market'], 'TIKTOK') !== false) $m_class = 'm-tiktok';
                    elseif (stripos($col['market'], 'LAZADA') !== false) $m_class = 'm-lazada';
                ?>
                <div class="sorting-column">
                    <div class="column-header <?= $m_class ?>">
                        <div style="font-size: 9px; font-weight: 800; opacity: 0.8; text-transform: uppercase;"><?= $col['market'] ?></div>
                        <h6 class="fw-900 mb-0"><?= $col['courier'] ?></h6>
                    </div>
                    
                    <div class="flex-grow-1 overflow-auto p-2" style="scrollbar-width: none;">
                        <?php foreach ($col['batches'] as $batch_name => $orders): ?>
                            <div class="mb-3">
                                <span class="batch-title-mini"><?= $batch_name ?></span>
                                <?php foreach ($orders as $order): ?>
                                    <div class="item-card">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="resi-text text-truncate me-2"><?= $order->resi_marketplace ?: 'NO-RESI' ?></span>
                                            <span class="spk-badge"><?= $order->nospk ?></span>
                                        </div>
                                        
                                        <div class="product-list">
                                            <?php foreach ($order->items as $item): ?>
                                                <div class="product-item">
                                                    <span class="qty-num"><?= $item->qty ?>x</span>
                                                    <span class="text-truncate"><?= strtoupper($item->product_nama) ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <div class="card-footer">
                                            <span><i class="far fa-user me-1"></i> <?= $order->konsumen_nama ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
