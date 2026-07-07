
    <div class="tabs">
        <ul class="nav nav-tabs text-left tabs-primary">
            <?php 
                $kategori = $this->uri->segment(4);
            ?>
            <li><a href="#1" data-toggle="tab">Product A3+ & Large Paper</a></li>
            <li><a href="#2" data-toggle="tab">Laminasi Digital</a></li>
            <li><a href="#3" data-toggle="tab">Finishing Digital</a></li>
            
        </ul>
        <div class="tab-content">
            <div id="1" class="tab-pane active"> <!-- Cetakan A3+ Lembaran -->
            <form action=<?= base_url('transaksi/action_add_temp') ?> method="post" id="form1">   
                <input type="hidden" value="<?= $kategori ?>" name="kategori_id" class="form-control" >
                <!-- <input type="hidden" value="1" name="subkategori_id" class="form-control" > -->
                <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >
                <input type="hidden" name= "panjang"  class="form-control" value="0.0" required>
                <input type="hidden" name= "lebar"  class="form-control"  value="0.0" required>
                
                <input type="hidden" name= "ket_4"  class="form-control"  value="0" required>
               

                <?php 
                    $bahan = $this->db->query("SELECT * FROM tbl_bahan where kategori_kode = '1' or kategori_kode = '2' ")->result();
                ?>
                <div class="form-group">
                    <label class='col-md-2'>Bahan</label>
                    <div class='col-md-2'>
                        <select name="subkategori_id" id="subkat" class="form-control" onchange="tampilkan()">
                            <option value="1">A3 + </option>
                            <option value="2">Large Paper</option>
                        </select>
                    </div>
                    <div class='col-md-3'>
                        <select name="bahan_id" id="bahan_id" style="width: 280px;"  data-plugin-selectTwo class="form-control populate placeholder" data-plugin-options='{ "placeholder": "Pilih Bahan Cetakan Digital", "allowClear": true }'>
                        <option value="">No Selected</option>
                            <?php 
                                foreach($bahan as $bhn){
                                    echo "<option value='$bhn->bahan_id'>$bhn->bahan_nama</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class='col-md-2' id="tampil"></div>
                    <div class='col-md-2' id="finish"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Cetakan / Finishing</label>
                    <div class='col-md-4'>
                        <select name="ket_1" id="ket_1" class="form-control">
                        
                        <?php 
                        foreach ($side as $key => $sd) {
                                if ($sd->side_id == $hrg->ket_1) 
                                {
                        ?>
                            <option value="<?= $sd->side_id ?>" selected><?= $sd->side_name ?></option>
                        <?php
                                }else{
                        ?>
                            <option value="<?= $sd->side_id ?>" ><?= $sd->side_name ?></option>
                        <?php
                                }
                            }
                        ?>
                        </select>
                    </div>
                    <div class='col-md-6'>
                        <select name="finishing" id="finishing" class="form-control">
                        
                            <option value="0">Tanpa Finishing</option>
                        
                            <option value="1" >Tambah Finishing</option>
                    
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
                            <input type="text" name="file" class="form-control col-md-12">
                            <span class="input-group-addon btn-warning">Nama File</span>
                        </div>
                            <button type="submit" class="btn btn-success col-md-12"><i class="icon-pencil"></i> Tambah Item</button>
                    </div>
                </div>
                            
                
                </form>
            </div>
            <div id="2" class="tab-pane"> <!-- Lamin A3+ Lembaran -->
            <?php echo form_open('transaksi/action_add_temp'); ?>   
                <input type="hidden" value="<?= $kategori ?>" name="kategori_id" class="form-control" >
                <input type="hidden" value="1" name="subkategori_id" class="form-control" >
                <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                <input type="hidden" name= "panjang"  class="form-control" value="0.0" required>
                <input type="hidden" name= "lebar"  class="form-control"  value="0.0" required>
                <input type="hidden" name= "ket_2"  class="form-control"  value="0" required>
                <input type="hidden" name= "ket_3"  class="form-control"  value="0" required>
                <input type="hidden" name= "ket_4"  class="form-control"  value="0" required>
                <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >
               

                <?php 
                    //$bahan = $this->db->query("SELECT * FROM tbl_bahan where bahan_id = ")->result();
                ?>
                <div class="form-group">
                    <label class='col-md-2'>Bahan</label>
                    <div class='col-md-10'>
                        <select name="bahan_id" id="bahan_id" style="width: 500px;"  data-plugin-selectTwo class="form-control populate placeholder" data-plugin-options='{ "placeholder": "Pilih Laminasi", "allowClear": true }'>
                            <option value="">No Selected</option>
                            <option value="40"> Laminasi Glossy</option>
                            <option value="41"> Laminasi Doft</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Cetakan / Finishing</label>
                    <div class='col-md-10'>
                        <select name="ket_1" id="ket_1" class="form-control">
                        
                        <?php 
                        foreach ($side as $key => $sd) {
                                if ($sd->side_id == $hrg->ket_1) 
                                {
                        ?>
                            <option value="<?= $sd->side_id ?>" selected><?= $sd->side_name ?></option>
                        <?php
                                }else{
                        ?>
                            <option value="<?= $sd->side_id ?>" ><?= $sd->side_name ?></option>
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
            <!-- Finishing Digital  -->
            <div id="3" class="tab-pane">
                <?php echo form_open('transaksi/action_add_temp'); ?>
                    <input type="hidden" value="<?= $kategori ?>" name="kategori_id" class="form-control" >
                    <input type="hidden" value="3" name="subkategori_id" class="form-control" >
                    <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                    <input type="hidden" value="<?= $transaksi ?>" name="kode_trx" class="form-control" >
                    <input type="hidden" value="<?= $kategorina ?>" name="kategorina" class="form-control" >
                    <input type="hidden" name= "panjang"  class="form-control" value="0.0" required>
                    <input type="hidden" name= "lebar"  class="form-control"  value="0.0" required>
                    <input type="hidden" name= "ket_1"  class="form-control"  value="0" required>
                    <input type="hidden" name= "ket_2"  class="form-control"  value="0" required>
                    <input type="hidden" name= "ket_3"  class="form-control"  value="0" required>
                    <input type="hidden" name= "ket_4"  class="form-control"  value="0" required>

                    <?php 
                        $finishing = $this->db->query("SELECT * FROM tbl_product WHERE subkategori_id = 3 ")->result();
                    ?>
                    <div class="form-group">
                        <label class='col-md-2'>Bahan Finishing</label>
                        <div class='col-md-4'>
                            <select style="width: 400px; height:20px"  name="product_id" id="bahan_finishing" required data-plugin-selectTwo class="form-control populate placeholder" data-plugin-options='{ "placeholder": "Pilih Product Finishing Digital", "allowClear": true }'>
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
                        <div class="col-md-6">
                            <select  name="harga_id" id="finishing_id" class="finishing form-control">
                                <option value="0">-- Pilih Product Finishing --</option>
                                
                            </select>
                        </div>
                        
                    </div>
                
                    <div class="form-group">
                        <label class="col-md-2 control-label">Jumlah</label>
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
            
    
    </div>
</div>

<script>
    function tampilkan(){
    var nama_kota=document.getElementById("form1").subkat.value;
        if (nama_kota=="2")
        {
            document.getElementById("tampil").innerHTML +=
            "<select name='ket_2' class='form-control'>"+
                "<option value='1'>Epson</option>"+
                "<option value='3'>UV</option>"+
                "<option value='2'>Outdoor</option>"+
            "</select>";

            document.getElementById("finish").innerHTML +=
            "<select name='ket_3' class='form-control'>"+
                "<option value='0'>Tanpa Laminasi</option>"+
                "<option value='1'>Laminasi Glossy</option>"+
                "<option value='2'>Laminasi Doft</option>"+
            "</select>";

        }else{
            document.getElementById("tampil").innerHTML="";
            document.getElementById("finish").innerHTML="";
        }
    }
</script>

    
	