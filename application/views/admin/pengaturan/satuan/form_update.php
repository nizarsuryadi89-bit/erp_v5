

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
                    
                    <form class="form-horizontal form-bordered" method="post" action="<?= base_url($controller.'/actionUpdate')?>">
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDefault">ID <?= $controller ?></label>
                        <div class="col-md-6">
                            <input name="satuan_id" type="text" class="form-control" id="inputDefault" value ="<?= $getlist['satuan_id']  ?>" readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Nama <?= $controller ?></label>
                        <div class="col-md-6">
                            <input name="satuan_nama" class="form-control" id="$controller         = $this->uri->segment(1);" type="text" value ="<?= $getlist['satuan_nama']  ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Keterangan <?= $controller ?></label>
                        <div class="col-md-6">
                            <textarea name="satuan_deskripsi" class="form-control" id="kategori_deskripsi">
                                <?= $getlist['satuan_deskripsi']?>
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