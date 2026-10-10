<div class="app-content d-block">
    <div class="container-fluid py-3">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Return Bahan: <?= $bahan['bahan_nama'] ?></h5>
                <div>
                    <a href="<?= base_url('warehouse/stok') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali ke Stok
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Nama Bahan</strong></td>
                                <td>: <?= $bahan['bahan_nama'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Barcode</strong></td>
                                <td>: <?= $bahan['barcode'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Kategori</strong></td>
                                <td>: <?= $bahan['kategori_nama'] ?? '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Total Return</strong></td>
                                <td>: <span class="badge bg-warning fs-6"><?= number_format($total_rows) ?> kali</span></td>
                            </tr>
                            <tr>
                                <td><strong>Total Qty Return</strong></td>
                                <td>: 
                                    <?php 
                                        $total_qty = 0;
                                        foreach($tampilData as $item) {
                                            $total_qty += $item->qty_return;
                                        }
                                    ?>
                                    <span class="text-danger fw-bold"><?= number_format($total_qty) ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: 
                                    <?php if($total_qty > 0): ?>
                                        <span class="badge bg-danger">Ada Return</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Tidak Ada Return</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php if($tampilData): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>No Return</th>
                                    <th>Tanggal Return</th>
                                    <th>Supplier</th>
                                    <th>Qty Return</th>
                                    <th>Harga Return</th>
                                    <th>Sub Total</th>
                                    <th>Alasan Return</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $no = 1;
                                    $grand_total = 0;
                                    foreach ($tampilData as $item) {
                                        $subtotal = $item->qty_return * $item->harga_return;
                                        $grand_total += $subtotal;
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <a href="<?= base_url('warehouse/detailReturnPembelian/'.$item->return_id) ?>" 
                                           class="text-decoration-none">
                                            <?= $item->no_return ?>
                                        </a>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($item->return_tgl)) ?></td>
                                    <td><?= $item->supplier_nama ?></td>
                                    <td class="text-end"><?= number_format($item->qty_return) ?></td>
                                    <td class="text-end">Rp. <?= number_format($item->harga_return) ?></td>
                                    <td class="text-end">Rp. <?= number_format($subtotal) ?></td>
                                    <td><?= $item->alasan_return ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-warning">
                                    <th colspan="6" class="text-end">Total Nilai Return:</th>
                                    <th class="text-end">Rp. <?= number_format($grand_total) ?></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle display-1 text-success"></i>
                        <h5 class="text-success mt-3">Tidak ada data return untuk bahan ini</h5>
                        <p class="text-muted">Bahan <?= $bahan['bahan_nama'] ?> belum pernah diretur</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> 