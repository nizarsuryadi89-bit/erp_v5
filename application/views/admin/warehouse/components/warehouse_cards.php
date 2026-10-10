<?php if (!empty($stock_summary)): ?>
    <?php foreach ($stock_summary as $warehouse): ?>
        <div class="col-md-6">
            <div class="warehouse-card">
                <div class="warehouse-header">
                    <div class="warehouse-icon">
                        <i class="fa fa-warehouse"></i>
                    </div>
                    <div class="warehouse-info">
                        <h5>
                            <?= htmlspecialchars($warehouse->gudang_nama) ?>
                        </h5>
                        <p>UID: #WH-
                            <?= str_pad($warehouse->gudang_id, 3, '0', STR_PAD_LEFT) ?>
                        </p>
                    </div>
                </div>
                <a href="<?= base_url('warehouse/stock?gudang_id=' . $warehouse->gudang_id) ?>"
                    class="btn btn-primary w-100 py-3 rounded-4 fw-900 border-0 shadow-sm">
                    <i class="fa fa-boxes me-2"></i>STOK GUDANG
                </a>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="col-12 text-center text-muted py-5">
        <p>No warehouse data found.</p>
    </div>
<?php endif; ?>