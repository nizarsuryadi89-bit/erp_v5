<?php $this->load->view('layout_admin/header_modul'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/tgm-gudang-stok.css') ?>">

<!-- <div class="app-content mt-2">
    <div class="container-fluid"> -->
<!-- Stats Summary -->
<div class="row g-3 mb-4 animate-up">
    <div class="col-6 col-md-3">
        <div class="summary-stat-card shadow-sm h-100">
            <div class="stat-v2-icon text-white" style="background: var(--primary-gradient)">
                <i class="fas fa-warehouse"></i>
            </div>
            <div>
                <div class="stat-v2-label">Total GUDANG</div>
                <div class="stat-v2-value">3</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="summary-stat-card shadow-sm h-100">
            <div class="stat-v2-icon text-white" style="background: var(--info-gradient)">
                <i class="fas fa-list-check"></i>
            </div>
            <div>
                <div class="stat-v2-label">Varian Aktif</div>
                <div class="stat-v2-value"><?= count($tampilData) ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="summary-stat-card shadow-sm h-100">
            <div class="stat-v2-icon text-white" style="background: var(--danger-gradient)">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div>
                <div class="stat-v2-label">Stok Kritis</div>
                <div class="stat-v2-value text-danger" id="critical-count">0</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="summary-stat-card shadow-sm h-100">
            <div class="stat-v2-icon text-white" style="background: var(--success-gradient)">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <div class="stat-v2-label">Stok Aman</div>
                <div class="stat-v2-value text-success" id="safe-count">0</div>
            </div>
        </div>
    </div>
</div>

<!-- Main Card -->
<div class="report-section-card animate-up" style="animation-delay: 0.1s">
    <div class="section-header">
        <h5><i class="fas fa-boxes"></i> Monitoring Stok Gudang Utama</h5>
        <div class="d-flex gap-2">
            <button onclick="resetStokGudang()" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold">
                <i class="fas fa-trash me-1"></i> Reset Stok & Log
            </button>
            <button onclick="location.reload()" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
            <a href="<?= base_url('warehouse/ledger') ?>" class="btn btn-sm btn-outline-light rounded-pill px-3 fw-bold">
                <i class="fas fa-book me-1"></i> Kartu Stok
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table premium-table table-hover mb-0 w-100" id="datatable-details">
            <thead>
                <tr>
                    <th width="40" class="text-center">No</th>
                    <th>Informasi Material</th>
                    <th class="text-center">Satuan Gudang</th>
                    <th class="text-center">Konversi</th>
                    <th class="text-center">Qty (Prod Unit)</th>
                    <th class="text-center">Stok Gudang</th>
                    <th class="text-center">Limit Aman</th>
                    <th width="100" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $critical = 0;
                foreach ($tampilData as $dt):
                    $total_stok = isset($stok_gudang[$dt->material_id]) ? $stok_gudang[$dt->material_id] : 0;
                    $isCritical = ($total_stok <= $dt->stok_min);
                    if ($isCritical)
                        $critical++;
                    ?>
                    <tr>
                        <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                        <td>
                            <div class="fw-800 text-dark"><?= htmlspecialchars($dt->varian) ?></div>
                            <div class="small text-muted font-monospace" style="font-size: 0.7rem;">
                                <i class="fas fa-barcode me-1"></i> <?= $dt->barcode ?: 'NO-BARCODE' ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="unit-pill"><?= htmlspecialchars($dt->satuan_gudang) ?></span>
                        </td>
                        <td class="text-center text-muted small">
                            <i class="fas fa-exchange-alt me-1 opacity-50"></i>
                            <?= number_format($dt->konversi ?: 1, 2) ?>
                        </td>
                        <td class="text-center">
                            <div class="fw-bold text-primary">
                                <?= number_format($total_stok * ($dt->konversi ?: 1), 2) ?>
                                <span class="small opacity-50"><?= $dt->satuan_produksi ?></span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="stock-value"><?= number_format($total_stok) ?></span>
                        </td>
                        <td class="text-center text-muted small fw-bold">
                            <?= number_format($dt->stok_min) ?>
                        </td>
                        <td class="text-center">
                            <span class="status-badge <?= $isCritical ? 'status-warning' : 'status-safe' ?>">
                                <?= $isCritical ? 'CRITICAL' : 'SAFE' ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
</div>

<script>
    function resetStokGudang() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Ini akan mereset semua stok dan log penggunaan pada gudang ini menjadi 0!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('warehouse/reset_stock_gudang/' . $selected_gudang) ?>";
            }
        });
    }

    $(document).ready(function () {
        const critical = <?= $critical ?>;
        const total = <?= count($tampilData) ?>;

        $('#critical-count').text(critical);
        $('#safe-count').text(total - critical);

        if ($.fn.DataTable) {
            $('#datatable-details').DataTable({
                "language": {
                    "sSearch": "Cari Cepat:",
                    "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" }
                },
                "pageLength": 25,
                "ordering": false,
                "dom": "<'row px-4 py-3'<'col-md-6'l><'col-md-6 d-flex justify-content-end text-end'f>>t<'row px-4 py-3'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>"
            });
        }
    });
</script>