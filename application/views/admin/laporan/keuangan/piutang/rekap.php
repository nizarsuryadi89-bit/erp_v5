<style type="text/css">
			table {
				border-collapse: collapse;
				width: 100%;
				font-size: 12px;
			}
			form#input[type=text] {
				font-size: 12px;
			}
		</style>
<div class="row">
        <!-- start: page -->
        <section class="panel">
                <header class="panel-heading">
                    
            
                    <h2 class="panel-title">Detail Pemasukan & Pengeluaran</h2>
                </header>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form action="" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label" for="inputDisabled"> Transkasi Dari</label>
                                <div class="col-md-4">
                                    <input type="date" name="tgl_awal" id="tanggal" value="<?= $hariini ?>" class="form-control">
                                </div>
                                <label class="col-md-1 control-label" for="inputDisabled"> s/d</label>
                                <div class="col-md-3">
                                    <input type="date" name="tgl_akhir" id="tanggal" class="form-control" value="<?= $hariini ?>">
                                </div>
                                <div class="col-md-2 text-center">
                                    <button class="btn btn-warning" type="submit" name="filter"><i class="fa fa-search"></i> Filter</button>
                                </div>
                            </div>
                            
                            </form>
                            
                        </div>
                    </div>
                    <hr>
                    <!-- <h4>-- Detail Pemasukan</h4> -->
                    <div class="row">
                        
                        <div class="col-sm-12">
                            <?php 
                                if (isset($_POST['filter'])) {
                                    $tgl_awal = $_POST['tgl_awal'];
                                    $tgl_akhir = $_POST['tgl_akhir'];

                                    echo "<h4 class='text-center''>Laporan Pemasukan dari tanggal ".date_indo($tgl_awal)." s/d ".date_indo($tgl_akhir)."</h4>";
                                    // echo "<br>";
                                }
                                
                            ?>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Metode Pembayaran</th>
                                    <th width="30%">Total Pembayaran</th>
                                    <th width="30%">Total Pembayaran Piutang</th>
                                </tr>
                                <tr>
                                    <th>1</th>
                                    <td width="15%">Tunai</td>
                                    <td width="35%" class="text-right"><?= number_format($cash['totalna']) ?></td>
                                    <td width="35%" class="text-right"><?= number_format($cashpiutang['totalna']) ?></td>
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
                                                    <td class="text-left">
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
                                                        <td class='text-left'>
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
                                                        <td class='text-left'>
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
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <?php 
                                            if (isset($_POST['filter'])) {
                                                $tgl_awal   = $_POST['tgl_awal'];
                                                $tgl_akhir  = $_POST['tgl_akhir'];
                                        ?>
                                            <!-- <a href="<?= base_url('keuangan/cetak_laporan_pemasukan/'.$tgl_awal.'/'.$tgl_akhir) ?>" class="btn btn-primary btn-sm" target="_blank"><i class="fa fa-print"></i> Cetak</a> -->
                                        <?php 
                                            }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-12">
                        <!-- <h4>-- Detail Pengeluaran </h4> -->
                            <?php 
                                if (isset($_POST['filter'])) {
                                    $tgl_awal = $_POST['tgl_awal'];
                                    $tgl_akhir = $_POST['tgl_akhir'];
                                    echo "<br>";
                                    echo "<h4 class='text-center'>Laporan Pengeluaran Harian dari tanggal ".date_indo($tgl_awal)." s/d ".date_indo($tgl_akhir)."</h4>";
                                
                                }
                                
                            ?>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Kategori Pengeluran</th>
                                    <th width="30%">Total Pengeluaran</th>  
                                </tr>
                                <?php 
                                    $no = 1;
                                    foreach ($kategori as $key => $value) {
                                        $total_pengeluaran = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total_pengeluaran FROM tbl_pengeluaran_harian WHERE kategori_pengeluaran_id = '$value->id' AND pengeluaran_tgl BETWEEN '$tgl_awal' AND '$tgl_akhir' ")->row_array();
                                        if ($total_pengeluaran['total_pengeluaran'] != 0) {
                                            echo "<tr>"; ?>
                                            <td><?= $no++ ?></td>
                                            <td class="text-left"><?= $value->kategori_pengeluaran ?></td>
                                            <td class="text-right"><?= number_format($total_pengeluaran['total_pengeluaran']) ?></td>
                                            <?php
                                            echo "</tr>";
                                            $grandtotalpengeluaran = $total_pengeluaran['total_pengeluaran'] + $grandtotalpengeluaran;
                                        }
                                    
                                    }
                                ?>
                                <tr>
                                    <td colspan="2" class="text-right">Grand Total</td>
                                    <td colspan="2" class="text-right">
                                        <?php 
                                            echo "<b>Rp. ".number_format($grandtotalpengeluaran).",-</b>";
                                        ?>
                                    </td>
                                </tr>
                                
                                
                            </table>
                        </div>

                        <div class="col-sm-12">
                        <!-- <h4>-- Detail Pengeluaran </h4> -->
                            <?php 
                                if (isset($_POST['filter'])) {
                                    $tgl_awal = $_POST['tgl_awal'];
                                    $tgl_akhir = $_POST['tgl_akhir'];
                                    echo "<br>";
                                    echo "<h4 class='text-center''>Laporan BEP (Break Event Point) Harian dari tanggal ".date_indo($tgl_awal)." s/d ".date_indo($tgl_akhir)."</h4>";
                                
                                }
                                
                            ?>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Keterangan</th>
                                    <th width="30%">Total Nominal</th>  
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Pemasukan</td>
                                    <td class='text-right'><?= number_format($grandtotal) ?></td>
                                    
                                </tr>
            
                                <tr>
                                    <td>2</td>
                                    <td>Pengeluaran</td>
                                    <td class='text-right'><?= number_format($grandtotalpengeluaran) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">Break Event Point</td>
                                    <td colspan="2" class="text-right">
                                        <?php 
                                            $bep = $grandtotal - $grandtotalpengeluaran;
                                            echo "<b>Rp. ".number_format($bep).",-</b>";
                                        ?>
                                    </td>
                                </tr>
                                
                                
                            </table>
                        </div>
                    </div>
                    
    </section>
</div>