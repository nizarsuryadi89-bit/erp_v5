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
                        <th width="40%">Total Pembayaran Transfer</th>
                        <th width="40%">Total Pembayaran Piutang Transfer</th>
                    </tr>
                    
                    <tr>
                        <th>1</th>
                        <td>Transfer
                            
                        </td>       
                        <td class="text-left">
                            <table class="table table-bordered">
                            <?php 
                                foreach ($rekening as $key => $value) {
                                    $total_transfer = $this->db->query("SELECT SUM(bayar_transfer) as total_transfer FROM tbl_pembayaran WHERE rekening_id = '$value->id' AND pembayaran_tgl like'$hariini%' ")->row_array();

                                    if ($total_transfer['total_transfer'] != 0) {
                                        echo "<tr>"; ?>
                                        <td width="50%">
                                            <img class="img img-thumbnail" width="50%" src=<?= base_url('assets/images/logo/'.$value->rekening_ket) ?>> <b><?= $value->no_rekening ?></b>
                                        </td>
                                        <?php
                                        echo "<td class='text-end'>Rp. ".number_format($total_transfer['total_transfer'])."</td>";
                                        echo "</tr>";
                                    }
                                    $total_trx_tf = $total_transfer['total_transfer']+$total_trx_tf;
                                }
                            ?>
                            </table>
                        </td>
                        <td class="text-left">
                            <table class="table table-bordered">
                            <?php 
                                foreach ($rekening as $key => $value) {
                                    $total_transfer = $this->db->query("SELECT SUM(bayar_transfer) as total_transfer FROM tbl_pembayaran_piutang WHERE rekening_id = '$value->id' AND pembayaran_tgl like'$hariini%' ")->row_array();

                                    if ($total_transfer['total_transfer'] != 0) {
                                        echo "<tr>"; ?>
                                        <td width="50%">
                                            <img class="img img-thumbnail" width="50%" src=<?= base_url('assets/images/logo/'.$value->rekening_ket) ?>> 
                                        </td>
                                        <?php
                                        echo "<td class='text-end'>Rp. ".number_format($total_transfer['total_transfer'])."</td>";
                                        echo "</tr>";
                                    }
                                    $total_piutang_tf = $total_transfer['total_transfer']+$total_piutang_tf;
                                }
                            ?>
                            </table>
                        </td>
                    </tr>
                    
                    
                    <tr>
                        <td colspan="2">Total Pemasukan Transfer</td>
                        <td class="text-end"><h4>Rp. <?=  number_format($total_trx_tf) ?>,-</h4></td>
                        <td class="text-end"><h4>Rp. <?=  number_format($total_piutang_tf) ?>,-</h4></td>
                    </tr>
                    <tr>
                        <td colspan="2">Grand Total</td>
                        <td colspan="2" class="text-end">
                            <?php 
                                $grandtotal = $total_trx_tf + $total_piutang_tf;
                                echo "<h3>Rp. ".number_format($grandtotal).",-</h3>";
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
                            
                            <th>Jumlah Transfer</th>
                            <th>Rekening Transfer</th>
                            
                            
                            
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
                            <td class="text-end"><?= number_format($dt->bayar_transfer) ?></td>
                            <td>
                                <?= $dt->rekening_bank ?> - <?= $dt->no_rekening ?><br>
                                <?= $dt->atas_nama ?>
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
    </div>
</div>