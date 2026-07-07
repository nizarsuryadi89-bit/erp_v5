<?php
// application/views/admin/warehouse/ledger_content.php
?>

<?php if ($current_gudang > 0): ?>
    <!-- Current Inventory Snapshot -->
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <?php
            $wh_idx = array_search($current_gudang, array_column($warehouses, 'gudang_id'));
            $wh_name = ($wh_idx !== false) ? $warehouses[$wh_idx]->gudang_nama : 'Gudang Terpilih';
            ?>
            <h5 class="card-title-premium mb-0">
                <i class="fas fa-microchip text-primary"></i> Snapshot Inventaris: <?= htmlspecialchars($wh_name) ?>
            </h5>
            <div class="d-flex gap-2">
                <button class="btn btn-premium btn-premium-primary py-2" data-bs-toggle="modal"
                    data-bs-target="#modalAdjustment">
                    <i class="fas fa-sync-alt me-2"></i>Penyesuaian Manual
                </button>
                <span class="badge-premium bg-soft-blue text-primary py-2 px-3">
                    <i class="fas fa-bolt me-1"></i> REALTIME
                </span>
            </div>
        </div>

        <div class="table-responsive" style="max-height: 500px;">
            <table class="table table-premium align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">MATERIAL & VARIAN</th>
                        <th class="text-center">SISA STOK (GUDANG)</th>
                        <th class="text-center">STOK KONVERSI (PRODUKSI)</th>
                        <th class="text-center">SATUAN DASAR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stock_summary)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="opacity-25 mb-2"><i class="fas fa-box-open fa-3x"></i></div>
                                <p class="text-muted fw-bold">Belum ada data stok di gudang ini.</p>
                            </td>
                        </tr>
                    <?php else:
                        foreach ($stock_summary as $s): ?>
                            <tr>
                                <td class="ps-4">
                                    <!-- <div class="fw-800 text-dark"><?= htmlspecialchars($s->bahan_nama) ?></div> -->
                                    <div class="small text-muted font-monospace mt-1 uppercase"><?= htmlspecialchars($s->varian) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $konversi = (float) ($s->konversi > 0 ? $s->konversi : 1);
                                    $total_pieces = round((float) $s->qty_base); // qty_base is already in pieces
                                    $divisor = max(1, (int) $konversi);
                                    $whole = floor($total_pieces / $divisor);
                                    $pieces = (int) $total_pieces % (int) $divisor;

                                    $label = ($whole > 0 || ($whole == 0 && $pieces == 0)) ? $whole . " " . $s->satuan_gudang : "";
                                    if ($pieces > 0 && $konversi > 1)
                                        $label .= " " . $pieces . " " . $s->satuan_produksi;
                                    ?>
                                    <div class="badge-premium bg-light text-dark fw-900 fs-6 border d-inline-block">
                                        <?= trim($label) ?>
                                    </div>
                                    <div class="small text-muted mt-1" style="font-size: 0.65rem;">
                                        (<?= number_format($s->qty_stock, 3) ?>             <?= htmlspecialchars($s->satuan_gudang) ?>)
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-900 text-primary fs-5">
                                        <?= trim($label) ?>
                                    </div>
                                    <div class="small text-muted opacity-75" style="font-size: 0.7rem;">Faktor Konversi: 1 :
                                        <?= number_format($s->konversi, 0) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge-premium bg-soft-blue text-primary"><?= htmlspecialchars($s->satuan_gudang) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Movement Audit Trail -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="card-title-premium mb-4"><i class="fas fa-history text-secondary"></i> Jejak Audit <span
                class="text-muted fw-normal opacity-50 ms-2">(100 Log Terakhir)</span></h5>
        <button class="btn btn-light btn-premium btn-refresh-ledger py-2 border">
            <i class="fas fa-sync-alt me-2"></i>Sinkronkan Log
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-premium align-middle mb-0">
            <thead>
                <tr>
                    <th class="text-center">TANGGAL & WAKTU</th>
                    <th>LOKASI</th>
                    <th>MATERIAL</th>
                    <th class="text-center">JENIS TRX</th>
                    <th class="text-center text-success">QTY MASUK</th>
                    <th class="text-center text-danger">QTY KELUAR</th>
                    <th class="text-center">SALDO TERAKHIR</th>
                    <th>CATATAN & REF</th>
                    <th>OTORISASI OLEH</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="opacity-25 mb-2"><i class="fas fa-stream fa-3x"></i></div>
                            <p class="text-muted fw-bold">Belum ada histori pergerakan barang.</p>
                        </td>
                    </tr>
                <?php else:
                    foreach ($logs as $log): ?>
                        <tr>
                            <td class="text-center">
                                <div class="fw-800 text-dark small"><?= date('d M Y', strtotime($log->created_at)) ?></div>
                                <div class="small text-muted font-monospace" style="font-size: 0.65rem;">
                                    <?= date('H:i:s', strtotime($log->created_at)) ?>
                                </div>
                            </td>
                            <td class="small fw-800 text-muted">
                                <?= $log->gudang_name ?? $log->gudang_nama ?>
                            </td>
                            <td>
                                <div class="fw-800 text-dark"><?= htmlspecialchars($log->bahan_nama) ?></div>
                                <div class="small text-muted uppercase" style="font-size: 0.65rem;">
                                    <?= htmlspecialchars($log->varian) ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php
                                $badge_style = 'bg-light text-muted';
                                if ($log->trx_type == 'Pembelian')
                                    $badge_style = 'bg-soft-success text-success';
                                elseif ($log->trx_type == 'Produksi')
                                    $badge_style = 'bg-soft-warning text-warning';
                                elseif ($log->trx_type == 'Opname')
                                    $badge_style = 'bg-soft-danger text-danger';
                                elseif (stripos($log->trx_type, 'Mutation') !== false)
                                    $badge_style = 'bg-soft-blue text-primary';
                                ?>
                                <span class="badge-premium <?= $badge_style ?> fw-900" style="font-size: 0.6rem;">
                                    <?= strtoupper($log->trx_type) ?>
                                </span>
                            </td>
                            <td class="text-center fw-900 text-success">
                                <?php if ($log->qty_in > 0): ?>
                                    <?php
                                    $konversi = (float) ($log->konversi > 0 ? $log->konversi : 1);
                                    $total_pieces = round((float) $log->qty_in * $konversi);
                                    $divisor = max(1, (int) $konversi);
                                    $whole = floor($total_pieces / $divisor);
                                    $pieces = $total_pieces % $divisor;
                                    $f = ($whole > 0) ? $whole . " " . $log->satuan_gudang : "";
                                    if ($pieces > 0 && $konversi > 1)
                                        $f .= " " . $pieces . " " . $log->satuan_produksi;
                                    echo "+" . trim($f ?: "0 " . $log->satuan_gudang);
                                    ?>
                                <?php else: ?>-<?php endif; ?>
                            </td>
                            <td class="text-center fw-900 text-danger">
                                <?php if ($log->qty_out > 0): ?>
                                    <?php
                                    $konversi = (float) ($log->konversi > 0 ? $log->konversi : 1);
                                    $total_pieces = round((float) $log->qty_out * $konversi);
                                    $divisor = max(1, (int) $konversi);
                                    $whole = floor($total_pieces / $divisor);
                                    $pieces = $total_pieces % $divisor;
                                    $f = ($whole > 0) ? $whole . " " . $log->satuan_gudang : "";
                                    if ($pieces > 0 && $konversi > 1)
                                        $f .= " " . $pieces . " " . $log->satuan_produksi;
                                    echo "-" . trim($f ?: "0 " . $log->satuan_gudang);
                                    ?>
                                <?php else: ?>-<?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="badge-premium bg-light text-dark fw-900 border">
                                    <?php
                                    $konversi = (float) ($log->konversi > 0 ? $log->konversi : 1);
                                    $total_pieces = round((float) $log->qty_balance); // qty_balance is ALREADY in pieces
                                    $divisor = max(1, (int) $konversi);
                                    $whole = floor($total_pieces / $divisor);
                                    $pieces = (int) $total_pieces % (int) $divisor;
                                    $f = ($whole > 0 || ($whole == 0 && $pieces == 0)) ? $whole . " " . $log->satuan_gudang : "";
                                    if ($pieces > 0 && $konversi > 1)
                                        $f .= " " . $pieces . " " . $log->satuan_produksi;
                                    echo trim($f);
                                    ?>
                                </div>
                            </td>
                            <td>
                                <div class="small lh-sm fw-800 text-dark"><?= $log->catatan ?: '-' ?></div>
                                <div class="small text-muted font-monospace mt-1" style="font-size: 0.65rem;">Ref:
                                    #<?= $log->trx_id ?></div>
                            </td>
                            <td>
                                <div class="badge-premium bg-soft-blue text-primary border-0 rounded-pill px-3 py-1 fw-800"
                                    style="font-size: 0.7rem;">
                                    <i class="fas fa-user-circle me-1 opacity-50"></i> <?= $log->username ?: 'SYS' ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>