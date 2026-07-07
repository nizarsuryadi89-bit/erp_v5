<!-- Contoh penggunaan status produksi di view -->

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Status Produksi</h5>
    </div>
    <div class="card-body">
        <?php foreach ($tampilData as $produksi): ?>
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>No SPK:</strong> <?= $produksi->nospk ?>
                </div>
                <div class="col-md-6">
                    <strong>Status:</strong> 
                    <?php if ($produksi->status_produksi == 'sedang_di_produksi'): ?>
                        <span class="badge bg-warning"><?= $produksi->status_produksi_text ?></span>
                    <?php else: ?>
                        <span class="badge bg-secondary"><?= $produksi->status_produksi_text ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($produksi->detail_produksi): ?>
                <div class="alert alert-info">
                    <strong>Detail Produksi:</strong><br>
                    <small>
                        Qty Produksi: <?= $produksi->detail_produksi->qty_produksi ?><br>
                        Mesin ID: <?= $produksi->detail_produksi->mesin_id ?><br>
                        Lokasi: <?= $produksi->detail_produksi->lokasi_cetak ?>
                    </small>
                </div>
            <?php endif; ?>
            
            <hr>
        <?php endforeach; ?>
    </div>
</div>

<!-- Contoh untuk tabel -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>No SPK</th>
            <th>Produk</th>
            <th>Status Produksi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tampilData as $produksi): ?>
            <tr>
                <td><?= $produksi->nospk ?></td>
                <td><?= $produksi->product_nama ?></td>
                <td>
                    <?php if ($produksi->status_produksi == 'sedang_di_produksi'): ?>
                        <span class="badge bg-warning"><?= $produksi->status_produksi_text ?></span>
                    <?php else: ?>
                        <span class="badge bg-secondary"><?= $produksi->status_produksi_text ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($produksi->status_produksi == 'belum_di_produksi'): ?>
                        <a href="<?= base_url('produksi/produksi_product/' . $produksi->routing_urutan . '/' . $produksi->produksi_id) ?>" 
                           class="btn btn-sm btn-primary">Mulai Produksi</a>
                    <?php else: ?>
                        <span class="text-muted">Sedang diproses</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table> 