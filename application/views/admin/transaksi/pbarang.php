<div class="row">
    <section class="panel">  
        <div class="panel-heading"><h4><?= $title ?></h4></div>


            <div class="panel-body">
                
                <table class="table table-bordered table-striped mb-none" id="datatable-details">		
                            
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>SPK</th>
                            
                            <th>Dateline</th>
                            
                            <th>Status</th>
                            
                        </tr>
                    </thead>

                    <tbody>
                        <?php 
                            $no =1;
                            foreach ($tampilData as $dt){
                                
                                if ($dt->status_produksi == '0'){
                                    $status = '<p class="text-warning">Menunggu</p>';
                                    $button = '<button type="button" data-toggle="modal" data-target="#update_modal'.$dt->id.'" class="btn btn-primary btn-xs">Produksi</a>';   
                                }elseif ($dt->status_produksi == '1'){
                                    $status = '<p class="text-success">Proses Produksi</p>';
                                    $button = '<a href="'.base_url('transaksi/produksi_done/'.$dt->id).'" class="btn btn-success btn-xs">Done</a>';
                                }elseif ($dt->status_produksi == '2'){
                                    $status = '<p class="text-danger">Pengambilan Barang</p>';
                                }else{
                                    $status = '<p class="text-primary">Selesai</p>';
                                }
                        ?>
                        <tr >
                            <td><?= $no ?></td>
                            <td>
                                <?= $dt->nospk ?> <br>
                                <!-- <?= $dt->operator_nama ?> -->
                            </td>
    
                            <td>
                                <?= date_indo($dt->dateline_tgl) ?><br>
                                <?= $dt->dateline_jam ?>
                            </td>
                            
                            
                            
                            
                            <td class="text-center">
                                
                                <?= $status ?>
                                <a class="btn btn-primary btn-block" href="<?= base_url('transaksi/cek_pb/'.$dt->nospk)?>">
                                    <i class="fa fa-edit"> </i> Cek Invoice 
                                </a>
                            </td>
                            
                        </tr>
                        
                        </div>
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








