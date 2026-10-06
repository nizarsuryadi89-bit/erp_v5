<?php
// application/views/admin/warehouse/ledger_content.php
?>

<?php if ($current_gudang == 0): ?>
    <!-- Recap Inventory Snapshot -->
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title-premium mb-0">
                <i class="fas fa-globe-asia text-primary"></i> Rekapitulasi Semua Gudang
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <div class="input-group" style="width: 250px;">
                    <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                    <input type="text" id="searchSnapshot" class="form-control border-start-0" placeholder="Cari material...">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-premium align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">MATERIAL & VARIAN</th>
                        <?php foreach($warehouses as $wh): ?>
                            <th class="text-center"><?= strtoupper($wh->gudang_nama) ?></th>
                        <?php endforeach; ?>
                        <th class="text-center" style="background:#eef2fa; color:#0b5ed7;">TOTAL KESELURUHAN</th>
                    </tr>
                </thead>
                <tbody id="snapshotTableBody">
                    <?php if (empty($stock_summary)): ?>
                        <tr>
                            <td colspan="<?= count($warehouses) + 2 ?>" class="text-center py-5">
                                <div class="opacity-25 mb-2"><i class="fas fa-box-open fa-3x"></i></div>
                                <p class="text-muted fw-bold">Belum ada data stok.</p>
                            </td>
                        </tr>
                    <?php else:
                        // Helper function to format compound
                        $format_compound = function($qty_base, $konversi, $satuan_gudang, $satuan_produksi) {
                            $divisor = max(1, (int) $konversi);
                            $is_negative = $qty_base < 0;
                            $abs_pieces = abs(round((float)$qty_base));
                            $whole = floor($abs_pieces / $divisor);
                            $pieces = (int) $abs_pieces % (int) $divisor;

                            $f = "";
                            if ($whole > 0 || ($whole == 0 && $pieces == 0)) {
                                $f .= $whole . " " . $satuan_gudang;
                            }
                            if ($pieces > 0 && $konversi > 1) {
                                $f .= ($f ? " " : "") . $pieces . " " . $satuan_produksi;
                            }
                            if ($is_negative && $f !== "0 " . $satuan_gudang) {
                                $f = "-" . $f;
                            }
                            return $f;
                        };

                        foreach ($stock_summary as $s): 
                            $konversi = (float) ($s->konversi > 0 ? $s->konversi : 1);
                    ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-800 text-dark"><?= htmlspecialchars($s->bahan_nama) ?></div>
                                    <div class="small text-muted mt-1">
                                        <?php if (strtolower(trim($s->bahan_nama)) !== strtolower(trim($s->varian)) && !empty(trim($s->varian))): ?>
                                            <span class="font-monospace uppercase"><?= htmlspecialchars($s->varian) ?></span> • 
                                        <?php endif; ?>
                                        <span style="font-size: 0.65rem;">ID: #<?= $s->material_id ?> <?= $s->barcode ? '| ' . htmlspecialchars($s->barcode) : '' ?></span>
                                    </div>
                                </td>
                                
                                <?php foreach($warehouses as $wh): 
                                    $g_qty = isset($s->gudang_qtys[$wh->gudang_id]) ? $s->gudang_qtys[$wh->gudang_id] : 0;
                                    $gf = $format_compound($g_qty, $konversi, $s->satuan_gudang, $s->satuan_produksi);
                                ?>
                                    <td class="text-center border-start border-light" style="min-width: 150px;">
                                        <div class="fw-bold fs-6 text-dark"><?= $gf ?></div>
                                        <div class="small text-muted fw-bold mt-1" style="font-size: 0.7rem;"><?= number_format($g_qty, 0, ',', '.') ?> <?= $s->satuan_produksi ?></div>
                                    </td>
                                <?php endforeach; ?>

                                <td class="text-center border-start border-light" style="background:#f8f9fc; min-width: 170px;">
                                    <?php $tf = $format_compound($s->qty_base, $konversi, $s->satuan_gudang, $s->satuan_produksi); ?>
                                    <div class="fw-900 text-primary fs-5"><?= $tf ?></div>
                                    <div class="small text-primary fw-bold mt-1" style="font-size: 0.75rem;"><?= number_format($s->qty_base, 0, ',', '.') ?> <?= $s->satuan_produksi ?></div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <?php
    $wh_idx = array_search($current_gudang, array_column($warehouses, 'gudang_id'));
    $wh_name = ($wh_idx !== false) ? $warehouses[$wh_idx]->gudang_nama : 'Gudang Terpilih';
    // Determine if this is a production/store warehouse
    $is_produksi = (stripos($wh_name, 'produksi') !== false || stripos($wh_name, 'toko') !== false);
    ?>
    <!-- Current Inventory Snapshot -->
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title-premium mb-0">
                <i class="fas fa-microchip text-primary"></i> Snapshot Inventaris: <?= htmlspecialchars($wh_name) ?>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <div class="input-group" style="width: 250px;">
                    <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                    <input type="text" id="searchSnapshot" class="form-control border-start-0" placeholder="Cari material...">
                </div>
                <button class="btn btn-premium btn-premium-primary py-2 text-nowrap" data-bs-toggle="modal"
                    data-bs-target="#modalAdjustment">
                    <i class="fas fa-sync-alt me-2"></i>Penyesuaian Manual
                </button>
                <span class="badge-premium bg-soft-blue text-primary py-2 px-3">
                    <i class="fas fa-bolt me-1"></i> REALTIME
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-premium align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">MATERIAL & VARIAN</th>
                        <?php if (!$is_produksi): ?>
                            <th class="text-center">SISA STOK (GUDANG)</th>
                        <?php endif; ?>
                        <th class="text-center"><?= $is_produksi ? 'STOK TERSEDIA (PRODUKSI)' : 'TOTAL STOK (PRODUKSI)' ?></th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody id="snapshotTableBody">
                    <?php if (empty($stock_summary)): ?>
                        <tr>
                            <td colspan="<?= $is_produksi ? '3' : '4' ?>" class="text-center py-5">
                                <div class="opacity-25 mb-2"><i class="fas fa-box-open fa-3x"></i></div>
                                <p class="text-muted fw-bold">Belum ada data stok di gudang ini.</p>
                            </td>
                        </tr>
                    <?php else:
                        foreach ($stock_summary as $s): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-800 text-dark"><?= htmlspecialchars($s->bahan_nama) ?></div>
                                    <div class="small text-muted mt-1">
                                        <?php if (strtolower(trim($s->bahan_nama)) !== strtolower(trim($s->varian)) && !empty(trim($s->varian))): ?>
                                            <span class="font-monospace uppercase"><?= htmlspecialchars($s->varian) ?></span> • 
                                        <?php endif; ?>
                                        <span style="font-size: 0.65rem;">ID: #<?= $s->material_id ?> <?= $s->barcode ? '| ' . htmlspecialchars($s->barcode) : '' ?></span>
                                    </div>
                                </td>
                                
                                <?php
                                $konversi = (float) ($s->konversi > 0 ? $s->konversi : 1);
                                $total_pieces = round((float) $s->qty_base); // qty_base is already in pieces
                                $divisor = max(1, (int) $konversi);
                                $is_negative = $total_pieces < 0;
                                $abs_pieces = abs($total_pieces);
                                $whole = floor($abs_pieces / $divisor);
                                $pieces = (int) $abs_pieces % (int) $divisor;

                                $f = "";
                                if ($whole > 0 || ($whole == 0 && $pieces == 0)) {
                                    $f .= "<span class='fw-900'>" . $whole . " " . $s->satuan_gudang . "</span>";
                                }
                                if ($pieces > 0 && $konversi > 1) {
                                    $f .= ($f ? " " : "") . "<span class='small fw-bold'>" . $pieces . " " . $s->satuan_produksi . "</span>";
                                }
                                if ($is_negative && $f !== "0 " . $s->satuan_gudang) {
                                    $f = "-" . $f;
                                }
                                ?>
                                
                                <?php if (!$is_produksi): ?>
                                <td class="text-center">
                                    <div class="fw-900 text-primary fs-5"><?= strip_tags($f) ?></div>
                                </td>
                                <?php endif; ?>

                                <td class="text-center">
                                    <div class="fw-900 text-primary fs-5">
                                        <?= number_format(round((float) $s->qty_base), 0, ',', '.') ?>
                                        <span class="badge-premium bg-soft-blue text-primary px-2 py-1 ms-1" style="font-size:0.7rem;"><?= $s->satuan_produksi ?></span>
                                    </div>
                                    <?php if ($is_produksi && $konversi > 1): ?>
                                        <div class="text-muted fw-bold mt-1" style="font-size: 0.75rem;"><?= strip_tags($f) ?></div>
                                    <?php endif; ?>
                                    <div class="text-muted mt-1" style="font-size: 0.65rem;">Faktor Konversi:
                                        1:<?= number_format((float)($s->konversi > 0 ? $s->konversi : 1), 0, ',', '.') ?></div>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="viewMaterialHistory(<?= $s->material_id ?>, <?= $current_gudang ?>, '<?= addslashes($s->bahan_nama) ?>', '<?= addslashes($s->varian) ?>')">
                                        <i class="fas fa-history me-1"></i> History
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<script>
    $('#searchSnapshot').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("#snapshotTableBody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
</script>
