

        <!-- start: page -->
        <section class="panel">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                        <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                    </div>
            
                    <h2 class="panel-title"><?= $title ?> </h2>
                </header>
                <div class="panel-body">
                    
                    
                    <table class="table table-bordered table-striped mb-none" id="datatable-details">		
								
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Keterangan</th>
                                <th>Jumlah <br> Stok Masuk di Aplikasi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $no =1;
                                foreach ($tampilData as $dt){
                                  
                            ?>
                            <tr >
                                <td><?= $no ?></td>
                                <td>
                                   
                                    <strong><?= $dt->bahan_nama ?></strong>  &nbsp;&nbsp;&nbsp;
                                    <a href="<?= base_url('warehouse/update_bahan/'.$dt->bahan_id)?>">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                
                                <td class="text-center"><strong class="text-success"><?= $dt->pengambilan_qty ?></strong></td>
                                
                               
                                
                                <td class="actions text-center">
                                    <form action="<?= base_url($controller.'/syncronisasi_stok_action/')?>" method="post">
                                        <input type="text" value="<?= $dt->pengambilan_qty ?>" name="qty_real">
                                        <input type="hidden" name="bahan_id" id="" value="<?= $dt->bahan_id ?>">
                                        <input type="hidden" name="pengambilan_id" id="" value="<?= $dt->pengambilan_id ?>">
                                        <input type="hidden" name="stok_id" id="" value="<?= $dt->stok_id?>">
                                        <input type="hidden" name="stok_awal" id="" value="<?= $dt->stok_awal?>">
                                        <input type="hidden" name="stok_tambah" id="" value="<?= $dt->stok_tambah?>">
                                        <input type="hidden" name="stok_kurang" id="" value="<?= $dt->stok_kurang?>">
                                        
                                        <button type="submit" class="btn btn-warning btn-xs btn-rounded"> Approve dan Synncronisasi </button><br>
                                    </form>
                                    
                                </td>
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