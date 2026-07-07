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
                <i class="fas fa-boxes"></i> Log Pemakaian Bahan (Real-time)
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
                            <th width="15%">Waktu</th>
                            <th width="15%">SPK / Produk</th>
                            <th width="25%">Bahan & Varian</th>
                            <th class="text-center" width="10%">Qty Dipotong</th>
                            <th class="text-center" width="10%">Satuan</th>
                            <th width="10%">Operator</th>
                            <th width="10%">Catatan</th>
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
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 mb-1 px-2 py-1"><i class="fas fa-file-invoice me-1"></i><?= htmlspecialchars($log->nospk ?? '-') ?></span>
                                        <div class="small fw-semibold text-dark text-truncate" style="max-width: 250px;" title="<?= htmlspecialchars($log->product_nama ?? '') ?>">
                                            <?= htmlspecialchars($log->product_nama ?? '-') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($log->bahan_nama ?? '-') ?></div>
                                        <div class="small text-muted"><i class="fas fa-tag me-1 text-info"></i><?= htmlspecialchars($log->material_nama ?? '-') ?></div>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            // Data di database berupa satuan gudang (qty_out) dengan presisi 2 desimal (bisa terjadi pembulatan)
                                            $qty_cut_gudang = abs((float)$log->qty_out); 
                                            $konversi = (float)$log->konversi > 0 ? (float)$log->konversi : 1;
                                            
                                            // Konversi kembali ke Satuan Produksi (sebagai fallback)
                                            $qty_cut_prod = $qty_cut_gudang * $konversi;
                                            
                                            // Untuk akurasi penuh, coba ekstrak qty dari catatan (contoh: "Pakai: 8 lbr")
                                            if (preg_match('/Pakai:\s*([\d\.]+)/i', $log->catatan, $matches)) {
                                                $qty_cut_prod = (float)$matches[1];
                                            }
                                        ?>
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge bg-danger fs-6 border border-danger shadow-sm px-3 py-2">-<?= str_replace('.00', '', number_format($qty_cut_prod, 2)) ?></span>
                                            <?php if (!empty($log->satuan_produksi) && !empty($log->satuan_gudang) && strtolower(trim($log->satuan_produksi)) !== strtolower(trim($log->satuan_gudang))): ?>
                                                <div class="small text-muted mt-1 fw-bold" style="font-size: 0.7rem;">
                                                    <i class="fas fa-exchange-alt me-1 text-info"></i><?= str_replace('.00', '', number_format($qty_cut_gudang, 2)) ?> <?= htmlspecialchars($log->satuan_gudang) ?>
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
