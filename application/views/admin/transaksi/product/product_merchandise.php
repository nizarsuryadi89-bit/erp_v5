<div class="modal-body">
    <div class="tabs">
        <ul class="nav nav-tabs text-left tabs-primary">
            <?php 
                $kategori = $this->uri->segment(4);
            ?>
            <li><a href="#1" data-toggle="tab">Display Promotion</a></li>
            <li><a href="#5" data-toggle="tab">Digital Promotion</a></li>
            <li><a href="#3" data-toggle="tab">Product Satuan (Merchandise, Stasionery & Apparel)</a></li>
            <li><a href="#2" data-toggle="tab">Marketplace</a></li>
            
            
        </ul>
        <div class="tab-content">
            <!-- Display Promosi -->
            <div id="1" class="tab-pane active">  
            <?php echo form_open('transaksi/action_add_temp'); ?>   
                
                <input type="hidden" value="<?= $kategori ?>" name="kategori_id" class="form-control" >
                <input type="hidden" value="9" name="subkategori_id" class="form-control" >
                <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >
                <input type="hidden" value="0.0" name="panjang" class="form-control" >
                <input type="hidden" value="0.0" name="lebar" class="form-control" >
                <input type="hidden" value="0" name="ket_3" class="form-control" >
                <input type="hidden" value="0" name="ket_4" class="form-control" >
                <input type="hidden" value="0" name="finishing" class="form-control" >

                <?php 
                    $bahan = $this->db->query("SELECT * FROM tbl_bahan WHERE kategori_kode = 4 ")->result();
                ?>
                <div class="form-group">
                    <label class="col-md-2 control-label">Paket Display / Mesin</label>
                    <div class='col-md-4'>
                        <select name="ket_2" id="ket_2" class="form-control">
                        <?php 
                            foreach($paketBanner as $paket){
                        ?>
                            <option value="<?= $paket->id_paket_banner?>"><?= $paket->nama_paket_banner?></option>    
                        <?php
                            }
                        ?>
                        </select>
                    </div>
                    <div class='col-md-3'>
                        <select name="ket_1" id="ket_1" class="form-control">
                            <option value="2">Outdoor</option>
                            <option value="1">Epson</option>
                            <option value="3">UV</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name='ket_3' class='form-control'>
                            <option value='0'>Tanpa Laminasi</option>
                            <option value='1'>Laminasi Glossy</option>
                            <option value='2'>Laminasi Doft</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Bahan Utama</label>
                    <div class='col-md-10'>
                        <select name="bahan_id" id="bahan_id" class="form-control">
                        <?php 
                            foreach($bahan as $bhn){
                        ?>
                            <option value="<?= $bhn->bahan_id?>"><?= $bhn->bahan_nama?></option>    
                        <?php
                            }
                        ?>
                        </select>
                    </div>
                    
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Qty</label>
                    <div class="col-md-10">
                        <div class="input-group mb-md">
                            <input type="text" name="qty" class="form-control" required onkeypress="return event.charCode >= 48 && event.charCode <=57">
                            <span class="input-group-addon btn-warning">Minimal Order 1 PCS</span>
                        </div>
                    </div>
                </div>
            
                <div class="form-group">
                <label class="col-md-2 control-label">Keterangan / File</label>
                <div class="col-md-10">
                    <div class="input-group mb-md">
                        <input type="text" name="file" class="form-control col-md-12">
                        <span class="input-group-addon btn-warning">Nama File</span>
                    </div>
                        <button type="submit" class="btn btn-success col-md-12"><i class="icon-pencil"></i> Tambah Item</button>
                    </div>
                </div>
            </form>
        </div>
            
    
        <div id="3" class="tab-pane">
            <?php echo form_open('transaksi/action_add_temp'); ?>
                <input type="hidden" value="<?= $kategori ?>" name="kategori_id" class="form-control" >
                <input type="hidden" value="11" name="subkategori_id" class="form-control" >
                <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >
                <input type="hidden" name="panjang" value="0.0" class="form-control" placeholder="Masukkan Panjang">
                <input type="hidden" name="lebar" value="0.0" class="form-control" placeholder="Masukkan Panjang">
                <?php 
                    $finishing = $this->db->query("SELECT * FROM tbl_product WHERE (subkategori_id = 13 or subkategori_id = 12 or subkategori_id = 11) ")->result();
                ?>
                <div class="form-group">
                    <label class='col-md-2'>Product Merchandise</label>
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
                            <option value="0">-- Pilih Product Finishing --</option>
                            
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-2 control-label">Metode Cetak - Jumlah</label>
                        
                        <div class="col-md-10">
                            <div class="input-group mb-md">
                                <input type="text" name="qty" class="form-control" required onkeypress="return event.charCode >= 48 && event.charCode <=57">
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
        
        <div id="5" class="tab-pane">
                <?php echo form_open('transaksi/action_add_temp'); ?>
                    <input type="hidden" value="<?= $kategori ?>" name="kategori_id" class="form-control" >
                    <input type="hidden" value="10" name="subkategori_id" class="form-control" >
                    <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                    <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                    <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >
                    <input type="hidden" name= "panjang"  class="form-control" value="0.0" required>
                    <input type="hidden" name= "lebar"  class="form-control"  value="0.0" required>
                    
                    <input type="hidden" name= "ket_3"  class="form-control"  value="0" required>
                    <input type="hidden" name= "ket_4"  class="form-control"  value="0" required>
                    <input type="hidden" name= "finishing"  class="form-control"  value="0" required>


                    <?php 
                        $bahan = $this->db->query("SELECT * FROM tbl_bahan where kategori_kode = 1")->result();
                    ?>
                    <div class="form-group">
                        <label class='col-md-2'>Paket Digital - Bahan - Side</label>
                        <div class='col-md-3'>
                            <select name="ket_2" id="ket_2" style="width: 250px;" data-plugin-selectTwo>
                                <?php 
                                    foreach($paketdigital as $pkt){
                                        echo "<option value='$pkt->paketdigital_id'>$pkt->nama_paket_digital</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class='col-md-3'>
                            <select name="bahan_id" id="bahan_id" style="width: 250px;" data-plugin-selectTwo>
                                <?php 
                                    foreach($bahan as $bhn){
                                        echo "<option value='$bhn->bahan_id'>$bhn->bahan_nama</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class='col-md-3'>
                            <select name="ket_1" id="ket_2" class="form-control">
                                <option value="1">1 Sisi</option>
                                <option value="2">2 Sisi</option>
                            </select>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <label class="col-md-2 control-label">Metode Cetak - Jumlah</label>
                        
                        <div class="col-md-10">
                            <div class="input-group mb-md">
                                <input type="text" name="qty" class="form-control" required onkeypress="return event.charCode >= 48 && event.charCode <=57">
                                <span class="input-group-addon btn-warning">Min Order 1 PCS</span>
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
        <div id="2" class="tab-pane">
        <?php echo form_open('transaksi/action_add_temp'); ?>
        <input type="hidden" value="<?= $kategori ?>" name="kategori_id" class="form-control" >
                <input type="hidden" value="11" name="subkategori_id" class="form-control" >
                <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >
                <input type="hidden" name="panjang" value="0.0" class="form-control" placeholder="Masukkan Panjang">
                <input type="hidden" name="lebar" value="0.0" class="form-control" placeholder="Masukkan Panjang">
                <?php 
                    $finishing = $this->db->query("SELECT * FROM tbl_product WHERE (subkategori_id = 13 or subkategori_id = 12 or subkategori_id = 11) ")->result();
                ?>
                <div class="form-group">
                    <label class='col-md-2'>Product Merchandise</label>
                    <div class='col-md-5'>
                        <select style="width: 450px; height:20px"  name="product_id" id="bahan_finishing" required data-plugin-selectTwo class="form-control populate placeholder" data-plugin-options='{ "placeholder": "Pilih Product Merchandise", "allowClear": true }'>
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
                            <option value="0">-- Pilih Product Finishing --</option>
                            
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-2 control-label">Metode Cetak - Jumlah</label>
                        
                        <div class="col-md-10">
                            <div class="input-group mb-md">
                                <input type="text" name="qty" class="form-control" required onkeypress="return event.charCode >= 48 && event.charCode <=57">
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
</div>