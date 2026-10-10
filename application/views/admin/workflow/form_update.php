

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
                    
                    <form class="form-horizontal form-bordered" method="post" action="<?= base_url('workflow/actionAdd')?>">
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDefault">ID Alur</label>
                        <div class="col-md-6">
                            <input name="alur_id" type="text" class="form-control" id="inputDefault" value ="<?= $getlist['alur_id']  ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Urutan Alur</label>
                        <div class="col-md-6">
                            <input name="alur_urutan" class="form-control" id="alur_urutan" type="text" value ="<?= $getlist['alur_urutan']  ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Nama Alur</label>
                        <div class="col-md-6">
                            <input name="alur_nama" class="form-control" id="alur_nama" type="text" value ="<?= $getlist['alur_nama']  ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Keterangan</label>
                        <div class="col-md-6">
                            <textarea name="alur_keterangan" class="form-control" id="alur_keterangan">
                                <?= $getlist['alur_keterangan']?>
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <div class="col-md-12" >
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="<?= base_url($controller.'/') ?>" class="btn btn-warning">Batal</a>
                        </div>
                    </div>
                    
                        
                    </div>

                </form>
            </div>
        </section>

    </div>
</div>