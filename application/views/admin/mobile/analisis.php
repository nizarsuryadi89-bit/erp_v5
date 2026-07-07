<div class="row g-3">
    <!-- Summary Cards -->
    <div class="col-6">
        <div class="p-3 bg-white rounded-4 shadow-sm h-100 border-start border-4 border-danger">
            <div class="small text-muted fw-bold mb-1">STOK KRITIS</div>
            <div class="h3 fw-900 text-danger mb-0">
                <?php
                $kritis = 0;
                foreach ($analisis_stok as $s) {
                    if ($s->stok < 10)
                        $kritis++; // Simple threshold
                }
                echo $kritis;
                ?>
            </div>
            <div class="small text-danger mt-1">Perlu Restock</div>
        </div>
    </div>
    <div class="col-6">
        <div class="p-3 bg-white rounded-4 shadow-sm h-100 border-start border-4 border-success">
            <div class="small text-muted fw-bold mb-1">FAST MOVING</div>
            <div class="h3 fw-900 text-success mb-0">
                <?php
                $fast = 0;
                foreach ($analisis_stok as $s) {
                    if ($s->terjual_bulan_ini > 50)
                        $fast++;
                }
                echo $fast;
                ?>
            </div>
            <div class="small text-success mt-1">Item Laris</div>
        </div>
    </div>

    <!-- Inventory Analysis Table -->
    <div class="col-12 mt-3">
        <div class="section-title mb-3">
            <i class="bi bi-graph-up-arrow text-primary"></i> Pergerakan Stok Bulan Ini
        </div>

        <?php foreach ($analisis_stok as $item):
            $turnover = ($item->terjual_bulan_ini > 0) ? 'text-success' : 'text-muted';
            $stock_status = ($item->stok < 10) ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success';
            ?>
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 16px;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0 w-75 text-truncate"><?= $item->bahan_nama ?></h6>
                        <span class="badge <?= $stock_status ?> rounded-pill">Sisa: <?= $item->stok ?></span>
                    </div>

                    <div class="d-flex justify-content-between align-items-end">
                        <div class="small text-muted">
                            Satuan: <?= $item->satuan_nama ?>
                        </div>
                        <div class="text-end">
                            <div class="small fw-bold text-muted">Terpakai Bulan Ini</div>
                            <div class="h5 fw-900 <?= $turnover ?> mb-0">
                                <i class="bi bi-arrow-down-circle-fill me-1"></i>
                                <?= number_format($item->terjual_bulan_ini) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar Stock Level -->
                    <div class="progress mt-3" style="height: 6px; border-radius: 10px;">
                        <?php
                        $percent = ($item->stok > 0) ? ($item->stok / ($item->stok + $item->terjual_bulan_ini)) * 100 : 0;
                        $color = ($percent < 20) ? 'bg-danger' : 'bg-primary';
                        ?>
                        <div class="progress-bar <?= $color ?>" role="progressbar" style="width: <?= $percent ?>%"></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($analisis_stok)): ?>
            <div class="text-center py-5">
                <p class="text-muted">Data penggunaan stok belum tersedia bulan ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>