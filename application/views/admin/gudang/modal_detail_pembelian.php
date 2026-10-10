<div class="purchase-details mb-3">
    <div class="details-grid row">
        <div class="col-md-4 mb-3">
            <div class="detail-item p-3 border rounded bg-light">
                <div class="detail-label text-muted small text-uppercase fw-bold mb-1">Nama Supplier</div>
                <div class="detail-value fw-bold text-dark h5 mb-0">
                    <?= $getData['supplier_nama'] ?>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="detail-item p-3 border rounded bg-light">
                <div class="detail-label text-muted small text-uppercase fw-bold mb-1">Tanggal Pembelian</div>
                <div class="detail-value fw-bold text-dark h5 mb-0">
                    <?= date('d F Y', strtotime($getData['pembelian_tgl'])) ?>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="detail-item p-3 border rounded bg-light">
                <div class="detail-label text-muted small text-uppercase fw-bold mb-1">Jumlah Item</div>
                <div class="detail-value fw-bold text-dark h5 mb-0">
                    <?= count($tampilData) ?> Item
                </div>
            </div>
        </div>
    </div>

    <?php
    $total_pembelian = 0;
    foreach ($tampilData as $value) {
        $total_pembelian += $value->qty * $value->harga_beli;
    }
    ?>

    <div class="total-amount bg-dark text-white p-3 rounded text-center mb-3">
        <div class="total-label opacity-75 small">Total Pembelian</div>
        <div class="total-value fw-bold h3 mb-0">Rp.
            <?= number_format($total_pembelian) ?>,-
        </div>
    </div>
</div>

<!-- Purchase Items Table -->
<div class="purchase-table border rounded overflow-hidden">
    <div class="table-header bg-secondary text-white p-3">
        <h6 class="mb-0 fw-bold">
            <i class="fa fa-list me-2"></i>
            Daftar Item Pembelian
        </h6>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="p-3 border-bottom">No</th>
                    <th class="p-3 border-bottom">Bahan Baku</th>
                    <th class="p-3 border-bottom text-center">Qty</th>
                    <th class="p-3 border-bottom text-end">Harga</th>
                    <th class="p-3 border-bottom text-end">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($tampilData as $key => $value) {
                    $jumlah = $value->qty * $value->harga_beli;
                    ?>
                    <tr>
                        <td class="p-3">
                            <?= $no ?>
                        </td>
                        <td class="p-3">
                            <strong class="text-primary">
                                <?php
                                echo $value->varian;
                                ?>
                            </strong>
                        </td>
                        <td class="p-3 text-center">
                            <span class="badge bg-info text-dark border">
                                <?= number_format($value->qty) ?>
                            </span>
                        </td>
                        <td class="p-3 text-end">
                            Rp.
                            <?= number_format($value->harga_beli) ?>
                        </td>
                        <td class="p-3 text-end">
                            <strong class="text-success fw-bold">Rp.
                                <?= number_format($jumlah) ?>,-
                            </strong>
                        </td>
                    </tr>
                    <?php
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>