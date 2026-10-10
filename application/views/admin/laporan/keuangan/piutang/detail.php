<!-- start: page -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <a href="<?= base_url($controller.'/piutang');?>" class="btn btn-dark">Kembali</a>
            <a href="#" class="btn btn-primary disabled">Pembayaran Piutang</a>
        </div>
        <div class="card-tools">
            <!-- Opsional: tambahkan icon toggle/dismiss jika ada implementasi JS -->
        </div>
    </div>

    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3 row">
                    <label class="col-sm-6 col-form-label">Nama Konsumen</label>
                    <div class="col-sm-6">
                        <b><?= $getpiutang['konsumen_nama'] ?></b>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-6 col-form-label">No HP Konsumen</label>
                    <div class="col-sm-6">
                        <b><?= $getpiutang['konsumen_nohp'] ?></b>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <h4 class="text-dark fw-bold text-center">Total Piutang:</h4>
                <br>
                <h1 class="text-dark fw-bold"><?= 'Rp. ' . number_format($getdata['total']) ?></h1>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>No SPK</th>
                        <th>Total Piutang</th>
                        <th>Bayar</th>
                        <th>Sisa</th>
                        <th>Bayar</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    $no = 1;
                    foreach ($getdetail as $dt): 
                ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td>
                            <?= $dt->nospk ?><br>
                            Transaksi : <?= date_indo($dt->piutang_tgl) ?> <br>
                            Tempo : <?= date_indo($dt->tempo_tgl) ?> <br>
                        </td>
                        <td>Rp. <?= number_format($dt->piutang_total) ?></td>
                        <td>Rp. <?= number_format($dt->piutang_bayar) ?></td>
                        <td>Rp. <?= number_format($dt->piutang_sisa) ?></td>
                        <td class="text-center">
                            <?php if ($dt->piutang_sisa != 0): ?>
                                <a href="<?= base_url($controller.'/bayar_piutang/'.$dt->nospk)?>" class="btn btn-warning w-100">Bayar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php 
                    $no++;
                    endforeach;
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- end: page -->
