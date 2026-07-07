<div class="app-content d-block">
    <div class="container-fluid py-3">

        <div class="card">

            <div class="card-header">
                <?php $this->load->view('admin/keuangan/menu_pemasukan'); ?>
            </div>
            <div class="card-body">
                <!-- Ringkasan Pembayaran -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 15%">Metode Pembayaran</th>
                                <th style="width: 40%">Total Pembayaran</th>
                                <th style="width: 40%">Total Pembayaran Piutang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Tunai -->
                            <tr>
                                <th>1</th>
                                <td>Tunai</td>
                                <td class="text-end">Rp. <?= number_format($cash['totalna']) ?>,-</td>
                                <td class="text-end">Rp. <?= number_format($cashpiutang['totalna']) ?>,-</td>
                            </tr>

                            <!-- Transfer -->
                            <tr>
                                <th>2</th>
                                <td>Transfer</td>
                                <td>
                                    <table class="table table-bordered">
                                        <?php foreach ($rekening as $value):
                                            $total_transfer = $this->db->query("
                                                SELECT SUM(bayar_transfer) as total_transfer 
                                                FROM tbl_pembayaran 
                                                WHERE rekening_id = '$value->id' 
                                                AND pembayaran_tgl LIKE '$bulanini%'
                                            ")->row_array();
                                            if ($total_transfer['total_transfer'] != 0): ?>
                                                <tr>
                                                    <td style="width: 50%">
                                                        <img class="img-thumbnail" width="50%"
                                                            src="<?= base_url('assets/images/logo/' . $value->rekening_ket) ?>">
                                                        <br><strong><?= $value->no_rekening ?></strong>
                                                    </td>
                                                    <td class="text-end">Rp.
                                                        <?= number_format($total_transfer['total_transfer']) ?>,-
                                                    </td>
                                                </tr>
                                            <?php endif; endforeach; ?>
                                    </table>
                                </td>
                                <td>
                                    <table class="table table-bordered">
                                        <?php foreach ($rekening as $value):
                                            $total_transfer = $this->db->query("
                                                SELECT SUM(bayar_transfer) as total_transfer 
                                                FROM tbl_pembayaran_piutang 
                                                WHERE rekening_id = '$value->id' 
                                                AND pembayaran_tgl LIKE '$bulanini%'
                                            ")->row_array();
                                            if ($total_transfer['total_transfer'] != 0): ?>
                                                <tr>
                                                    <td style="width: 50%">
                                                        <img class="img-thumbnail" width="50%"
                                                            src="<?= base_url('assets/images/logo/' . $value->rekening_ket) ?>">
                                                    </td>
                                                    <td class="text-end">Rp.
                                                        <?= number_format($total_transfer['total_transfer']) ?>,-
                                                    </td>
                                                </tr>
                                            <?php endif; endforeach; ?>
                                    </table>
                                </td>
                            </tr>

                            <!-- EDC -->
                            <tr>
                                <th>3</th>
                                <td>Electronic Data Capture (EDC)</td>
                                <td>
                                    <table class="table table-bordered">
                                        <?php foreach ($edc as $value):
                                            $total_debit = $this->db->query("
                                                SELECT SUM(bayar_debit) as total_debit 
                                                FROM tbl_pembayaran 
                                                WHERE id_edc = '$value->edc_id' 
                                                AND pembayaran_tgl LIKE '$bulanini%'
                                            ")->row_array();
                                            if ($total_debit['total_debit'] != 0): ?>
                                                <tr>
                                                    <td style="width: 50%">
                                                        <img class="img-thumbnail" width="50%"
                                                            src="<?= base_url('assets/images/logo/' . $value->edc_ket) ?>">
                                                    </td>
                                                    <td class="text-end">Rp. <?= number_format($total_debit['total_debit']) ?>,-
                                                    </td>
                                                </tr>
                                            <?php endif; endforeach; ?>
                                    </table>
                                </td>
                                <td>
                                    <table class="table table-bordered">
                                        <?php foreach ($edc as $value):
                                            $total_debit = $this->db->query("
                                                SELECT SUM(bayar_debit) as total_debit 
                                                FROM tbl_pembayaran_piutang 
                                                WHERE id_edc = '$value->edc_id' 
                                                AND pembayaran_tgl LIKE '$bulanini%'
                                            ")->row_array();
                                            if ($total_debit['total_debit'] != 0): ?>
                                                <tr>
                                                    <td style="width: 50%">
                                                        <img class="img-thumbnail" width="50%"
                                                            src="<?= base_url('assets/images/logo/' . $value->edc_ket) ?>">
                                                    </td>
                                                    <td class="text-end">Rp. <?= number_format($total_debit['total_debit']) ?>,-
                                                    </td>
                                                </tr>
                                            <?php endif; endforeach; ?>
                                    </table>
                                </td>
                            </tr>

                            <!-- E-Wallet -->
                            <tr>
                                <th>4</th>
                                <td>E-Wallet</td>
                                <td>
                                    <table class="table table-bordered">
                                        <?php foreach ($ewallet as $value):
                                            $total_ewallet = $this->db->query("
                                                SELECT SUM(bayar_ewallet) as total_ewallet 
                                                FROM tbl_pembayaran 
                                                WHERE ewallet_id = '$value->id' 
                                                AND pembayaran_tgl LIKE '$bulanini%'
                                            ")->row_array();
                                            if ($total_ewallet['total_ewallet'] != 0): ?>
                                                <tr>
                                                    <td style="width: 50%">
                                                        <img class="img-thumbnail" width="50%"
                                                            src="<?= base_url('assets/images/logo/' . $value->wallet_ket) ?>">
                                                    </td>
                                                    <td class="text-end">Rp.
                                                        <?= number_format($total_ewallet['total_ewallet']) ?>,-
                                                    </td>
                                                </tr>
                                            <?php endif; endforeach; ?>
                                    </table>
                                </td>
                                <td>
                                    <table class="table table-bordered">
                                        <?php foreach ($ewallet as $value):
                                            $total_ewallet = $this->db->query("
                                                SELECT SUM(bayar_ewallet) as total_ewallet 
                                                FROM tbl_pembayaran_piutang 
                                                WHERE ewallet_id = '$value->id' 
                                                AND pembayaran_tgl LIKE '$bulanini%'
                                            ")->row_array();
                                            if ($total_ewallet['total_ewallet'] != 0): ?>
                                                <tr>
                                                    <td style="width: 50%">
                                                        <img class="img-thumbnail" width="50%"
                                                            src="<?= base_url('assets/images/logo/' . $value->wallet_ket) ?>">
                                                    </td>
                                                    <td class="text-end">Rp.
                                                        <?= number_format($total_ewallet['total_ewallet']) ?>,-
                                                    </td>
                                                </tr>
                                            <?php endif; endforeach; ?>
                                    </table>
                                </td>
                            </tr>

                            <!-- Total & Grand Total -->
                            <tr>
                                <td colspan="2">Total Pemasukan</td>
                                <td class="text-end">
                                    <h4><?= number_format($total_pemasukan['totalna']) ?></h4>
                                </td>
                                <td class="text-end">
                                    <h4><?= number_format($total_pemasukan_piutang['totalna']) ?></h4>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">Grand Total</td>
                                <td colspan="2" class="text-end">
                                    <?php
                                    $grandtotal = $total_pemasukan['totalna'] + $total_pemasukan_piutang['totalna'];
                                    echo "<h3>Rp. " . number_format($grandtotal) . ",-</h3>";
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>

                <!-- Detail Transaksi -->
                <div class="table-responsive">
                    <table id="datatable-details" class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 10%">NO SPK</th>
                                <th style="width: 15%">Konsumen</th>
                                <th>Total Transaksi</th>
                                <th>Cash</th>
                                <th>Transfer</th>
                                <th>EDC</th>
                                <th>E-Wallet</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($pemasukan as $dt): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $dt->nospk ?></td>
                                    <td>
                                        <?= $dt->konsumen_nama ?><br>
                                        <?= $dt->konsumen_nohp ?>
                                    </td>
                                    <td class="text-end">Rp. <?= number_format($dt->grand_total) ?></td>
                                    <td class="text-end">Rp. <?= number_format($dt->bayar_tunai) ?></td>
                                    <td class="text-end">Rp. <?= number_format($dt->bayar_transfer) ?></td>
                                    <td class="text-end">Rp. <?= number_format($dt->bayar_debit) ?></td>
                                    <td class="text-end">Rp. <?= number_format($dt->bayar_ewallet) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>