

        <!-- start: page -->
        <section class="panel">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                        <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                    </div>
            
                    <h2 class="panel-title"><?= $title ?></h2>
                </header>
                <div class="panel-body">
                    <div class="row">
                        
                        <div class="col-sm-6">
                            Jumlah Data : <?= $total_rows ?>
                        </div>
                        <div class="col-sm-6 text-right">
                            <div class="mb-md">
                                <a href="<?= base_url($controller.'/formAdd')?>" class="btn btn-primary">Add <?= $divisi_nama ?> <i class="fa fa-plus"></i></a>
                                <a href="<?= base_url($controller.'/formAdd')?>" class="btn btn-success">Cetak <?= $divisi_nama ?> <i class="fa fa-print"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <table class="table table-bordered table-striped mb-none" id="datatable-details">		
								
                        <thead>
                            <tr>
                                <th>No</th>
                               
                                <th>Nama Lengkap</th>
                               
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $no =1;
                                foreach ($tampilData as $dt){
                                    
                            ?>
                            <tr >
                                <td><?= $no ?></td>
                              
                                <td><?= $dt->karyawan_nama ?></td>
                                <td class="actions text-center" >
                                    <a href="<?= base_url($controller.'/viewData/'.$dt->kategori_id)?>" class="btn btn-success"><i class="fa fa-list"></i></a>
                                    <a href="<?= base_url($controller.'/formUpdate/'.$dt->kategori_id)?>" class="btn btn-warning"><i class="fa fa-pencil"></i></a>
                                    <a href="<?= base_url($controller.'/actionDelete/'.$dt->kategori_id)?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
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