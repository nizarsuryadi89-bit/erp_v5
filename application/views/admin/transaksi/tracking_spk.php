<div class="container-fluid px-4">
    <!-- Header & Description -->
    <div class="row mb-3">
        <div class="col-12">
            <h2 class="fw-bold mb-1"><i class="bi bi-gear-wide-connected"></i> Produksi</h2>
            <p class="text-muted mb-0">Pantau dan kelola proses produksi secara real-time. Lihat status, progres, dan detail setiap SPK produksi.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <?php
            $total = count($tampilData);
            $berjalan = 0; $selesai = 0; $pb = 0; $menunggu = 0; $total_qty = 0; $total_qty_produksi = 0;
            foreach ($tampilData as $dt) {
                $total_qty += (int)$dt->qty;
                $total_qty_produksi += (int)$dt->qty_produksi;
                if ($dt->status_produksi == '0') $menunggu++;
                elseif ($dt->status_produksi == '1') $berjalan++;
                elseif ($dt->status_produksi == '2') $pb++;
                elseif ($dt->status_produksi == '3') $selesai++;
            }
        ?>
        <div class="col-6 col-md-2">
            <div class="card text-bg-primary shadow-sm">
                <div class="card-body p-2 text-center">
                    <div class="fs-4 fw-bold"><?= $total ?></div>
                    <div class="small">Total Produksi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card text-bg-warning shadow-sm">
                <div class="card-body p-2 text-center">
                    <div class="fs-4 fw-bold"><?= $menunggu ?></div>
                    <div class="small">Menunggu</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card text-bg-info shadow-sm">
                <div class="card-body p-2 text-center">
                    <div class="fs-4 fw-bold"><?= $berjalan ?></div>
                    <div class="small">Sedang Berjalan</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card text-bg-danger shadow-sm">
                <div class="card-body p-2 text-center">
                    <div class="fs-4 fw-bold"><?= $pb ?></div>
                    <div class="small">PB/Pengambilan Barang</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card text-bg-success shadow-sm">
                <div class="card-body p-2 text-center">
                    <div class="fs-4 fw-bold"><?= $selesai ?></div>
                    <div class="small">Selesai</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card text-bg-light shadow-sm">
                <div class="card-body p-2 text-center">
                    <div class="fs-5 fw-bold text-dark">Qty: <?= $total_qty_produksi ?> / <?= $total_qty ?></div>
                    <div class="small text-dark">Total Qty Produksi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter/Search Bar -->
    <div class="row mb-3">
        <div class="col-12">
            <form class="row g-2 align-items-center" method="get" action="">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="spk" placeholder="Cari SPK/Nama Produk...">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">Status</option>
                        <option value="0">Menunggu</option>
                        <option value="1">Sedang Berjalan</option>
                        <option value="2">PB/Perbaikan</option>
                        <option value="3">Selesai</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="operator" placeholder="Operator/Mesin">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="tanggal">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="row">
        <section class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" id="datatable-details">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>SPK</th>
                                    <th>Lokasi Cetak</th>
                                    
                                  
                                    <th width="20%">Nama Konsumen</th>
                                    <th width="20%">Product Utama</th>
                                  
                                   
                                    <th>Status</th>
                                   
                                </tr>
                            </thead>
                           <tbody>
                                <?php 
                                    $no = 1;
                                    $groupedData = []; // Array untuk menyimpan data yang dikelompokkan berdasarkan nospk

                                    // Kelompokkan data berdasarkan nospk
                                    foreach ($tampilData as $dt) {
                                        $groupedData[$dt->nospk][] = $dt; // Mengelompokkan berdasarkan nospk
                                    }

                                    // Looping melalui setiap grup yang sudah dikelompokkan berdasarkan nospk
                                    foreach ($groupedData as $nospk => $dataGroup): 
                                        $firstData = $dataGroup[0]; // Ambil data pertama untuk nospk ini
                                ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td>
                                        <span class="fw-bold">#<?= $nospk ?></span><br>
                                        <!-- <a href="<?= base_url('transaksi/cetak_invoice_ulang/'.$nospk)?>" target="blank"><span class="badge bg-warning">Cetak SPK</span></a> -->
                                    </td>
                                    <td>
                                        <?php 
                                        // Menampilkan lokasi cetak yang sesuai untuk nospk ini
                                        if ($firstData->lokasi_cetak == '2'): ?>
                                            <span class="badge bg-secondary">TGM Pusat</span>
                                        <?php elseif ($firstData->lokasi_cetak == '3'): ?>
                                            <span class="badge bg-light text-dark">TGM Merch</span>
                                        <?php endif; ?>
                                        
                                    </td>
                                    <td>
                                        <span class="fw-semibold"><?= $firstData->konsumen_nama ?? '-' ?></span>
                                    </td>
                                    <td class="text-start">
                                        <?php
                                        
                                        // Menampilkan produk dan operator untuk nospk pertama
                                        echo '<b>' . $firstData->product_nama . '</b>';
                                        
                                        // echo 'Operator : ' . $firstData->operator_nama;
                                        ?>
                                    </td>
                                    
                                    
                                    <td class="text-center">
                                        <?= $status ?>
                                    </td>
                                    
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
        </section>
    </div>
</div>








