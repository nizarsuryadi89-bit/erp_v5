<div class="row p-4">
    <div class="card">
        <div class="card-header">
            <?php $this->load->view('admin/keuangan/menu_pemasukan_harian'); ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Metode Pembayaran</th>
                            <th width="30%">Total Pembayaran</th>
                            <th width="30%">Total Pembayaran Piutang</th>
                        </tr>
                        <tr>
                            <th>1</th>
                            <td>E-Wallet</td>
                            <td class="text-center">
                            <table class="table table-bordered">
                                <?php 
                                    foreach ($ewallet as $key => $value) {
                                        $total_debit = $this->db->query("SELECT SUM(bayar_ewallet) as total_ewallet FROM tbl_pembayaran WHERE ewallet_id = '$value->id' AND pembayaran_tgl = '$hariini' ")->row_array();

                                        if ($total_debit['total_ewallet'] != 0) 
                                        {
                                        ?>
                                            <tr>
                                                <td>
                                                    <img class="img img-thumbnail" width="25%" src=<?= base_url('assets/images/logo/'.$value->wallet_ket) ?>>
                                                </td>
                                                <td class='text-end'><?= number_format($total_debit['total_ewallet']) ?></td>
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
                                        $total_debit = $this->db->query("SELECT SUM(bayar_ewallet) as total_ewallet FROM tbl_pembayaran_piutang WHERE ewallet_id = '$value->id'  AND pembayaran_tgl = '$hariini' ")->row_array();

                                        if ($total_debit['total_ewallet'] != 0) 
                                        {
                                        ?>
                                            <tr>
                                                <td>
                                                    <img class="img img-thumbnail" width="25%" src=<?= base_url('assets/images/logo/'.$value->wallet_ket) ?>>
                                                </td>
                                                <td class='text-end'><?= number_format($total_debit['total_ewallet']) ?></td>
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
                            <td class="text-end"><h4><?=  number_format($total_trx_ewallet) ?></h4></td>
                            <td class="text-end"><h4><?=  number_format($total_piutang_ewallet) ?></h4></td>
                        </tr>
                        <tr>
                            <td colspan="2">Grand Total</td>
                            <td colspan="2" class="text-end">
                                <?php 
                                    $grandtotal = $total_trx_ewallet + $total_piutang_ewallet;
                                    echo "<b>Rp. ".number_format($grandtotal).",-</b>";
                                ?>
                            </td>
                        </tr>
                        
                    </table>
                    <hr>
                    <table class="table table-bordered table-hover mb-none" id="datatable-detail">		
                                
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <td width="10%">NO SPK</td>
                                <th width="15%">Konsumen</th>
                                <th>Total Transaksi</th>
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
                                
                                
                                <td class="text-end">
                                    Rp. <?= number_format($dt->grand_total) ?>
                                </td>
                                
                                
                                <td class="text-end"><?= number_format($dt->bayar_ewallet) ?></td>
                                
                            </tr>
                            <?php 
                                $no++;
                                }
                            ?>
                        </tbody>
                    </table>
                
            </div>
        </div>
    </div>
</div>