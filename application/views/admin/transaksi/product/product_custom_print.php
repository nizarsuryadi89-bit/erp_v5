
    <div class="tabs">
        <ul class="nav nav-tabs text-left tabs-primary">
            <?php 
                $subkategori = $this->db->query("SELECT * FROM tbl_subkategori WHERE kategori_id = 5")->result();
                foreach($subkategori as $sub){
                }
            ?>
           
            <li><a href="#1" data-toggle="tab">Platbed & Rotary & Grafir</a></li>
            <li><a href="#2" data-toggle="tab">DTG & DTF</a></li>
            <li><a href="#3" data-toggle="tab">Polyflex & Cutting</a></li>
            
        </ul>
        <div class="tab-content">
            <div id="1" class="tab-pane active"> <!-- Cetakan A3+ Lembaran -->
            <?php echo form_open('transaksi/action_add_temp'); ?>   
                <input type="hidden" value="4" name="kategori_id" class="form-control" >
                <input type="hidden" value="16" name="subkategori_id" class="form-control" >
                <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >
               
                <input type="hidden" name= "ket_2"  class="form-control"  value="0" required>
                <input type="hidden" name= "ket_3"  class="form-control"  value="0" required>
                <input type="hidden" name= "ket_4"  class="form-control"  value="0" required>
               

                <?php 
                    $finishing = $this->db->query("SELECT * FROM tbl_product WHERE (subkategori_id = 16) OR (subkategori_id = 18)  ")->result();
                ?>
                <div class="form-group">
                    <label class='col-md-2'>Jasa Cetak</label>
                    <div class='col-md-5'>
                        <select style="width: 400px; height:20px"  name="product_id" id="bahan_finishing" required data-plugin-selectTwo class="form-control populate placeholder" data-plugin-options='{ "placeholder": "Pilih Product Merchandise", "allowClear": true }'>
                                <option value="">Pilih Bahan Finishing</option>
                                <?php 
                                    foreach ($finishing as $fin):
                                ?>
                                <option value="<?= $fin->product_id ?>"><?= $fin->product_nama ?></option>
                                <?php
                                    endforeach;
                                ?>
                            </select>
                    </div>
                    <div class="col-md-5">
                        <select  name="harga_id" id="finishing_id" class="finishing form-control">
                            <option value="0">-- Pilih Jasa Print Table Platbed --</option>
                            
                        </select>
                    </div>
                    
                </div>
                <div class="form-group">
                    <label class='col-md-2'>P X L</label>
                    <div class='col-md-5'>
                        <input type="text" name="panjang" class="form-control" placeholder="Masukkan Panjang">
                        
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="lebar" class="form-control" placeholder="Masukkan Lebar">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Jumlah</label>
                    <div class="col-md-10">
                        <div class="input-group mb-md">
                            <input type="text" name="qty" class="form-control" required onkeypress="return event.charCode >= 48 && event.charCode <=57">
                            <span class="input-group-addon btn-warning">Min Order 10CM<sup>2</sup></span>
                        </div>
                    </div>
                </div>
                
            
                <div class="form-group">
                    <label class="col-md-2 control-label">Keterangan / File</label>
                    <div class="col-md-10">
                        <div class="input-group mb-md">
                            <input type="text" name="file" class="form-control col-md-11">
                            <span class="input-group-addon btn-warning">Nama File</span>
                        </div>
                            <button type="submit" class="btn btn-success col-md-12"><i class="icon-pencil"></i> Tambah Item</button>
                    </div>
                </div>
                            
                
                </form>
            </div>
            
        
            <div id="2" class="tab-pane"> <!-- Cetakan A3+ Lembaran -->
            <?php echo form_open('transaksi/action_add_temp'); ?>   
                <input type="hidden" value="4" name="kategori_id" class="form-control" >
                <input type="hidden" value="31" name="subkategori_id" class="form-control" >
                <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >
                <input type="hidden" name= "panjang"  class="form-control" value="0.0" required>
                <input type="hidden" name= "lebar"  class="form-control"  value="0.0" required>
                <input type="hidden" name= "ket_2"  class="form-control"  value="0" required>
                <input type="hidden" name= "ket_3"  class="form-control"  value="0" required>
                <input type="hidden" name= "ket_4"  class="form-control"  value="0" required>
               
                <div class="form-group">
                    <label class='col-md-2'>JASA CETAK - UKURAN FILE</label>
                    <div class='col-md-5'>
                        <select style="width: 440px; height:20px"  name="bahan_id"  required data-plugin-selectTwo class="form-control populate placeholder" data-plugin-options='{ "placeholder": "Pilih Product Merchandise", "allowClear": true }'>
                            <option value="198">JASA CETAK DTG</option>
                            <option value="199">JASA CETAK DTF</option>
                                    
                        </select>
                    </div>
                    <div class="col-md-5">
                    <select name="ket_1" id="ket_1" class="form-control">
                        <option value="-">-- Pilih Ukuran File --</option>
                        <?php 
                            foreach ($ukuranfile as $key => $value) {    
                        ?>
                        <option value="<?= $value->ukuran_file_id ?>" ><?= $value->ukuran_file_nama ?></option>
                        <?php    
                            }
                        ?>
                    </select>
                    </div>
                    
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Jumlah</label>
                    <div class="col-md-10">
                        <div class="input-group mb-md">
                            <input type="text" name="qty" class="form-control col-md-12" required onkeypress="return event.charCode >= 48 && event.charCode <=57">
                            <span class="input-group-addon btn-warning">Min Order 1 Lembar</span>
                        </div>
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-md-2 control-label">Keterangan / File</label>
                    <div class="col-md-10">
                        <div class="input-group mb-md">
                            <input type="text" name="file" class="form-control col-md-12" required>
                            <span class="input-group-addon btn-warning">Nama File</span>
                        </div>
                            <button type="submit" class="btn btn-success col-md-12"><i class="icon-pencil"></i> Tambah Item</button>
                    </div>
                </div>
                            
                
                </form>
            </div>
            
        
        <div id="3" class="tab-pane"> <!-- Cetakan A3+ Lembaran -->
            <?php echo form_open('transaksi/action_add_temp'); ?>   
                <input type="hidden" value="4" name="kategori_id" class="form-control" >
                <input type="hidden" value="32" name="subkategori_id" class="form-control" >
                <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >
                <input type="hidden" name= "panjang"  class="form-control" value="0.0" required>
                <input type="hidden" name= "lebar"  class="form-control"  value="0.0" required>
                <input type="hidden" name= "ket_2"  class="form-control"  value="0" required>
                <input type="hidden" name= "ket_3"  class="form-control"  value="0" required>
                <input type="hidden" name= "ket_4"  class="form-control"  value="0" required>
               
                <div class="form-group">
                    <label class='col-md-2'>JASA CETAK - UKURAN FILE <br> WARNA</label>
                    <div class='col-md-4'>
                        <select style="width: 300px; height:20px"  name="bahan_id"  required data-plugin-selectTwo class="form-control populate placeholder" data-plugin-options='{ "placeholder": "Pilih Product Merchandise", "allowClear": true }'>
                            <option value="166">JASA CETAK POLYPLEX</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="ket_1" id="ket_1" class="form-control">
                            <option value="-">-- Pilih Ukuran File --</option>
                            <?php 
                                foreach ($ukuranfile as $key => $value) {    
                            ?>
                            <option value="<?= $value->ukuran_file_id ?>" ><?= $value->ukuran_file_nama ?></option>
                            <?php    
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="ket_2" id="ket_2" class="form-control">
                            <option value="0">-- Pilih Warna--</option>
                            <?php 
                                foreach ($warna as $key => $value) {
                                    if ($value->warna_id == $hrg->ket_2) 
                                    {
                            ?>
                                    <option value="<?= $value->warna_id ?>" selected> <?= $value->warna ?> </option>
                            <?php
                                    }
                                    else{
                            ?>
                            
                            <option value="<?= $value->warna_id ?>" ><?= $value->warna ?></option>
                            <?php
                                }
                            }
                        ?>
                        </select>
                    </div>
                    
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Jumlah</label>
                    <div class="col-md-10">
                        <div class="input-group mb-md">
                            <input type="text" name="qty" class="form-control col-md-12" required onkeypress="return event.charCode >= 48 && event.charCode <=57">
                            <span class="input-group-addon btn-warning">Min Order 1 Lembar</span>
                        </div>
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-md-2 control-label">Keterangan / File</label>
                    <div class="col-md-10">
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
    
	