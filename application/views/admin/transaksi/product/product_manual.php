<div class="modal-body">
    <div class="tabs">
        <ul class="nav nav-tabs text-left tabs-primary">
            <?php 
                foreach($subkategori as $sub){
                }
            ?>
            <li><a href="#1" data-toggle="tab">TRANSAKSI MANUAL</a></li>
           
        </ul>
        <div class="tab-content">
            
            <div id="1" class="tab-pane active"> <!-- Jersey -->
            <?php echo form_open('transaksi/action_add_temp'); ?>   
                
                    <input type="hidden" value="5" name="kategori_id" class="form-control" >
                    <input type="hidden" value="100" name="subkategori_id" class="form-control" >
                    <input type="hidden" value="471" name="bahan_id" class="form-control" >
                    <input type="hidden" value="<?= $this->session->userdata('id'); ?>" name="user_id" class="form-control" >
                    <input type="hidden" value="<?= $kode ?>" name="kode_trx" class="form-control" >
                    <input type="hidden" name= "ket_1"  class="form-control"  value="0" required>
                    <input type="hidden" name= "ket_2"  class="form-control"  value="0" required>
                    <input type="hidden" name= "ket_3"  class="form-control"  value="0" required>
                    <input type="hidden" name= "ket_4"  class="form-control"  value="0" required>
                    

                    <div class="form-group">
                        <label class='col-md-3'>Nama Pesanan / Jasa / Product </label>
                        <div class='col-md-9'>
                            <input type="text" name="product_nama" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class='col-md-3'>Qty / Harga Satuan </label>
                        <div class='col-md-3'>
                            <input type="text" name="qty" class="form-control">
                        </div>
                        <div class='col-md-6'>
                            <input type="text" name="product_nama" class="form-control">
                        </div>
                    </div>
                
                
                    
                
                    <div class="form-group">
                
                        <button type="submit" class="btn btn-success col-md-12"><i class="icon-pencil"></i> Tambah Item</button>
                    </div>
                    </div>
                </form>
            </div>
            
            
        </div>
    
    
    
</div>