<div class="app-content d-block">
    <div class="container-fluid py-3">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Laporan Pembelian</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Tanggal Pembelian</th>
                                <th>Supplier</th>
                                <th>Bahan</th>
                                <th>Harga<br>Satuan</th>
                                <th>Qty</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            $total = 0;
                            $totalharga = 0;

                            foreach ($tampilData as $value):
                                $jumlah = $value->pemb_qty * $value->pemb_harga;
                                $total += $value->pemb_qty;
                                $totalharga += $jumlah;
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td class="text-end"><?= date_indo($value->pembelian_tgl) ?></td>
                                <td class="text-end"><?= htmlspecialchars($value->supplier_nama) ?></td>
                                <td class="text-end"><?= htmlspecialchars($value->bahan_nama) ?></td>
                                <td class="text-end"><?= number_format($value->pemb_harga) ?></td>
                                <td class="text-end"><?= number_format($value->pemb_qty) ?></td>
                                <td class="text-end"><?= number_format($jumlah) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="fw-bold">
                                <td colspan="5" class="text-end">Total</td>
                                <td class="text-end"><?= number_format($total) ?></td>
                                <td class="text-end"><?= number_format($totalharga) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
