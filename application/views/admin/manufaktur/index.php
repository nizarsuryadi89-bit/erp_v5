

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
                                <a href="<?= base_url($controller.'/formAdd')?>" class="btn btn-primary">Tambah  <?= $controller ?>  <i class="fa fa-plus"></i></a>
                                <a href="<?= base_url($controller.'/formAdd')?>" class="btn btn-success">Cetak <?= $controller ?> <i class="fa fa-print"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <table class="table table-bordered table-striped mb-none" id="datatable-details">		
								
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Product - Bahan</th>
                                <th>Kategori / Sub Kategori</th>
                                <th>Satuan</th>
                                <th>Jumlah Detail Product</th>
                                <th hidden></th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $no =1;
                                foreach ($tampilData as $dt){
                                    $status = ($dt->status == '1') ? 'Aktif' : 'Tidak Aktif';
                                    $jml_detail = $this->M_master->jml_detail($dt->product_id)->num_rows();
                            ?>
                            <tr >
                                <td><?= $no ?></td>
                                
                                <td>
                                    <b><?= $dt->product_nama ?></b> &nbsp;|| &nbsp; <?= $dt->bahan_nama ?> 
                                    <?= $dt->product_deskripsi ?> 
                                </td>
                                <td>
                                    <?= $dt->kategori_nama ?> &nbsp; ||  &nbsp; 
                                    <b><?= $dt->subkategori_nama ?> </b>
                                </td>
                                <td><?= $dt->satuan_nama ?></td>
                                <td class="text-center">
                                    <?= $jml_detail ?>
                                </td>
                                <td hidden>
                                    <table>
                                        <tr>
                                            <td>
                                                Step 1 : <?= $dt->step_1 ?> <br>
                                                Step 2 : <?= $dt->step_2 ?> <br>
                                                Step 3 : <?= $dt->step_3 ?> <br>
                                                Step 4 : <?= $dt->step_4 ?> <br>
                                                Step 5 : <?= $dt->step_5 ?> <br>
                                            </td>
                                            <td>
                                                1 : Kasir <br>
                                                2 : OP A3+ <br>
                                                3 : OP Large Format <br>
                                                4 : OP Textile <br>
                                                5 : OP Sublime <br>
                                                
                                            </td>
                                            <td>
                                                6 : OP Finishing Digital <br>
                                                7 : OP Finishing Flotter <br>
                                                8 : PB <br>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                </td>
                                <td class="text-center" width="10%" >
                                    <a class="btn btn-primary btn-xs" href="<?= base_url($controller.'/detail/'.$dt->product_id)?>" ><i class="fa fa-list"></i></a>
                                    <a class="btn btn-success btn-xs" href="<?= base_url($controller.'/formUpdate/'.$dt->product_id)?>"><i class="fa fa-pencil"></i></a>
                                    <a class="btn btn-danger btn-xs" href="<?= base_url($controller.'/actionDelete/'.$dt->product_id)?>"><i class="fa fa-trash-o"></i></a>
                                    <a class="btn btn-warning btn-xs" href="<?= base_url($controller.'/copyproduct/'.$dt->product_id)?>"><i class="fa fa-copy"></i></a>
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