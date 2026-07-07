<div class="app-content d-block">
    <div class="container-fluid py-3">
    <div class="card">
        <div class="card-header">
            <?php $this->load->view('admin/keuangan/menu_pemasukan'); ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <?php
                    $total_trx_ewallet = 0;
                    $total_piutang_ewallet = 0;
                ?>
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
                            <th>1</th>
                            <td>E-Wallet</td>
                            <td>
                                <table class="table table-bordered mb-0">
                                    <?php foreach ($ewallet as $value): ?>
                                        <?php
                                            $total_ewallet = $this->db->query("SELECT SUM(bayar_ewallet) as total_ewallet FROM tbl_pembayaran WHERE ewallet_id = '$value->id' AND pembayaran_tgl LIKE '$bulanini%'")->row_array();
                                            if ($total_ewallet['total_ewallet'] != 0):
                                                $total_trx_ewallet += $total_ewallet['total_ewallet'];
                                        ?>
                                            <tr>
                                                <td width="50%">
                                                    <img class="img-thumbnail" width="50%" src="<?= base_url('assets/images/logo/' . $value->wallet_ket) ?>">
                                                </td>
                                                <td class="text-end">Rp. <?= number_format($total_ewallet['total_ewallet']) ?>,-</td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                            <td>
                                <table class="table table-bordered mb-0">
                                    <?php foreach ($ewallet as $value): ?>
                                        <?php
                                            $total_ewallet_piutang = $this->db->query("SELECT SUM(bayar_ewallet) as total_ewallet FROM tbl_pembayaran_piutang WHERE ewallet_id = '$value->id' AND pembayaran_tgl LIKE '$bulanini%'")->row_array();
                                            if ($total_ewallet_piutang['total_ewallet'] != 0):
                                                $total_piutang_ewallet += $total_ewallet_piutang['total_ewallet'];
                                        ?>
                                            <tr>
                                                <td>
                                                    <img class="img-thumbnail" width="25%" src="<?= base_url('assets/images/logo/' . $value->wallet_ket) ?>">
                                                </td>
                                                <td class="text-end">Rp. <?= number_format($total_ewallet_piutang['total_ewallet']) ?>,-</td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Total Pemasukan</strong></td>
                            <td class="text-end"><h4>Rp. <?= number_format($total_trx_ewallet) ?>,-</h4></td>
                            <td class="text-end"><h4>Rp. <?= number_format($total_piutang_ewallet) ?>,-</h4></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Grand Total</strong></td>
                            <td colspan="2" class="text-end">
                                <?php 
                                    $grandtotal = $total_trx_ewallet + $total_piutang_ewallet;
                                    echo "<h4><strong>Rp. " . number_format($grandtotal) . ",-</strong></h4>";
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
                <hr>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-none" id="datatable-details">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">NO SPK</th>
                            <th width="15%">Konsumen</th>
                            <th>Total Transaksi</th>
                            <th>E-Wallet</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($pemasukan as $dt): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $dt->nospk ?></td>
                            <td><?= $dt->konsumen_nama ?><br><?= $dt->konsumen_nohp ?></td>
                            <td class="text-end">Rp. <?= number_format($dt->grand_total) ?>,-</td>
                            <td class="text-end">Rp. <?= number_format($dt->bayar_ewallet) ?>,-</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
