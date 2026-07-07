
<div class="row">
    

        <!-- start: page -->
        <section class="panel">
                <header class="panel-heading">
            
                </header>
                <div class="panel-body">
                    <div class="row">
                        
                        <div class="col-sm-6">
                            <!-- Jumlah Data : <?= $total_rows ?> -->
                        </div>
                        <div class="col-sm-6 text-right">
                            <div class="mb-md">
                                <!-- <a href="<?= base_url($controller.'/formAdd')?>" class="btn btn-primary">Add <?= $controller ?> <i class="fa fa-plus"></i></a>
                                <a href="<?= base_url($controller.'/formAdd')?>" class="btn btn-success">Cetak <?= $controller ?> <i class="fa fa-print"></i></a> -->
                            </div>
                        </div>
                    </div>
                    
                    <table class="table table-bordered table-striped mb-none" id="datatable-details">		
								
                        <thead>
                            <tr>
                                <th>No</th>
                                <th width="40%">Keterangan</th>
                                <th width="10%">Satuan</th>
                                <th width="10%">Stok <br> Awal Bulan</th>
                                <th width="10%">Stok <br> Tambah</th>
                                <th width="10%">Kirim Ke <br> Gudang Jasa</th>
                                <th width="10%">Stok <br>Aktif</th>
                                
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $no =1;
                                foreach ($tampilData as $dt){
                                    $status         = ($dt->status == '1') ? 'Aktif' : 'Tidak Aktif';
                                    $stokaktif      = (@$dt->stok_awal + @$dt->stok_tambah) - @$dt->stok_kurang;
                                    $stokturunan    = $stokaktif * @$dt->satuan_turunan;
                                    $stokmin        = ($stokaktif < $dt->stok_min) ? '<span class="label label-danger">Stok Min</span>' : '<span class="label label-success">Stok Max</span>';
                            ?>
                            <tr >
                                <td><?= $no ?></td>
                                <td>
                                    <!-- Barcode : <strong><?= $dt->barcode ?></strong>   <br> -->
                                    <strong><?= $dt->bahan_nama ?></strong>  &nbsp;&nbsp;&nbsp;
                                    <a href="<?= base_url('bahan/formUpdate/'.$dt->bahan_id)?>">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <td><?= $dt->satuan_nama ?></td>
                                <td class="text-right"><strong class="text-success"><?= $dt->stok_awal ?></strong></td>
                                <td class="text-right"><a href="<?= base_url('warehouse/getTambahan/'.$dt->bahan_id)?>"><?= $dt->stok_tambah ?></a></td>
                                <td class="text-right"><a href="<?= base_url('warehouse/getPengurangan/'.$dt->bahan_id)?>"><?= $dt->stok_kurang ?></td>
                                <td class="text-right">
                                    <?php 
                                        if ($stokaktif > 0){
                                            echo '<strong class="text-success">'.number_format($stokaktif).'</strong>';
                                            echo '<br>';
                                            // echo '<strong class="text-success">'.number_format($stokturunan).'</strong>';
                                        }else{
                                            echo '<strong class="text-danger">'.$stokaktif.'</strong>';
                                        }
                                    ?>
                                </td>
                                
                                <td class="actions text-center">
                                    <?php 
                                    //echo date('d');
                                        if ((date('d') > '28') AND (date('d') <= '31')) {
                                    ?>
                                    <form action="<?= base_url($controller.'/reloadstok/')?>" method="post">
                                        <input type="hidden" name="stok_id" id="" value="<?= $dt->stok_id?>">
                                        <input type="hidden" name="stok_awal" id="" value="<?= $dt->stok_awal?>">
                                        <input type="hidden" name="stok_tambah" id="" value="<?= $dt->stok_tambah?>">
                                        <input type="hidden" name="stok_kurang" id="" value="<?= $dt->stok_kurang?>">
                                        <input type="hidden" name="stokaktif" id="" value="<?= $stokaktif?>">
                                        <button type="submit" class="btn btn-primary btn-xs"> Reload </button><br>
                                    </form>
                                    <?php 
                                        }
                                    ?>
                                    <?= $stokmin ?>
                                </td>
                            </tr>
                            <?php 
                                $no++;
                                }
                            ?>
                        </tbody>
                    </table>
                    <hr>
                    Keterangan : 
                    <ul>
                        <li>Stok Awal : Stok Awal Bulan</li>
                        <li>Stok Tambahan : Stok Tambahan pada bulan berjalan</li>
                        <li>Stok Keluar : Stok yang diambil pada bulan berjalan </li>
                        <li>Stok Aktif : Stok yang tersisa di gudang </li>
                    </ul>
                </div>
            </section>
        <!-- end: page -->
    </section>

</div>