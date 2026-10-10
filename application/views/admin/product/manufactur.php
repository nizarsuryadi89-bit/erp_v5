<form class="form-horizontal" method="post" action="<?= base_url($controller.'/actionupdataproduct')?>">
    <input type="hidden" name="kategori_id" value="<?= $product['kategori_id'] ?>">
    <input type="hidden" name="subkategori_id" value="<?= $product['subkategori_id'] ?>">
    
    
    <div class="form-group">
    <label class="col-md-2 control-label" for="inputDisabled">Bahan </label>
    <div class="col-md-10">
        <select name="bahan_id" id="bahan_id" class="form-control" readonly>
            
            <?php foreach($bahan as $b): 
                if ($b->bahan_id == $product['bahan_id']){
            ?>
                <option value="<?= $b->bahan_id ?>" selected><?= $b->bahan_nama ?></option>
            <?php 
                }else{


            ?>
                <option value="<?= $b->bahan_id ?>"><?= $b->bahan_nama ?></option>
            <?php
                }
                endforeach; 
            ?>
        </select>
    </div>
    </div>

    <div class="form-group">
    <label class="col-md-2 control-label" for="inputDisabled">Nama Product</label>
    <div class="col-md-5">
        <input name="product_id" value="<?= $product['product_id']?>" class="form-control" id="product_id" type="hidden" required placeholder="Nama Product">
        <input name="product_nama" value="<?= $product['product_nama']?>" class="form-control" id="product_nama" type="text" required placeholder="Nama Product" readonly>
    </div>
    <div class="col-md-5">
        <select name="satuan_id" id="satuan_id" class="form-control" readonly>
            <option value="">-- Pilih Satuan --</option>
            <?php foreach($satuan as $k): ?>
                <?php 
                    if ($k->satuan_id == $product['satuan_id']){
                ?>
                <option value="<?= $k->satuan_id ?>" selected><?= $k->satuan_nama ?></option>
                <?php
                    }else{
                ?>
                <option value="<?= $k->satuan_id ?>" ><?= $k->satuan_nama ?></option>
                <?php
                    }
                ?>
                
                
            <?php endforeach; 
            ?>
        </select>
    </div>
    </div>

    <div class="form-group">
    <label class="col-md-2 control-label" for="inputDisabled">Waktu Produksi </label>
    <div class="col-md-5">
        <input name="product_waktu_pengerjaan" class="form-control"  value="<?= $product['product_waktu_pengerjaan'] ?>" id="product_waktu_pengerjaan" type="number" >
    </div>
    <div class="col-md-5">
        <select name="satuan_waktu_id" id="satuan_waktu_id" class="form-control">
            <option value="">-- Pilih Satuan Waktu --</option>
            <?php foreach($waktu as $w): ?>
                <?php 
                    if ($w->waktu_id == $product['satuan_waktu_id']){
                ?>
                    <option value="<?= $w->waktu_id ?>" selected><?= $w->waktu_nama ?></option>
                <?php 
                    }
                ?>
                    
            <?php endforeach; ?>
        </select>
    </div>
    </div>

    <div class="form-group">
    <label class="col-md-2 control-label" for="inputDisabled">Flow Job (1,2,3,4,5) Operator Dikerjakan Oleh </label>
    <div class="col-md-2">
        <select name="step_1" id="" class="form-control">
            <?php 
                foreach($step as $s):
                    if ($product['step_1'] == $s->operator_id){
            ?>
                    <option value="<?= $s->operator_id ?>" selected><?= $s->operator_nama ?></option>
            <?php 
                    }else{
            ?>
                    <option value="<?= $s->operator_id ?>"><?= $s->operator_nama ?></option>
            <?php
                    }
                endforeach;
            ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="step_2" id="" class="form-control">
        <option value="0">Pilih Langkah ke 2</option>
            <?php 
                
                foreach($step as $s):
                    if ($product['step_2'] == $s->operator_id){
            ?>
                    <option value="<?= $s->operator_id ?>" selected><?= $s->operator_nama ?></option>
            <?php 
                    }else{
            ?>
                    <option value="<?= $s->operator_id ?>"><?= $s->operator_nama ?></option>
            <?php
                    }
                endforeach;
            ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="step_3" id="" class="form-control">
        <option value="0">Pilih Langkah ke 3</option>
            <?php 
                
                foreach($step as $s):
                    if ($product['step_3'] == $s->operator_id){
            ?>
                    <option value="<?= $s->operator_id ?>" selected><?= $s->operator_nama ?></option>
            <?php 
                    }else{
            ?>
                    <option value="<?= $s->operator_id ?>"><?= $s->operator_nama ?></option>
            <?php
                    }
                endforeach;
            ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="step_4" id="" class="form-control">
        <option value="0">Pilih Langkah ke 4</option>
        <?php 
                
                foreach($step as $s):
                    if ($product['step_4'] == $s->operator_id){
            ?>
                    <option value="<?= $s->operator_id ?>" selected><?= $s->operator_nama ?></option>
            <?php 
                    }else{
            ?>
                    <option value="<?= $s->operator_id ?>"><?= $s->operator_nama ?></option>
            <?php
                    }
                endforeach;
            ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="step_5" id="" class="form-control">
            <option value="0">Pilih Langkah ke 5</option>
            <?php 
                
                foreach($step as $s):
                    if ($product['step_5'] == $s->operator_id){
            ?>
                    <option value="<?= $s->operator_id ?>" selected><?= $s->operator_nama ?></option>
            <?php 
                    }else{
            ?>
                    <option value="<?= $s->operator_id ?>"><?= $s->operator_nama ?></option>
            <?php
                    }
                endforeach;
            ?>
        </select>
    </div>

    </div>
    <div class="form-group">
    <label class="col-md-2 control-label" for="inputDisabled">Jenis Produksi</label>
    <div class="col-md-4">
        <select name="tipe_id" id="satuan_id" class="form-control" >
            <option value="">-- Pilih Type --</option>
            <?php foreach($type as $k): ?>
                <?php 
                    if ($k->tipe_id == $product['tipe_id']){
                ?>
                <option value="<?= $k->tipe_id ?>" selected><?= $k->tipe_nama ?></option>
                <?php
                    }else{
                ?>
                <option value="<?= $k->tipe_id ?>" ><?= $k->tipe_nama ?></option>
                <?php
                    }
                ?>
                
                
            <?php endforeach; 
            ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="lokasi_produksi_1" id="satuan_id" class="form-control" >
            <option value="">-- Pilih Type --</option>
            <?php foreach($perusahaan as $k): ?>
                <?php 
                    if ($k->id_perusahaan == $product['perusahaan_id']){
                ?>
                <option value="<?= $k->id_perusahaan ?>" selected><?= $k->nama_perusahaan ?></option>
                <?php
                    }else{
                ?>
                <option value="<?= $k->id_perusahaan ?>" ><?= $k->nama_perusahaan ?></option>
                <?php
                    }
                ?>
                
                
            <?php endforeach; 
            ?>
        </select>
        
    </div>
    <div class="col-md-3">
        <select name="lokasi_produksi_2" id="satuan_id" class="form-control" >
            <option value="">-- Pilih Type --</option>
            <?php foreach($perusahaan as $k): ?>
                <?php 
                    if ($k->id_perusahaan == $product['perusahaan_id']){
                ?>
                <option value="<?= $k->id_perusahaan ?>" selected><?= $k->nama_perusahaan ?></option>
                <?php
                    }else{
                ?>
                <option value="<?= $k->id_perusahaan ?>" ><?= $k->nama_perusahaan ?></option>
                <?php
                    }
                ?>
                
                
            <?php endforeach; 
            ?>
        </select>
        
    </div>
    </div>
    <div class="form-group">
    <label class="col-md-2 control-label" for="inputDisabled">Keterangan </label>
    <div class="col-md-10">
        <textarea name="product_deskripsi" class="form-control" id="product_deskripsi">
            <?= $product['product_deskripsi']; ?>
        </textarea>

        <input type="hidden" name="product_id" value="<?= $product['product_id']?>">
    </div>
    </div>
    <div class="form-group text-center">
    <div class="col-md-12" >
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="<?= base_url($controller.'/product') ?>" class="btn btn-warning">Batal</a>
    </div>
    </div>
</form>