<div class="row">
    <section class="panel">
            
            <div class="panel-body">
                <div class="col-md-12">
                <form action="<?= base_url('keuangan/laporan_periode_keuangan')?>" method="post">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Tanggal Awal</label>
                            <input type="date" name="tgl_awal" class="form-control" required value="<?= $tgl_awal ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Tanggal Akhir</label>
                            <input type="date" name="tgl_akhir" class="form-control" required value="<?= $tgl_akhir ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit" name="set">Set</button>
                        </div>
                    </div>

                </div>
                </form>
                </div>
                <br>
                <div class="panel-body">
                    <h4>Laporan Keuangan Tanggal Periode Tanggal <?= date_indo($tgl_awal) ?> Sampai <?= date_indo($tgl_akhir) ?> </h4>
                    <hr>
                <div class="row">
                    <div class="col-md-12">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Metode Pembayaran</th>
                                    <th width="40%">Total Pembayaran</th>
                                    <th width="40%">Total Pembayaran Piutang</th>
                                </tr>
                                <tr>
                                    <th>1</th>
                                    <td width="20%">Tunai</td>
                                    <td width="40%" class="text-right">Rp. <?= number_format($cash['totalna']) ?>,-</td>
                                    <td width="40%" class="text-right">Rp. <?= number_format($cashpiutang['totalna']) ?>,-</td>
                                </tr>
                                <tr>
                                    <th>2</th>
                                    <td>Transfer
                                        
                                    </td>       
                                    <td class="text-left">
                                        <table class="table table-bordered">
                                        <?php 
                                            foreach ($rekening as $key => $value) {
                                                $total_transfer = $this->db->query("SELECT SUM(bayar_transfer) as total_transfer FROM tbl_pembayaran 
                                                                                        WHERE rekening_id = '$value->id' AND 
                                                                                            (pembayaran_tgl BETWEEN '$tgl_awal' AND '$tgl_akhir') ")->row_array();

                                                if ($total_transfer['total_transfer'] != 0) {
                                                    echo "<tr>"; ?>
                                                    <td width="50%">
                                                        <!-- <img class="img img-thumbnail" width="50%" src=<?= base_url('assets/images/logo/'.$value->rekening_ket) ?>> <br> -->
                                                        <b>Rekening : <?= $value->no_rekening ?></b>
                                                    </td>
                                                    <?php
                                                    echo "<td class='text-right'>Rp. ".number_format($total_transfer['total_transfer']).",- </td>";
                                                    echo "</tr>";
                                                }
                                            }
                                        ?>
                                        </table>
                                    </td>
                                    <td class="text-left">
                                        <table class="table table-bordered">
                                        <?php 
                                            foreach ($rekening as $key => $value) {
                                                $total_transfer = $this->db->query("SELECT SUM(bayar_transfer) as total_transfer 
                                                                                        FROM tbl_pembayaran_piutang 
                                                                                            WHERE rekening_id = '$value->id' AND (pembayaran_tgl BETWEEN
                                                                                                '$tgl_awal' AND '$tgl_akhir') ")->row_array();

                                                if ($total_transfer['total_transfer'] != 0) {
                                                    echo "<tr>"; ?>
                                                    <td width="50%">
                                                        <img class="img img-thumbnail" width="50%" src=<?= base_url('assets/images/logo/'.$value->rekening_ket) ?>> 
                                                    </td>
                                                    <?php
                                                    echo "<td class='text-right'>Rp ".number_format($total_transfer['total_transfer']).",- </td>";
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
                                        <table class="table table-bordered">
                                        <?php 
                                            foreach ($edc as $key => $value) {
                                                $total_debit = $this->db->query("SELECT SUM(bayar_debit) as total_debit 
                                                                                    FROM tbl_pembayaran WHERE id_edc = '$value->edc_id' 
                                                                                        AND (pembayaran_tgl BETWEEN '$tgl_awal' AND '$tgl_akhir') ")->row_array();

                                                if ($total_debit['total_debit'] != 0) 
                                                {
                                                    echo "<tr>"; ?>
                                                    <td width="50%">
                                                        <img class="img img-thumbnail" width="50%" src=<?= base_url('assets/images/logo/'.$value->edc_ket) ?>>
                                                    </td>
                                                    <?php
                                                    echo "<td class='text-right'>Rp. ".number_format($total_debit['total_debit']).",- </td>";
                                                    echo "</tr>";
                                                }
                                            }
                                        ?>
                                        </table>
                                    </td>
                                    <td class="text-left">
                                        <table class="table table-bordered">
                                        <?php 
                                            foreach ($edc as $key => $value) {
                                                $total_debit = $this->db->query("SELECT SUM(bayar_debit) as total_debit FROM tbl_pembayaran_piutang WHERE id_edc = '$value->edc_id'  AND pembayaran_tgl like'$bulanini%' ")->row_array();

                                                if ($total_debit['total_debit'] != 0) 
                                                {
                                                    echo "<tr>"; ?>
                                                    <td width="50%">
                                                        <img class="img img-thumbnail" width="50%" src=<?= base_url('assets/images/logo/'.$value->edc_ket) ?>> 
                                                    </td>
                                                    <?php
                                                    echo "<td class='text-right'>Rp. ".number_format($total_debit['total_debit']).",- </td>";
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
                                    <td class="text-left">
                                    <table class="table table-bordered">
                                        <?php 
                                            foreach ($ewallet as $key => $value) {
                                                $total_debit = $this->db->query("SELECT SUM(bayar_ewallet) as total_ewallet FROM tbl_pembayaran WHERE ewallet_id = '$value->id' AND pembayaran_tgl like'$bulanini%' ")->row_array();

                                                if ($total_debit['total_ewallet'] != 0) 
                                                {
                                                ?>
                                                    <tr>
                                                        <td width="50%">
                                                            <img class="img img-thumbnail" width="50%" src=<?= base_url('assets/images/logo/'.$value->wallet_ket) ?>>
                                                        </td>
                                                        <td class='text-right'>Rp. <?= number_format($total_debit['total_ewallet']) ?>,- </td>
                                                    </tr>
                                                <?php
                                                }
                                            }
                                        ?>
                                    </table>
                                    </td>
                                    <td class="text-center">
                                    <table class="table table-bordered">
                                        <?php 
                                            foreach ($ewallet as $key => $value) {
                                                $total_debit = $this->db->query("SELECT SUM(bayar_ewallet) as total_ewallet FROM tbl_pembayaran_piutang WHERE ewallet_id = '$value->id'  AND pembayaran_tgl like'$bulanini%' ")->row_array();

                                                if ($total_debit['total_ewallet'] != 0) 
                                                {
                                                ?>
                                                    <tr>
                                                        <td width="50%">
                                                            <img class="img img-thumbnail" width="50%" src=<?= base_url('assets/images/logo/'.$value->wallet_ket) ?>>
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
                                    <td class="text-right"><h4><?=  number_format($total_pemasukan['totalna']) ?></h4></td>
                                    <td class="text-right"><h4><?=  number_format($total_pemasukan_piutang['totalna']) ?></h4></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Grand Total</td>
                                    <td colspan="2" class="text-right">
                                        <?php 
                                            $grandtotal = $total_pemasukan['totalna'] + $total_pemasukan_piutang['totalna'];
                                            echo "<h3>Rp. ".number_format($grandtotal).",-</h3>";
                                        ?>
                                    </td>
                                </tr>
                                
                            </table>
                        </div>
                        
                </div>
                <hr>
                <table class="table table-bordered table-hover mb-none" id="datatable-details">		
                            
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <td width="10%">NO SPK</td>
                            <th width="15%">Konsumen</th>
                            <th>Total Transaksi</th>
                            <th>Cash</th>
                            <th>Transfer</th>
                            <th>EDC</th>
                            <th>E-Wallet</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $no =1;
                            foreach ($pemasukan as $dt){
                        ?>
                        <tr >
                            <td><?= $no ?></td>
                            <td><?= $dt->nospk ?></td>
                            <td>
                                <?= $dt->konsumen_nama ?> <br>
                                <?= $dt->konsumen_nohp ?> <br>
                            </td>
                            <td class="text-right">
                                Rp. <?= number_format($dt->grand_total) ?>
                            </td>
                            <td class="text-right"><?= number_format($dt->bayar_tunai) ?></td>
                            <td class="text-right"><?= number_format($dt->bayar_transfer) ?></td>
                            <td class="text-right"><?= number_format($dt->bayar_debit) ?></td>
                            <td class="text-right"><?= number_format($dt->bayar_ewallet) ?></td>
                        </tr>
                        <?php 
                            $no++;
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    <!-- end: page -->
</section>
</div>