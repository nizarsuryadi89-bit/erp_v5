

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
                            <a href="<?= base_url($controller.'/formAdd')?>" class="btn btn-primary">Add <?= $controller ?> <i class="fa fa-plus"></i></a>
                                <a href="<?= base_url($controller.'/formAdd')?>" class="btn btn-success">Cetak <?= $controller ?> <i class="fa fa-print"></i></a>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped" id="datatable-editable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Workflow</th>
                                <th>Workflow Urutan</th>
                                <th>Workflow Keterangan</th>
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
                                <td><?= $dt->alur_nama ?></td>
                                <td class="text-center"><?= $dt->alur_urutan ?></td>
                                <td><p><?= $dt->alur_keterangan ?></p></td>
                                
                                <td class="actions text-center" >
                                    <a href="<?= base_url($controller.'/viewData/'.$dt->alur_id)?>" ><i class="fa fa-list"></i></a>
                                    <a href="<?= base_url($controller.'/formUpdate/'.$dt->alur_id)?>"><i class="fa fa-pencil"></i></a>
                                    <a href="<?= base_url($controller.'/actionDelete/'.$dt->alur_id)?>"><i class="fa fa-trash-o"></i></a>
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