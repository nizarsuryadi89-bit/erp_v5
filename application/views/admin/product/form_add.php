<div class="row">
            <section class="panel">
                <header class="panel-heading">
                    <a class="btn btn-dark" href="<?= base_url('warehouse/bahan')?>">Kembali</a>
                </header>
                <div class="panel-body">
                    <section class="panel form-wizard" id="w2">
                            <form class="form-horizontal form-bordered" method="post" action="<?= base_url('product/actionAdd')?>">
                            <div class="tab-content">
								<div id="w2-account" class="tab-pane active">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="inputDisabled">Kategori </label>
                                        <div class="col-md-5">
                                            <select data-plugin-selectTwo name="kategori_id" id="kategori_id" class="form-control">
                                                <option value="">-- Pilih Kategori --</option>
                                                <?php foreach($kategori as $k): ?>
                                                    <option value="<?= $k->kategori_id ?>"><?= $k->kategori_nama ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-5">
                                            <select data-plugin-selectTwo name="subkategori_id" id="subkategori_id" class="subkategori form-control">
                                                <option value="0">-- Pilih Sub Kategori --</option>
                                                <?php foreach($subkategori as $k): ?>
                                                    <option value="<?= $k->subkategori_id ?>"><?= $k->subkategori_nama ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="inputDisabled">Bahan </label>
                                        <?php 
                                            if ($this->uri->segment(3) == null)
                                            {
                                        ?>
                                        <div class="col-md-10">
                                            <select data-plugin-selectTwo name="bahan_id" id="bahan_id" class="form-control">
                                                <option value="">-- Pilih Bahan Baku --</option>
                                                <?php foreach($bahan as $b): ?>
                                                    <option value="<?= $b->bahan_id ?>"><?= $b->bahan_nama ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <?php
                                            }else{
                                        ?>
                                        <div class="col-md-10">
                                                <input type="text" class="form-control" name="" id="" value="<?= $bahan['bahan_nama']?>" readonly>
                                                <input type="hidden" name="bahan_id" id="" value="<?= $bahan['bahan_id']?>">
                                        
                                        </div>
                                        <?php
                                            }
                                        ?>
                                    </div>
                                   
                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="inputDisabled">Nama <?= $controller ?></label>
                                        <div class="col-md-7">
                                            <input name="product_nama" class="form-control" id="product_nama" type="text" required placeholder="Nama Product">
                                        </div>
                                        <div class="col-md-3">
                                            <select data-plugin-selectTwo name="satuan_id" id="satuan_id" class="form-control">
                                                <option value="">-- Pilih Satuan --</option>
                                                <?php foreach($satuan as $k): ?>
                                                    <option value="<?= $k->satuan_id ?>"><?= $k->satuan_nama ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="inputDisabled">Waktu Produksi </label>
                                        <div class="col-md-3">
                                            <input name="product_waktu_pengerjaan" class="form-control" id="product_waktu_pengerjaan" type="number" >
                                        </div>
                                        <div class="col-md-3">
                                            <select data-plugin-selectTwo name="satuan_waktu_id" id="satuan_waktu_id" class="form-control">
                                                <option value="">-- Pilih Satuan Waktu --</option>
                                                <?php foreach($waktu as $w): ?>
                                                    <option value="<?= $w->waktu_id ?>"><?= $w->waktu_nama ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select data-plugin-selectTwo name="tipe_id" id="tipe_id" class="form-control">
                                                <option value="">-- Jenis Product --</option>
                                                <?php foreach($tipe as $w): ?>
                                                    <option value="<?= $w->tipe_id ?>"><?= $w->tipe_nama ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="inputDisabled">Keterangan <?= $controller ?></label>
                                        <div class="col-md-10">
                                            <textarea name="product_deskripsi" class="form-control" id="product_deskripsi"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group text-center">
                                        <div class="col-md-12" >
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                            <a href="<?= base_url($controller.'/') ?>" class="btn btn-warning">Batal</a>
                                        </div>
                                    </div>
                                
                            </form>
                            
                        </div>
            </div>
        </section>

    </div>
</div>

