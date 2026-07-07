<div class="app-content">
    <div class="container-fluid">
    
    <!-- start: page -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <strong>Jumlah Data:</strong> <?= $total_rows ?>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="datatable-details">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Konsumen</th>
                                <th>No HP</th>
                                <th>Jumlah Transaksi</th>
                                <th>Total Transaksi</th>
                                <th>Point</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $no = 1;
                                foreach ($tampil_data as $dt):
                                    $point = $dt->total_transaksi / 250000;
                            ?>
                            <tr>
                                <td><?= $no ?></td>
                                <td><?= $dt->konsumen_nama ?></td>
                                <td><?= $dt->konsumen_nohp ?></td>
                                <td><?= $dt->jumlah_transaksi ?></td>
                                <td class="text-end">Rp. <?= number_format($dt->total_transaksi) ?></td>
                                <td class="text-end"><?= floor($point) ?></td>
                            </tr>
                            <?php 
                                $no++;
                                endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- end: page -->
</div>
