<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0"><i class="fa fa-history text-primary me-2"></i>History</h4>
    <a href="<?= base_url('warehouse/ledger') ?>"
        class="btn btn-link btn-sm fw-800 text-decoration-none p-0">Lihat Semua</a>
</div>

<?php if (!empty($low_stock_materials)): ?>
    <div class="low-stock-alert">
        <i class="fa fa-exclamation-triangle"></i>
        <div>
            <h6>Stok Dibawah Minimum</h6>
            <p><?= count($low_stock_materials) ?> material butuh pengisian segera</p>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($recent_mutations)): ?>
    <?php foreach (array_slice($recent_mutations, 0, 6) as $mutation): ?>
        <div class="mutation-item">
            <div class="mutation-icon <?= strtolower($mutation->jenis_mutasi) ?>">
                <i
                    class="fa fa-<?= $mutation->jenis_mutasi == 'MASUK' ? 'plus' : ($mutation->jenis_mutasi == 'KELUAR' ? 'minus' : 'exchange-alt') ?> small"></i>
            </div>
            <div class="mutation-info">
                <h6><?= htmlspecialchars($mutation->bahan_nama) ?></h6>
                <p><?= $mutation->jenis_mutasi ?> &bull;
                    <?= date('H:i', strtotime($mutation->waktu_mutasi)) ?>
                </p>
            </div>
            <div class="mutation-qty">
                <h6 class="<?= $mutation->jenis_mutasi == 'MASUK' ? 'text-success' : 'text-danger' ?>">
                    <?= $mutation->jenis_mutasi == 'MASUK' ? '+' : '-' ?>
                    <?= number_format($mutation->qty_mutasi) ?>
                </h6>
                <p><?= htmlspecialchars($mutation->satuan_nama) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="text-center text-muted py-5">
        <div class="mb-3 opacity-25">
            <i class="fa fa-clipboard-list fa-3x"></i>
        </div>
        <p class="small fw-bold">Belum ada aktifitas mutasi hari ini</p>
    </div>
<?php endif; ?>
