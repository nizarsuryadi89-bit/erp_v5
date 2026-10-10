<html>
    <head>
        <title>Laporan Pemasukan</title>
        <link rel="stylesheet" href="<?= base_url('/')?>assets/vendor/bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" href="<?= base_url('/')?>assets/vendor/font-awesome/css/font-awesome.css" />
        <style type="text/css">
			table {
				border-collapse: collapse;
				width: 100%;
				font-size: 11px;
			}
			form#input[type=text] {
				font-size: 11px;
			}
		</style>
    </head>
</html>
<div class="container">
    <br>
    <img src="<?= base_url('/')?>assets/images/logo.png" width="125" height="35" alt="Porto Admin" >
    <br>
    <h6 class="text-left">Laporan Pemasukan Keuangan <br> tanggal <?= date_indo($tgl_awal)." s/d ".date_indo($tgl_akhir) ?></h6>
    <table class="table table-bordered">
        <tr>
            <th width="5%">No</th>
            <th width="25%">Metode Pembayaran</th>
            <th width="30%">Total Pembayaran</th>
            <th width="30%">Total Pembayaran Piutang</th>
        </tr>
        <tr>
            <th>1</th>
            <td width="40%">Tunai</td>
            <td class="text-right"><?= number_format($cash['totalna']) ?></td>
            <td class="text-right"><?= number_format($cashpiutang['totalna']) ?></td>
        </tr>
        <tr>
            <th>2</th>
            <td>Transfer
                
            </td>       
            <td class="text-left">
                <table>
                <?php 
                    foreach ($rekening as $key => $value) {
                        $total_transfer = $this->db->query("SELECT SUM(bayar_transfer) as total_transfer FROM tbl_pembayaran WHERE rekening_id = '$value->id' AND pembayaran_tgl BETWEEN '$tgl_awal' AND '$tgl_akhir' ")->row_array();

                        if ($total_transfer['total_transfer'] != 0) {
                            echo "<tr>"; ?>
                            <td>
                                <img class="img img-thumbnail" width="25%" src=<?= base_url('assets/images/logo/'.$value->rekening_ket) ?>> <b><?= $value->no_rekening ?></b>
                            </td>
                            <?php
                            echo "<td class='text-right'>".number_format($total_transfer['total_transfer'])."</td>";
                            echo "</tr>";
                        }
                    }
                ?>
                </table>
            </td>
            <td class="text-left">
                <table>
                <?php 
                    foreach ($rekening as $key => $value) {
                        $total_transfer = $this->db->query("SELECT SUM(bayar_transfer) as total_transfer FROM tbl_pembayaran_piutang WHERE rekening_id = '$value->id' AND pembayaran_tgl BETWEEN '$tgl_awal' AND '$tgl_akhir' ")->row_array();

                        if ($total_transfer['total_transfer'] != 0) {
                            echo "<tr>"; ?>
                            <td>
                                <img class="img img-thumbnail" width="25%" src=<?= base_url('assets/images/logo/'.$value->rekening_ket) ?>> <?= $value->rekening_ket ?>
                            </td>
                            <?php
                            echo "<td class='text-right'>".number_format($total_transfer['total_transfer'])."</td>";
                            echo "</tr>";
                        }
                    }
                ?>
                </table>
            </td>
        </tr>
        
        <tr>
            <th>3</th>
            <td>Electronic Data Capture (EDC)</td>
            <td class="text-left">
                <table>
                <?php 
                    foreach ($edc as $key => $value) {
                        $total_debit = $this->db->query("SELECT SUM(bayar_debit) as total_debit FROM tbl_pembayaran WHERE id_edc = '$value->edc_id' AND pembayaran_tgl BETWEEN '$tgl_awal' AND '$tgl_akhir' ")->row_array();

                        if ($total_debit['total_debit'] != 0) 
                        {
                            echo "<tr>"; ?>
                            <td>
                                <img class="img img-thumbnail" width="25%" src=<?= base_url('assets/images/logo/'.$value->edc_ket) ?>>
                            </td>
                            <?php
                            echo "<td class='text-right'>".number_format($total_debit['total_debit'])."</td>";
                            echo "</tr>";
                        }
                    }
                ?>
                </table>
            </td>
            <td class="text-left">
                <table>
                <?php 
                    foreach ($edc as $key => $value) {
                        $total_debit = $this->db->query("SELECT SUM(bayar_debit) as total_debit FROM tbl_pembayaran_piutang WHERE id_edc = '$value->edc_id' AND pembayaran_tgl BETWEEN '$tgl_awal' AND '$tgl_akhir' ")->row_array();

                        if ($total_debit['total_debit'] != 0) 
                        {
                            echo "<tr>"; ?>
                            <td>
                                <img class="img img-thumbnail" width="25%" src=<?= base_url('assets/images/logo/'.$value->edc_ket) ?>> <?= $value->edc_nama ?>
                            </td>
                            <?php
                            echo "<td class='text-right'>".number_format($total_debit['total_debit'])."</td>";
                            echo "</tr>";
                        }
                    }
                ?>
                </table>
            </td>
            
        </tr>
        <tr>
            <th>4</th>
            <td>E-Wallet</td>
            <td class="text-center">
            <table>
                <?php 
                    foreach ($ewallet as $key => $value) {
                        $total_debit = $this->db->query("SELECT SUM(bayar_ewallet) as total_ewallet FROM tbl_pembayaran WHERE ewallet_id = '$value->id' AND pembayaran_tgl BETWEEN '$tgl_awal' AND '$tgl_akhir' ")->row_array();

                        if ($total_debit['total_ewallet'] != 0) 
                        {
                        ?>
                            <tr>
                                <td>
                                    <img class="img img-thumbnail" width="25%" src=<?= base_url('assets/images/logo/'.$value->wallet_ket) ?>>
                                </td>
                                <td class='text-right'><?= number_format($total_debit['total_ewallet']) ?></td>
                            </tr>
                        <?php
                        }
                    }
                ?>
            </table>
            </td>
            <td class="text-center">
            <table>
                <?php 
                    foreach ($ewallet as $key => $value) {
                        $total_debit = $this->db->query("SELECT SUM(bayar_ewallet) as total_ewallet FROM tbl_pembayaran_piutang WHERE ewallet_id = '$value->id' AND pembayaran_tgl BETWEEN '$tgl_awal' AND '$tgl_akhir' ")->row_array();

                        if ($total_debit['total_ewallet'] != 0) 
                        {
                        ?>
                            <tr>
                                <td>
                                    <img class="img img-thumbnail" width="25%" src=<?= base_url('assets/images/logo/'.$value->wallet_ket) ?>>
                                </td>
                                <td class='text-right'><?= number_format($total_debit['total_ewallet']) ?></td>
                            </tr>
                        <?php
                        }
                    }
                ?>
            </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">Total Pemasukan</td>
            <td class="text-right"><b><?=  number_format($total_pemasukan['totalna']) ?></b></td>
            <td class="text-right"><b><?=  number_format($total_pemasukan_piutang['totalna']) ?></b></td>
        </tr>
        <tr>
            <td colspan="2">Grand Total</td>
            <td colspan="2" class="text-right">
                <?php 
                    $grandtotal = $total_pemasukan['totalna'] + $total_pemasukan_piutang['totalna'];
                    echo "<b>Rp. ".number_format($grandtotal).",-</b>";
                ?>
            </td>
        </tr>
        
    </table>
</div>