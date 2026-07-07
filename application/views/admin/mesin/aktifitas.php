<div class="p-4" style="background: #f8fafc; min-height: 100vh;">
    <!-- HEADER SECTION -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h4 class="fw-900 text-dark mb-1 text-uppercase ls-1">
                <i class="fas fa-chart-line me-2 text-primary"></i> Aktifitas Mesin
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small fw-bold">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>" class="text-muted">ERP</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('mesin') ?>" class="text-muted">Mesin</a></li>
                    <li class="breadcrumb-item active text-primary">Statistik Produksi</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="btn-group rounded-pill overflow-hidden shadow-sm">
                <button class="btn btn-white btn-sm fw-800 px-3 active border">SEMUA</button>
                <button class="btn btn-white btn-sm fw-800 px-3 border">HARI INI</button>
                <button class="btn btn-white btn-sm fw-800 px-3 border">BULAN INI</button>
            </div>
            <button type="button" class="btn btn-primary btn-premium rounded-pill px-4 shadow-sm"
                onclick="window.print()">
                <i class="fas fa-print me-2"></i> CETAK LAPORAN
            </button>
        </div>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-primary border-4">
                <div class="text-muted small fw-800 text-uppercase ls-1">Total Pekerjaan</div>
                <div class="d-flex align-items-center justify-content-between mt-1">
                    <div class="fw-900 fs-3 text-dark">
                        <?= number_format(array_sum(array_column($aktifitas, 'total_pekerjaan'))) ?>
                    </div>
                    <i class="fas fa-tasks text-primary opacity-25 fs-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-success border-4">
                <div class="text-muted small fw-800 text-uppercase ls-1">Total Output (Qty)</div>
                <div class="d-flex align-items-center justify-content-between mt-1">
                    <div class="fw-900 fs-3 text-dark">
                        <?= number_format(array_sum(array_column($aktifitas, 'total_qty'))) ?>
                    </div>
                    <i class="fas fa-check-double text-success opacity-25 fs-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-danger border-4">
                <div class="text-muted small fw-800 text-uppercase ls-1">Total Reject</div>
                <div class="d-flex align-items-center justify-content-between mt-1">
                    <div class="fw-900 fs-3 text-dark">
                        <?= number_format(array_sum(array_column($aktifitas, 'total_reject'))) ?>
                    </div>
                    <i class="fas fa-times-circle text-danger opacity-25 fs-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-info border-4">
                <div class="text-muted small fw-800 text-uppercase ls-1">Total Luas (M²)</div>
                <div class="d-flex align-items-center justify-content-between mt-1">
                    <div class="fw-900 fs-3 text-dark">
                        <?= number_format(array_sum(array_column($aktifitas, 'total_m2')), 2) ?>
                    </div>
                    <i class="fas fa-ruler-combined text-info opacity-25 fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN TABLE CARD -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom border-light">
            <h6 class="fw-800 text-dark mb-0 ls-1 text-uppercase">Produktivitas Per Mesin</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="bg-light bg-opacity-50">
                            <th class="ps-4 py-3 fw-900 text-muted small text-uppercase ls-1">ID</th>
                            <th class="py-3 fw-900 text-muted small text-uppercase ls-1">INFORMASI MESIN</th>
                            <th class="py-3 fw-900 text-muted small text-uppercase ls-1 text-center">STATUS</th>
                            <th class="py-3 fw-900 text-muted small text-uppercase ls-1 text-center">TOTAL JOBS</th>
                            <th class="py-3 fw-900 text-muted small text-uppercase ls-1 text-center">GOOD QTY</th>
                            <th class="py-3 fw-900 text-muted small text-uppercase ls-1 text-center">REJECT</th>
                            <th class="py-3 fw-900 text-muted small text-uppercase ls-1 text-center">LUAS (M²)</th>
                            <th class="pe-4 py-3 fw-900 text-muted small text-uppercase ls-1 text-center">EFISIENSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aktifitas as $ak):
                            $total_all = (float) $ak->total_qty + (float) $ak->total_reject;
                            $efficiency = $total_all > 0 ? round(($ak->total_qty / $total_all) * 100, 1) : 0;
                            $eff_color = $efficiency > 95 ? 'success' : ($efficiency > 80 ? 'warning' : 'danger');

                            $is_active = ($ak->jobs_in_progress > 0);
                            ?>
                            <tr class="border-light border-bottom">
                                <td class="ps-4 fw-800 text-muted">#
                                    <?= $ak->mesin_id ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-pill bg-light text-primary p-2 me-3 d-flex align-items-center justify-content-center"
                                            style="width: 38px; height: 38px;">
                                            <i class="fas fa-robot small"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bolder text-dark">
                                                <?= strtoupper($ak->mesin_nama) ?>
                                            </div>
                                            <div class="text-muted small fw-bold opacity-75">
                                                <?= $ak->nama_perusahaan ?> •
                                                <?= $ak->kategori ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if ($is_active): ?>
                                        <span class="badge bg-primary rounded-pill px-3 py-2 fw-800 blink"
                                            style="font-size: 0.65rem;">
                                            <i class="fas fa-sync-alt fa-spin me-1"></i> RUNNING (
                                            <?= $ak->jobs_in_progress ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border rounded-pill px-3 py-2 fw-800"
                                            style="font-size: 0.65rem;">
                                            IDLE
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center fw-800 text-dark">
                                    <?= number_format($ak->total_pekerjaan) ?>
                                </td>
                                <td class="text-center fw-800 text-success">
                                    <?= number_format($ak->total_qty) ?>
                                </td>
                                <td class="text-center fw-800 text-danger">
                                    <?= number_format($ak->total_reject) ?>
                                </td>
                                <td class="text-center fw-800 text-info">
                                    <?= number_format($ak->total_m2, 2) ?>
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="fw-900 text-<?= $eff_color ?> mb-1">
                                            <?= $efficiency ?>%
                                        </div>
                                        <div class="progress w-100" style="height: 4px; max-width: 80px;">
                                            <div class="progress-bar bg-<?= $eff_color ?>" role="progressbar"
                                                style="width: <?= $efficiency ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-1 {
        letter-spacing: 0.5px;
    }

    .fw-800 {
        font-weight: 800;
    }

    .fw-900 {
        font-weight: 900;
    }

    .blink {
        animation: blinker 2s linear infinite;
    }

    @keyframes blinker {
        50% {
            opacity: 0.7;
        }
    }

    .btn-premium {
        background: #1e293b;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-premium:hover {
        background: #0f172a;
        transform: translateY(-1px);
    }

    @media print {

        .app-header,
        .app-sidebar,
        .btn-group,
        .btn-premium {
            display: none !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #eee !important;
        }

        .p-4 {
            padding: 0 !important;
        }
    }
</style>