

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
                                
                                <a class="mb-xs mt-xs mr-xs modal-sizes btn btn-default" href="#modalLG">Add <?= $controller ?></a>
                                <a href="<?= base_url($controller.'/formAdd')?>" class="btn btn-success">Cetak <?= $controller ?> <i class="fa fa-print"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <table class="table table-bordered table-striped mb-none" id="datatable-details">		
								
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <!-- <th>Kategori URL</th> -->
                                
                                <th>Kategori Keterangan</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $no =1;
                                foreach ($tampilData as $dt){
                                    $status = ($dt->status == 1) ? 'Aktif' : 'Tidak Aktif';
                            ?>
                            <tr >
                                <td><?= $no ?></td>
                                <td><?= $dt->kategori_nama ?></td>
                                <!-- <td><?= $dt->kategori_url ?></td> -->
                                <td class="text-center" width="40%">
                                    <p class="text-left">
                                    <?= $dt->kategori_deskripsi ?>
                                    </p>
                                </td>
                                <td><?= $status ?></td>
                                
                                
                                <td class="actions text-center" >
                                    <a href="<?= base_url($controller.'/viewData/'.$dt->kategori_id)?>" ><i class="fa fa-list"></i></a>
                                    <a href="<?= base_url($controller.'/formUpdate/'.$dt->kategori_id)?>"><i class="fa fa-pencil"></i></a>
                                    <a href="<?= base_url($controller.'/actionDelete/'.$dt->kategori_id)?>"><i class="fa fa-trash-o"></i></a>
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


<div id="modalLG" class="modal-block modal-block-lg mfp-hide">
    <section class="panel">
        <header class="panel-heading">
            <h2 class="panel-title">Tambah Data Kategori Assets</h2>
        </header>
        <div class="panel-body">
            <table>
                <tr>
                    <td>Nama Kategori</td>
                    <td>:</td>
                    <td><input name="kategori_nama" type="text" class="form-control"></td>
                </tr>
                <tr>
                    <td>Keterangan Kategori</td>
                    <td>:</td>
                    <td><textarea name="kategori_keterangan" class="form-control" id="" cols="30" rows="4"></textarea></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">
                        <hr>
                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </td>
                </tr>
            </table>
        </div>
        
    </section>
</div>