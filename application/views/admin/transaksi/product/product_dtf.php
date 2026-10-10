<div class="modal-body">
    <div class="tabs">
        <ul class="nav nav-tabs text-right tabs-primary">
            <?php 
                foreach($subkategori as $sub){
                }
            ?>
            <li><a href="#1" data-toggle="tab">Textile Meteran</a></li>
            <li><a href="#2" data-toggle="tab">DTF Meteran</a></li>
            <li><a href="#3" data-toggle="tab">Finishing</a></li>
            
            
        </ul>
        <div class="tab-content">
            
            <div id="1" class="tab-pane active"> <!-- Cetakan Metera -->
            <?php echo form_open('transaksi/action_add_temp'); ?>   
                
                <input type="hidden" value="8" name="kategori_id" class="form-control" >
                <input type="hidden" value="19" name="subkategori_id" class="form-control" >
                <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                <input type="hidden" value="<?= $kode ?>" name="kode_trx" class="form-control" >

                <?php 
                    $bahan = $this->db->query("SELECT * FROM tbl_bahan WHERE kategori_kode = 8 ")->result();
                ?>
                <div class="form-group">
                    <label class='col-md-3'>Bahan</label>
                    <div class='col-md-9'>
                        <select name="bahan_id" id="bahan_id" style="width: 500px;" data-plugin-selectTwo>
                            <?php 
                                foreach($bahan as $bhn){
                                    echo "<option value='$bhn->bahan_id'>$bhn->bahan_nama</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                
                
                <div class="form-group">
                        <label class='col-md-3'>P X L</label>
                        <div class='col-md-4'>
                            <input type="text" name="panjang" class="form-control" placeholder="Masukkan Panjang">
                            
                        </div>
                        <div class="col-md-4">
                            <select name="lebar" id="lebar" class="form-control">
                                <option value="0.6">60 cm</option>
                                <option value="0.9">90 cm</option>
                                <option value="1.2">120 cm</option>
                                <option value="1.5">150 cm</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Jumlah</label>
                        <div class="col-md-9">
                            <div class="input-group mb-md">
                                <input type="text" name="qty" class="form-control" required onkeypress="return event.charCode >= 48 && event.charCode <=57">
                                <span class="input-group-addon btn-warning">Min Order 1 Meter</span>
                            </div>
                        </div>
                    </div>
                
                    <div class="form-group">
                    <label class="col-md-3 control-label">Keterangan / File</label>
                    <div class="col-md-9">
                        <div class="input-group mb-md">
                            <input type="text" name="file" class="form-control col-md-12" required>
                            <span class="input-group-addon btn-warning">Nama File</span>
                        </div>
                            <button type="submit" class="btn btn-success col-md-12"><i class="icon-pencil"></i> Tambah Item</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div id="2" class="tab-pane">
            <?php echo form_open('transaksi/action_add_temp'); ?>
                <input type="hidden" value="8" name="kategori_id" class="form-control" >
                <input type="hidden" value="20" name="subkategori_id" class="form-control" >
                <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                <input type="hidden" value="<?= $kode ?>" name="kode_trx" class="form-control" >
                <input type="hidden" name="qty" value="1" class="form-control" placeholder="Masukkan Panjang">
                <input type="hidden" name="lebar" value="1" class="form-control" placeholder="Masukkan Panjang">
                    
                    <div class="form-group">
                        <label class='col-md-3'>Bahan Utama </label>
                        <div class='col-md-5'>
                            <input type="text" name="bahan" class="form-control" value="DTF Meteran" readonly>
                            <input type="hidden" name="bahan_id" class="form-control" readonly value="205">
                        </div>
                        
                    </div>
                    <div class="form-group">
                        <label class='col-md-3'>Ukuran Panjang </label>
                        <div class='col-md-9'>
                        <div class="input-group mb-md">
                            <input type="text" name="panjang" class="form-control col-md-12" required>
                            <span class="input-group-addon btn-warning">Dalam Satuan Meter</span>
                        </div>
                        </div>
                        
                    </div>
                    <div class="form-group">
                    <label class="col-md-3 control-label">Keterangan / File</label>
                    <div class="col-md-9">
                        <div class="input-group mb-md">
                            <input type="text" name="file" class="form-control col-md-12" required>
                            <span class="input-group-addon btn-warning">Nama File</span>
                        </div>
                            <button type="submit" class="btn btn-success col-md-12"><i class="icon-pencil"></i> Tambah Item</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Finishing Textile  -->
            <div id="3" class="tab-pane">
                <?php echo form_open('transaksi/action_add_temp'); ?>
                    <input type="hidden" value="8" name="kategori_id" class="form-control" >
                    <input type="hidden" value="22" name="subkategori_id" class="form-control" >
                    <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                    <input type="hidden" value="<?= $kode ?>" name="kode_trx" class="form-control" >
                    <input type="hidden" name="qty" value="1" class="form-control" placeholder="Masukkan Panjang">
                    <input type="hidden" name="lebar" value="1" class="form-control" placeholder="Masukkan Panjang">
                    <?php 
                        $bahan = $this->db->query("SELECT * FROM tbl_bahan WHERE kategori_kode = 15 ")->result();
                    ?>
                    <div class="form-group">
                        <label class='col-md-3'>Finishing DTF</label>
                        <div class='col-md-9'>
                            <select name="bahan_id" id="bahan_id" style="width: 300px;" data-plugin-selectTwo>
                                <?php 
                                    foreach($bahan as $bhn){
                                        echo "<option value='$bhn->bahan_id'>$bhn->bahan_nama</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <label class="col-md-3 control-label">Jumlah</label>
                        <div class="col-md-9">
                            <div class="input-group mb-md">
                                <input type="text" name="panjang" class="form-control" required onkeypress="return event.charCode >= 48 && event.charCode <=57">
                                <span class="input-group-addon btn-warning">Minimal Order 1 PCS</span>
                            </div>
                        </div>
                    </div>
                
                    <div class="form-group">
                    <label class="col-md-3 control-label">Keterangan / File</label>
                    <div class="col-md-9">
                        <div class="input-group mb-md">
                            <input type="text" name="file" class="form-control col-md-12" required>
                            <span class="input-group-addon btn-warning">Nama File</span>
                        </div>
                            <button type="submit" class="btn btn-success col-md-12"><i class="icon-pencil"></i> Tambah Item</button>
                        </div>
                    </div>
                </form>
            </div>
            </div>
            
    </div>
    
    <br>
</div>