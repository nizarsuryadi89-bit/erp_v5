<div class="app-content d-block">
    <div class="container-fluid py-3">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Return Pembelian</h5>
                <div>
                    <a href="<?= base_url('warehouse/cetakReturnPembelian/'.$return_id) ?>" class="btn btn-secondary" target="_blank">
                        <i class="bi bi-printer"></i> Cetak
                    </a>
                    <a href="<?= base_url('warehouse/returnPembelian') ?>" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>No Return</strong></td>
                                <td>: <?= $getData['no_return'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Return</strong></td>
                                <td>: <?= date('d/m/Y', strtotime($getData['return_tgl'])) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Supplier</strong></td>
                                <td>: <?= $getData['supplier_nama'] ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Karyawan</strong></td>
                                <td>: <?= $getData['nama_lengkap'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total Return</strong></td>
                                <td>: Rp. <?= number_format($getData['return_total']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Keterangan</strong></td>
                                <td>: <?= $getData['keterangan'] ?: '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Bahan</th>
                                <th>Satuan</th>
                                <th>Qty Return</th>
                                <th>Harga Return</th>
                                <th>Sub Total</th>
                                <th>Alasan Return</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $no = 1;
                                $total_return = 0;
                                foreach ($tampilData as $key => $value) {
                                    $subtotal = $value->qty_return * $value->harga_return;
                                    $total_return += $subtotal;
                            ?>
                            <tr>
                                <td><?= $no ?></td>
                                <td><?= $value->bahan_nama ?></td>
                                <td><?= $value->satuan_nama ?></td>
                                <td class="text-end"><?= number_format($value->qty_return) ?></td>
                                <td class="text-end">Rp. <?= number_format($value->harga_return) ?></td>
                                <td class="text-end">Rp. <?= number_format($subtotal) ?></td>
                                <td><?= $value->alasan_return ?></td>
                            </tr>
                            <?php
                                    $no++;
                                }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">Total Return:</th>
                                <th class="text-end">Rp. <?= number_format($total_return) ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 