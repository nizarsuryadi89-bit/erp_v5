<div class="table-responsive">
    <table class="table table-bordered table-hover w-100" id="tableDetailPembelian">
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="15%">Waktu</th>
                <th width="20%">No Faktur / Supplier</th>
                <th class="text-center" width="15%">Jumlah</th>
                <th class="text-center" width="10%">Satuan</th>
                <th width="15%">Penerima</th>
                <th width="20%">Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)): ?>
                <?php $no = 1; foreach ($logs as $log): ?>
                    <tr>
                        <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?= date('d M Y', strtotime($log->created_at)) ?></div>
                            <div class="small text-muted"><i class="far fa-clock me-1"></i><?= date('H:i:s', strtotime($log->created_at)) ?></div>
                        </td>
                        <td>
                            <?php if (!empty($log->nospk)): ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 mb-1 px-2 py-1"><i class="fas fa-file-invoice-dollar me-1"></i><?= htmlspecialchars($log->nospk) ?></span>
                                <div class="small fw-semibold text-dark text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($log->supplier_nama ?? '') ?>">
                                    <?= htmlspecialchars($log->supplier_nama ?? '-') ?>
                                </div>
                            <?php else: ?>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 mb-1 px-2 py-1"><i class="fas fa-sync-alt me-1"></i>STOK AWAL / OPNAME</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php 
                                $qty = abs((float)($log->qty_in > 0 ? $log->qty_in : $log->qty_out)); 
                            ?>
                            <span class="badge <?= $log->qty_in > 0 ? 'bg-success' : 'bg-primary' ?> fs-6 shadow-sm px-3 py-2">
                                <?= $log->qty_in > 0 ? '+' : '-' ?><?= str_replace('.00', '', number_format($qty, 2)) ?>
                            </span>
                        </td>
                        <td class="text-center fw-bold text-muted">
                            <?= htmlspecialchars($log->satuan_gudang ?? '-') ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 25px; height: 25px;">
                                    <i class="fas fa-user text-secondary" style="font-size: 0.7rem;"></i>
                                </div>
                                <span class="small fw-bold text-dark"><?= htmlspecialchars($log->operator_nama ?? 'Sistem') ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="small text-muted" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="<?= htmlspecialchars($log->catatan ?? '') ?>">
                                <?= htmlspecialchars($log->catatan ?? '-') ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    $('#tableDetailPembelian').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "pageLength": 10,
        "ordering": false
    });
</script>
