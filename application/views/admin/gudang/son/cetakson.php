<head>
    <style>
        .table-bordered > tbody > tr > td,
        .table-bordered > tbody > tr > th,
        .table-bordered > tfoot > tr > td,
        .table-bordered > tfoot > tr > th,
        .table-bordered > thead > tr > td,
        .table-bordered > thead > tr > th {
            border: 1px solid #000;
            font-family: lato, Helvetica, sans-serif;
        }
    </style>
</head>

<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered" width="100%">
            <thead>
                <tr>
                    <th colspan="6" class="text-center">
                        <img src="<?= base_url('assets/images/logo.png') ?>" width="150px" alt="Logo"><br><br>
                        Kartu Stok Opname Bulan <b><?= bulan($bulanini) ?></b>
                    </th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Tanggal<br>Stok Opname</th>
                    <th>Nama Bahan</th>
                    <th>Stok Real</th>
                    <th>Stok Aplikasi</th>
                    <th>Selisih</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $no = 1;
                    foreach ($tampilData as $dt):
                        $selisih = $dt->son_real - $dt->son_aplikasi;
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td align="center"><?= date_indo($dt->son_tgl) ?></td>
                    <td><?= $dt->bahan_nama ?></td>
                    <td align="right"><strong><?= $dt->son_real ?></strong> <?= $dt->satuan_nama ?></td>
                    <td align="right"><strong><?= $dt->son_aplikasi ?></strong> <?= $dt->satuan_nama ?></td>
                    <td align="right">
                        <strong class="<?= $selisih >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $selisih ?>
                        </strong> <?= $dt->satuan_nama ?>
                    </td>
                </tr>
                <?php endforeach; ?>

                <tr>
                    <td colspan="6">
                        <table width="100%">
                            <tr>
                                <td width="50%" align="center">
                                    Mengetahui,<br>Pimpinan Perusahaan<br><br><br>
                                    <p><u><b>MUHAMMAD FAHMI IRWAN</b></u></p>
                                </td>
                                <td width="50%" align="center">
                                    Bekasi, <?= date('d F Y') ?><br><br><br>
                                    <p><u><b>WAGE SUDIRMAN</b></u></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
