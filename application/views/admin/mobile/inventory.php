<div class="profile-card">
    <div class="card shadow-sm mb-3">
        <div class="card-body text-center">
            <!-- <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" class="img-fluid mb-2" style="max-width: 100px;"> -->
            <h5 class="card-title">Gudang Utama</h5>
            <p class="card-text">Monitoring Stok Bahan Baku</p>
            <a href="<?= base_url('warehouse/monitoring') ?>" class="btn btn-primary">
                <i class="bi bi-eye"></i> Lihat Monitoring
            </a>
        </div>
        <div class="card-body text-center">
            <h5 class="card-title">Daftar Stok Bahan Baku</h5>
            <p class="card-text">Berikut adalah daftar stok bahan baku yang tersedia di gudang utama.</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <div class="table-wrapper">
                    <div class="d-none d-md-block">
                        <h6 class="mb-3">Daftar Stok Bahan Baku (Desktop View)</h6>
                        <div class="table-wrapper-desktop table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Bahan</th>
                                        <th class="text-end">Jumlah Stok</th>
                                        <th class="text-end">Satuan</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tampildata as $p): 
                                        $stok     = ($p->stok_awal + $p->stok_tambah) - $p->stok_kurang;
                                        $stok_min = $p->stok_min;
                                        $is_low   = $stok < $stok_min;
                                    ?>
                                    <tr class="<?= $is_low ? 'table-warning' : '' ?>">
                                        <td><?= htmlspecialchars($p->bahan_nama) ?></td>
                                        <td class="text-end"><?= number_format($stok) ?></td>
                                        <td class="text-end"><?= $p->satuan_nama ?></td>
                                        <td class="text-center">
                                            <?php if ($is_low): ?>
                                                <span class="badge bg-danger">Stok Menipis</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Aman</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="d-block d-md-none">
                        <h6 class="mb-3">Daftar Stok Bahan Baku (Mobile View)</h6>
                        <div class="table-responsive-mobile">
                            <?php foreach ($tampildata as $p): 
                                $stok     = ($p->stok_awal + $p->stok_tambah) - $p->stok_kurang;
                                $stok_min = $p->stok_min;
                                $is_low   = $stok < $stok_min;
                            ?>
                            <div class="card shadow-sm mb-2 <?= $is_low ? 'border-warning bg-light' : '' ?>">
                                <div class="card-body p-2">
                                    <h6 class="mb-1"><?= htmlspecialchars($p->bahan_nama) ?></h6>
                                    <p class="mb-1">Jumlah Stok: <strong><?= number_format($stok) ?></strong></p>
                                    <p class="mb-1">Satuan: <?= $p->satuan_nama ?></p>
                                    <p class="mb-0">
                                        Status:
                                        <?php if ($is_low): ?>
                                            <span class="badge bg-danger">Stok Menipis</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Aman</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="d-none d-md-block">
    <h6 class="mb-3">Daftar Stok Bahan Baku (Desktop View)</h6>
    </div>                  

    <!-- Tabel untuk Desktop/Tablet -->
    

    <!-- Tampilan Kartu untuk Mobile -->
    
    </div>
     </div>
    </div>
