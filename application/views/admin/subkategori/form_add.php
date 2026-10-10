

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
                    <?php 
                        $order = $urutan['subkategori_urutan']+1;
                    ?>
                    <form class="form-horizontal form-bordered" method="post" action="<?= base_url($controller.'/actionAdd')?>">
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Urutan <?= $controller ?></label>
                        <div class="col-md-6">
                            <input name="subkategori_urutan" class="form-control" id="subkategori_urutan" type="number" value="<?= $order ?>" autofocus>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Kategori </label>
                        <div class="col-md-6">
                            <select name="kategori_id" id="kategori_id" class="formcontrol">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach($kategori as $k): ?>
                                    <option value="<?= $k->kategori_id ?>"><?= $k->kategori_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Nama <?= $controller ?></label>
                        <div class="col-md-6">
                            <input name="subkategori_nama" class="form-control" id="subkategori_nama" type="text" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Keterangan <?= $controller ?></label>
                        <div class="col-md-6">
                            <textarea name="subkategori_deskripsi" class="form-control" id="subkategori_deskripsi"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Status <?= $controller ?></label>
                        <div class="col-md-6">
                            <input type="radio" name="status" value="1" selected> Aktif &nbsp;&nbsp;&nbsp;
                            <input type="radio" name="status" value="0"> Tidak Aktif
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