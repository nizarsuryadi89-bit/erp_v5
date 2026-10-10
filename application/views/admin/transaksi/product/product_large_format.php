
    <div class="tabs">
        <ul class="nav nav-tabs text-left tabs-warning">
            <?php 
                $kategori = $this->uri->segment(4);
            ?>
            <li><a href="#1" data-toggle="tab">Bahan Banner & Sticker</a></li>
            <li><a href="#3" data-toggle="tab">Finishing Flotter</a></li>       
            <li><a href="#4" data-toggle="tab">Textile Meteran</a></li>
            <li><a href="#5" data-toggle="tab">DTF Meteran</a></li>
            <li><a href="#6" data-toggle="tab">Finishing Textile</a></li>     
        </ul>
        <div class="tab-content">
            
            <div id="1" class="tab-pane active"> <!-- Cetakan Metera -->
                <?php echo form_open('transaksi/action_add_temp'); ?>   
                
                <input type="hidden" value="<?= $kategori ?>" name="kategori_id" class="form-control" >
                <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >
               
                <input type="hidden" name= "ket_2"  class="form-control"  value="0" required>
                <input type="hidden" name= "ket_3"  class="form-control"  value="0" required>
                <input type="hidden" name= "ket_4"  class="form-control"  value="0" required>

                
                <div class="form-group">
                    <label class='col-md-2'>Product</label>
                    <div class='col-md-3'>
                        <select name="subkategori_id" id="subkategori_id" class="form-control">
                            <option value="0">Pilih Jenis</option>
                            <option value="4">Banner</option>
                            <option value="5">Sticker</option>
                            <option value="6">Laminasi</option>
                        </select>
                    </div>
                    <div class='col-md-3'>
                        <select data-plugin-selectTwo name="bahan_id" id="bahan_id" class="bahan form-control">
                            <option value="0">-- Pilih Sub Kategori --</option>
                            
                        </select>
                    </div>
                    <div class='col-md-2'>
                        <select name="ket_1" id="ket_1" class="form-control">
                            <?php 
                                foreach($mesin as $ms){
                            ?>
                            <option value="<?= $ms->mesin_id ?>"><?= $ms->mesin_nama?></option>
                          
                            <?php 
                                }
                            ?>
                        </select>
                    </div>
                    <div class='col-md-2'>
                        <select name="finishing" id="finishing" class="form-control">
                        
                            <option value="0">Tanpa Finishing</option>
                        
                            <option value="1" >Tambah Finishing</option>
                    
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
                            <span class="input-group-addon btn-warning">Min Order 1 m<sup>2</sup></span>
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
            
            <!-- Finishing Large Format  -->
            <div id="3" class="tab-pane">
                <?php echo form_open('transaksi/action_add_temp'); ?>
                    <input type="hidden" value="<?= $kategori ?>" name="kategori_id" class="form-control" >
                    <input type="hidden" value="9" name="subkategori_id" class="form-control" >
                    <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                    <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                    <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >
                    <input type="hidden" name="panjang" value="0.0" class="form-control" placeholder="Masukkan Panjang">
                    <input type="hidden" name="lebar" value="0.0" class="form-control" placeholder="Masukkan Panjang">
                    <?php 
                        $bahan = $this->db->query("SELECT * FROM tbl_bahan WHERE kategori_kode = 9 ")->result();
                    ?>
                    <div class="form-group">
                        <label class='col-md-2'>Bahan / Jasa Finishing</label>
                        <div class='col-md-10'>
                            <select name="bahan_id" id="bahan_id" style="width: 600px;" data-plugin-selectTwo>
                                <?php 
                                    foreach($bahan as $bhn){
                                        echo "<option value='$bhn->bahan_id'>$bhn->bahan_nama</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <label class="col-md-2 control-label">Jumlah</label>
                        <div class="col-md-10">
                            <div class="input-group mb-md">
                                <input type="text" name="qty" class="form-control" required onkeypress="return event.charCode >= 48 && event.charCode <=57">
                                <span class="input-group-addon btn-warning">Minimal Order 1 PCS</span>
                            </div>
                            <button type="submit" class="btn btn-success col-md-12"><i class="icon-pencil"></i> Tambah Item</button>
                        </div>
                    </div>
                    
                
                </form>
            </div>
            <div id="4" class="tab-pane"> <!-- Cetakan Metera -->
                <?php echo form_open('transaksi/action_add_temp'); ?>   
                
                <input type="hidden" value="<?= $kategori ?>" name="kategori_id" class="form-control" >
                <input type="hidden" value="17" name="subkategori_id" class="form-control" >
                <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >

                <?php 
                    $bahan = $this->db->query("SELECT * FROM tbl_bahan WHERE kategori_kode = 8 ")->result();
                ?>
                <div class="form-group">
                    <label class='col-md-2'>Bahan</label>
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
                        <label class='col-md-2'>P X L</label>
                        <div class='col-md-5'>
                            <input type="text" name="panjang" class="form-control" placeholder="Masukkan Panjang">
                            
                        </div>
                        <div class="col-md-5">
                            <select name="lebar" id="lebar" class="form-control">
                                <option value="0.6">60 cm</option>
                                <option value="0.9">90 cm</option>
                                <option value="1.2">120 cm</option>
                                <option value="1.5">150 cm</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Jumlah</label>
                        <div class="col-md-10">
                            <div class="input-group mb-md">
                                <input type="text" name="qty" class="form-control" required onkeypress="return event.charCode >= 48 && event.charCode <=57">
                                <span class="input-group-addon btn-warning">Min Order 1 Meter</span>
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
            
            <div id="5" class="tab-pane">
                <?php echo form_open('transaksi/action_add_temp'); ?>
                <input type="hidden" value="<?= $kategori ?>" name="kategori_id" class="form-control" >
                <input type="hidden" value="18" name="subkategori_id" class="form-control" >
                <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >
                <input type="hidden" name="qty" value="1" class="form-control" placeholder="Masukkan Panjang">
                <input type="hidden" name="lebar" value="1" class="form-control" placeholder="Masukkan Panjang">
                    
                    <div class="form-group">
                        <label class='col-md-2'>Bahan Utama </label>
                        <div class='col-md-5'>
                            <input type="text" name="bahan" class="form-control" value="DTF Meteran" readonly>
                            <input type="hidden" name="bahan_id" class="form-control" readonly value="205">
                        </div>
                        
                    </div>
                    <div class="form-group">
                        <label class='col-md-2'>Ukuran Panjang </label>
                        <div class='col-md-10'>
                        <div class="input-group mb-md">
                            <input type="text" name="panjang" class="form-control col-md-12" required>
                            <span class="input-group-addon btn-warning">Dalam Satuan Meter</span>
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
            <!-- Finishing Textile  -->
            <div id="6" class="tab-pane">
                <?php echo form_open('transaksi/action_add_temp'); ?>
                    <input type="hidden" value="<?= $kategori ?>" name="kategori_id" class="form-control" >
                    <input type="hidden" value="19" name="subkategori_id" class="form-control" >
                    <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                    <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                    <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >
                    <input type="hidden" name="qty" value="1" class="form-control" placeholder="Masukkan Panjang">
                    <input type="hidden" name="lebar" value="1" class="form-control" placeholder="Masukkan Panjang">
                    <?php 
                        $bahan = $this->db->query("SELECT * FROM tbl_bahan WHERE kategori_kode = 15 ")->result();
                    ?>
                    <div class="form-group">
                        <label class='col-md-2'>Finishing Textile</label>
                        <div class='col-md-10'>
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
                        <label class="col-md-2 control-label">Jumlah</label>
                        <div class="col-md-10">
                            <div class="input-group mb-md">
                                <input type="text" name="panjang" class="form-control" required onkeypress="return event.charCode >= 48 && event.charCode <=57">
                                <span class="input-group-addon btn-warning">Minimal Order 1 PCS</span>
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
