<div class="row p-4">
    <div class="card">
        <div class="card-header">
            <?php $this->load->view('admin/keuangan/menu_pemasukan_harian'); ?>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                        <table class="table table-bordered">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Metode Pembayaran</th>
                                <th width="30%">Total Pembayaran</th>
                                <th width="30%">Total Pembayaran Piutang</th>
                            </tr>
                            <tr>
                                <th>1</th>
                                <td width="20%">Tunai</td>
                                <td class="text-end">Rp. <?= number_format($cash['totalna']) ?>,- </td>
                                <td class="text-end">Rp. <?= number_format($cashpiutang['totalna']) ?>,- </td>
                            </tr>
                            
                            <tr>
                                <td colspan="2">Total Pemasukan</td>
                                <td class="text-end"><h4>Rp. <?=  number_format($cash['totalna']) ?>,- </h4></td>
                                <td class="text-end"><h4>Rp. <?=  number_format($cashpiutang['totalna']) ?>,- </h4></td>
                            </tr>
                            <tr>
                                <td colspan="2">Grand Total</td>
                                <td colspan="2" class="text-end">
                                    <?php 
                                        $grandtotal = $cash['totalna'] + $cashpiutang['totalna'];
                                        echo "<h4>Rp. ".number_format($grandtotal).",-</h4>";
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
                                <th>Cash</th>
                                
                                
                                
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
                                <td class="text-end"><?= number_format($dt->bayar_tunai) ?></td>
                                
                                
                                
                                
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
</div>