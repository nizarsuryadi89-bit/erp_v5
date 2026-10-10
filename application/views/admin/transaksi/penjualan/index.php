<div class="row">
    <section class="panel">
        <header class="panel-heading">
            <h2 class="panel-title text-left"><?= $title ?>
                <?php
                if ($this->uri->segment(3) == 'onday') {
                    echo date_indo(date('Y-m-d')) ?>
                </h2>
                <?php
                } else {
                    echo date_indo(date('Y-m')) ?> </h2>
                <?php
                }
                ?>

            Jumlah Data : <?= $total_rows ?> <br>
            Total Transaksi : <b>Rp. <?= number_format($total_trx['total']); ?>,-</b>

        </header>
        <div class="panel-body">
            <table class="table table-bordered table-hover mb-none" id="datatable-detail">

                <thead>
                    <tr>

                        <th width="15%">No SPK</th>
                        <th width="25%">Tanggal Transaksi</th>
                        <th width="25%">Konsumen</th>
                        <th>Metode Transaksi</th>
                        <th>Total Transaksi</th>
                        <th hidden>Detail Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($tampilData as $dt) {
                        // $status = ($dt->status == '1') ? 'Aktif' : 'Tidak Aktif';
                        ?>
                        <tr>
                            <td>
                                <?= $dt->nospk ?><br>

                            </td>
                            <td>
                                Tanggal : <?= date_indo($dt->pembayaran_tgl) ?> &nbsp;
                                Jam : <?= $dt->pembayaran_jam ?> <br>
                            </td>

                            <td>
                                <?= $dt->konsumen_nama ?>
                                (<?= $dt->konsumen_nohp ?>)
                            </td>
                            <td>
                                <?= $dt->nama_jenis_transaksi ?>
                                <?php
                                if ($dt->id_jenis_transaksi == '2') {
                                    echo '<br>';
                                    echo '<b>' . $dt->marketplace_nama . '</b> ';
                                }
                                ?>
                            </td>
                            <td class="text-right">
                                Rp. <?= number_format($dt->sub_total) ?>
                            </td>
                            <td hidden>
                                <table class="table table-bordered">
                                    <tr>
                                        <td>Total Transaksi</td>
                                        <td rowspan="2" width="50%">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td width="30%">Pembayaran</td>
                                                    <td>Total Bayar</td>
                                                    <td>Keterangan</td>
                                                </tr>
                                                <tr>
                                                    <td>Tunai</td>
                                                    <td class="text-right"><?= number_format($dt->bayar_tunai) ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Transfer</td>
                                                    <td class="text-right"><?= number_format($dt->bayar_transfer) ?></td>
                                                    <td class="text-center"><?= $dt->no_rekening ?></td>
                                                </tr>
                                                <tr>
                                                    <td>EDC</td>
                                                    <td class="text-right"><?= number_format($dt->bayar_debit) ?></td>
                                                    <td class="text-center">
                                                        <b><?= $dt->edc_nama ?></b>
                                                        <?= $dt->nomor_debit ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>E-Wallet</td>
                                                    <td class="text-right"><?= number_format($dt->bayar_ewallet) ?></td>
                                                    <td class="text-center"><?= $dt->ewallet_nama ?></td>
                                                </tr>
                                            </table>

                                        </td>
                                        <td rowspan="2" width="15%" class="text-center">
                                            <a class="btn btn-primary btn-block"
                                                href="<?= base_url('transaksi/cetak_invoice_ulang/' . $dt->nospk) ?>">
                                                <i class="fa fa-print"> </i> Cetak Invoice
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="30%">
                                            - Sub Total : <?= number_format($dt->sub_total) ?> <br>
                                            - Diskon : <?= number_format($dt->diskon) ?> <br>
                                            - Ongkir : <?= number_format($dt->ongkir) ?> <br>
                                            - Grand Total : <?= number_format($dt->grand_total) ?>
                                            </ul>
                                        </td>


                                    </tr>
                                </table>
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

<!-- end: page -->
</section>
</div>