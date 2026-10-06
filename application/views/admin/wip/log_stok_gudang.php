<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    .report-section-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    .section-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .section-header h5 {
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .table th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        vertical-align: middle;
        white-space: nowrap;
    }
    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }
</style>

<div class="app-content mt-3">
    <div class="report-section-card">
        <div class="section-header">
            <h5>
                <i class="fas fa-boxes"></i> Log Mutasi Stok Gudang
            </h5>
            <div class="d-flex align-items-center gap-2">
                <button onclick="location.reload()" class="btn btn-sm btn-light rounded-pill px-3 fw-bold shadow-sm">
                    <i class="fas fa-sync-alt me-1"></i> Refresh Data
                </button>
            </div>
        </div>

        <div class="p-4 bg-white">
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100" id="tableKonsumsiBahan">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="12%">Waktu</th>
                            <th width="15%">Transaksi</th>
                            <th width="20%">Bahan & Varian</th>
                            <th width="10%">Gudang</th>
                            <th class="text-center" width="7%">Masuk</th>
                            <th class="text-center" width="7%">Keluar</th>
                            <th class="text-center" width="7%">Sisa</th>
                            <th width="8%">Satuan</th>
                            <th width="9%">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle mb-2 fa-2x opacity-50"></i><br>
                                    Belum ada data pemotongan bahan hari ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($logs as $log): ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= date('d M Y', strtotime($log->created_at)) ?></div>
                                        <div class="small text-muted"><i class="far fa-clock me-1"></i><?= date('H:i:s', strtotime($log->created_at)) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><i class="fas fa-exchange-alt me-1"></i><?= htmlspecialchars($log->trx_type ?? '-') ?></div>
                                        <?php if (!empty($log->nospk)): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 mt-1 px-2 py-1"><i class="fas fa-file-invoice me-1"></i><?= htmlspecialchars($log->nospk) ?></span>
                                        <?php endif; ?>
                                        <div class="small text-muted mt-1">Oleh: <?= htmlspecialchars($log->operator_nama ?? 'Sistem') ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($log->bahan_nama ?? '-') ?></div>
                                        <div class="small text-muted"><i class="fas fa-tag me-1 text-info"></i><?= htmlspecialchars($log->material_nama ?? '-') ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($log->gudang_nama ?? '-') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ((float)$log->qty_in > 0): ?>
                                            <span class="badge bg-success fs-6">+<?= str_replace('.00', '', number_format($log->qty_in, 2)) ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ((float)$log->qty_out > 0): ?>
                                            <span class="badge bg-danger fs-6">-<?= str_replace('.00', '', number_format($log->qty_out, 2)) ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark fs-6"><?= str_replace('.00', '', number_format($log->qty_balance, 2)) ?></span>
                                    </td>
                                    <td class="text-center fw-bold text-muted">
                                        <?= htmlspecialchars($log->satuan_nama ?? '-') ?>
                                    </td>
                                    <td>
                                        <span class="small text-muted" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;" title="<?= htmlspecialchars($log->catatan ?? '') ?>">
                                            <?= htmlspecialchars($log->catatan ?? '-') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#tableKonsumsiBahan').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "pageLength": 25,
            "ordering": false // Karena sudah diurutkan dari DB
        });
    }
});
</script>
