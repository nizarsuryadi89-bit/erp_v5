<div class="table-responsive">
    <table class="table table-bordered table-hover w-100" id="tableDetailKonsumsi">
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="12%">Waktu</th>
                <th width="20%">SPK / Produk</th>
                <th class="text-center" width="10%">Dipotong</th>
                <th class="text-center" width="10%">Satuan</th>
                <th width="13%">Operator</th>
                <th width="15%">Asal Gudang</th>
                <th width="15%">Catatan</th>
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
                            <?php
                                $itemTitle = !empty($log->product_nama) ? htmlspecialchars($log->product_nama) : 'Data Produk Terhapus / SPK Dihapus';
                                if (!empty($log->db_ket_1)) {
                                    $itemTitle .= ' ' . htmlspecialchars($log->db_ket_1);
                                }
                                
                                $meta = !empty($log->metadata) ? json_decode($log->metadata, true) : [];
                                if (!empty($meta) && is_array($meta)) {
                                    $fin_parts = [];
                                    if (!empty($meta['finishing'])) $fin_parts[] = $meta['finishing'];
                                    if (!empty($meta['ukuran'])) $fin_parts[] = $meta['ukuran'];
                                    if (!empty($fin_parts)) {
                                        $itemTitle .= ' [' . htmlspecialchars(implode(', ', $fin_parts)) . ']';
                                    }
                                }
                            ?>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 mb-1 px-2 py-1">
                                <i class="fas fa-file-invoice me-1"></i><?= htmlspecialchars($log->nospk ?? 'SPK Terhapus') ?>
                            </span>
                            <div class="small fw-semibold text-dark text-truncate" style="max-width: 250px;" title="<?= $itemTitle ?>">
                                <?= $itemTitle ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <?php 
                                $is_return = ($log->trx_type === 'Return');
                                $qty_cut_gudang = $is_return ? abs((float)$log->qty_in) : abs((float)$log->qty_out); 
                                $konversi = (float)$log->konversi > 0 ? (float)$log->konversi : 1;
                                
                                if (isset($log->qty_base) && (float)$log->qty_base > 0) {
                                    $qty_cut_prod = (float)$log->qty_base;
                                } else {
                                    $qty_cut_prod = $qty_cut_gudang * $konversi;
                                    if (preg_match('/Pakai:\s*([\d\.]+)/i', $log->catatan, $matches)) {
                                        $qty_cut_prod = (float)$matches[1];
                                    }
                                }
                            ?>
                            <div class="d-flex flex-column align-items-center">
                                <?php if ($is_return): ?>
                                    <span class="badge bg-success fs-6 border border-success shadow-sm px-3 py-2">+<?= str_replace(',00', '', number_format($qty_cut_prod, 2, ',', '.')) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger fs-6 border border-danger shadow-sm px-3 py-2">-<?= str_replace(',00', '', number_format($qty_cut_prod, 2, ',', '.')) ?></span>
                                <?php endif; ?>
                                
                                <?php if (!empty($log->satuan_produksi) && !empty($log->satuan_gudang) && strtolower(trim($log->satuan_produksi)) !== strtolower(trim($log->satuan_gudang))): ?>
                                    <div class="small text-muted mt-1 fw-bold" style="font-size: 0.7rem;">
                                        <i class="fas fa-exchange-alt me-1 text-info"></i><?= str_replace(',00', '', number_format($qty_cut_gudang, 2, ',', '.')) ?> <?= htmlspecialchars($log->satuan_gudang) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-center fw-bold text-muted">
                            <?= htmlspecialchars($log->satuan_produksi ?? $log->satuan_gudang ?? '-') ?>
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
                            <div class="small fw-bold text-primary"><i class="fas fa-warehouse me-1"></i><?= htmlspecialchars($log->gudang_nama ?? 'Gudang Pusat') ?></div>
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
    $('#tableDetailKonsumsi').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "pageLength": 10,
        "ordering": false
    });
</script>
