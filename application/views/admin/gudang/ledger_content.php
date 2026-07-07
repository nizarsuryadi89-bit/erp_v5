<?php if ($current_gudang > 0): ?>
    <!-- Current Inventory Snapshot -->
    <div class="report-section-card animate-up">
        <div class="section-header" style="background: var(--info-gradient)">
            <?php
            $wh_idx = array_search($current_gudang, array_column($warehouses, 'gudang_id'));
            $wh_name = ($wh_idx !== false) ? $warehouses[$wh_idx]->gudang_nama : 'Gudang Terpilih';
            ?>
            <h5><i class="fas fa-microchip"></i> Snapshot Stok: <?= $wh_name ?></h5>
            <div>
                <button class="btn btn-sm btn-warning text-dark fw-900 px-3 rounded-pill me-2 shadow-sm border-0"
                    data-bs-toggle="modal" data-bs-target="#modalAdjustment">
                    <i class="fas fa-sync-alt me-1"></i> SINKRONISASI MANUAL
                </button>
                <span class="badge bg-white text-info small fw-bold px-3 py-1 rounded-pill">REALTIME</span>
            </div>
        </div>
        <div class="table-responsive" style="max-height: 450px;">
            <table class="table premium-table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Identitas Material / Varian</th>
                        <th class="text-center">Sisa Stok Saat Ini</th>
                        <th class="text-center">Satuan Dasar</th>
                        <th class="text-center">Stok Unit Produksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stock_summary)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Belum ada data stok di gudang ini.</td>
                        </tr>
                    <?php else:
                        foreach ($stock_summary as $s): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-800 text-dark"><?= $s->bahan_nama ?></div>
                                    <div class="small text-muted"><?= $s->varian ?></div>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $konversi = (float) ($s->konversi ?: 1);
                                    $total_pieces = round((float) $s->qty * $konversi);
                                    $whole = floor($total_pieces / $konversi);
                                    $pieces = $total_pieces % $konversi;

                                    $label = ($whole > 0 || ($whole == 0 && $pieces == 0)) ? $whole . " " . $s->satuan_gudang : "";
                                    if ($pieces > 0 && $konversi > 1)
                                        $label .= " " . $pieces . " " . $s->satuan_produksi;
                                    ?>
                                    <div class="balance-box d-inline-block"><?= trim($label) ?></div>
                                    <div class="small text-muted font-monospace mt-1" style="font-size: 0.65rem;">
                                        (<?= number_format($s->qty, 3) ?>             <?= $s->satuan_gudang ?>)</div>
                                </td>
                                <td class="text-center"><span
                                        class="badge-premium bg-light text-dark border"><?= $s->satuan_gudang ?></span></td>
                                <td class="text-center">
                                    <div class="fw-900 text-primary">
                                        <?= trim($label) ?>
                                        <div class="small fw-normal opacity-50" style="font-size: 0.7rem;">Conversion:
                                            1:<?= number_format($s->konversi, 0) ?></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Movement Audit Trail -->
