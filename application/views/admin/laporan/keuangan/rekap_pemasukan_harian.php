<div class="row p-4">
    <div class="card">
        <div class="card-header">
            <?php $this->load->view('admin/keuangan/menu_pemasukan_harian'); ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Metode Pembayaran</th>
                            <th width="30%">Total Pembayaran</th>
                            <th width="30%">Total Pembayaran Piutang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>1</th>
                            <td>Tunai</td>
                            <td class="text-end">Rp. <?= number_format($cash['totalna']) ?>,-</td>
                            <td class="text-end">Rp. <?= number_format($cashpiutang['totalna']) ?>,-</td>
                        </tr>
                        <tr>
                            <th>2</th>
                            <td>Transfer</td>
                            <td>
                                <table class="table table-bordered">
                                    <?php foreach ($rekening as $value): ?>
                                        <?php $total_transfer = $this->db->query("SELECT SUM(bayar_transfer) as total_transfer FROM tbl_pembayaran WHERE rekening_id = '$value->id' AND pembayaran_tgl = '$hariini'")->row_array(); ?>
                                        <?php if ($total_transfer['total_transfer'] != 0): ?>
                                            <tr>
                                                <td width="50%">
                                                    <img class="img-thumbnail" width="50%"
                                                        src="<?= base_url('assets/images/logo/' . $value->rekening_ket) ?>">
                                                    <br><?= $value->no_rekening ?>
                                                </td>
                                                <td class="text-end">Rp.
                                                    <?= number_format($total_transfer['total_transfer']) ?>,-
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                            <td>
                                <table class="table table-bordered">
                                    <?php foreach ($rekening as $value): ?>
                                        <?php $total_transfer = $this->db->query("SELECT SUM(bayar_transfer) as total_transfer FROM tbl_pembayaran_piutang WHERE rekening_id = '$value->id' AND pembayaran_tgl = '$hariini'")->row_array(); ?>
                                        <?php if ($total_transfer['total_transfer'] != 0): ?>
                                            <tr>
                                                <td width="50%">
                                                    <img class="img-thumbnail" width="50%"
                                                        src="<?= base_url('assets/images/logo/' . $value->rekening_ket) ?>">
                                                </td>
                                                <td class="text-end">Rp.
                                                    <?= number_format($total_transfer['total_transfer']) ?>,-
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th>3</th>
                            <td>Electronic Data Capture (EDC)</td>
                            <td>
                                <table class="table table-bordered">
                                    <?php foreach ($edc as $value): ?>
                                        <?php $total_debit = $this->db->query("SELECT SUM(bayar_debit) as total_debit FROM tbl_pembayaran WHERE id_edc = '$value->edc_id' AND pembayaran_tgl = '$hariini'")->row_array(); ?>
                                        <?php if ($total_debit['total_debit'] != 0): ?>
                                            <tr>
                                                <td width="50%">
                                                    <img class="img-thumbnail" width="50%"
                                                        src="<?= base_url('assets/images/logo/' . $value->edc_ket) ?>">
                                                </td>
                                                <td class="text-end">Rp. <?= number_format($total_debit['total_debit']) ?>,-
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                            <td>
                                <table class="table table-bordered">
                                    <?php foreach ($edc as $value): ?>
                                        <?php $total_debit = $this->db->query("SELECT SUM(bayar_debit) as total_debit FROM tbl_pembayaran_piutang WHERE id_edc = '$value->edc_id' AND pembayaran_tgl = '$hariini'")->row_array(); ?>
                                        <?php if ($total_debit['total_debit'] != 0): ?>
                                            <tr>
                                                <td width="50%">
                                                    <img class="img-thumbnail" width="50%"
                                                        src="<?= base_url('assets/images/logo/' . $value->edc_ket) ?>">
                                                </td>
                                                <td class="text-end">Rp. <?= number_format($total_debit['total_debit']) ?>,-
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th>4</th>
                            <td>E-Wallet</td>
                            <td>
                                <table class="table table-bordered">
                                    <?php foreach ($ewallet as $value): ?>
                                        <?php $total_ewallet = $this->db->query("SELECT SUM(bayar_ewallet) as total_ewallet FROM tbl_pembayaran WHERE ewallet_id = '$value->id' AND pembayaran_tgl = '$hariini'")->row_array(); ?>
                                        <?php if ($total_ewallet['total_ewallet'] != 0): ?>
                                            <tr>
                                                <td><img class="img-thumbnail" width="25%"
                                                        src="<?= base_url('assets/images/logo/' . $value->wallet_ket) ?>"></td>
                                                <td class="text-end">Rp. <?= number_format($total_ewallet['total_ewallet']) ?>,-
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                            <td>
                                <table class="table table-bordered">
                                    <?php foreach ($ewallet as $value): ?>
                                        <?php $total_ewallet = $this->db->query("SELECT SUM(bayar_ewallet) as total_ewallet FROM tbl_pembayaran_piutang WHERE ewallet_id = '$value->id' AND pembayaran_tgl = '$hariini'")->row_array(); ?>
                                        <?php if ($total_ewallet['total_ewallet'] != 0): ?>
                                            <tr>
                                                <td><img class="img-thumbnail" width="25%"
                                                        src="<?= base_url('assets/images/logo/' . $value->wallet_ket) ?>"></td>
                                                <td class="text-end">Rp. <?= number_format($total_ewallet['total_ewallet']) ?>,-
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Total Pemasukan</td>
                            <td class="text-end">
                                <h4>Rp. <?= number_format($total_pemasukan['totalna']) ?>,-</h4>
                            </td>
                            <td class="text-end">
                                <h4>Rp. <?= number_format($total_pemasukan_piutang['totalna']) ?>,-</h4>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Grand Total</td>
                            <td colspan="2" class="text-end">
                                <h4>Rp.
                                    <?= number_format($total_pemasukan['totalna'] + $total_pemasukan_piutang['totalna']) ?>,-
                                </h4>
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
                            <th>Cash</th>
                            <th>Transfer</th>
                            <th>EDC</th>
                            <th>E-Wallet</th>
                            <th>Piutang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($pemasukan as $dt): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $dt->nospk ?></td>
                                <td><?= $dt->konsumen_nama ?><br><?= $dt->konsumen_nohp ?></td>
                                <td class="text-end">Rp. <?= number_format($dt->grand_total) ?></td>
                                <td class="text-end">Rp. <?= number_format($dt->bayar_tunai) ?></td>
                                <td class="text-end">Rp. <?= number_format($dt->bayar_transfer) ?></td>
                                <td class="text-end">Rp. <?= number_format($dt->bayar_debit) ?></td>
                                <td class="text-end">Rp. <?= number_format($dt->bayar_ewallet) ?></td>
                                <td class="text-end">Rp. <?= number_format($dt->piutang) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- end: page -->
</div>