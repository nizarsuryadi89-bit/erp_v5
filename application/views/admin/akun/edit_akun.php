<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title"><a href="<?= base_url('karyawan/akun')?>">Kembali</a> Update Akun</h2>
            </header>
            <div class="panel-body">
                <form class="form-horizontal"  method="post" action="<?= base_url('karyawan/aksi_edit_akun')?>">
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDefault">ID User</label>
                        <div class="col-md-9">
                            <input name="user_id" type="text" class="form-control" id="inputDefault" value ="<?= $getlist['user_id'] ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Username </label>
                        <div class="col-md-9">
                            <input name="username" class="form-control" id="barcode" type="text" value ="<?= $getlist['username']  ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Password</label>
                        <div class="col-md-9">
                            <input name="password" class="form-control" id="supplier_nama" type="text" value ="<?= $getlist['password']  ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Nama Karyawan</label>
                        <div class="col-md-9">
                            <select name="karyawan_id" class="form-control">
                                <?php foreach ($karyawan as $j): ?>
                                <option value="<?= $j->karyawan_id ?>" <?= $getlist['karyawan_id'] == $j->karyawan_id ? 'selected' : '' ?>>
                                    <?= $j->nama_lengkap ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Store</label>
                        <div class="col-md-9">
                            <select name="perusahaan_id" id="perusahaan_id" class="form-control">
                                <?php 
                                    foreach ($perusahaan as $kar){
                                        if ($kar->id_perusahaan == $getlist['perusahaan_id']){
                                ?>
                                <option value="<?= $kar->id_perusahaan?>" selected><?= $kar->nama_perusahaan ?></option>
                                <?php 
                                        }else{
                                ?>
                                <option value="<?= $kar->id_perusahaan?>"><?= $kar->nama_perusahaan ?></option>
                                <?php
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="inputDisabled">Email</label>
                        <div class="col-md-9">
                            <input name="email" class="form-control" id="supplier_email" type="text" value ="<?= $getlist['email']  ?>">
                        </div>
                    </div>
                
                    
                    <div class="form-group text-center">
                        <div class="col-md-12" >
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="<?= base_url($controller.'/bahan') ?>" class="btn btn-warning">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        </div>
    </div>
</div>