<div class="report-section-card animate-up" style="animation-delay: 0.1s">
    <div class="section-header">
        <h5><i class="fas fa-history"></i> Audit Trail (100 Log Terakhir)</h5>
        <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold btn-refresh-ledger">
            <i class="fas fa-sync-alt me-1"></i> Refresh
        </button>
    </div>
    <div class="table-responsive">
        <table class="table premium-table table-hover mb-0">
            <thead>
                <tr>
                    <th class="text-center">Timestamp</th>
                    <th>Lokasi</th>
                    <th>Material</th>
                    <th class="text-center">Tipe Log</th>
                    <th class="text-center">Qty Masuk</th>
                    <th class="text-center">Qty Keluar</th>
                    <th class="text-center">Saldo Akhir</th>
                    <th>Catatan & Ref</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted"><i
                                class="fas fa-stream fa-2x mb-2 d-block opacity-20"></i>Belum ada histori pergerakan barang.
                        </td>
                    </tr>
                <?php else:
                    foreach ($logs as $log): ?>
                        <tr>
                            <td class="text-center">
                                <div class="fw-800 text-dark"><?= date('d/m/y', strtotime($log->created_at)) ?></div>
                                <div class="small text-muted font-monospace"><?= date('H:i', strtotime($log->created_at)) ?>
                                </div>
                            </td>
                            <td><span class="badge-premium bg-light text-dark border"><i
                                        class="fas fa-warehouse me-1 opacity-50"></i> <?= $log->gudang_nama ?></span></td>
                            <td>
                                <div class="fw-800 text-dark"><?= $log->bahan_nama ?></div>
                                <div class="small text-muted"><?= $log->varian ?></div>
                            </td>
                            <td class="text-center">
                                <?php
                                $badge_class = 'bg-secondary';
                                if ($log->trx_type == 'Pembelian')
                                    $badge_class = 'bg-primary';
                                elseif ($log->trx_type == 'Produksi')
                                    $badge_class = 'bg-warning text-dark';
                                elseif ($log->trx_type == 'Opname')
                                    $badge_class = 'bg-danger';
                                elseif ($log->trx_type == 'Mutation')
                                    $badge_class = 'bg-info';
                                ?>
                                <span class="badge-premium <?= $badge_class ?>"><?= $log->trx_type ?></span>
                            </td>
                            <td class="text-center qty-in">
                                <?php if ($log->qty_in > 0): ?>
                                    <?php
                                    $konversi = (float) ($log->konversi ?: 1);
                                    $total_pieces = round((float) $log->qty_in * $konversi);
                                    $whole = floor($total_pieces / $konversi);
                                    $pieces = $total_pieces % $konversi;
                                    $f = ($whole > 0) ? $whole . " " . $log->satuan_gudang : "";
                                    if ($pieces > 0 && $konversi > 1)
                                        $f .= " " . $pieces . " " . $log->satuan_produksi;
                                    echo "+" . trim($f ?: "0 " . $log->satuan_gudang);
                                    ?>
                                <?php else: ?>-<?php endif; ?>
                            </td>
                            <td class="text-center qty-out">
                                <?php if ($log->qty_out > 0): ?>
                                    <?php
                                    $konversi = (float) ($log->konversi ?: 1);
                                    $total_pieces = round((float) $log->qty_out * $konversi);
                                    $whole = floor($total_pieces / $konversi);
                                    $pieces = $total_pieces % $konversi;
                                    $f = ($whole > 0) ? $whole . " " . $log->satuan_gudang : "";
                                    if ($pieces > 0 && $konversi > 1)
                                        $f .= " " . $pieces . " " . $log->satuan_produksi;
                                    echo "-" . trim($f ?: "0 " . $log->satuan_gudang);
                                    ?>
                                <?php else: ?>-<?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="balance-box">
                                    <?php
                                    $konversi = (float) ($log->konversi ?: 1);
                                    $total_pieces = round((float) $log->qty_balance * $konversi);
                                    $whole = floor($total_pieces / $konversi);
                                    $pieces = $total_pieces % $konversi;
                                    $f = ($whole > 0 || ($whole == 0 && $pieces == 0)) ? $whole . " " . $log->satuan_gudang : "";
                                    if ($pieces > 0 && $konversi > 1)
                                        $f .= " " . $pieces . " " . $log->satuan_produksi;
                                    echo trim($f);
                                    ?>
                                </div>
                            </td>
                            <td>
                                <div class="small lh-sm fw-bold"><?= $log->catatan ?: '-' ?></div>
                                <div class="small text-muted font-monospace mt-1" style="font-size: 0.65rem;">Ref:
                                    #<?= $log->trx_id ?></div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="badge bg-primary-subtle text-primary rounded-pill px-2"
                                        style="font-size: 0.65rem;">
                                        <i class="fas fa-user-circle me-1"></i><?= $log->username ?: 'SYS' ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>