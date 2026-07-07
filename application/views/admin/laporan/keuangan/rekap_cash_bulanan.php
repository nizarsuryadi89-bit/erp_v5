<div class="app-content d-block">
    <div class="container-fluid py-3">
        <div class="card">

            <!-- Menu Pemasukan -->
            <div class="card-header">
                <?php $this->load->view('admin/keuangan/menu_pemasukan'); ?>
            </div>

            <div class="card-body">
                <!-- Tabel Rekap -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Metode Pembayaran</th>
                                <th width="40%">Total Pembayaran</th>
                                <th width="40%">Total Pembayaran Piutang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Tunai</td>
                                <td class="text-end">Rp. <?= number_format($cash['totalna']) ?></td>
                                <td class="text-end">Rp. <?= number_format($cashpiutang['totalna']) ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Grand Total</strong></td>
                                <td colspan="2" class="text-end">
                                    <?php 
                                        $grandtotal = $cash['totalna'] + $cashpiutang['totalna'];
                                        echo "<strong>Rp. " . number_format($grandtotal) . ",-</strong>";
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tabel Detail -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-none" id="datatable-details">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">NO SPK</th>
                                <th width="35%">Konsumen</th>
                                <th>Total Transaksi</th>
                                <th>Cash</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $no = 1;
                                foreach ($pemasukan as $dt): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $dt->nospk ?></td>
                                <td>
                                    <?= $dt->konsumen_nama ?><br>
                                    <?= $dt->konsumen_nohp ?>
                                </td>
                                <td class="text-end">Rp. <?= number_format($dt->grand_total) ?></td>
                                <td class="text-end">Rp. <?= number_format($dt->bayar_tunai) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
            <!-- End Card Body -->
        </div>
    </div>
</div>
