<div class="row">
    

        <section class="panel">
                <header class="panel-heading">
                    <a href="<?= base_url('manufaktur/routing/'.$product)?>" class="">Kembali</a>
                </header>
                <div class="panel-body">
                    <div class="row">
                        
                        <div class="col-sm-6">
                            Jumlah Step : <?= $total_rows ?>
                        </div>
                        <div class="col-sm-6 text-right">
                            <div class="mb-md">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahLembur">
                                Tambah Data Step Routing
                            </button>
                            </div>
                        </div>
                    </div>
                    
                    <table class="table table-bordered table-striped mb-none" id="datatable-detail">		
								
                        <thead>
                            <tr>
                                
                                <th>No Urutan</th>
                                <th>Nama Step Ruoting</th>
                                <th>Operasi</th>
                                <th>Operator / Mesin</th>
                                <th>Durasi Waktu Pengerjaan (Menit)</th>
                                <th width="15%">Actions</th>
                            
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $no =1;
                                foreach ($step_routing as $dt){
                                    $status         = ($dt->is_active == '1') ? 'Aktif' : 'Tidak Aktif';
                                    $jml_routing    = $this->M_master->steprouting($dt->routing_id)->num_rows();
                                    $routing_step   = $this->M_master->steprouting($dt->routing_id)->result();
                                    
                            ?>
                            <tr >
                                
                                
                                <td><b><?= $dt->sequence ?></b></td>
                                <td><b><?= $dt->routing_name ?></b></td>
                                <td><?= $dt->operation ?></td>
                                <td><?= $dt->name ?></td>
                                <td><?= $jml_routing ?></td>
                                
                                <td class="text-center" width="10%" >
                                    <!-- <a class="btn btn-primary" href="<?= base_url($controller.'/detail/'.$dt->product_id)?>" ><i class="fa fa-list"></i></a> -->
                                    <!-- <a class="btn btn-success btn-xs" href="<?= base_url($controller.'/formUpdate/'.$dt->product_id)?>"><i class="fa fa-pencil"></i></a> -->
                                    <a class="btn btn-danger" href="<?= base_url($controller.'/actionDelete/'.$dt->product_id)?>"><i class="fa fa-trash-o"></i></a>
                                    <!-- <a class="btn btn-warning" href="<?= base_url($controller.'/copyproduct/'.$dt->product_id)?>"><i class="fa fa-copy"></i></a> -->
                                </td>
                                
                                
                            </tr>
                            <?php 
                                
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <!-- end: page -->
    </section>
</div>


<!-- Modal -->
    <div class="modal fade" id="modalTambahLembur" tabindex="-1" role="dialog" aria-labelledby="modalLabelLembur" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('manufaktur/aksi_simpan_step_routing') ?>" method="post">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modalLabelLembur">Tambah Data Routing</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>   
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Routing</label>
                    <input type="text" name="routing_name" class="form-control" value="<?= $getrouting['routing_name'] ?>" readonly>
                    <input type="hidden" name="routing_id" class="form-control" value="<?= $getrouting['routing_id'] ?>" readonly>
                </div>

                <div class="form-group">
                    <label>No Urut</label>
                    <input type="text" name="sequence" class="form-control" value="">
                    
                </div>

                <div class="form-group">
                    <label>Operasi</label>
                    <input type="text" name="operation" class="form-control" value="">
                    
                </div>
                <div class="form-group">
                    <label>Durasi</label>
                    <input type="text" name="duration_mins" class="form-control" value="">
                    
                </div>
                 <div class="form-group">
                    <label>Catatan</label>
                    <input type="text" name="notes" class="form-control" value="">
                    
                </div>

                <div class="form-group">
                    <label>Operator</label>
                    <select name="workcenter_id" id="workcenter_id" class="form-control">
                        <?php 
                            foreach($workcenters as $p){
                        ?>
                            <option value="<?= $p->workcenter_id ?>"><?= $p->name ?></option>
                        <?php 
                            }
                        ?>

                    </select>
                </div>


                <div class="modal-footer">
                <button type="submit" class="btn btn-success">Simpan</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
    </div>
    </div>