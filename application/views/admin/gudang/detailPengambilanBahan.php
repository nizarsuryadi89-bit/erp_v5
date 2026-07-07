

        <!-- start: page -->
        <section class="panel">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                        <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                    </div>

                    <h2 class="panel-title"> <a href="<?= base_url('warehouse/laporan-stok')?>">Kembali</a> -- <?= $title ?> </h2>
                </header>
                <div class="panel-body">

                        
                        
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Pengambilan</th>
                                    <th>Petugas</th>
                                    <th>Dikirim Ke Gudang Jasa</th>
                                    <th>Bahan</th>
                                    
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $no = 1;
                                    foreach ($tampilData as $key => $value) {
                                    
                                ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td class="text-left"><?= date_indo($value->pengambilan_tgl) ?></td>
                                    <td class="text-left"><?= $value->karyawan_nama ?></td>
                                    <td class="text-left"><?= $value->nama_perusahaan ?></td>
                                    <td class="text-left"><?= $value->bahan_nama ?></td>
                                
                                    <td class="text-right"><?= number_format($value->pengambilan_qty) ?></td>
                                    
                                    
                                </tr>
                                <?php
                                
                                    $no++;
                                    }
                                ?>
                                
                                
                            </tbody>
                        </table>
                        
                        
                    
                    
                
                    
                </div>
            </section>
        <!-- end: page -->
    </section>

</div>