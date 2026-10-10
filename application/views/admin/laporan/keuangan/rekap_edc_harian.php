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
                            <td>Electronic Data Capture (EDC)</td>
                            <td class="text-left">
                                <table class="table table-bordered">
                                <?php 
                                    foreach ($edc as $key => $value) {
                                        $total_debit = $this->db->query("SELECT SUM(bayar_debit) as total_debit FROM tbl_pembayaran WHERE id_edc = '$value->edc_id' AND pembayaran_tgl  = '$hariini' ")->row_array();

                                        if ($total_debit['total_debit'] != 0) 
                                        {
                                            echo "<tr>"; ?>
                                            <td width="50%">
                                                <img class="img img-thumbnail" width="50%" src=<?= base_url('assets/images/logo/'.$value->edc_ket) ?>>
                                            </td>
                                            <?php
                                            echo "<td class='text-end'>Rp. ".number_format($total_debit['total_debit']).",- </td>";
                                            echo "</tr>";
                                        }
                                        $total_trx_edc = $total_debit['total_debit']+ $total_trx_edc;
                                    }
                                ?>
                                </table>
                            </td>
                            <td class="text-left">
                                <table class="table table-bordered">
                                <?php 
                                    foreach ($edc as $key => $value) {
                                        $total_debit = $this->db->query("SELECT SUM(bayar_debit) as total_debit FROM tbl_pembayaran_piutang WHERE id_edc = '$value->edc_id'  AND pembayaran_tgl = '$hariini' ")->row_array();

                                        if ($total_debit['total_debit'] != 0) 
                                        {
                                            echo "<tr>"; ?>
                                            <td width="50%">
                                                <img class="img img-thumbnail" width="50%" src=<?= base_url('assets/images/logo/'.$value->edc_ket) ?>>
                                            </td>
                                            <?php
                                            echo "<td class='text-end'>Rp. ".number_format($total_debit['total_debit']).",- </td>";
                                            echo "</tr>";
                                        }
                                        $total_piutang_edc = $total_debit['total_debit']+$total_piutang_edc;
                                    }
                                ?>
                                </table>
                            </td>
                            
                        </tr>
                        
                            <tr>
                            <td colspan="2">Total Pemasukan</td>
                            <td class="text-end"><h4><?=  number_format($total_trx_edc) ?></h4></td>
                            <td class="text-end"><h4><?=  number_format($total_piutang_edc) ?></h4></td>
                        </tr>
                            <tr>
                            <td colspan="2">Grand Total</td>
                            <td colspan="2" class="text-end">
                                <?php 
                                    $grandtotal = $total_trx_edc + $total_piutang_edc;
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
                                <th>Bayar Menggunakan EDC</th>
                                <th>Mesin EDC</th>
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
                                <td class="text-end"><?= number_format($dt->bayar_debit) ?></td>
                                <td>
                                    <?= $dt->edc_nama ?>
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