<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start">
            <div>
                <h3 class="mb-1">Laporan Akumulasi Stok Bahan Baku</h3>
                <p class="mb-0 text-muted">Rekapitulasi stok seluruh gudang (Utama, Produksi 1 & 2) per bahan baku</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="<?= base_url('warehouse/export_akumulasi_stok_excel') ?>" class="btn btn-success me-2">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
                <a href="<?= base_url('warehouse/export_akumulasi_stok_pdf') ?>" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
            </div>
        </div>
        <div class="card-body pb-2">
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <div class="alert alert-primary mb-0">
                        <strong>Total Bahan Baku:</strong> <?= isset($akumulasi_stok) ? count($akumulasi_stok) : 0 ?> item
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-success mb-0">
                        <strong>Total Stok Semua Gudang:</strong> 
                        <?php 
                        $total_stok = 0;
                        if(isset($akumulasi_stok)) {
                            foreach($akumulasi_stok as $row) {
                                $total_stok += $row['stok_aktif'];
                            }
                        }
                        echo number_format($total_stok);
                        ?>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-info mb-0">
                        <strong>Update Terakhir:</strong> <?= date('d/m/Y H:i') ?>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle fs-5" id="akumulasi-stok-table">
                    <thead class="table-dark text-center align-middle">
                        <tr>
                            <th>No</th>
                            <th>Nama Bahan</th>
                            <th>Satuan</th>
                            <th>Stok Gudang Utama</th>
                            <th>Stok Gudang Produksi 1</th>
                            <th>Stok Gudang Produksi 2</th>
                            <th>Stok Semua</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($akumulasi_stok)): $no=1; foreach($akumulasi_stok as $row): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($row['bahanbaku_nama']) ?></strong></td>
                            <td><?= htmlspecialchars($row['satuan_nama']) ?></td>
                            <td class="text-end <?= $row['stok_gudang_utama'] < 0 ? 'text-danger' : 'text-success' ?>">
                                <?= number_format($row['stok_gudang_utama']) ?>
                            </td>
                            <td class="text-end <?= $row['stok_gudang_1'] < 0 ? 'text-danger' : 'text-success' ?>">
                                <?= number_format($row['stok_gudang_1']) ?>
                            </td>
                            <td class="text-end <?= $row['stok_gudang_2'] < 0 ? 'text-danger' : 'text-success' ?>">
                                <?= number_format($row['stok_gudang_2']) ?>
                            </td>
                            <td class="text-end fw-bold <?= $row['stok_aktif'] < 0 ? 'text-danger' : 'text-success' ?>">
                                <?= number_format($row['stok_aktif']) ?>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox display-6"></i><br><span class="fs-5">Belum ada data stok akumulasi.</span></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 